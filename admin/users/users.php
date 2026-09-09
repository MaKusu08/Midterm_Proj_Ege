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

$users = [];

try {

    $pdo = getConnection();

    $stmt = $pdo->prepare("
        SELECT
            id,
            username,
            full_name,
            email,
            phone,
            role,
            created_at,
            updated_at
        FROM users
        ORDER BY id DESC
    ");

    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $users = [];

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Users | PickServe Admin</title>

    <link rel="stylesheet" href="../../styles/style.css">
    <link rel="stylesheet" href="../../styles/admin_styles.css">

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

                <h1>
                    Users
                </h1>

                <p>
                    View all registered PickServe users.
                </p>

            </div>

        </div>


        <div class="admin-table-wrapper">

            <table class="admin-table">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>User</th>

                        <th>Email</th>

                        <th>Phone</th>

                        <th>Role</th>

                        <th>Registered</th>

                        <th>Actions</th>

                    </tr>

                </thead>

                <tbody>

                <?php if (empty($users)): ?>

                    <tr>

                        <td
                            colspan="7"
                            class="empty-table"
                        >
                            No users found.
                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($users as $user): ?>

                        <?php

                        $role = strtolower(
                            trim($user["role"])
                        );

                        ?>

                        <tr>

                            <!-- ID -->

                            <td>

                                <strong>
                                    #<?= (int) $user["id"] ?>
                                </strong>

                            </td>


                            <!-- USER -->

                            <td>

                                <strong>
                                    <?= htmlspecialchars(
                                        $user["full_name"]
                                    ) ?>
                                </strong>

                                <small>
                                    @<?= htmlspecialchars(
                                        $user["username"]
                                    ) ?>
                                </small>

                            </td>


                            <!-- EMAIL -->

                            <td>

                                <?= htmlspecialchars(
                                    $user["email"]
                                ) ?>

                            </td>


                            <!-- PHONE -->

                            <td>

                                <?= !empty($user["phone"])
                                    ? htmlspecialchars($user["phone"])
                                    : "—"
                                ?>

                            </td>


                            <!-- ROLE -->

                            <td>

                                <span
                                    class="status-badge status-<?= htmlspecialchars($role) ?>"
                                >

                                    <?= htmlspecialchars(
                                        ucfirst($role)
                                    ) ?>

                                </span>

                            </td>


                            <!-- REGISTERED -->

                            <td>

                                <small>

                                    <?= htmlspecialchars(
                                        date(
                                            "M d, Y h:i A",
                                            strtotime(
                                                $user["created_at"]
                                            )
                                        )
                                    ) ?>

                                </small>

                            </td>


                            <!-- ACTIONS -->

                            <td>

                                <div class="admin-actions">

                                    <a
                                        href="user_edit.php?id=<?= (int) $user["id"] ?>"
                                        class="admin-edit-btn"
                                    >
                                        Edit
                                    </a>

                                    <?php if (
                                        (int) $user["id"] !==
                                        (int) $_SESSION["user_id"]
                                    ): ?>

                                        <a
                                            href="user_delete.php?id=<?= (int) $user["id"] ?>"
                                            class="admin-delete-btn"
                                            onclick="return confirm('Are you sure you want to delete this user?');"
                                        >
                                            Delete
                                        </a>

                                    <?php else: ?>

                                        <small>
                                            Current Account
                                        </small>

                                    <?php endif; ?>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>

</body>

</html>