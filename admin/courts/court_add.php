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
| Form Values
|--------------------------------------------------------------------------
*/

$court_name = "";
$location   = "";
$image      = "";
$rating     = "0.0";
$status     = "available";
$error      = "";

/*
|--------------------------------------------------------------------------
| Add Court
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $court_name = trim($_POST["court_name"] ?? "");
    $location   = trim($_POST["location"] ?? "");
    $rating     = trim($_POST["rating"] ?? "0.0");
    $status     = strtolower(
        trim($_POST["status"] ?? "available")
    );

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

    if (
        $error === "" &&
        isset($_FILES["image"])
    ) {

        if (
            $_FILES["image"]["error"] ===
            UPLOAD_ERR_OK
        ) {

            $file = $_FILES["image"];

            $allowedTypes = [
                "image/jpeg" => "jpg",
                "image/png"  => "png",
                "image/webp" => "webp"
            ];

            $fileType = mime_content_type(
                $file["tmp_name"]
            );

            if (!isset($allowedTypes[$fileType])) {

                $error =
                    "Only JPG, PNG, and WEBP images are allowed.";

            } elseif (
                $file["size"] > 5 * 1024 * 1024
            ) {

                $error =
                    "Image must not be larger than 5MB.";

            } else {

                $extension =
                    $allowedTypes[$fileType];

                $fileName =
                    "court_" .
                    time() .
                    "_" .
                    bin2hex(random_bytes(4)) .
                    "." .
                    $extension;

                $uploadDirectory =
                    "../../assets/";

                $uploadPath =
                    $uploadDirectory .
                    $fileName;

                if (
                    move_uploaded_file(
                        $file["tmp_name"],
                        $uploadPath
                    )
                ) {

                    $image =
                        "assets/" .
                        $fileName;

                } else {

                    $error =
                        "Failed to upload the image.";

                }

            }

        } elseif (
            $_FILES["image"]["error"] !==
            UPLOAD_ERR_NO_FILE
        ) {

            $error =
                "There was an error uploading the image.";

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Insert Court
    |--------------------------------------------------------------------------
    */

    if ($error === "") {

        try {

            $pdo = getConnection();

            $stmt = $pdo->prepare("
                INSERT INTO courts
                (
                    court_name,
                    location,
                    image,
                    rating,
                    status
                )
                VALUES
                (
                    :court_name,
                    :location,
                    :image,
                    :rating,
                    :status
                )
            ");

            $stmt->execute([
                ":court_name" => $court_name,
                ":location"   => $location,
                ":image"      => $image,
                ":rating"     => $rating,
                ":status"     => $status
            ]);

            header(
                "Location: courts.php?success=" .
                urlencode(
                    "Court added successfully."
                )
            );

            exit;

        } catch (PDOException $e) {

            /*
            |--------------------------------------------------------------------------
            | Remove Uploaded Image If Database Insert Fails
            |--------------------------------------------------------------------------
            */

            if ($image !== "") {

                $uploadedFile =
                    "../../" . $image;

                if (file_exists($uploadedFile)) {
                    unlink($uploadedFile);
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

    <title>Add Court | PickServe</title>

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
                Add Court
            </h1>

            <p>
                Add a new pickleball court to PickServe.
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
                    placeholder="e.g. PickServe Court 1"
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
                    placeholder="e.g. Dumaguete City"
                    maxlength="255"
                    required
                >

            </div>


            <!-- Image Upload -->

            <div class="admin-form-group">

                <label for="image">
                    Court Image
                </label>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                >

                <small>
                    Upload a JPG, PNG, or WEBP image.
                    Maximum size: 5MB.
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
                    Add Court
                </button>

            </div>

        </form>

    </section>

</main>

</body>

</html>

