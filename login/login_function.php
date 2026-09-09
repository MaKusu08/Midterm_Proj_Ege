
<?php

session_start();

require_once "../database/db.php";
require_once "../includes/validation.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["login"])) {
    header("Location: login.php");
    exit;
}

$result = validateLoginInput($_POST);

$errors = $result["errors"];
$data   = $result["data"];

if (!empty($errors)) {

    header(
        "Location: login.php?status=error&message=" .
        urlencode(implode(" ", $errors))
    );

    exit;
}

$username = $data["username"];
$password = $data["password"];

try {

    $pdo = getConnection();

    $stmt = $pdo->prepare("
        SELECT
            id,
            username,
            password,
            full_name,
            email,
            phone,
            role
        FROM users
        WHERE username = :username
        LIMIT 1
    ");

    $stmt->execute([
        ":username" => $username
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    /*
    |--------------------------------------------------------------------------
    | Check User
    |--------------------------------------------------------------------------
    */

    if (!$user) {

        header(
            "Location: login.php?status=error&message=" .
            urlencode("Invalid username or password.")
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Check Password
    |--------------------------------------------------------------------------
    */

    if (!password_verify($password, $user["password"])) {

        header(
            "Location: login.php?status=error&message=" .
            urlencode("Invalid username or password.")
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Get Role
    |--------------------------------------------------------------------------
    */

    $role = strtolower(trim((string) $user["role"]));

    /*
    |--------------------------------------------------------------------------
    | Validate Role
    |--------------------------------------------------------------------------
    */

    if ($role !== "user" && $role !== "admin") {

        header(
            "Location: login.php?status=error&message=" .
            urlencode("Invalid account role.")
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Create Session
    |--------------------------------------------------------------------------
    */

    session_regenerate_id(true);

    $_SESSION["user_id"]   = $user["id"];
    $_SESSION["username"]  = $user["username"];
    $_SESSION["full_name"] = $user["full_name"];
    $_SESSION["email"]     = $user["email"];
    $_SESSION["phone"]     = $user["phone"];
    $_SESSION["role"]      = $role;

    /*
    |--------------------------------------------------------------------------
    | Redirect
    |--------------------------------------------------------------------------
    */

    if ($role === "admin") {

        header("Location: ../admin/dashboard.php");
        exit;
    }

    if ($role === "user") {

        header("Location: ../dashboard.php");
        exit;
    }

} catch (PDOException $e) {

    header(
        "Location: login.php?status=error&message=" .
        urlencode("Database error. Please try again.")
    );

    exit;
}

