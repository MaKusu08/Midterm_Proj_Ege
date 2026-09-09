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

$reservation_id = (int) ($_GET["id"] ?? 0);

if ($reservation_id <= 0) {
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
     * Only confirmed reservations can be deleted
     */

    if ($reservation["status"] !== "confirmed") {
        header("Location: reservations.php?error=locked");
        exit;
    }

    /*
     * Delete reservation
     */

    $stmt = $pdo->prepare("
        DELETE FROM reservations
        WHERE id = :id
        AND status = 'confirmed'
    ");

    $stmt->execute([
        ":id" => $reservation_id
    ]);

    header("Location: reservations.php?success=deleted");
    exit;

} catch (PDOException $e) {

    header("Location: reservations.php?error=database");
    exit;
}