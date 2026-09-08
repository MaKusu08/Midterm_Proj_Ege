<?php
// PickServe - Court Reservation Landing Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PickServe | Pickleball Court Booking</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="site-header">
    <div class="container nav-wrap">
        <a href="#home" class="brand">
            <img src="assets/logo.png" alt="PickServe logo">
        </a>

        <nav class="main-nav">
            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#how-it-works">How it works</a>
            <a href="#courts">Courts</a>
            <a href="#contact">Contact</a>
        </nav>

        <div class="nav-actions">
            <a href="#" class="btn btn-light">Sign up</a>
            <a href="#" class="btn btn-light">Login</a>
            <a href="#courts" class="btn btn-primary">Book Now</a>
        </div>
    </div>
</header>

<main>
    <!-- HERO -->
    <section id="home" class="hero">
        <div class="hero-copy">
            <div class="eyebrow"><span></span>Court booking, Dumaguete &amp; nearby</div>

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
                <a href="#courts" class="btn btn-primary btn-large">Find a court near me</a>
                <a href="#how-it-works" class="btn btn-outline btn-large">See how booking works</a>
            </div>

            <div class="stats">
                <div>
                    <b>22+</b>
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
            <img src="assets/court1.png" alt="Indoor pickleball courts">
        </div>
    </section>

    <!-- ABOUT -->
    <section id="about" class="about section-light">
        <div class="about-images">
            <div class="photo photo-back"></div>
            <div class="photo photo-middle"></div>
            <div class="photo photo-front">
                <img src="assets/paddle1.png" alt="Pickleball paddle and ball">
            </div>
        </div>

        <div class="about-copy">
            <div class="section-label"><span></span>About PickServe</div>
            <h2>Built by Dumaguete players, for Dumaguete players.</h2>

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
        <div class="section-label yellow"><span></span>Three steps, one paddle</div>

        <div class="how-heading">
            <div>
                <h2>Booking a court shouldn’t take longer than the game.</h2>
                <p>
                    No downloads required for the venue, no calling ahead —
                    just pick your spot, pick your time, and show up ready to serve.
                </p>
            </div>
            <a href="#courts" class="btn btn-primary">Book your first court here!</a>
        </div>

        <div class="steps">
            <article class="step-card step-one">
                <b>1</b>
                <h3>Search your City</h3>
                <p>Filter by neighborhood, indoor or outdoor, and time of day to see every open slot nearby.</p>
            </article>

            <article class="step-card step-two">
                <b>2</b>
                <h3>Lock your Slot</h3>
                <p>Reserve instantly and pay online — your confirmation doubles as your entry pass at the venue.</p>
            </article>

            <article class="step-card step-three">
                <b>3</b>
                <h3>Show up and Play</h3>
                <p>Flash your booking at the front desk, grab a paddle, and get on the court. That’s it.</p>
            </article>
        </div>
    </section>

    <!-- COURTS -->
    <section id="courts" class="courts">
        <div class="courts-intro">
            <div class="section-label"><span></span>Where to Play</div>
            <h2>Courts across Dumaguete and the towns next door.</h2>
            <p>
                Every listing shows live availability, surface type, and whether
                it's indoor or under the sun — so you know exactly what you're
                booking before you show up.
            </p>
        </div>

        <div class="court-grid">
            <article class="court-card">
                <img src="assets/sunset.jpg" alt="Pickleball court">
                <div class="court-info">
                    <h3>Sunset</h3>
                    <p>Dumaguete City</p>
                    <span>★★★★★</span>
                </div>
            </article>

            <article class="court-card">
                <img src="assets/bread.jpg" alt="Pickleball equipment">
                <div class="court-info">
                    <h3>Bread o' Clock</h3>
                    <p>Dumaguete City</p>
                    <span>★★★★★</span>
                </div>
            </article>

            <article class="court-card">
                <img src="assets/teacool.jpg" alt="Indoor pickleball court">
                <div class="court-info">
                    <h3>Teacool Bulls</h3>
                    <p>Dumaguete City</p>
                    <span>★★★★★</span>
                </div>
            </article>

            <article class="court-card">
                <img src="assets/river.jpg" alt="Pickleball paddle">
                <div class="court-info">
                    <h3>The Riverside</h3>
                    <p>Dumaguete City</p>
                    <span>★★★★★</span>
                </div>
            </article>

            <article class="court-card">
                <img src="assets/dome.jpg" alt="Pickleball courts">
                <div class="court-info">
                    <h3>Pickle Dome</h3>
                    <p>Dumaguete City</p>
                    <span>★★★★★</span>
                </div>
            </article>
        </div>
    </section>

    <!-- CONTACT -->
    <section id="contact" class="contact section-dark">
        <div class="contact-copy">
            <div class="section-label yellow"><span></span>Get in Touch</div>
            <h2>Questions about a <strong>booking</strong>, or want to list your court?</h2>
            <p>
                Our team replies within one business day. For venue partnerships,
                use the “I run a court” option below.
            </p>

            <div class="contact-details">
                <p><b>EMAIL:</b> hello@pickserve.ph</p>
                <p><b>CONTACT #:</b> +63 ## ### ####</p>
                <p><b>HOURS:</b> Mon–Sat, 8am–8pm PHT</p>
            </div>
        </div>

        <form class="contact-form" action="#" method="post">
            <label for="name">Name</label>
            <input id="name" name="name" type="text" placeholder="John Doe">

            <label for="email">Email</label>
            <input id="email" name="email" type="email" placeholder="hello@example.com">

            <label for="role">I AM A</label>
            <select id="role" name="role">
                <option>PLAYER/COURT OWNER</option>
                <option>PLAYER</option>
                <option>COURT OWNER</option>
            </select>

            <label for="message">Message</label>
            <textarea id="message" name="message" placeholder="Value"></textarea>

            <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
    </section>
</main>

<footer class="site-footer">
    <div class="footer-main">
        <div class="footer-brand">
            <img src="assets/logo.png" alt="PickServe logo">
            <div class="socials">
                <a href="#" aria-label="Facebook">f</a>
                <a href="#" aria-label="Instagram">◎</a>
            </div>
        </div>

        <div class="footer-column">
            <h3>PLAY</h3>
            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#how-it-works">How it works</a>
            <a href="#courts">Courts</a>
            <a href="#contact">Contact</a>
        </div>

        <div class="footer-column">
            <h3>VENUES</h3>
            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#how-it-works">How it works</a>
            <a href="#courts">Courts</a>
            <a href="#contact">Contact</a>
        </div>

        <div class="footer-column">
            <h3>SUPPORT</h3>
            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#how-it-works">How it works</a>
            <a href="#courts">Courts</a>
            <a href="#contact">Contact</a>
        </div>
    </div>

    <div class="footer-bottom">
        <span>2026</span>
        <span>PICKSERVE</span>
        <span>DUMAGUETE</span>
    </div>
</footer>

</body>
</html>
