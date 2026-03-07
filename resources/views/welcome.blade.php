<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BoomPapers | Professional Paper Writing</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Poppins:wght@500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue: #1d7bff;
            --dark: #14141a;
            --muted: #5c5c6a;
            --bg: #f7f8fb;
            --accent: #f7ad26;
            --card: #ffffff;
            --border: #e6e8f0;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Manrope', 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--dark);
            min-height: 100vh;
        }
        a { color: inherit; text-decoration: none; }
        header {
            position: sticky;
            top: 0;
            z-index: 10;
            background: rgba(247,248,251,0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(230,232,240,0.6);
        }
        .nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 22px;
            letter-spacing: -0.2px;
        }
        .brand .mark {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: linear-gradient(135deg, #1d7bff, #25c7ff);
            display: grid;
            place-items: center;
            color: white;
            font-size: 18px;
            font-weight: 800;
            box-shadow: 0 12px 35px rgba(29,123,255,0.25);
        }
        .brand .text strong { color: var(--blue); }
        .menu {
            display: flex;
            align-items: center;
            gap: 26px;
            font-weight: 600;
            color: #202028;
        }
        .menu a {
            position: relative;
            padding: 6px 0;
            font-size: 15px;
        }
        .menu a::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -6px;
            height: 3px;
            width: 0;
            background: var(--blue);
            border-radius: 12px;
            transition: width .18s ease;
        }
        .menu a:hover::after,
        .menu a.active::after { width: 18px; }
        .actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .btn {
            border-radius: 10px;
            padding: 12px 18px;
            font-weight: 700;
            border: 1px solid transparent;
            transition: transform .15s ease, box-shadow .15s ease, border .15s ease;
            font-size: 14px;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn-ghost {
            background: #ffffff;
            border-color: var(--border);
            color: var(--dark);
        }
        .btn-ghost:hover {
            border-color: var(--blue);
            box-shadow: 0 10px 25px rgba(29,123,255,0.12);
        }
        .btn-primary {
            background: linear-gradient(135deg, #1d7bff, #25c7ff);
            color: white;
            box-shadow: 0 12px 30px rgba(29,123,255,0.35);
        }
        main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 56px 24px 80px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 32px;
            align-items: center;
        }
        .hero {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            color: var(--blue);
            background: #eaf3ff;
            padding: 8px 14px;
            border-radius: 999px;
            width: fit-content;
            letter-spacing: 0.2px;
        }
        h1 {
            font-size: clamp(36px, 4vw + 8px, 54px);
            line-height: 1.1;
            margin: 0;
            font-weight: 800;
            letter-spacing: -0.6px;
        }
        h1 .highlight { color: var(--blue); }
        .subtext {
            color: var(--muted);
            font-size: 18px;
            line-height: 1.6;
            max-width: 580px;
        }
        .cta-row {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--dark);
        }
        .btn-outline:hover {
            border-color: var(--blue);
            box-shadow: 0 8px 24px rgba(17, 23, 32, 0.08);
        }
        .ratings {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            max-width: 520px;
        }
        .badge {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 20px 40px rgba(17, 24, 39, 0.05);
        }
        .badge .icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-weight: 800;
            color: #fff;
        }
        .badge .text { font-weight: 700; }
        .hero-visual {
            position: relative;
            min-height: 360px;
            display: grid;
            place-items: center;
        }
        .orbital {
            position: absolute;
            width: 420px;
            height: 420px;
            border-radius: 999px;
            background: radial-gradient(circle at 30% 30%, rgba(29,123,255,0.16), rgba(29,123,255,0) 55%),
                        radial-gradient(circle at 70% 70%, rgba(37,199,255,0.12), rgba(37,199,255,0) 50%);
            border: 1px dashed rgba(29,123,255,0.18);
            filter: drop-shadow(0 25px 40px rgba(0,0,0,0.06));
        }
        .card {
            position: absolute;
            background: #ffffff;
            border: 1px solid rgba(230,232,240,0.9);
            box-shadow: 0 24px 60px rgba(23,44,85,0.12);
            border-radius: 18px;
            padding: 18px;
            width: 220px;
        }
        .card h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #2a2a32;
            letter-spacing: 0.2px;
        }
        .chart {
            height: 96px;
            background: linear-gradient(135deg, rgba(29,123,255,0.12), rgba(37,199,255,0.05));
            border-radius: 12px;
            position: relative;
            overflow: hidden;
        }
        .chart::after {
            content: "";
            position: absolute;
            inset: 16px 18px;
            border-radius: 10px;
            border: 1px dashed rgba(29,123,255,0.35);
        }
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f9fafc;
            border-radius: 999px;
            padding: 8px 12px;
            font-weight: 700;
            font-size: 13px;
            color: var(--muted);
        }
        .card:nth-child(1) { top: 16%; left: 6%; }
        .card:nth-child(2) { bottom: 10%; left: 14%; }
        .card:nth-child(3) { top: 22%; right: 10%; }
        .card:nth-child(4) { bottom: 12%; right: 0; }
        @media (max-width: 900px) {
            header { position: static; }
            .nav { gap: 16px; }
            .menu { display: none; }
            main { grid-template-columns: 1fr; padding: 40px 20px 60px; }
            .hero-visual { min-height: 320px; }
        }
    </style>
