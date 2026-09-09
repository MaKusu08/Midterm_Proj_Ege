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
| Get Courts
|--------------------------------------------------------------------------
*/

try {

    $pdo = getConnection();

    $stmt = $pdo->query("
        SELECT
            id,
            court_name,
            location,
            image,
            rating,
            status,
            created_at
        FROM courts
        ORDER BY id ASC
    ");

    $courts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    die(
        "Database Error: " .
        htmlspecialchars($e->getMessage())
    );

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

    <title>Manage Courts | PickServe</title>

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
        <small>ADMIN</small>
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
                Manage Courts
            </h1>

            <p>
                Add, edit, and manage PickServe courts.
            </p>

        </div>


        <a
            href="court_add.php"
            class="admin-primary-btn"
        >
            + Add Court
        </a>

    </div>


    <!-- ============================================================
         MESSAGES
    ============================================================= -->

    <?php if (!empty($_GET["success"])): ?>

        <div class="admin-message success">

            <?= htmlspecialchars($_GET["success"]) ?>

        </div>

    <?php endif; ?>


    <?php if (!empty($_GET["error"])): ?>

        <div class="admin-message error">

            <?= htmlspecialchars($_GET["error"]) ?>

        </div>

    <?php endif; ?>


    <!-- ============================================================
         COURTS SECTION
    ============================================================= -->

    <section class="admin-section">

        <div class="admin-section-header">

            <div>

                <h2>
                    Courts
                </h2>

                <p>
                    <?= count($courts) ?> court(s) registered.
                </p>

            </div>

        </div>


        <?php if (empty($courts)): ?>

            <div class="admin-empty">

                <h3>
                    No Courts Found
                </h3>

                <p>
                    There are currently no courts in the system.
                </p>

                <a
                    href="court_add.php"
                    class="admin-primary-btn"
                >
                    + Add Court
                </a>

            </div>


        <?php else: ?>


            <div class="admin-table-wrapper">

                <table class="admin-table">

                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Image
                            </th>

                            <th>
                                Court
                            </th>

                            <th>
                                Location
                            </th>

                            <th>
                                Rating
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Created
                            </th>

                            <th>
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($courts as $court): ?>

                        <?php

                        $status = strtolower(
                            trim((string) $court["status"])
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | Image Path
                        |--------------------------------------------------------------------------
                        */

                        $image = trim(
                            (string) $court["image"]
                        );

                        if ($image === "") {

                            $imagePath = "../../assets/court1.png";

                        } elseif (
                            preg_match(
                                '/^(https?:)?\/\//i',
                                $image
                            )
                        ) {

                            $imagePath = $image;

                        } else {

                            $image = str_replace(
                                "\\",
                                "/",
                                $image
                            );

                            $image = ltrim(
                                $image,
                                "/"
                            );

                            while (
                                str_starts_with(
                                    $image,
                                    "../"
                                )
                            ) {

                                $image = substr(
                                    $image,
                                    3
                                );

                            }

                            if (
                                str_starts_with(
                                    $image,
                                    "assets/"
                                )
                            ) {

                                $imagePath = "../../" . $image;

                            } else {

                                $imagePath =
                                    "../../assets/" . $image;

                            }

                        }

                        ?>


                        <tr>

                            <!-- ID -->

                            <td>

                                #<?= (int) $court["id"] ?>

                            </td>


                            <!-- IMAGE -->

                            <td>

                                <img
                                    src="<?= htmlspecialchars($imagePath) ?>"
                                    alt="<?= htmlspecialchars($court["court_name"]) ?>"
                                    class="admin-court-image"
                                >

                            </td>


                            <!-- COURT -->

                            <td>

                                <strong>
                                    <?= htmlspecialchars(
                                        $court["court_name"]
                                    ) ?>
                                </strong>

                            </td>


                            <!-- LOCATION -->

                            <td>

                                <?= htmlspecialchars(
                                    $court["location"]
                                ) ?>

                            </td>


                            <!-- RATING -->

                            <td>

                                ⭐
                                <?= number_format(
                                    (float) $court["rating"],
                                    1
                                ) ?>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <?php if ($status === "available"): ?>

                                    <span class="admin-status available">
                                        Available
                                    </span>

                                <?php elseif ($status === "maintenance"): ?>

                                    <span class="admin-status maintenance">
                                        Maintenance
                                    </span>

                                <?php else: ?>

                                    <span class="admin-status">

                                        <?= htmlspecialchars(
                                            ucfirst($status)
                                        ) ?>

                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- CREATED -->

                            <td>

                                <?= htmlspecialchars(
                                    date(
                                        "M d, Y",
                                        strtotime(
                                            $court["created_at"]
                                        )
                                    )
                                ) ?>

                            </td>


                            <!-- ACTIONS -->

                            <td>

                                <div class="admin-actions">

                                    <a
                                        href="court_edit.php?id=<?= (int) $court["id"] ?>"
                                        class="admin-edit-btn"
                                    >
                                        Edit
                                    </a>


                                    <a
                                        href="court_delete.php?id=<?= (int) $court["id"] ?>"
                                        class="admin-delete-btn"
                                        onclick="return confirm('Are you sure you want to delete this court?');"
                                    >
                                        Delete
                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </section>

</main>

</body>

</html>

