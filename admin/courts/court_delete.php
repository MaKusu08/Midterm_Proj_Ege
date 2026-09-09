<?php

session_start();

require_once "../../database/db.php";

/*
|--------------------------------------------------------------------------
| Admin Access
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {
    header("Location: ../../login/login.php");
    exit;
}

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../../dashboard.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Get Court ID
|--------------------------------------------------------------------------
*/

$court_id = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

if (!$court_id || $court_id < 1) {

    header(
        "Location: courts.php?error=" .
        urlencode("Invalid court ID.")
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Delete Court
|--------------------------------------------------------------------------
*/

try {

    $pdo = getConnection();


    /*
    |--------------------------------------------------------------------------
    | Check Court Exists
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT id
        FROM courts
        WHERE id = :id
        LIMIT 1
    ");

    $stmt->execute([
        ":id" => $court_id
    ]);

    if (!$stmt->fetch()) {

        header(
            "Location: courts.php?error=" .
            urlencode("Court not found.")
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Court
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        DELETE FROM courts
        WHERE id = :id
    ");

    $stmt->execute([
        ":id" => $court_id
    ]);


    /*
    |--------------------------------------------------------------------------
    | Success
    |--------------------------------------------------------------------------
    */

    header(
        "Location: courts.php?success=" .
        urlencode("Court deleted successfully.")
    );

    exit;


} catch (PDOException $e) {

    /*
    |--------------------------------------------------------------------------
    | Foreign Key / Database Error
    |--------------------------------------------------------------------------
    */

    header(
        "Location: courts.php?error=" .
        urlencode(
            "Unable to delete this court. It may have existing reservations."
        )
    );

    exit;
}

