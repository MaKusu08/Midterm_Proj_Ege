<?php

session_start();

require_once "../../database/db.php";

/*
|--------------------------------------------------------------------------
| Admin Access
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {
    header("Location: ../../login/login.php");
    exit;
}

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../../dashboard.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Get Court ID
|--------------------------------------------------------------------------
*/

$court_id = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

if (!$court_id || $court_id < 1) {
    header(
        "Location: courts.php?error=" .
        urlencode("Invalid court ID.")
    );
    exit;
}

/*
|--------------------------------------------------------------------------
| Get Court
|--------------------------------------------------------------------------
*/

try {

    $pdo = getConnection();

    $stmt = $pdo->prepare("
        SELECT
            id,
            court_name,
            location,
            image,
            rating,
            status
        FROM courts
        WHERE id = :id
        LIMIT 1
    ");

    $stmt->execute([
        ":id" => $court_id
    ]);

    $court = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$court) {

        header(
            "Location: courts.php?error=" .
            urlencode("Court not found.")
        );

        exit;
    }

} catch (PDOException $e) {

    die(
        "Database Error: " .
        htmlspecialchars($e->getMessage())
    );
}

/*
|--------------------------------------------------------------------------
| Form Values
|--------------------------------------------------------------------------
*/

$court_name = $court["court_name"];
$location   = $court["location"];
$image      = $court["image"];
$rating     = $court["rating"];
$status     = $court["status"];

$error = "";

