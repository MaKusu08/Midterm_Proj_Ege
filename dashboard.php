<?php

session_start();

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

$user_id   = $_SESSION["user_id"];
$username  = $_SESSION["username"] ?? "";
$full_name = $_SESSION["full_name"] ?? "";

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard | PickServe</title>

<link rel="stylesheet" href="styles/style.css">
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

    <a href="booking/booking.php">
        Book a Court
    </a>

    <a href="booking/reservations.php">
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

<main>

<div class="dashboard-container">

    <!-- ============================================================
         WELCOME
    ============================================================= -->

    <section class="welcome">

        <div class="welcome-text">

            <h1>
                Welcome back,
                <span><?= htmlspecialchars($full_name) ?></span>!
            </h1>

            <p>
                Ready to play? Find a court and reserve your next game.
            </p>

        </div>

        <!-- PROFILE -->

        <div class="profile-card">

            <div class="profile-icon">
                <?= htmlspecialchars(strtoupper(substr($full_name, 0, 1))) ?>
            </div>

            <div class="profile-info">

                <h3>
                    <?= htmlspecialchars($full_name) ?>
                </h3>

                <p>
                    @<?= htmlspecialchars($username) ?>
                </p>

            </div>

        </div>

    </section>

    <!-- ============================================================
         DASHBOARD CARDS
    ============================================================= -->

    <section class="dashboard-grid">

        <!-- BOOK COURT -->

        <div class="dashboard-card">

            <div>

                <div class="card-icon">
                    +
                </div>

                <h2>
                    Book a Court
                </h2>

                <p>
                    Find an available pickleball court and choose
                    your preferred date and time.
                </p>

            </div>

            <a href="booking/booking.php" class="card-button">
                Book Now →
            </a>

        </div>

        <!-- MY RESERVATIONS -->

        <div class="dashboard-card">

            <div>

                <div class="card-icon">
                    ✓
                </div>

                <h2>
                    My Reservations
                </h2>

                <p>
                    View your upcoming and previous court
                    reservations in one place.
                </p>

            </div>

            <a href="booking/reservations.php" class="card-button secondary-button">
                View Reservations →
            </a>

        </div>

    </section>

    <!-- ============================================================
         QUICK INFO
    ============================================================= -->

    <section class="quick-info">

        <h2>
            Let's get you on the court!
        </h2>

        <p>
            PickServe makes it easy to find and reserve pickleball
            courts. Choose a court, select your schedule, and get
            ready to play.
        </p>

    </section>

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