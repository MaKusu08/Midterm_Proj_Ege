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

$messages = [];
$error_message = "";

try {

    $pdo = getConnection();

    $stmt = $pdo->prepare("
        SELECT
            id,
            name,
            email,
            role,
            message,
            created_at
        FROM contact_messages
        ORDER BY created_at DESC
    ");

    $stmt->execute();

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $error_message = $e->getMessage();

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Messages | PickServe Admin</title>

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

        <a href="../users/users.php">
            Users
        </a>

        <a href="messages.php" class="active">
            Messages
        </a>

        <a href="../../logout.php" class="logout-btn">
            Logout
        </a>

    </nav>

</header>


<main class="admin-main">

    <div class="admin-container">


        <div class="admin-users-header">

            <div>

                <div class="admin-label">

                    <span></span>

                    MESSAGE MANAGEMENT

                </div>


                <h1>
                    Messages
                </h1>


                <p>
                    View messages submitted by players and court owners.
                </p>

            </div>

        </div>


        <?php if ($error_message !== ""): ?>

            <div class="admin-message error">

                Database Error:

                <?= htmlspecialchars($error_message) ?>

            </div>

        <?php endif; ?>


        <?php if (isset($_GET["success"])): ?>

            <?php if ($_GET["success"] === "deleted"): ?>

                <div class="admin-message success">

                    Message deleted successfully.

                </div>

            <?php endif; ?>

        <?php endif; ?>


        <?php if (isset($_GET["error"])): ?>

            <div class="admin-message error">

                Unable to process the request.

            </div>

        <?php endif; ?>


        <div class="messages-table-wrapper">

            <table class="messages-table">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Sender</th>

                        <th>Email</th>

                        <th>Role</th>

                        <th>Message</th>

                        <th>Sent</th>

                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>


                <?php if (empty($messages)): ?>

                    <tr>

                        <td colspan="7">

                            <div class="messages-empty">

                                <h3>
                                    No Messages Found
                                </h3>

                                <p>
                                    There are currently no messages.
                                </p>

                            </div>

                        </td>

                    </tr>


                <?php else: ?>


                    <?php foreach ($messages as $item): ?>

                        <?php

                        $role = strtoupper(
                            trim($item["role"] ?? "")
                        );

                        ?>


                        <tr>


                            <td>

                                <strong>
                                    #<?= (int) $item["id"] ?>
                                </strong>

                            </td>


                            <td>

                                <div class="message-sender">


                                    <div class="message-avatar">

                                        <?= htmlspecialchars(
                                            strtoupper(
                                                substr(
                                                    trim($item["name"] ?? ""),
                                                    0,
                                                    1
                                                )
                                            )
                                        ) ?>

                                    </div>


                                    <div class="message-sender-info">

                                        <span class="message-name">

                                            <?= htmlspecialchars(
                                                $item["name"] ?? ""
                                            ) ?>

                                        </span>

                                    </div>

                                </div>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $item["email"] ?? ""
                                ) ?>

                            </td>


                            <td>

                                <span
                                    class="message-role <?= htmlspecialchars(
                                        strtolower(
                                            str_replace(
                                                " ",
                                                "-",
                                                $role
                                            )
                                        )
                                    ) ?>"
                                >

                                    <?= htmlspecialchars($role) ?>

                                </span>

                            </td>


                            <td>

                                <div class="message-content">

                                    <?= nl2br(
                                        htmlspecialchars(
                                            $item["message"] ?? ""
                                        )
                                    ) ?>

                                </div>

                            </td>


                            <td>

                                <small>

                                    <?= htmlspecialchars(
                                        date(
                                            "M d, Y h:i A",
                                            strtotime(
                                                $item["created_at"]
                                            )
                                        )
                                    ) ?>

                                </small>

                            </td>


                            <td>

                                <div class="admin-actions">

                                    <a
                                        href="message_delete.php?id=<?= (int) $item["id"] ?>"
                                        class="admin-delete-btn"
                                        onclick="return confirm('Are you sure you want to delete this message?');"
                                    >
                                        Delete
                                    </a>

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