</head>
<body>
<header>
    <div class="nav">
        <a class="brand" href="#">
            <span class="mark">✍</span>
            <span class="text">Boom<strong>Papers</strong></span>
        </a>
        <nav class="menu">
            <a href="{{ url('/') }}" class="active">Home</a>
            <a href="#how">How it Works</a>
            <a href="#services">Services</a>
            <a href="{{ route('writers.index') }}">Writers</a>
            <a href="#reviews">Reviews</a>
        </nav>
        <div class="actions">
            <a class="btn btn-ghost" href="{{ route('order', ['tab' => 'existing']) }}">Sign In</a>
            <a class="btn btn-primary" href="{{ route('order', ['tab' => 'new']) }}">Order Now</a>
        </div>
    </div>
    </header>

<main>
    <section class="hero">
        <span class="eyebrow">Trusted by 25k+ students</span>
        <h1>Professional <span class="highlight">Paper Writing</span> Service that guarantees results</h1>
        <p class="subtext">
            Hire a dedicated academic writer with subject expertise, 24/7 communication, and industry-leading turnaround times.
            Every paper is 100% original and tailored to your rubric.
        </p>
        <div class="cta-row">
            <a class="btn btn-primary" href="{{ route('order', ['tab' => 'new']) }}">Order Now</a>
            <a class="btn btn-outline" href="#chat">Live Chat</a>
            <span class="pill">Fast delivery · Free revisions</span>
        </div>
        <div class="ratings">
            <div class="badge">
                <div class="icon" style="background:#00b67a;">4.4★</div>
                <div class="text">Trustpilot</div>
            </div>
            <div class="badge">
                <div class="icon" style="background:#f36;">4.2★</div>
                <div class="text">Sitejabber</div>
            </div>
            <div class="badge">
                <div class="icon" style="background:#000;">4.9★</div>
                <div class="text">Reviews.io</div>
            </div>
        </div>
    </section>

    <section class="hero-visual" aria-hidden="true">
        <div class="orbital"></div>
        <div class="card">
            <h4>Business Plan</h4>
            <div class="chart"></div>
        </div>
        <div class="card">
            <h4>Problem Solving</h4>
            <div class="pill">Data · Finance · Math</div>
        </div>
        <div class="card">
            <h4>Research Paper</h4>
            <div class="chart"></div>
        </div>
        <div class="card">
            <h4>Essay</h4>
            <div class="pill">Creative · Argumentative</div>
        </div>
    </section>
</main>
</body>
</html>
