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

/*
|--------------------------------------------------------------------------
| Get User Information
|--------------------------------------------------------------------------
*/

$full_name = $_SESSION["full_name"] ?? "";
$username  = $_SESSION["username"] ?? "";

/*
|--------------------------------------------------------------------------
| Get Courts From Database
|--------------------------------------------------------------------------
*/

try {

    $pdo = getConnection();

    $stmt = $pdo->prepare(
        "SELECT
            id,
            court_name,
            location,
            image,
            rating,
            status
         FROM courts
         WHERE status = 'available'
         ORDER BY id ASC"
    );

    $stmt->execute();

    $courts = $stmt->fetchAll();

} catch (PDOException $e) {

    $courts = [];

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

    <title>Book a Court | PickServe</title>

    <link
        rel="stylesheet"
        href="styles/style.css"
    >

    <link
        rel="stylesheet"
        href="styles/booking_style.css"
    >

    <link rel="stylesheet" href="styles/dashboard_style.css">


</head>

<body>

<!-- ================================================================
     NAVBAR
================================================================ -->

<header class="navbar">

    <a href="dashboard.php" class="logo">
        Pick<span>Serve</span>
    </a>

    <nav class="nav-links">

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="booking.php" class="active">
            Book a Court
        </a>

        <a href="reservations.php">
            My Reservations
        </a>

        <a href="logout.php" class="logout-btn">
            Logout
        </a>

    </nav>

</header>


<!-- ================================================================
     MAIN
================================================================ -->

<main class="booking-main">

    <div class="booking-container">


        <!-- ============================================================
             PAGE HEADER
        ============================================================= -->

        <section class="booking-header">

            <p class="booking-label">
                PICK YOUR COURT
            </p>

            <h1>
                Book a <span>Court</span>
            </h1>

            <p class="booking-description">
                Choose from our available pickleball courts and
                reserve your preferred schedule.
            </p>

        </section>


        <!-- ============================================================
             COURT LIST
        ============================================================= -->

        <section class="court-list">

            <?php if (!empty($courts)): ?>

                <?php foreach ($courts as $index => $court): ?>

                    <article class="booking-court-card">

                        <!-- COURT IMAGE -->

                        <div class="court-image">

                            <img
                                src="<?= htmlspecialchars($court["image"]) ?>"
                                alt="<?= htmlspecialchars($court["court_name"]) ?> pickleball court"
                            >

                        </div>


                        <!-- COURT NUMBER -->

                        <div class="court-number">

                            <?= sprintf(
                                "%02d",
                                $index + 1
                            ) ?>

                        </div>


                        <!-- COURT DETAILS -->

                        <div class="court-details">

                            <h2>
                                <?= htmlspecialchars(
                                    $court["court_name"]
                                ) ?>
                            </h2>

                            <p>
                                <?= htmlspecialchars(
                                    $court["location"]
                                ) ?>
                            </p>

                            <span class="court-rating">

                                <?php
                                $rating = (float) $court["rating"];

                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $rating ? "★" : "☆";
                                }
                                ?>

                                <?= number_format($rating, 1) ?>

                            </span>

                            <span class="court-status available">
                                ● Available
                            </span>

                        </div>


                        <!-- BOOK BUTTON -->

                        <a
                            href="booking_form.php?court_id=<?= (int) $court["id"] ?>"
                            class="book-court-button"
                        >
                            Book Now →
                        </a>

                    </article>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="no-courts">

                    <h2>
                        No Courts Available
                    </h2>

                    <p>
                        There are currently no courts available for booking.
                        Please check again later.
                    </p>

                </div>

            <?php endif; ?>

        </section>


    </div>

</main>


<!-- ================================================================
     FOOTER
================================================================ -->

<footer class="booking-footer">

    <p>
        © <?= date("Y") ?> PickServe. All rights reserved.
    </p>

</footer>


</body>

</html>