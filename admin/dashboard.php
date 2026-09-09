<?php

session_start();

require_once "../database/db.php";

/*
|--------------------------------------------------------------------------
| Admin Access Protection
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login/login.php");
    exit;
}

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../dashboard.php");
    exit;
}

try {

    $pdo = getConnection();

    /*
    |--------------------------------------------------------------------------
    | Dashboard Statistics
    |--------------------------------------------------------------------------
    */

    // Total users
    $stmt = $pdo->query("
        SELECT COUNT(*)
        FROM users
        WHERE roles = 'user'
    ");

    $totalUsers = (int) $stmt->fetchColumn();


    // Total courts
    $stmt = $pdo->query("
        SELECT COUNT(*)
        FROM courts
    ");

    $totalCourts = (int) $stmt->fetchColumn();


    // Available courts
    $stmt = $pdo->query("
        SELECT COUNT(*)
        FROM courts
        WHERE status = 'available'
    ");

    $availableCourts = (int) $stmt->fetchColumn();


    // Maintenance courts
    $stmt = $pdo->query("
        SELECT COUNT(*)
        FROM courts
        WHERE status = 'maintenance'
    ");

    $maintenanceCourts = (int) $stmt->fetchColumn();


    // Total reservations
    $stmt = $pdo->query("
        SELECT COUNT(*)
        FROM reservations
    ");

    $totalReservations = (int) $stmt->fetchColumn();


    // Pending reservations
    $stmt = $pdo->query("
        SELECT COUNT(*)
        FROM reservations
        WHERE status = 'pending'
    ");

    $pendingReservations = (int) $stmt->fetchColumn();


    // Confirmed reservations
    $stmt = $pdo->query("
        SELECT COUNT(*)
        FROM reservations
        WHERE status = 'confirmed'
    ");

    $confirmedReservations = (int) $stmt->fetchColumn();


    // Cancelled reservations
    $stmt = $pdo->query("
        SELECT COUNT(*)
        FROM reservations
        WHERE status = 'cancelled'
    ");

    $cancelledReservations = (int) $stmt->fetchColumn();


    /*
    |--------------------------------------------------------------------------
    | Recent Reservations
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->query("
        SELECT
            r.id,
            r.booking_date,
            r.booking_time,
            r.duration,
            r.status,
            r.court_number,
            u.username,
            u.full_name
        FROM reservations r
        INNER JOIN users u
            ON r.user_id = u.id
        ORDER BY r.created_at DESC
        LIMIT 8
    ");

    $recentReservations = $stmt->fetchAll(PDO::FETCH_ASSOC);


} catch (PDOException $e) {

    $totalUsers = 0;
    $totalCourts = 0;
    $availableCourts = 0;
    $maintenanceCourts = 0;
    $totalReservations = 0;
    $pendingReservations = 0;
    $confirmedReservations = 0;
    $cancelledReservations = 0;
    $recentReservations = [];
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

    <title>Admin Dashboard | PickServe</title>

    <link
        rel="stylesheet"
        href="../styles/style.css"
    >

    <link
        rel="stylesheet"
        href="../styles/admin_styles.css"
    >

</head>

<body>

<!-- =========================================================
     ADMIN NAVBAR
========================================================= -->

<header class="admin-navbar">

    <div class="admin-logo">

        Pick<span>Serve</span>

        <small>ADMIN</small>

    </div>

    <nav class="admin-nav">

        <a
            href="dashboard.php"
            class="active"
        >
            Dashboard
        </a>

        <a href="courts/courts.php">
            Courts
        </a>

        <a href="reservations/reservations.php">
            Reservations
        </a>

        <a href="users/users.php">
            Users
        </a>

        <a href="messages/messages.php">
            Messages
        </a>

        <a
            href="../logout.php"
            class="logout-btn"
        >
            Logout
        </a>

    </nav>

</header>


<!-- =========================================================
     MAIN DASHBOARD
========================================================= -->

<main class="admin-main">

    <div class="admin-container">

        <!-- PAGE HEADER -->

        <section class="admin-header">

            <div>

                <span class="admin-label">

                    <span></span>

                    ADMIN PANEL

                </span>

                <h1>
                    Dashboard
                </h1>

                <p>

                    Welcome back,

                    <strong>
                        <?= htmlspecialchars(
                            $_SESSION["full_name"]
                            ?? $_SESSION["username"]
                        ) ?>
                    </strong>.

                    Manage your PickServe reservation system.

                </p>

            </div>

        </section>


        <!-- =====================================================
             STATISTICS
        ====================================================== -->

        <section class="admin-stats">

            <div class="admin-stat-card">

                <div class="stat-icon">
                    👥
                </div>

                <div>

                    <span>
                        Total Users
                    </span>

                    <strong>
                        <?= $totalUsers ?>
                    </strong>

                </div>

            </div>


            <div class="admin-stat-card">

                <div class="stat-icon">
                    🏟
                </div>

                <div>

                    <span>
                        Total Courts
                    </span>

                    <strong>
                        <?= $totalCourts ?>
                    </strong>

                </div>

            </div>


            <div class="admin-stat-card">

                <div class="stat-icon">
                    ✓
                </div>

                <div>

                    <span>
                        Available Courts
                    </span>

                    <strong>
                        <?= $availableCourts ?>
                    </strong>

                </div>

            </div>


            <div class="admin-stat-card">

                <div class="stat-icon">
                    📅
                </div>

                <div>

                    <span>
                        Total Reservations
                    </span>

                    <strong>
                        <?= $totalReservations ?>
                    </strong>

                </div>

            </div>

        </section>


        <!-- =====================================================
             RESERVATION STATUS
        ====================================================== -->

        <section class="admin-section">

            <div class="section-title">

                <div>

                    <span class="admin-label">

                        <span></span>

                        RESERVATIONS

                    </span>

                    <h2>
                        Reservation Overview
                    </h2>

                </div>

                <a
                    href="reservations/reservations.php"
                    class="admin-view-button"
                >
                    View All
                </a>

            </div>


            <div class="reservation-overview">

                <div class="overview-card pending">

                    <span>
                        Pending
                    </span>

                    <strong>
                        <?= $pendingReservations ?>
                    </strong>

                </div>


                <div class="overview-card confirmed">

                    <span>
                        Confirmed
                    </span>

                    <strong>
                        <?= $confirmedReservations ?>
                    </strong>

                </div>


                <div class="overview-card cancelled">

                    <span>
                        Cancelled
                    </span>

                    <strong>
                        <?= $cancelledReservations ?>
                    </strong>

                </div>


                <div class="overview-card maintenance">

                    <span>
                        Maintenance Courts
                    </span>

                    <strong>
                        <?= $maintenanceCourts ?>
                    </strong>

                </div>

            </div>

        </section>


        <!-- =====================================================
             RECENT RESERVATIONS
        ====================================================== -->

        <section class="admin-section">

            <div class="section-title">

                <div>

                    <span class="admin-label">

                        <span></span>

                        RECENT ACTIVITY

                    </span>

                    <h2>
                        Recent Reservations
                    </h2>

                </div>

                <a
                    href="reservations/reservations.php"
                    class="admin-view-button"
                >
                    Manage Reservations
                </a>

            </div>


            <div class="admin-table-wrapper">

                <table class="admin-table">

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>User</th>

                            <th>Court</th>

                            <th>Date</th>

                            <th>Time</th>

                            <th>Duration</th>

                            <th>Status</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php if (empty($recentReservations)): ?>

                        <tr>

                            <td
                                colspan="7"
                                class="empty-table"
                            >
                                No reservations found.
                            </td>

                        </tr>

                    <?php else: ?>

                        <?php foreach (
                            $recentReservations
                            as $reservation
                        ): ?>

                            <tr>

                                <td>
                                    #<?= (int) $reservation["id"] ?>
                                </td>

                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $reservation["full_name"]
                                            ?: $reservation["username"]
                                        ) ?>

                                    </strong>

                                    <small>

                                        @<?= htmlspecialchars(
                                            $reservation["username"]
                                        ) ?>

                                    </small>

                                </td>

                                <td>

                                    Court
                                    <?= (int) $reservation["court_number"] ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars(
                                        $reservation["booking_date"]
                                    ) ?>

                                </td>

                                <td>

                                    <?= date(
                                        "h:i A",
                                        strtotime(
                                            $reservation["booking_time"]
                                        )
                                    ) ?>

                                </td>

                                <td>

                                    <?= (int) $reservation["duration"] ?>
                                    hr

                                </td>

                                <td>

                                    <span
                                        class="status-badge status-<?= htmlspecialchars(
                                            $reservation["status"]
                                        ) ?>"
                                    >

                                        <?= ucfirst(
                                            htmlspecialchars(
                                                $reservation["status"]
                                            )
                                        ) ?>

                                    </span>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>


        <!-- =====================================================
             QUICK ACTIONS
        ====================================================== -->

        <section class="admin-section">

            <div class="section-title">

                <div>

                    <span class="admin-label">

                        <span></span>

                        QUICK ACTIONS

                    </span>

                    <h2>
                        Manage PickServe
                    </h2>

                </div>

            </div>


            <div class="quick-actions">

                <a
                    href="courts/courts.php"
                    class="quick-action"
                >

                    <strong>
                        Manage Courts
                    </strong>

                    <span>
                        Add, edit, or update court status →
                    </span>

                </a>


                <a
                    href="reservations/reservations.php"
                    class="quick-action"
                >

                    <strong>
                        Manage Reservations
                    </strong>

                    <span>
                        View and update customer bookings →
                    </span>

                </a>


                <a
                    href="users/users.php"
                    class="quick-action"
                >

                    <strong>
                        Manage Users
                    </strong>

                    <span>
                        View registered PickServe users →
                    </span>

                </a>


                <a
                    href="messages/messages.php"
                    class="quick-action"
                >

                    <strong>
                        Messages
                    </strong>

                    <span>
                        View customer inquiries →
                    </span>

                </a>

            </div>

        </section>

    </div>

</main>

</body>

</html>
