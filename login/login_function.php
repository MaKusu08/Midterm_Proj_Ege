<?php

session_start();

require_once "../database/db.php";
require_once "../includes/validation.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["login"])) {
    header("Location: login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Validate Login Input
|--------------------------------------------------------------------------
*/

$result = validateLoginInput($_POST);

$errors = $result["errors"];
$data   = $result["data"];

if (!empty($errors)) {

    $message = implode(" ", $errors);

    header(
        "Location: login.php?status=error&message=" .
        urlencode($message)
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| Get Validated Data
|--------------------------------------------------------------------------
*/

$username = $data["username"];
$password = $data["password"];

/*
|--------------------------------------------------------------------------
| Database Processing
|--------------------------------------------------------------------------
*/

try {

    $pdo = getConnection();

    /*
    |----------------------------------------------------------------------
    | Find User
    |----------------------------------------------------------------------
    */

    $stmt = $pdo->prepare(
        "SELECT
            id,
            username,
            password,
            full_name
         FROM users
         WHERE username = :username
         LIMIT 1"
    );

    $stmt->execute([
        ":username" => $username
    ]);

    $user = $stmt->fetch();

    /*
    |----------------------------------------------------------------------
    | Verify User
    |----------------------------------------------------------------------
    */

    if (!$user || !password_verify($password, $user["password"])) {

        header(
            "Location: login.php?status=error&message=" .
            urlencode("Invalid username or password.")
        );

        exit;
    }

    /*
    |----------------------------------------------------------------------
    | Create Secure Session
    |----------------------------------------------------------------------
    */

    session_regenerate_id(true);

    $_SESSION["user_id"] = $user["id"];
    $_SESSION["username"] = $user["username"];
    $_SESSION["full_name"] = $user["full_name"];

    /*
    |----------------------------------------------------------------------
    | Login Successful
    |----------------------------------------------------------------------
    */

    header("Location: dashboard.php");
    exit;

} catch (PDOException $e) {

    header(
        "Location: login.php?status=error&message=" .
        urlencode("Login failed. Please try again.")
    );

    exit;
}

