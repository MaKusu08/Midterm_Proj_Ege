<?php
// PickServe - Court Reservation Landing Page

require_once "database/db.php";
require_once "includes/validation.php";


/*
|--------------------------------------------------------------------------
| Contact Form Variables
|--------------------------------------------------------------------------
*/

$contact_success = "";
$contact_error = "";

$contact_name = "";
$contact_email = "";
$contact_role = "PLAYER/COURT OWNER";
$contact_message = "";


/*
|--------------------------------------------------------------------------
| CONTACT FORM
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["contact_form"])) {

    $validation = validateContactInput($_POST);

    $contact_errors = $validation["errors"];
    $contact_data = $validation["data"];

    $contact_name = $contact_data["name"];
    $contact_email = $contact_data["email"];
    $contact_role = $contact_data["role"];
    $contact_message = $contact_data["message"];


    if (empty($contact_errors)) {

        try {

            $pdo = getConnection();

            $stmt = $pdo->prepare("
                INSERT INTO contact_messages
                (
                    name,
                    email,
                    role,
                    message
                )
                VALUES
                (
                    :name,
                    :email,
                    :role,
                    :message
                )
            ");

            $stmt->execute([
                ":name" => $contact_name,
                ":email" => $contact_email,
                ":role" => $contact_role,
                ":message" => $contact_message
            ]);

            $contact_success = "Your message has been sent successfully.";

            // Clear form after successful submission
            $contact_name = "";
            $contact_email = "";
            $contact_role = "PLAYER/COURT OWNER";
            $contact_message = "";

        } catch (PDOException $e) {

            $contact_error = "Something went wrong while sending your message. Please try again.";

        }

    } else {

        // Show first validation error
        $contact_error = $contact_errors[0];

    }

}


/*
|--------------------------------------------------------------------------
| GET COURTS
|--------------------------------------------------------------------------
*/

try {

    $pdo = getConnection();

    $stmt = $pdo->prepare(
        "SELECT
            id,
            court_name,
            location,
            image,
            rating,
            status
         FROM courts
         ORDER BY id ASC"
    );

    $stmt->execute();

    $courts = $stmt->fetchAll();

} catch (PDOException $e) {

    $courts = [];

}


?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>PickServe | Pickleball Court Booking</title>

    <link rel="stylesheet" href="styles/style.css">

    <style>
        .contact-message {
            width: 100%;
            padding: 14px 16px;
            margin-bottom: 18px;
            border: 2px solid #000;
            border-radius: 10px;
            font-family: "Montserrat", Arial, sans-serif;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.4;
        }

        .contact-message.success {
            background: #dff5df;
            color: #176b1c;
        }

        .contact-message.error {
            background: #ffe1dc;
            color: #a32613;
        }
    </style>

</head>

<body>

<header class="site-header">

    <div class="container nav-wrap">

        <a href="#home" class="brand">
            <img src="assets/logo.png" alt="PickServe logo">
        </a>

        <nav class="main-nav">

            <a href="#home">
                Home
            </a>

            <a href="#about">
                About
            </a>

            <a href="#how-it-works">
                How it works
            </a>

            <a href="#courts">
                Courts
            </a>

            <a href="#contact">
                Contact
            </a>

        </nav>

        <div class="nav-actions">

            <a
                href="register/register.php"
                class="btn btn-light"
            >
                Sign up
            </a>

            <a
                href="login/login.php"
                class="btn btn-light"
            >
                Login
            </a>

            <a
                href="login/login.php"
                class="btn btn-primary"
            >
                Book Now
            </a>

        </div>

    </div>

</header>


