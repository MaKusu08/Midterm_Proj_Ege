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

$user_id = (int) ($_GET["id"] ?? 0);

if ($user_id <= 0) {
    header("Location: users.php?error=invalid");
    exit;
}

/*
 * Do not allow admin to delete their own account.
 */
if ($user_id === (int) $_SESSION["user_id"]) {
    header("Location: users.php?error=selfdelete");
    exit;
}

try {

    $pdo = getConnection();

    $stmt = $pdo->prepare("
        SELECT
            id,
            username,
            full_name,
            role
        FROM users
        WHERE id = :id
        LIMIT 1
    ");

    $stmt->execute([
        ":id" => $user_id
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: users.php?error=notfound");
        exit;
    }

    /*
     * Delete user.
     *
     * reservations.user_id has ON DELETE CASCADE,
     * so their reservations will also be deleted.
     */
    $stmt = $pdo->prepare("
        DELETE FROM users
        WHERE id = :id
    ");

    $stmt->execute([
        ":id" => $user_id
    ]);

    header("Location: users.php?success=deleted");
    exit;

} catch (PDOException $e) {

    header("Location: users.php?error=database");
    exit;
}