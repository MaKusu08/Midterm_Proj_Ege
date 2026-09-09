<?php

session_start();

require_once "database/db.php";

/*
|--------------------------------------------------------------------------
| Check Login
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {
    header("Location: login/login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Only Allow POST
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: booking.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Get User ID From Session
|--------------------------------------------------------------------------
*/

$user_id = (int) $_SESSION["user_id"];

/*
|--------------------------------------------------------------------------
| Get Form Data
|--------------------------------------------------------------------------
*/

$court_id = isset($_POST["court_id"])
    ? (int) $_POST["court_id"]
    : 0;

$court_number = isset($_POST["court_number"])
    ? (int) $_POST["court_number"]
    : 0;

$booking_date = trim($_POST["booking_date"] ?? "");

$booking_time = trim($_POST["booking_time"] ?? "");

$duration = isset($_POST["duration"])
    ? (int) $_POST["duration"]
    : 0;


/*
|--------------------------------------------------------------------------
| Basic Validation
|--------------------------------------------------------------------------
*/

if ($court_id <= 0) {
    header("Location: booking.php");
    exit;
}

if (!in_array($court_number, [1, 2, 3], true)) {

    header(
        "Location: booking_form.php?court_id="
        . $court_id
        . "&error=court_number"
    );

    exit;
}

if (
    empty($booking_date) ||
    empty($booking_time) ||
    $duration <= 0
) {

    header(
        "Location: booking_form.php?court_id="
        . $court_id
        . "&error=invalid"
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Validate Date
|--------------------------------------------------------------------------
*/

$date_object = DateTime::createFromFormat(
    "Y-m-d",
    $booking_date
);

if (
    !$date_object ||
    $date_object->format("Y-m-d") !== $booking_date
) {

    header(
        "Location: booking_form.php?court_id="
        . $court_id
        . "&error=date"
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Validate Time
|--------------------------------------------------------------------------
*/

$time_object = DateTime::createFromFormat(
    "H:i",
    $booking_time
);

if (
    !$time_object ||
    $time_object->format("H:i") !== $booking_time
) {

    header(
        "Location: booking_form.php?court_id="
        . $court_id
        . "&error=time"
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Only Allow 1, 2, or 3 Hours
|--------------------------------------------------------------------------
*/

if (!in_array($duration, [1, 2, 3], true)) {

    header(
        "Location: booking_form.php?court_id="
        . $court_id
        . "&error=duration"
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Prevent Past Dates
|--------------------------------------------------------------------------
*/

$today = date("Y-m-d");

if ($booking_date < $today) {

    header(
        "Location: booking_form.php?court_id="
        . $court_id
        . "&error=past"
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Prevent Past Time When Booking Today
|--------------------------------------------------------------------------
*/

if ($booking_date === $today) {

    $current_time = date("H:i");

    if ($booking_time <= $current_time) {

        header(
            "Location: booking_form.php?court_id="
            . $court_id
            . "&error=past_time"
        );

        exit;
    }
}


/*
|--------------------------------------------------------------------------
| Database
|--------------------------------------------------------------------------
*/

try {

    $pdo = getConnection();


    /*
    |--------------------------------------------------------------------------
    | Check Court
    |--------------------------------------------------------------------------
    */

    $court_stmt = $pdo->prepare(
        "SELECT
            id,
            court_name,
            status
         FROM courts
         WHERE id = :court_id
         LIMIT 1"
    );

    $court_stmt->execute([
        ":court_id" => $court_id
    ]);

    $court = $court_stmt->fetch();


    /*
    |--------------------------------------------------------------------------
    | Court Not Found
    |--------------------------------------------------------------------------
    */

    if (!$court) {

        header(
            "Location: booking.php?error=court"
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Check Court Availability
    |--------------------------------------------------------------------------
    */

    $court_status = strtolower(
        trim($court["status"])
    );

    if ($court_status !== "available") {

        header(
            "Location: booking_form.php?court_id="
            . $court_id
            . "&error=unavailable"
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Check For Overlapping Reservations
    |--------------------------------------------------------------------------
    |
    | Only checks the selected physical court number.
    |
    | Example:
    |
    | Court 1 → 2:00 PM - 4:00 PM
    | Court 2 → 2:00 PM - 4:00 PM
    |
    | These are allowed because they are different courts.
    |
    |--------------------------------------------------------------------------
    */

    $overlap_stmt = $pdo->prepare(
        "SELECT id
         FROM reservations
         WHERE court_id = :court_id
         AND court_number = :court_number
         AND booking_date = :booking_date
         AND status IN ('pending', 'confirmed')

         AND booking_time < ADDTIME(
             :new_booking_time_1,
             SEC_TO_TIME(:duration_1 * 3600)
         )

         AND ADDTIME(
             booking_time,
             SEC_TO_TIME(duration * 3600)
         ) > :new_booking_time_2

         LIMIT 1"
    );


    $overlap_stmt->execute([
        ":court_id" => $court_id,

        ":court_number" => $court_number,

        ":booking_date" => $booking_date,

        ":new_booking_time_1" => $booking_time,

        ":duration_1" => $duration,

        ":new_booking_time_2" => $booking_time
    ]);


    $existing_booking = $overlap_stmt->fetch();


    /*
    |--------------------------------------------------------------------------
    | Booking Already Exists
    |--------------------------------------------------------------------------
    */

    if ($existing_booking) {

        header(
            "Location: booking_form.php?court_id="
            . $court_id
            . "&error=booked"
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Insert Reservation
    |--------------------------------------------------------------------------
    */

    $insert_stmt = $pdo->prepare(
        "INSERT INTO reservations (
            user_id,
            court_id,
            court_number,
            booking_date,
            booking_time,
            duration,
            status
        )
        VALUES (
            :user_id,
            :court_id,
            :court_number,
            :booking_date,
            :booking_time,
            :duration,
            'pending'
        )"
    );


    $insert_stmt->execute([

        ":user_id" => $user_id,

        ":court_id" => $court_id,

        ":court_number" => $court_number,

        ":booking_date" => $booking_date,

        ":booking_time" => $booking_time,

        ":duration" => $duration

    ]);


    /*
    |--------------------------------------------------------------------------
    | Success
    |--------------------------------------------------------------------------
    */

    header(
        "Location: reservations.php?success=1"
    );

    exit;


} catch (PDOException $e) {


    /*
    |--------------------------------------------------------------------------
    | Database Error
    |--------------------------------------------------------------------------
    */

    header(
        "Location: booking_form.php?court_id="
        . $court_id
        . "&error=database"
    );

    exit;
}