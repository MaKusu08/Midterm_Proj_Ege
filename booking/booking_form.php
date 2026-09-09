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

/*
|--------------------------------------------------------------------------
| Get User Information
|--------------------------------------------------------------------------
*/

$user_id   = (int) $_SESSION["user_id"];
$username  = $_SESSION["username"] ?? "";
$full_name = $_SESSION["full_name"] ?? "";

/*
|--------------------------------------------------------------------------
| Get Court ID
|--------------------------------------------------------------------------
*/

$court_id = isset($_GET["court_id"])
    ? (int) $_GET["court_id"]
    : 0;

if ($court_id <= 0) {
    header("Location: booking.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Get Selected Court
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
         WHERE id = :court_id
         LIMIT 1"
    );

    $stmt->execute([
        ":court_id" => $court_id
    ]);

    $court = $stmt->fetch();

} catch (PDOException $e) {

    $court = false;

}

/*
|--------------------------------------------------------------------------
| Court Not Found
|--------------------------------------------------------------------------
*/

if (!$court) {
    header("Location: booking.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Check Court Status
|--------------------------------------------------------------------------
*/

$status = strtolower(
    trim($court["status"])
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Book <?= htmlspecialchars($court["court_name"]) ?> | PickServe
    </title>

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
        href="../styles/bookingform_style.css"
    >

</head>

<body>


<!-- ================================================================
     NAVBAR
================================================================ -->

<header class="navbar">

    <a
        href="../dashboard.php"
        class="logo"
    >
        Pick<span>Serve</span>
    </a>


    <nav class="nav-links">

        <a href="../dashboard.php">
            Dashboard
        </a>

        <a
            href="booking.php"
            class="active"
        >
            Book a Court
        </a>

        <a href="reservations.php">
            My Reservations
        </a>

        <a
            href="../logout.php"
            class="logout-btn"
        >
            Logout
        </a>

    </nav>

</header>


<!-- ================================================================
     MAIN
================================================================ -->

<main class="booking-form-main">

    <div class="booking-form-container">


        <!-- BACK BUTTON -->

        <a
            href="booking.php"
            class="back-button"
        >
            ← Back to Courts
        </a>


        <!-- ========================================================
             BOOKING CARD
        ========================================================= -->

        <div class="booking-form-card">


            <!-- ====================================================
                 COURT IMAGE
            ===================================================== -->

            <div>

                <img
                    src="../<?= htmlspecialchars($court["image"]) ?>"
                    alt="<?= htmlspecialchars($court["court_name"]) ?> pickleball court"
                    class="selected-court-image"
                >

            </div>


            <!-- ====================================================
                 BOOKING CONTENT
            ===================================================== -->

            <div class="booking-form-content">


                <!-- COURT NAME -->

                <h1>
                    <?= htmlspecialchars($court["court_name"]) ?>
                </h1>


                <!-- LOCATION -->

                <p class="court-location">
                    <?= htmlspecialchars($court["location"]) ?>
                </p>


                <!-- =================================================
                     RATING
                ================================================== -->

                <p>

                    <?php

                    $rating = (float) $court["rating"];

                    for ($i = 1; $i <= 5; $i++) {

                        echo $i <= $rating
                            ? "★"
                            : "☆";

                    }

                    ?>

                    <?= number_format($rating, 1) ?>

                </p>


                <!-- =================================================
                     STATUS
                ================================================== -->

                <?php if ($status === "available"): ?>

                    <div class="court-status available">
                        ● Available
                    </div>

                <?php elseif ($status === "maintenance"): ?>

                    <div class="court-status maintenance">
                        ● Maintenance
                    </div>

                <?php else: ?>

                    <div class="court-status">
                        ● <?= htmlspecialchars(ucfirst($status)) ?>
                    </div>

                <?php endif; ?>


                <!-- =================================================
                     BOOKING FORM
                ================================================== -->

                <?php if ($status === "available"): ?>

                    <form
                        action="process_booking.php"
                        method="POST"
                    >


                        <!-- COURT ID -->

                        <input
                            type="hidden"
                            name="court_id"
                            value="<?= (int) $court["id"] ?>"
                        >


                        <!-- USER ID -->

                        <input
                            type="hidden"
                            name="user_id"
                            value="<?= $user_id ?>"
                        >


                        <!-- =================================================
                             COURT NUMBER
                        ================================================== -->

                        <div class="booking-form-group">

                            <label for="court_number">
                                Court Number
                            </label>

                            <select
                                id="court_number"
                                name="court_number"
                                required
                            >

                                <option value="">
                                    Select court
                                </option>

                                <option value="1">
                                    Court 1
                                </option>

                                <option value="2">
                                    Court 2
                                </option>

                                <option value="3">
                                    Court 3
                                </option>

                            </select>

                        </div>


                        <!-- =================================================
                             DATE
                        ================================================== -->

                        <div class="booking-form-group">

                            <label for="booking_date">
                                Date
                            </label>

                            <input
                                type="date"
                                id="booking_date"
                                name="booking_date"
                                min="<?= date("Y-m-d") ?>"
                                required
                            >

                        </div>


                        <!-- =================================================
                             TIME
                        ================================================== -->

                        <div class="booking-form-group">

                            <label for="booking_time">
                                Time
                            </label>

                            <input
                                type="time"
                                id="booking_time"
                                name="booking_time"
                                required
                            >

                        </div>


                        <!-- =================================================
                             DURATION
                        ================================================== -->

                        <div class="booking-form-group">

                            <label for="duration">
                                Duration
                            </label>

                            <select
                                id="duration"
                                name="duration"
                                required
                            >

                                <option value="">
                                    Select duration
                                </option>

                                <option value="1">
                                    1 Hour
                                </option>

                                <option value="2">
                                    2 Hours
                                </option>

                                <option value="3">
                                    3 Hours
                                </option>

                            </select>

                        </div>


                        <!-- =================================================
                             SUBMIT BUTTON
                        ================================================== -->

                        <button
                            type="submit"
                            class="booking-submit card-button"
                        >
                            Confirm Booking →
                        </button>

                    </form>


                <?php else: ?>


                    <!-- =================================================
                         UNAVAILABLE COURT
                    ================================================== -->

                    <div class="maintenance-message">

                        <strong>
                            This court is currently unavailable.
                        </strong>

                        <p>

                            This court cannot be booked while it is
                            <?= htmlspecialchars($status) ?>.

                        </p>

                        <a href="booking.php">
                            ← Choose another court
                        </a>

                    </div>

                <?php endif; ?>


            </div>

        </div>

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

