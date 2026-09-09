<?php

session_start();

require_once "../database/db.php";

/*
|--------------------------------------------------------------------------
| Check Login
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login/login.php");
    exit;
}

$user_id   = (int) $_SESSION["user_id"];
$username  = $_SESSION["username"] ?? "";
$full_name = $_SESSION["full_name"] ?? "";

/*
|--------------------------------------------------------------------------
| Get User Reservations
|--------------------------------------------------------------------------
*/

$reservations = [];

try {

    $pdo = getConnection();

    $stmt = $pdo->prepare(
        "SELECT
            r.id,
            r.court_number,
            r.booking_date,
            r.booking_time,
            r.duration,
            r.status,
            r.created_at,
            c.court_name,
            c.location
         FROM reservations r
         INNER JOIN courts c
            ON r.court_id = c.id
         WHERE r.user_id = :user_id
         ORDER BY r.booking_date DESC, r.booking_time DESC"
    );

    $stmt->execute([
        ":user_id" => $user_id
    ]);

    $reservations = $stmt->fetchAll();

} catch (PDOException $e) {

    $reservations = [];

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

    <title>My Reservations | PickServe</title>

    <link
        rel="stylesheet"
        href="../styles/style.css"
    >

    <link
        rel="stylesheet"
        href="../styles/dashboard_style.css"
    >

    <link
        rel="stylesheet"
        href="../styles/reservation_style.css"
    >

</head>

<body>

<!-- ================================================================
     NAVBAR
================================================================ -->

<header class="navbar">

    <a href="../dashboard.php" class="logo">
        Pick<span>Serve</span>
    </a>

    <nav class="nav-links">

        <a href="../dashboard.php">
            Dashboard
        </a>

        <a href="booking.php">
            Book a Court
        </a>

        <a href="reservations.php" class="active">
            My Reservations
        </a>

        <a href="../logout.php" class="logout-btn">
            Logout
        </a>

    </nav>

</header>


<!-- ================================================================
     MAIN CONTENT
================================================================ -->

<main class="reservations-main">

    <div class="reservations-container">


        <!-- ========================================================
             PAGE HEADER
        ========================================================= -->

        <div class="reservations-header">

            <h1>
                My Reservations
            </h1>

            <p>
                View your upcoming and previous court reservations.
            </p>

        </div>


        <!-- ========================================================
             SUCCESS MESSAGE
        ========================================================= -->

        <?php if (isset($_GET["success"])): ?>

            <div class="success-message">

                Your booking has been successfully submitted!

            </div>

        <?php endif; ?>


        <!-- ========================================================
             ERROR MESSAGE
        ========================================================= -->

        <?php if (isset($_GET["error"])): ?>

            <div class="error-message">

                There was a problem loading your reservation.

            </div>

        <?php endif; ?>


        <!-- ========================================================
             NO RESERVATIONS
        ========================================================= -->

        <?php if (empty($reservations)): ?>

            <div class="empty-reservations">

                <h2>
                    No Reservations Yet
                </h2>

                <p>
                    You don't have any court reservations yet.
                </p>

                <a
                    href="booking.php"
                    class="book-button"
                >
                    Book a Court →
                </a>

            </div>


        <?php else: ?>


            <!-- ====================================================
                 RESERVATION LIST
            ===================================================== -->

            <?php foreach ($reservations as $reservation): ?>

                <?php

                $status = strtolower(
                    trim($reservation["status"])
                );

                ?>

                <div class="reservation-card">


                    <!-- ==============================================
                         RESERVATION HEADER
                    =============================================== -->

                    <div class="reservation-top">

                        <div>

                            <h2>
                                <?= htmlspecialchars(
                                    $reservation["court_name"]
                                ) ?>
                            </h2>

                            <p class="reservation-location">

                                <?= htmlspecialchars(
                                    $reservation["location"]
                                ) ?>

                            </p>


                            <!-- COURT NUMBER -->

                            <span class="court-number">

                                Court
                                <?= (int) $reservation["court_number"] ?>

                            </span>

                        </div>


                        <!-- STATUS -->

                        <span
                            class="reservation-status status-<?= htmlspecialchars($status) ?>"
                        >

                            <?= htmlspecialchars(
                                ucfirst($status)
                            ) ?>

                        </span>

                    </div>


                    <!-- ==============================================
                         RESERVATION DETAILS
                    =============================================== -->

                    <div class="reservation-details">


                        <!-- DATE -->

                        <div class="detail-box">

                            <span>
                                Date
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    date(
                                        "F d, Y",
                                        strtotime(
                                            $reservation["booking_date"]
                                        )
                                    )
                                ) ?>

                            </strong>

                        </div>


                        <!-- TIME -->

                        <div class="detail-box">

                            <span>
                                Time
                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    date(
                                        "h:i A",
                                        strtotime(
                                            $reservation["booking_time"]
                                        )
                                    )
                                ) ?>

                            </strong>

                        </div>


                        <!-- DURATION -->

                        <div class="detail-box">

                            <span>
                                Duration
                            </span>

                            <strong>

                                <?= (int) $reservation["duration"] ?>

                                hour<?= $reservation["duration"] != 1 ? "s" : "" ?>

                            </strong>

                        </div>


                    </div>

                </div>

            <?php endforeach; ?>


        <?php endif; ?>

    </div>

</main>


<!-- ================================================================
     FOOTER
================================================================ -->

<footer class="dashboard-footer">

    <p>
        © <?= date("Y") ?> PickServe. All rights reserved.
    </p>

</footer>

</body>

</html>

