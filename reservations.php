<?php

session_start();

require_once "database/db.php";

/*
|--------------------------------------------------------------------------
| Check Login
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {
    header("Location: login/login.php");
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
        href="styles/style.css"
    >

    <style>

        .reservations-main {
            padding: 50px 20px;
        }

        .reservations-container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .reservations-header {
            margin-bottom: 30px;
        }

        .reservations-header h1 {
            margin-bottom: 8px;
        }

        .reservations-header p {
            color: #666;
        }

        .success-message {
            background: #e8f7ed;
            color: #237a3b;
            border: 1px solid #b9e5c4;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f1aeb5;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .reservation-card {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .reservation-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .reservation-top h2 {
            margin: 0 0 5px;
        }

        .reservation-location {
            color: #666;
            margin: 0;
        }

        .court-number {
            display: inline-block;
            margin-top: 10px;
            padding: 6px 12px;
            border-radius: 6px;
            background: #f0f0f0;
            font-size: 14px;
            font-weight: 700;
        }

        .reservation-status {
            padding: 7px 14px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 700;
            text-transform: capitalize;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .status-completed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .reservation-details {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .detail-box {
            background: #f7f7f7;
            padding: 15px;
            border-radius: 8px;
        }

        .detail-box span {
            display: block;
            font-size: 13px;
            color: #777;
            margin-bottom: 5px;
        }

        .detail-box strong {
            font-size: 16px;
        }

        .empty-reservations {
            text-align: center;
            background: #fff;
            padding: 50px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .empty-reservations h2 {
            margin-bottom: 10px;
        }

        .empty-reservations p {
            color: #666;
            margin-bottom: 25px;
        }

        .book-button {
            display: inline-block;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
        }

        @media (max-width: 700px) {

            .reservation-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .reservation-details {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>

<header class="navbar">

    <a href="dashboard.php" class="logo">
        Pick<span>Serve</span>
    </a>

    <nav class="nav-links">

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="booking.php">
            Book a Court
        </a>

        <a href="reservations.php" class="active">
            My Reservations
        </a>

        <a href="logout.php" class="logout-btn">
            Logout
        </a>

    </nav>

</header>


<main class="reservations-main">

    <div class="reservations-container">

        <div class="reservations-header">

            <h1>
                My Reservations
            </h1>

            <p>
                View your upcoming and previous court reservations.
            </p>

        </div>


        <!-- =========================================================
             SUCCESS MESSAGE
        ========================================================== -->

        <?php if (isset($_GET["success"])): ?>

            <div class="success-message">

                Your booking has been successfully submitted!

            </div>

        <?php endif; ?>


        <!-- =========================================================
             ERROR MESSAGE
        ========================================================== -->

        <?php if (isset($_GET["error"])): ?>

            <div class="error-message">

                There was a problem loading your reservation.

            </div>

        <?php endif; ?>


        <!-- =========================================================
             NO RESERVATIONS
        ========================================================== -->

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


            <!-- =====================================================
                 RESERVATION LIST
            ====================================================== -->

            <?php foreach ($reservations as $reservation): ?>

                <?php

                $status = strtolower(
                    trim($reservation["status"])
                );

                ?>

                <div class="reservation-card">


                    <!-- =================================================
                         RESERVATION HEADER
                    ================================================== -->

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

                            <?= htmlspecialchars($status) ?>

                        </span>

                    </div>


                    <!-- =================================================
                         RESERVATION DETAILS
                    ================================================== -->

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


<footer class="dashboard-footer">

    <p>
        © <?= date("Y") ?> PickServe. All rights reserved.
    </p>

</footer>

</body>

</html>