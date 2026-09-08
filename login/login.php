<?php

session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | PickServe</title>

</head>

<body>

    <h1>Login Your Account</h1>

    <form method="POST" action="login_function.php">

        <label>Username</label><br>

        <input
            type="text"
            name="username"
            required
        >

        <br><br>


        <label>Password</label><br>

        <input
            type="password"
            name="password"
            required
        >

        <br><br>


        <button type="submit" name="login">
            Login
        </button>

    </form>


    <p>
        Don't have an account?
        <a href="../register/register.php">Register</a>
    </p>


    <?php if ($status === 'error' && $message): ?>

        <script>
            alert(<?= json_encode($message) ?>);
        </script>

    <?php endif; ?>


    <?php if ($status === 'success' && $message): ?>

        <script>
            alert(<?= json_encode($message) ?>);
        </script>

    <?php endif; ?>

</body>

</html>

