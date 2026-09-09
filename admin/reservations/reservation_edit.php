<?php

session_start();

require_once "../../database/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../../login/login.php");
    exit;
}

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../../dashboard.php");
    exit;
}

$reservation_id = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;

if ($reservation_id <= 0) {
    header("Location: reservations.php?error=invalid");
    exit;
}

$pdo = getConnection();

try {

    $stmt = $pdo->prepare("
        SELECT
            r.id,
            r.user_id,
            r.court_id,
            r.court_number,
            r.booking_date,
            r.booking_time,
            r.duration,
            r.status,

            u.username,
            u.full_name,

            c.court_name,
            c.location

        FROM reservations r

        INNER JOIN users u
            ON r.user_id = u.id

        INNER JOIN courts c
            ON r.court_id = c.id

        WHERE r.id = :id

        LIMIT 1
    ");

    $stmt->execute([
        ":id" => $reservation_id
    ]);

    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        header("Location: reservations.php?error=notfound");
        exit;
    }

    if ($reservation["status"] !== "pending") {
        header("Location: reservations.php?error=locked");
        exit;
    }


    $courtStmt = $pdo->query("
        SELECT
            id,
            court_name,
            location,
            status
        FROM courts
        ORDER BY id ASC
    ");

    $courts = $courtStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    header("Location: reservations.php?error=database");
    exit;

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

    <title>Edit Reservation | PickServe Admin</title>

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

<header class="admin-navbar">

    <a
        href="../dashboard.php"
        class="admin-logo"
    >

        Pick<span>Serve</span>

        <small>
            ADMIN PANEL
        </small>

    </a>


    <nav class="admin-nav">

        <a href="../dashboard.php">
            Dashboard
        </a>

        <a href="../courts/courts.php">
            Courts
        </a>

        <a
            href="reservations.php"
            class="active"
        >
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


<main class="admin-main">

    <div class="admin-container">

        <div class="court-edit-header">

            <div>

                <h1>
                    Edit Reservation
                </h1>

                <p>
                    Update the pending reservation details.
                </p>

            </div>

            <a
                href="reservations.php"
                class="admin-secondary-btn"
            >
                Back
            </a>

        </div>


        <div class="admin-form-section">

            <form
                action="reservation_update.php"
                method="POST"
                class="admin-form"
            >

                <input
                    type="hidden"
                    name="reservation_id"
                    value="<?= (int) $reservation["id"] ?>"
                >


                <div class="admin-form-group">

                    <label>
                        Customer
                    </label>

                    <input
                        type="text"
                        value="<?= htmlspecialchars(
                            $reservation["full_name"]
                        ) ?> (@<?= htmlspecialchars(
                            $reservation["username"]
                        ) ?>)"
                        readonly
                    >

                </div>


                <div class="admin-form-group">

                    <label for="court_id">
                        Court
                    </label>

                    <select
                        name="court_id"
                        id="court_id"
                        required
                    >

                        <?php foreach ($courts as $court): ?>

                            <option
                                value="<?= (int) $court["id"] ?>"
                                <?= (int) $court["id"] === (int) $reservation["court_id"]
                                    ? "selected"
                                    : "" ?>
                                <?= $court["status"] === "maintenance"
                                    && (int) $court["id"] !== (int) $reservation["court_id"]
                                    ? "disabled"
                                    : "" ?>
                            >

                                <?= htmlspecialchars(
                                    $court["court_name"]
                                ) ?>

                                -
                                <?= htmlspecialchars(
                                    $court["location"]
                                ) ?>

                                <?php if ($court["status"] === "maintenance"): ?>
                                    (Maintenance)
                                <?php endif; ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="admin-form-group">

                    <label for="court_number">
                        Court Number
                    </label>

                    <select
                        name="court_number"
                        id="court_number"
                        required
                    >

                        <option
                            value="1"
                            <?= (int) $reservation["court_number"] === 1
                                ? "selected"
                                : "" ?>
                        >
                            Court 1
                        </option>

                        <option
                            value="2"
                            <?= (int) $reservation["court_number"] === 2
                                ? "selected"
                                : "" ?>
                        >
                            Court 2
                        </option>

                        <option
                            value="3"
                            <?= (int) $reservation["court_number"] === 3
                                ? "selected"
                                : "" ?>
                        >
                            Court 3
                        </option>

                    </select>

                </div>


                <div class="admin-form-group">

                    <label for="booking_date">
                        Booking Date
                    </label>

                    <input
                        type="date"
                        name="booking_date"
                        id="booking_date"
                        value="<?= htmlspecialchars(
                            $reservation["booking_date"]
                        ) ?>"
                        required
                    >

                </div>


                <div class="admin-form-group">

                    <label for="booking_time">
                        Booking Time
                    </label>

                    <input
                        type="time"
                        name="booking_time"
                        id="booking_time"
                        value="<?= htmlspecialchars(
                            substr(
                                $reservation["booking_time"],
                                0,
                                5
                            )
                        ) ?>"
                        required
                    >

                </div>


                <div class="admin-form-group">

                    <label for="duration">
                        Duration
                    </label>

                    <select
                        name="duration"
                        id="duration"
                        required
                    >

                        <option
                            value="1"
                            <?= (int) $reservation["duration"] === 1
                                ? "selected"
                                : "" ?>
                        >
                            1 Hour
                        </option>

                        <option
                            value="2"
                            <?= (int) $reservation["duration"] === 2
                                ? "selected"
                                : "" ?>
                        >
                            2 Hours
                        </option>

                        <option
                            value="3"
                            <?= (int) $reservation["duration"] === 3
                                ? "selected"
                                : "" ?>
                        >
                            3 Hours
                        </option>

                    </select>

                </div>


                <div class="admin-form-group">

                    <label for="status">
                        Status
                    </label>

                    <select
                        name="status"
                        id="status"
                        required
                    >

                        <option value="pending">
                            Pending
                        </option>

                        <option value="confirmed">
                            Confirmed
                        </option>

                        <option value="cancelled">
                            Cancelled
                        </option>

                        <option value="completed">
                            Completed
                        </option>

                    </select>

                </div>


                <div class="admin-form-actions">

                    <a
                        href="reservations.php"
                        class="admin-secondary-btn"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        name="update_reservation"
                        class="admin-primary-btn"
                    >
                        Update Reservation
                    </button>

                </div>

            </form>

        </div>

    </div>

</main>

</body>

</html>