<main>

    <!-- HERO -->

    <section id="home" class="hero">

        <div class="hero-copy">

            <div class="eyebrow">
                <span></span>
                Court booking, Dumaguete &amp; nearby
            </div>

            <h1>
                RESERVE YOUR NEXT
                <strong>PICKLEBALL</strong> COURT IN
                SECONDS
            </h1>

            <p>
                PickServe connects you to open courts around Dumaguete —
                real-time slots, no group chat chaos, no back-and-forth with
                the front desk.
            </p>

            <div class="hero-buttons">

                <a
                    href="#courts"
                    class="btn btn-primary btn-large"
                >
                    Find a court near me
                </a>

                <a
                    href="#how-it-works"
                    class="btn btn-outline btn-large"
                >
                    See how booking works
                </a>

            </div>

            <div class="stats">

                <div>
                    <b><?= count($courts) ?>+</b>
                    <span>Courts</span>
                </div>

                <div>
                    <b>6</b>
                    <span>Areas Covered</span>
                </div>

                <div>
                    <b>4.8 ★</b>
                    <span>Rating</span>
                </div>

            </div>

        </div>


        <div class="hero-image">

            <img
                src="assets/court1.png"
                alt="Indoor pickleball courts"
            >

        </div>

    </section>


    <!-- ABOUT -->

    <section id="about" class="about section-light">

        <div class="about-images">

            <div class="photo photo-back"></div>

            <div class="photo photo-middle"></div>

            <div class="photo photo-front">

                <img
                    src="assets/paddle1.png"
                    alt="Pickleball paddle and ball"
                >

            </div>

        </div>


        <div class="about-copy">

            <div class="section-label">
                <span></span>
                About PickServe
            </div>

            <h2>
                Built by Dumaguete players, for Dumaguete players.
            </h2>

            <p>
                PickServe started as a shared spreadsheet between a handful
                of players trying to split time on one court near Rizal
                Boulevard. It grew into a proper booking network covering
                the city and the towns just south and north of it — Sibulan,
                Bacong, Valencia, and Dauin.
            </p>

            <p>
                We're not a national platform trying to cover every province.
                We're focused on making it easy to find an open court, avoid
                double-bookings, and grow the local scene one match at a time.
            </p>

            <div class="about-stats">

                <div>
                    <b>2026</b>
                    <span>Founded in Dumaguete</span>
                </div>

                <div>
                    <b>300+</b>
                    <span>Local Players</span>
                </div>

            </div>

        </div>

    </section>


    <!-- HOW IT WORKS -->

    <section id="how-it-works" class="how section-dark">

        <div class="section-label yellow">
            <span></span>
            Three steps, one paddle
        </div>


        <div class="how-heading">

            <div>

                <h2>
                    Booking a court shouldn’t take longer than the game.
                </h2>

                <p>
                    No downloads required for the venue, no calling ahead —
                    just pick your spot, pick your time, and show up ready to serve.
                </p>

            </div>

        </div>


        <div class="steps">

            <article class="step-card step-one">

                <b>1</b>

                <h3>
                    Search your City
                </h3>

                <p>
                    Filter by neighborhood, indoor or outdoor, and time of day
                    to see every open slot nearby.
                </p>

            </article>


            <article class="step-card step-two">

                <b>2</b>

                <h3>
                    Lock your Slot
                </h3>

                <p>
                    Reserve instantly and pay online — your confirmation
                    doubles as your entry pass at the venue.
                </p>

            </article>


            <article class="step-card step-three">

                <b>3</b>

                <h3>
                    Show up and Play
                </h3>

                <p>
                    Flash your booking at the front desk, grab a paddle,
                    and get on the court. That’s it.
                </p>

            </article>

        </div>

    </section>


    <!-- COURTS -->

    <section id="courts" class="courts">


<div class="courts-intro">

    <div class="section-label">
        <span></span>
        Where to Play
    </div>

    <h2>
        Courts across Dumaguete and the towns next door.
    </h2>

    <p>
        Every listing shows live availability, surface type, and whether
        it's indoor or under the sun — so you know exactly what you're
        booking before you show up.
    </p>

</div>


<div class="court-grid">

    <?php if (!empty($courts)): ?>

        <?php foreach ($courts as $court): ?>

            <?php
            $status = strtolower(trim($court['status']));
            ?>

            <article class="court-card">

                <img
                    src="<?= htmlspecialchars($court['image']) ?>"
                    alt="<?= htmlspecialchars($court['court_name']) ?> pickleball court"
                >

                <div class="court-info">

                    <h3>
                        <?= htmlspecialchars($court['court_name']) ?>
                    </h3>

                    <p>
                        <?= htmlspecialchars($court['location']) ?>
                    </p>

                    <span>
                        ★★★★★
                    </span>

                    <?php if ($status === 'available'): ?>

                        <div class="court-status available">
                            ● Available
                        </div>

                    <?php elseif ($status === 'maintenance'): ?>

                        <div class="court-status maintenance">
                            ● Maintenance
                        </div>

                    <?php else: ?>

                        <div class="court-status">
                            ● <?= htmlspecialchars(ucfirst($status)) ?>
                        </div>

                    <?php endif; ?>

                </div>

            </article>

        <?php endforeach; ?>

    <?php else: ?>

        <p>
            No courts are currently available.
        </p>

    <?php endif; ?>

