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

$user_id = (int) ($_GET["id"] ?? 0);

if ($user_id <= 0) {
    header("Location: users.php?error=invalid");
    exit;
}

try {

    $pdo = getConnection();

    $stmt = $pdo->prepare("
        SELECT
            id,
            username,
            full_name,
            email,
            phone,
            role
        FROM users
        WHERE id = :id
        LIMIT 1
    ");

    $stmt->execute([
        ":id" => $user_id
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: users.php?error=notfound");
        exit;
    }

} catch (PDOException $e) {

    header("Location: users.php?error=database");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit User | PickServe Admin</title>

    <link rel="stylesheet" href="../../styles/style.css">
    <link rel="stylesheet" href="../../styles/admin_style.css">

</head>

<body>

<header class="admin-navbar">

    <a href="../dashboard.php" class="admin-logo">

        Pick<span>Serve</span>

        <small>
            ADMIN PANEL
        </small>

    </a>

    <nav class="admin-nav">

        <a href="../dashboard.php">
            Dashboard
        </a>

        <a href="../courts/courts.php">
            Courts
        </a>

        <a href="../reservations/reservations.php">
            Reservations
        </a>

        <a href="users.php" class="active">
            Users
        </a>

        <a href="../messages/messages.php">
            Messages
        </a>

        <a href="../../logout.php" class="logout-btn">
            Logout
        </a>

    </nav>

</header>


<main class="admin-main">

    <div class="admin-container">

        <div class="admin-page-header">

            <div>

                <div class="admin-label">

                    <span></span>

                    USER MANAGEMENT

                </div>

                <h1>
                    Edit User
                </h1>

                <p>
                    Update the user's account information.
                </p>

            </div>

            <a
                href="users.php"
                class="admin-secondary-btn"
            >
                Back to Users
            </a>

        </div>


        <section class="admin-form-section">

            <form
                action="user_update.php"
                method="POST"
                class="admin-form"
            >

                <input
                    type="hidden"
                    name="user_id"
                    value="<?= (int) $user["id"] ?>"
                >


                <div class="admin-form-group">

                    <label for="username">
                        Username
                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        maxlength="50"
                        required
                        value="<?= htmlspecialchars($user["username"]) ?>"
                    >

                </div>


                <div class="admin-form-group">

                    <label for="full_name">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="full_name"
                        name="full_name"
                        maxlength="100"
                        required
                        value="<?= htmlspecialchars($user["full_name"]) ?>"
                    >

                </div>


                <div class="admin-form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        maxlength="100"
                        required
                        value="<?= htmlspecialchars($user["email"]) ?>"
                    >

                </div>


                <div class="admin-form-group">

                    <label for="phone">
                        Phone
                    </label>

                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        maxlength="20"
                        value="<?= htmlspecialchars($user["phone"] ?? "") ?>"
                    >

                </div>


                <div class="admin-form-group">

                    <label for="role">
                        Role
                    </label>

                    <select
                        id="role"
                        name="role"
                        required
                    >

                        <option
                            value="user"
                            <?= $user["role"] === "user" ? "selected" : "" ?>
                        >
                            User
                        </option>

                        <option
                            value="admin"
                            <?= $user["role"] === "admin" ? "selected" : "" ?>
                        >
                            Admin
                        </option>

                    </select>

                </div>


                <div class="admin-form-group">

                    <label for="password">
                        New Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        maxlength="255"
                        placeholder="Leave blank to keep current password"
                    >

                </div>


                <div class="admin-form-actions">

                    <a
                        href="users.php"
                        class="admin-secondary-btn"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        name="update_user"
                        class="admin-primary-btn"
                    >
                        Update User
                    </button>

                </div>

            </form>

        </section>

    </div>

</main>

</body>

</html>