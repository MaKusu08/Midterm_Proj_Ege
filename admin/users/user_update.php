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
    !isset($_POST["update_user"])
) {
    header("Location: users.php");
    exit;
}

$user_id   = (int) ($_POST["user_id"] ?? 0);
$username  = trim($_POST["username"] ?? "");
$full_name = trim($_POST["full_name"] ?? "");
$email     = trim($_POST["email"] ?? "");
$phone     = trim($_POST["phone"] ?? "");
$role      = trim($_POST["role"] ?? "");
$password  = $_POST["password"] ?? "";

if (
    $user_id <= 0 ||
    $username === "" ||
    $full_name === "" ||
    $email === "" ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    !in_array($role, ["user", "admin"], true)
) {
    header("Location: users.php?error=invalid");
    exit;
}

try {

    $pdo = getConnection();

    /*
     * Make sure username is not already used
     */
    $stmt = $pdo->prepare("
        SELECT id
        FROM users
        WHERE username = :username
        AND id != :id
        LIMIT 1
    ");

    $stmt->execute([
        ":username" => $username,
        ":id"       => $user_id
    ]);

    if ($stmt->fetch()) {
        header(
            "Location: user_edit.php?id=" .
            $user_id .
            "&error=username"
        );
        exit;
    }

    /*
     * Make sure email is not already used
     */
    $stmt = $pdo->prepare("
        SELECT id
        FROM users
        WHERE email = :email
        AND id != :id
        LIMIT 1
    ");

    $stmt->execute([
        ":email" => $email,
        ":id"    => $user_id
    ]);

    if ($stmt->fetch()) {
        header(
            "Location: user_edit.php?id=" .
            $user_id .
            "&error=email"
        );
        exit;
    }

    /*
     * Prevent admin from removing
     * their own admin role.
     */
    if (
        $user_id === (int) $_SESSION["user_id"] &&
        $role !== "admin"
    ) {
        header(
            "Location: user_edit.php?id=" .
            $user_id .
            "&error=selfrole"
        );
        exit;
    }

    /*
     * Update password only if a new password
     * was entered.
     */
    if ($password !== "") {

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = $pdo->prepare("
            UPDATE users
            SET
                username = :username,
                password = :password,
                full_name = :full_name,
                email = :email,
                phone = :phone,
                role = :role,
                updated_at = NOW()
            WHERE id = :id
        ");

        $stmt->execute([
            ":username"  => $username,
            ":password"  => $hashedPassword,
            ":full_name" => $full_name,
            ":email"     => $email,
            ":phone"     => $phone !== "" ? $phone : null,
            ":role"      => $role,
            ":id"        => $user_id
        ]);

    } else {

        $stmt = $pdo->prepare("
            UPDATE users
            SET
                username = :username,
                full_name = :full_name,
                email = :email,
                phone = :phone,
                role = :role,
                updated_at = NOW()
            WHERE id = :id
        ");

        $stmt->execute([
            ":username"  => $username,
            ":full_name" => $full_name,
            ":email"     => $email,
            ":phone"     => $phone !== "" ? $phone : null,
            ":role"      => $role,
            ":id"        => $user_id
        ]);
    }

    /*
     * Update current session if admin edited
     * their own account.
     */
    if ($user_id === (int) $_SESSION["user_id"]) {

        $_SESSION["username"]  = $username;
        $_SESSION["full_name"] = $full_name;
        $_SESSION["email"]     = $email;
        $_SESSION["phone"]     = $phone;
        $_SESSION["role"]      = $role;
    }

    header("Location: users.php?success=updated");
    exit;

} catch (PDOException $e) {

    header(
        "Location: user_edit.php?id=" .
        $user_id .
        "&error=database"
    );

    exit;
}