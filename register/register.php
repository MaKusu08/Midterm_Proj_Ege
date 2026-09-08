<?php

session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: ../dashboard.php");
    exit;
}

$status  = $_GET["status"] ?? null;
$message = $_GET["message"] ?? null;

/*
|--------------------------------------------------------------------------
| Get Previous Form Data
|--------------------------------------------------------------------------
*/

$formData = $_SESSION["register_data"] ?? [];

unset($_SESSION["register_data"]);

$username  = $formData["username"] ?? "";
$full_name = $formData["full_name"] ?? "";
$email     = $formData["email"] ?? "";
$phone     = $formData["phone"] ?? "";

?>

<!DOCTYPE html>

<html lang="en">

<head>


<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Register | PickServe</title>

<link rel="stylesheet" href="../styles/style.css">
<link rel="stylesheet" href="../styles/register_style.css">


</head>

<body>

<main class="register-page">


<div class="register-container">


    <!-- LOGO -->

    <div class="register-logo">

        <a href="../index.php">
            Pick<span>Serve</span>
        </a>

    </div>


    <!-- HEADING -->

    <h1>
        Create Account
    </h1>

    <p class="register-subtitle">
        Create your PickServe account and start booking courts.
    </p>


    <!-- ERROR MESSAGE -->

    <?php if ($status === "error" && $message): ?>

        <div class="register-message error">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php endif; ?>


    <!-- SUCCESS MESSAGE -->

    <?php if ($status === "success" && $message): ?>

        <div class="register-message success">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php endif; ?>


    <!-- REGISTER FORM -->

    <form
        method="POST"
        action="register_function.php"
        class="register-form"
    >

        <label for="username">
            Username
        </label>

        <input
            type="text"
            id="username"
            name="username"
            placeholder="Enter username"
            value="<?= htmlspecialchars($username) ?>"
            autocomplete="username"
            required
        >


        <label for="full_name">
            Full Name
        </label>

        <input
            type="text"
            id="full_name"
            name="full_name"
            placeholder="Enter your full name"
            value="<?= htmlspecialchars($full_name) ?>"
            autocomplete="name"
            required
        >


        <label for="email">
            Email
        </label>

        <input
            type="email"
            id="email"
            name="email"
            placeholder="Enter your email"
            value="<?= htmlspecialchars($email) ?>"
            autocomplete="email"
            required
        >


        <label for="phone">
            Phone Number
        </label>

        <input
            type="text"
            id="phone"
            name="phone"
            placeholder="Enter phone number"
            value="<?= htmlspecialchars($phone) ?>"
            autocomplete="tel"
        >


        <label for="password">
            Password
        </label>

        <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter password"
            autocomplete="new-password"
            required
        >


        <label for="confirm_password">
            Confirm Password
        </label>

        <input
            type="password"
            id="confirm_password"
            name="confirm_password"
            placeholder="Confirm your password"
            autocomplete="new-password"
            required
        >


        <button
            type="submit"
            name="register"
            class="register-button"
        >
            Create Account
        </button>

    </form>


    <!-- LOGIN -->

    <p class="register-login">

        Already have an account?

        <a href="../login/login.php">
            Login
        </a>

    </p>


    <!-- HOME -->

    <a href="../index.php" class="register-home">
        ← Back to Home
    </a>


</div>


</main>

</body>

</html>
