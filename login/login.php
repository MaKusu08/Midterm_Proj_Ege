<?php

session_start();

if (isset($_SESSION['user_id'])) {

    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header('Location: ../admin/dashboard.php');
        exit;
    }

    header('Location: ../dashboard.php');
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

    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="../styles/login_style.css">

</head>

<body>

<main class="login-page">

    <div class="login-container">

        <!-- LOGO -->
        <div class="login-logo">

            <a href="../index.php">
                Pick<span>Serve</span>
            </a>

        </div>

        <!-- HEADING -->
        <h1>
            Login Your Account
        </h1>

        <p class="login-subtitle">
            Sign in to manage your PickServe reservations.
        </p>

        <!-- LOGIN FORM -->
        <form
            method="POST"
            action="login_function.php"
            class="login-form"
        >

            <label for="username">
                Username
            </label>

            <input
                type="text"
                id="username"
                name="username"
                autocomplete="username"
                required
            >

            <label for="password">
                Password
            </label>

            <input
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                required
            >

            <button
                type="submit"
                name="login"
                class="login-button"
            >
                Login
            </button>

        </form>

        <!-- REGISTER -->
        <p class="login-register">

            Don't have an account?

            <a href="../register/register.php">
                Register
            </a>

        </p>

        <!-- HOME -->
        <a href="../index.php" class="login-home">
            ← Back to Home
        </a>

    </div>

</main>

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