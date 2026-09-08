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

</head>

<body>

    <h1>Create Account</h1>

    <?php if ($status === "error" && $message): ?>

        <p style="color: red;">
            <?= htmlspecialchars($message) ?>
        </p>

    <?php endif; ?>


    <?php if ($status === "success" && $message): ?>

        <p style="color: green;">
            <?= htmlspecialchars($message) ?>
        </p>

    <?php endif; ?>


    <form method="POST" action="register_function.php">

        <input
            type="text"
            name="username"
            placeholder="Username"
            value="<?= htmlspecialchars($username) ?>"
            required
        >

        <br><br>


        <input
            type="text"
            name="full_name"
            placeholder="Full Name"
            value="<?= htmlspecialchars($full_name) ?>"
            required
        >

        <br><br>


        <input
            type="email"
            name="email"
            placeholder="Email"
            value="<?= htmlspecialchars($email) ?>"
            required
        >

        <br><br>


        <input
            type="text"
            name="phone"
            placeholder="Phone Number"
            value="<?= htmlspecialchars($phone) ?>"
        >

        <br><br>


        <input
            type="password"
            name="password"
            placeholder="Password"
            required
        >

        <br><br>


        <input
            type="password"
            name="confirm_password"
            placeholder="Confirm Password"
            required
        >

        <br><br>


        <button type="submit" name="register">
            Register
        </button>

    </form>


    <p>
        Already have an account?
        <a href="../login/login.php">Login</a>
    </p>

</body>

</html>

