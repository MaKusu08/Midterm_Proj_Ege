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

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: messages.php?error=invalid");
    exit;
}

$message_id = (int) $_GET["id"];

try {

    $pdo = getConnection();

    $stmt = $pdo->prepare("
        DELETE FROM contact_messages
        WHERE id = :id
    ");

    $stmt->execute([
        ":id" => $message_id
    ]);

    header("Location: messages.php?success=deleted");
    exit;

} catch (PDOException $e) {

    header("Location: messages.php?error=delete");
    exit;
}