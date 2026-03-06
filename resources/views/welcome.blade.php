<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Informasi Perpustakaan</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        .hero-section {
            position: relative;
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }

        .background-overlay {
            position: absolute;
            inset: 0;
            background-image: url('https://images.pexels.com/photos/7244565/pexels-photo-7244565.jpeg');
            background-size: cover;
            background-position: center;
            filter: blur(8px) brightness(0.7);
            z-index: -2;
            transform: scale(1.1);
        }

        .vignette {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle, transparent 20%, rgba(0,0,0,0.4) 100%);
            z-index: -1;
        }

        .content-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2rem;
            animation: fadeIn 1s ease-out;
        }

        .school-logo {
            width: 250px;
            filter: drop-shadow(0 10px 15px rgba(0,0,0,0.3));
            transition: transform 0.3s ease;
        }

        .school-logo:hover {
            transform: scale(1.05);
        }

        .app-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            margin: 0;
            color: #E2E8F0;
            letter-spacing: 1px;
        }

        .nav-buttons {
            display: flex;
            gap: 4rem;
            margin-top: 1rem;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: white;
            transition: all 0.3s ease;
        }

        .icon-wrapper {
            width: 120px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            transition: all 0.3s ease;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));
        }

        .nav-item:hover .icon-wrapper {
            transform: translateY(-8px);
            filter: drop-shadow(0 8px 12px rgba(0,0,0,0.5));
        }

        .nav-label {
            font-weight: 600;
            font-size: 1.5rem;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 640px) {
            .app-title { font-size: 1.8rem; }
            .school-logo { width: 180px; }
            .nav-buttons { gap: 2rem; }
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="background-overlay"></div>
        <div class="vignette"></div>

        <div class="content-card">
            <!-- School Logo Placeholder -->
            <img src="https://img.icons8.com/color/512/school-building.png" alt="School Logo" class="school-logo">
            
            <h1 class="app-title">Sistem Informasi Perpustakaan</h1>

            <div class="nav-buttons">
                <!-- Redirect to Terminal (Anggota) -->
                <a href="{{ route('terminal.index') }}" class="nav-item">
                    <div class="icon-wrapper">
                        <img src="https://img.icons8.com/bubbles/300/user-male.png" alt="User Icon" class="w-full">
                    </div>
                    <span class="nav-label">Anggota</span>
                </a>

                <!-- Redirect to Admin Login -->
                <a href="{{ route('login') }}" class="nav-item">
                    <div class="icon-wrapper">
                        <img src="https://img.icons8.com/bubbles/300/key.png" alt="Admin Icon" class="w-full">
                    </div>
                    <span class="nav-label">Admin</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
