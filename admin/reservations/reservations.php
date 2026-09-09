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

$reservations = [];

try {

    $pdo = getConnection();

    $stmt = $pdo->prepare("
        SELECT
            r.id,
            r.user_id,
            r.court_id,
            r.court_number,
            r.booking_date,
            r.booking_time,
            r.duration,
            r.status,
            r.created_at,

            u.username,
            u.full_name,
            u.email,

            c.court_name,
            c.location

        FROM reservations r

        INNER JOIN users u
            ON r.user_id = u.id

        INNER JOIN courts c
            ON r.court_id = c.id

        ORDER BY
            r.booking_date DESC,
            r.booking_time DESC
    ");

    $stmt->execute();

    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $reservations = [];

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Reservations | PickServe Admin</title>

    <link
        rel="stylesheet"
        href="../../styles/style.css"
    >

    <link
        rel="stylesheet"
        href="../../styles/admin_styles.css"
    >

</head>

<body>

<header class="admin-navbar">

    <a
        href="../dashboard.php"
        class="admin-logo"
    >
        Pick<span>Serve</span>
        <small>ADMIN PANEL</small>
    </a>

    <nav class="admin-nav">

        <a href="../dashboard.php">
            Dashboard
        </a>

        <a href="../courts/courts.php">
            Courts
        </a>

        <a
            href="reservations.php"
            class="active"
        >
            Reservations
        </a>

        <a href="../users/users.php">
            Users
        </a>

        <a href="../messages/messages.php">
            Messages
        </a>

        <a
            href="../../logout.php"
            class="logout-btn"
        >
            Logout
        </a>

    </nav>

</header>


<main class="admin-main">

    <div class="admin-container">

        <div class="admin-page-header">

            <div>

                <h1>
                    Reservations
                </h1>

                <p>
                    View and manage customer court reservations.
                </p>

            </div>

        </div>


        <?php if (isset($_GET["success"])): ?>

            <?php if ($_GET["success"] === "updated"): ?>

                <div class="admin-message success">
                    Reservation updated successfully.
                </div>

            <?php elseif ($_GET["success"] === "deleted"): ?>

                <div class="admin-message success">
                    Reservation deleted successfully.
                </div>

            <?php endif; ?>

        <?php endif; ?>


        <?php if (isset($_GET["error"])): ?>

            <div class="admin-message error">

                <?php

                $error = $_GET["error"];

                switch ($error) {

                    case "invalid":
                        echo "Invalid reservation information.";
                        break;

                    case "notfound":
                        echo "Reservation was not found.";
                        break;

                    case "locked":
                        echo "Only pending reservations can be edited.";
                        break;

                    case "conflict":
                        echo "The selected court and time already have a reservation.";
                        break;

                    case "maintenance":
                        echo "The selected court is currently under maintenance.";
                        break;

                    case "past":
                        echo "The booking date and time cannot be in the past.";
                        break;

                    case "court":
                        echo "The selected court was not found.";
                        break;

                    case "database":
                        echo "A database error occurred. Please try again.";
                        break;

                    default:
                        echo "Something went wrong.";
                        break;

                }

                ?>

            </div>

        <?php endif; ?>


        <div class="admin-table-wrapper">

            <table class="admin-table">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Customer</th>

                        <th>Court</th>

                        <th>Date</th>

                        <th>Time</th>

                        <th>Duration</th>

                        <th>Status</th>

                        <th>Created</th>

                        <th>Action</th>

                    </tr>

                </thead>

                <tbody>

                <?php if (empty($reservations)): ?>

                    <tr>

                        <td
                            colspan="9"
                            class="empty-table"
                        >
                            No reservations found.
                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($reservations as $reservation): ?>

                        <?php

                        $status = strtolower(
                            trim(
                                (string) $reservation["status"]
                            )
                        );

                        ?>

                        <tr>

                            <!-- ID -->

                            <td>

                                <strong>
                                    #<?= (int) $reservation["id"] ?>
                                </strong>

                            </td>


                            <!-- CUSTOMER -->

                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        $reservation["full_name"]
                                    ) ?>

                                </strong>

                                <small>

                                    @<?= htmlspecialchars(
                                        $reservation["username"]
                                    ) ?>

                                </small>

                                <small>

                                    <?= htmlspecialchars(
                                        $reservation["email"]
                                    ) ?>

                                </small>

                            </td>


                            <!-- COURT -->

                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        $reservation["court_name"]
                                    ) ?>

                                </strong>

                                <small>

                                    <?= htmlspecialchars(
                                        $reservation["location"]
                                    ) ?>

                                </small>

                                <small>

                                    Court
                                    <?= (int) $reservation["court_number"] ?>

                                </small>

                            </td>


                            <!-- DATE -->

                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        date(
                                            "M d, Y",
                                            strtotime(
                                                $reservation["booking_date"]
                                            )
                                        )
                                    ) ?>

                                </strong>

                            </td>


                            <!-- TIME -->

                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        date(
                                            "h:i A",
                                            strtotime(
                                                $reservation["booking_time"]
                                            )
                                        )
                                    ) ?>

                                </strong>

                            </td>


                            <!-- DURATION -->

                            <td>

                                <strong>

                                    <?= (int) $reservation["duration"] ?>

                                    hour<?= $reservation["duration"] != 1 ? "s" : "" ?>

                                </strong>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <span
                                    class="status-badge status-<?= htmlspecialchars($status) ?>"
                                >

                                    <?= htmlspecialchars(
                                        ucfirst($status)
                                    ) ?>

                                </span>

                            </td>


                            <!-- CREATED -->

                            <td>

                                <small>

                                    <?= htmlspecialchars(
                                        date(
                                            "M d, Y h:i A",
                                            strtotime(
                                                $reservation["created_at"]
                                            )
                                        )
                                    ) ?>

                                </small>

                            </td>


                            <!-- ACTION -->

                            <td>

                                <div class="admin-actions">

                                    <?php if ($status === "pending"): ?>

                                        <a
                                            href="reservation_edit.php?id=<?= (int) $reservation["id"] ?>"
                                            class="admin-edit-btn"
                                        >
                                            Edit
                                        </a>

                                    <?php elseif ($status === "confirmed"): ?>

                                        <a
                                            href="reservation_delete.php?id=<?= (int) $reservation["id"] ?>"
                                            class="admin-delete-btn"
                                            onclick="return confirm('Are you sure you want to delete this confirmed reservation?');"
                                        >
                                            Delete
                                        </a>

                                    <?php else: ?>

                                        <small>
                                            Locked
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