/*
|--------------------------------------------------------------------------
| Update Court
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $court_name = trim($_POST["court_name"] ?? "");
    $location   = trim($_POST["location"] ?? "");
    $rating     = trim($_POST["rating"] ?? "0.0");
    $status     = strtolower(trim($_POST["status"] ?? ""));

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if ($court_name === "") {

        $error = "Court name is required.";

    } elseif ($location === "") {

        $error = "Location is required.";

    } elseif (
        !is_numeric($rating) ||
        $rating < 0 ||
        $rating > 5
    ) {

        $error = "Rating must be between 0 and 5.";

    } elseif (
        $status !== "available" &&
        $status !== "maintenance"
    ) {

        $error = "Invalid court status.";
    }

    /*
    |--------------------------------------------------------------------------
    | Image Upload
    |--------------------------------------------------------------------------
    */

    $newImage = null;

    if (
        $error === "" &&
        isset($_FILES["image"]) &&
        $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {

        if ($_FILES["image"]["error"] === UPLOAD_ERR_OK) {

            $file = $_FILES["image"];

            $allowedTypes = [
                "image/jpeg" => "jpg",
                "image/png"  => "png",
                "image/webp" => "webp"
            ];

            $fileType = mime_content_type($file["tmp_name"]);

            if (!isset($allowedTypes[$fileType])) {

                $error = "Only JPG, PNG, and WEBP images are allowed.";

            } elseif ($file["size"] > 5 * 1024 * 1024) {

                $error = "Image must not be larger than 5MB.";

            } else {

                $extension = $allowedTypes[$fileType];

                $fileName =
                    "court_" .
                    time() .
                    "_" .
                    bin2hex(random_bytes(4)) .
                    "." .
                    $extension;

                $uploadDirectory = "../../assets/";

                $uploadPath =
                    $uploadDirectory .
                    $fileName;

                if (
                    move_uploaded_file(
                        $file["tmp_name"],
                        $uploadPath
                    )
                ) {

                    $newImage = "assets/" . $fileName;

                } else {

                    $error = "Failed to upload the image.";
                }
            }

        } else {

            $error = "There was an error uploading the image.";
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update Database
    |--------------------------------------------------------------------------
    */

    if ($error === "") {

        try {

            /*
            |--------------------------------------------------------------------------
            | Keep Existing Image If No New Image Was Uploaded
            |--------------------------------------------------------------------------
            */

            if ($newImage !== null) {

                $image = $newImage;
            }

            $stmt = $pdo->prepare("
                UPDATE courts
                SET
                    court_name = :court_name,
                    location = :location,
                    image = :image,
                    rating = :rating,
                    status = :status
                WHERE id = :id
            ");

            $stmt->execute([
                ":court_name" => $court_name,
                ":location"   => $location,
                ":image"      => $image,
                ":rating"     => $rating,
                ":status"     => $status,
                ":id"         => $court_id
            ]);

            /*
            |--------------------------------------------------------------------------
            | Delete Old Image After Successful Update
            |--------------------------------------------------------------------------
            */

            if (
                $newImage !== null &&
                !empty($court["image"])
            ) {

                $oldImage = $court["image"];

                if (
                    !preg_match(
                        '/^(https?:)?\/\//i',
                        $oldImage
                    )
                ) {

                    $oldImage = ltrim(
                        str_replace("\\", "/", $oldImage),
                        "/"
                    );

                    while (
                        str_starts_with(
                            $oldImage,
                            "../"
                        )
                    ) {
                        $oldImage = substr(
                            $oldImage,
                            3
                        );
                    }

                    if (
                        str_starts_with(
                            $oldImage,
                            "assets/"
                        )
                    ) {

                        $oldImagePath =
                            "../../" . $oldImage;

                        if (
                            file_exists(
                                $oldImagePath
                            )
                        ) {

                            unlink(
                                $oldImagePath
                            );
                        }
                    }
                }
            }

            header(
                "Location: courts.php?success=" .
                urlencode(
                    "Court updated successfully."
                )
            );

            exit;

        } catch (PDOException $e) {

            /*
            |--------------------------------------------------------------------------
            | Remove New Image If Database Update Fails
            |--------------------------------------------------------------------------
            */

            if ($newImage !== null) {

                $newImagePath =
                    "../../" . $newImage;

                if (
                    file_exists(
                        $newImagePath
                    )
                ) {

                    unlink(
                        $newImagePath
                    );
                }
            }

            $error =
                "Database Error: " .
                $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Court | PickServe</title>

    <link
        rel="stylesheet"
        href="../../styles/style.css"
    >

    <link
        rel="stylesheet"
        href="../../styles/admin_styles.css"
    >

</head>

<body>

<!-- ================================================================
     ADMIN NAVBAR
================================================================ -->

<header class="admin-navbar">

    <a
        href="../dashboard.php"
        class="admin-logo"
    >
        Pick<span>Serve</span>
    </a>

    <nav class="admin-nav">

        <a href="../dashboard.php">
            Dashboard
        </a>

        <a
            href="courts.php"
            class="active"
        >
            Courts
        </a>

        <a href="../reservations/reservations.php">
            Reservations
        </a>

        <a href="../users/users.php">
            Users
        </a>

        <a href="../messages/messages.php">
            Messages
        </a>

        <a
            href="../../logout.php"
            class="logout-btn"
        >
            Logout
        </a>

    </nav>

</header>


<!-- ================================================================
     MAIN
================================================================ -->

<main class="admin-main">

    <div class="admin-page-header">

        <div>

            <h1>
                Edit Court
            </h1>

            <p>
                Update the information for this court.
            </p>

        </div>

        <a
            href="courts.php"
            class="admin-secondary-btn"
        >
            ← Back to Courts
        </a>

    </div>


    <!-- ============================================================
         ERROR
    ============================================================= -->

    <?php if ($error !== ""): ?>

        <div class="admin-message error">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <!-- ============================================================
         FORM
    ============================================================= -->

    <section class="admin-form-section">

        <form
            method="POST"
            enctype="multipart/form-data"
            class="admin-form"
        >

            <!-- Court Name -->

            <div class="admin-form-group">

                <label for="court_name">
                    Court Name
                </label>

                <input
                    type="text"
                    id="court_name"
                    name="court_name"
                    value="<?= htmlspecialchars($court_name) ?>"
                    maxlength="100"
                    required
                >

            </div>


            <!-- Location -->

            <div class="admin-form-group">

                <label for="location">
                    Location
                </label>

                <input
                    type="text"
                    id="location"
                    name="location"
                    value="<?= htmlspecialchars($location) ?>"
                    maxlength="255"
                    required
                >

            </div>


            <!-- Image Upload -->

            <div class="admin-form-group">

                <label for="image">
                    Court Image
                </label>

                <?php if (!empty($image)): ?>

                    <small>
                        Current image:
                        <strong>
                            <?= htmlspecialchars(
                                basename($image)
                            ) ?>
                        </strong>
                    </small>

                <?php endif; ?>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                >

                <small>
                    Choose a new image only if you want to replace
                    the current image. JPG, PNG, or WEBP — maximum 5MB.
                </small>

            </div>


            <!-- Rating -->

            <div class="admin-form-group">

                <label for="rating">
                    Rating
                </label>

                <input
                    type="number"
                    id="rating"
                    name="rating"
                    value="<?= htmlspecialchars($rating) ?>"
                    min="0"
                    max="5"
                    step="0.1"
                    required
                >

            </div>


            <!-- Status -->

            <div class="admin-form-group">

                <label for="status">
                    Status
                </label>

                <select
                    id="status"
                    name="status"
                    required
                >

                    <option
                        value="available"
                        <?= $status === "available"
                            ? "selected"
                            : "" ?>
                    >
                        Available
                    </option>

                    <option
                        value="maintenance"
                        <?= $status === "maintenance"
                            ? "selected"
                            : "" ?>
                    >
                        Maintenance
                    </option>

                </select>

            </div>


            <!-- Buttons -->

            <div class="admin-form-actions">

                <a
                    href="courts.php"
                    class="admin-secondary-btn"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="admin-primary-btn"
                >
                    Save Changes
                </button>

            </div>

        </form>

    </section>

</main>

</body>

</html>