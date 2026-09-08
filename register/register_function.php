<?php

session_start();

require_once "../database/db.php";
require_once "../includes/validation.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["register"])) {
    header("Location: register.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Validate Registration Input
|--------------------------------------------------------------------------
*/

$result = validateRegisterInput($_POST);

if (!empty($result["errors"])) {

    /*
    |----------------------------------------------------------------------
    | Save Form Data
    |----------------------------------------------------------------------
    | Do NOT save the password.
    */

    $_SESSION["register_data"] = [
        "username"  => $result["data"]["username"],
        "full_name" => $result["data"]["full_name"],
        "email"     => $result["data"]["email"],
        "phone"     => $result["data"]["phone"]
    ];

    $message = implode(" ", $result["errors"]);

    header(
        "Location: register.php?status=error&message=" .
        urlencode($message)
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| Database Processing
|--------------------------------------------------------------------------
*/

try {

    $pdo = getConnection();

    /*
    |----------------------------------------------------------------------
    | Check Existing Username or Email
    |----------------------------------------------------------------------
    */

    $stmt = $pdo->prepare(
        "SELECT id
         FROM users
         WHERE username = :username
         OR email = :email
         LIMIT 1"
    );

    $stmt->execute([
        ":username" => $result["data"]["username"],
        ":email" => $result["data"]["email"]
    ]);

    if ($stmt->fetch()) {

        $_SESSION["register_data"] = [
            "username"  => $result["data"]["username"],
            "full_name" => $result["data"]["full_name"],
            "email"     => $result["data"]["email"],
            "phone"     => $result["data"]["phone"]
        ];

        header(
            "Location: register.php?status=error&message=" .
            urlencode("Username or email already exists.")
        );

        exit;
    }

    /*
    |----------------------------------------------------------------------
    | Hash Password
    |----------------------------------------------------------------------
    */

    $hashedPassword = password_hash(
        $result["data"]["password"],
        PASSWORD_DEFAULT
    );

    /*
    |----------------------------------------------------------------------
    | Insert User
    |----------------------------------------------------------------------
    */

    $stmt = $pdo->prepare(
        "INSERT INTO users
        (
            username,
            password,
            full_name,
            email,
            phone
        )
        VALUES
        (
            :username,
            :password,
            :full_name,
            :email,
            :phone
        )"
    );

    $stmt->execute([
        ":username"  => $result["data"]["username"],
        ":password"  => $hashedPassword,
        ":full_name" => $result["data"]["full_name"],
        ":email"     => $result["data"]["email"],
        ":phone"     => $result["data"]["phone"]
    ]);

    /*
    |----------------------------------------------------------------------
    | Clear Saved Form Data
    |----------------------------------------------------------------------
    */

    unset($_SESSION["register_data"]);

    /*
    |----------------------------------------------------------------------
    | Registration Successful
    |----------------------------------------------------------------------
    */

    header(
        "Location: register.php?status=success&message=" .
        urlencode("Registration successful! You can now log in.")
    );

    exit;

} catch (PDOException $e) {

    header(
        "Location: register.php?status=error&message=" .
        urlencode("Registration failed. Please try again.")
    );

    exit;
}

