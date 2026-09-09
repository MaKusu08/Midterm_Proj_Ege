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

if (
    $_SERVER["REQUEST_METHOD"] !== "POST" ||
    !isset($_POST["update_reservation"])
) {
    header("Location: reservations.php");
    exit;
}

$reservation_id = (int) ($_POST["reservation_id"] ?? 0);
$court_id       = (int) ($_POST["court_id"] ?? 0);
$court_number   = (int) ($_POST["court_number"] ?? 0);
$booking_date   = trim($_POST["booking_date"] ?? "");
$booking_time   = trim($_POST["booking_time"] ?? "");
$duration       = (int) ($_POST["duration"] ?? 0);
$status         = trim($_POST["status"] ?? "");

$allowed_statuses = [
    "pending",
    "confirmed",
    "cancelled",
    "completed"
];

if (
    $reservation_id <= 0 ||
    $court_id <= 0 ||
    !in_array($court_number, [1, 2, 3], true) ||
    empty($booking_date) ||
    empty($booking_time) ||
    !in_array($duration, [1, 2, 3], true) ||
    !in_array($status, $allowed_statuses, true)
) {
    header("Location: reservations.php?error=invalid");
    exit;
}

try {

    $pdo = getConnection();

    /*
     * Check reservation
     */

    $stmt = $pdo->prepare("
        SELECT id, status
        FROM reservations
        WHERE id = :id
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

    /*
     * Only pending reservations can be edited
     */

    if ($reservation["status"] !== "pending") {
        header("Location: reservations.php?error=locked");
        exit;
    }


    /*
     * Check court
     */

    $stmt = $pdo->prepare("
        SELECT id, status
        FROM courts
        WHERE id = :id
        LIMIT 1
    ");

    $stmt->execute([
        ":id" => $court_id
    ]);

    $court = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$court) {
        header("Location: reservations.php?error=court");
        exit;
    }

    if ($court["status"] === "maintenance") {
        header("Location: reservations.php?error=maintenance");
        exit;
    }


    /*
     * Check past date/time
     */

    $startTime = strtotime(
        $booking_date . " " . $booking_time
    );

    if ($startTime === false || $startTime < time()) {
        header("Location: reservations.php?error=past");
        exit;
    }


    /*
     * Check overlapping reservations
     */

    $stmt = $pdo->prepare("
        SELECT
            id,
            booking_time,
            duration
        FROM reservations
        WHERE court_id = :court_id
        AND court_number = :court_number
        AND booking_date = :booking_date
        AND status IN ('pending', 'confirmed')
        AND id != :reservation_id
    ");

    $stmt->execute([
        ":court_id"       => $court_id,
        ":court_number"   => $court_number,
        ":booking_date"   => $booking_date,
        ":reservation_id" => $reservation_id
    ]);

    $conflicts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $newStart = strtotime(
        $booking_date . " " . $booking_time
    );

    $newEnd = $newStart + ($duration * 60 * 60);


    foreach ($conflicts as $conflict) {

        $existingStart = strtotime(
            $booking_date . " " . $conflict["booking_time"]
        );

        $existingEnd =
            $existingStart +
            ((int) $conflict["duration"] * 60 * 60);

        /*
         * Overlap detected
         */

        if (
            $newStart < $existingEnd &&
            $newEnd > $existingStart
        ) {
            header("Location: reservations.php?error=conflict");
            exit;
        }
    }


    /*
     * Update reservation
     */

    $stmt = $pdo->prepare("
        UPDATE reservations
        SET
            court_id = :court_id,
            court_number = :court_number,
            booking_date = :booking_date,
            booking_time = :booking_time,
            duration = :duration,
            status = :status
        WHERE id = :id
    ");

    $stmt->execute([
        ":court_id"     => $court_id,
        ":court_number" => $court_number,
        ":booking_date" => $booking_date,
        ":booking_time" => $booking_time,
        ":duration"     => $duration,
        ":status"       => $status,
        ":id"           => $reservation_id
    ]);


    header("Location: reservations.php?success=updated");
    exit;


} catch (PDOException $e) {

    header("Location: reservations.php?error=database");
    exit;
}