</div>


</section>



    <!-- CONTACT -->

    <section id="contact" class="contact section-dark">

        <div class="contact-copy">

            <div class="section-label yellow">
                <span></span>
                Get in Touch
            </div>

            <h2>
                Questions about a <strong>booking</strong>, or want to list your court?
            </h2>

            <p>
                Our team replies within one business day. For venue partnerships,
                use the “I run a court” option below.
            </p>


            <div class="contact-details">

                <p>
                    <b>EMAIL:</b>
                    hello@pickserve.ph
                </p>

                <p>
                    <b>CONTACT #:</b>
                    +63 ## ### ####
                </p>

                <p>
                    <b>HOURS:</b>
                    Mon–Sat, 8am–8pm PHT
                </p>

            </div>

        </div>


        <form
            class="contact-form"
            action="index.php#contact"
            method="post"
        >

    <input
        type="hidden"
        name="contact_form"
        value="1"
    >


    <?php if ($contact_success !== ""): ?>

        <div class="contact-message success">
            <?= htmlspecialchars($contact_success) ?>
        </div>

    <?php endif; ?>


    <?php if ($contact_error !== ""): ?>

        <div class="contact-message error">
            <?= htmlspecialchars($contact_error) ?>
        </div>

    <?php endif; ?>


    <label for="name">
        Name
    </label>

    <input
        id="name"
        name="name"
        type="text"
        placeholder="John Doe"
        value="<?= htmlspecialchars($contact_name) ?>"
        required
    >


    <label for="email">
        Email
    </label>

    <input
        id="email"
        name="email"
        type="email"
        placeholder="hello@example.com"
        value="<?= htmlspecialchars($contact_email) ?>"
        required
    >


    <label for="role">
        I AM A
    </label>

    <select
        id="role"
        name="role"
        required
    >

        <option
            value="PLAYER/COURT OWNER"
            <?= $contact_role === "PLAYER/COURT OWNER" ? "selected" : "" ?>
        >
            PLAYER/COURT OWNER
        </option>

        <option
            value="PLAYER"
            <?= $contact_role === "PLAYER" ? "selected" : "" ?>
        >
            PLAYER
        </option>

        <option
            value="COURT OWNER"
            <?= $contact_role === "COURT OWNER" ? "selected" : "" ?>
        >
            COURT OWNER
        </option>

    </select>


    <label for="message">
        Message
    </label>

    <textarea
        id="message"
        name="message"
        placeholder="Write your message..."
        required
    ><?= htmlspecialchars($contact_message) ?></textarea>


    <button
        type="submit"
        class="btn btn-primary"
    >
        Send Message
    </button>

</form>

    </section>

</main>


<!-- FOOTER -->

<footer class="site-footer">

    <div class="footer-main">

        <div class="footer-brand">

            <img
                src="assets/logo.png"
                alt="PickServe logo"
            >

            <div class="socials">

                <a
                    href="#"
                    aria-label="Facebook"
                >
                    f
                </a>

                <a
                    href="#"
                    aria-label="Instagram"
                >
                    ◎
                </a>

            </div>

        </div>


        <div class="footer-column">

            <h3>
                PLAY
            </h3>

            <a href="register/register.php">
                Sign Up
            </a>

            <a href="login/login.php">
                Login
            </a>

            <a href="#how-it-works">
                How it works
            </a>

        </div>


        <div class="footer-column">

            <h3>
                VENUES
            </h3>

            <a href="#courts">
                Courts
            </a>

        </div>


        <div class="footer-column">

            <h3>
                SUPPORT
            </h3>

            <a href="#contact">
                Contact
            </a>

        </div>

    </div>


    <div class="footer-bottom">

        <span>
            2026
        </span>

        <span>
            PICKSERVE
        </span>

        <span>
            DUMAGUETE
        </span>

    </div>

</footer>

</body>
</html>