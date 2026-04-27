<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Rumah Inovasi Indonesia | Coming Soon</title>
    <!-- Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #6366f1; /* Matched to Main App Indigo */
            --bg-dark: #0f172a; /* Matched to Main App Slate 900 */
            --text-white: #f8fafc;
            --glass: rgba(15, 23, 42, 0.85); /* Deep glass like Main App */
            --glass-border: rgba(255, 255, 255, 0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-dark);
            color: var(--text-white);
            height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Virtual Reality Background */
        .bg-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: url('{{ asset('coming_soon_bg.png') }}') no-repeat center center/cover;
            animation: slowZoom 30s infinite alternate ease-in-out;
        }

        @keyframes slowZoom {
            from { transform: scale(1); }
            to { transform: scale(1.15); }
        }

        .bg-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, transparent 0%, var(--bg-dark) 95%);
        }

        /* Premium Glass Container */
        .container {
            position: relative;
            z-index: 1;
            max-width: 900px;
            width: 90%;
            padding: 4rem 3rem;
            background: var(--glass);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid var(--glass-border);
            border-radius: 40px;
            text-align: center;
            box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.6);
            animation: slideUp 1.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .subtitle {
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.5rem;
            color: var(--primary);
            text-transform: uppercase;
            margin-bottom: 2rem;
            display: block;
            opacity: 0.8;
        }

        h1 {
            font-size: clamp(2rem, 6vw, 4rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 2rem;
            background: linear-gradient(to right, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.01em;
        }

        .divider {
            width: 80px;
            height: 3px;
            background: linear-gradient(to right, var(--primary), transparent);
            margin: 0 auto 2.5rem;
            border-radius: 10px;
        }

        .tagline {
            font-size: 1.3rem;
            color: #94a3b8;
            margin-bottom: 4rem;
            font-weight: 300;
            font-style: normal;
            letter-spacing: 0.05em;
        }

        .footer {
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3rem;
            font-weight: 600;
        }

        /* Floating tech accents */
        .accent {
            position: absolute;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, var(--primary) 0%, transparent 70%);
            opacity: 0.15;
            filter: blur(40px);
            z-index: 0;
            pointer-events: none;
        }

        @media (max-width: 640px) {
            .container { padding: 3rem 1.5rem; border-radius: 30px; }
            .subtitle { letter-spacing: 0.3rem; font-size: 0.7rem; }
            .tagline { font-size: 1.1rem; }
        }
    </style>
</head>
<body>
    <div class="bg-container">
        <div class="bg-overlay"></div>
    </div>

    <!-- Background Accents -->
    <div class="accent" style="top: 10%; left: 10%;"></div>
    <div class="accent" style="bottom: 15%; right: 10%;"></div>

    <div class="container">
        <span class="subtitle">Coming Soon</span>
        <h1>Virtual Rumah Inovasi Indonesia</h1>
        <div class="divider"></div>
        <p class="tagline">Terasa dekat dan nyata.</p>
        <div class="footer">by brin</div>
    </div>
</body>
</html>
