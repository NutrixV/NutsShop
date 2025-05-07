<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NutsShop</title>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            background: #f7fafc;
            color: #4a5568;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
        }
        .container {
            max-width: 800px;
            text-align: center;
            padding: 2rem;
        }
        h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #2d3748;
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        .logo {
            margin-bottom: 2rem;
            font-size: 4rem;
        }
        .links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }
        .links a {
            text-decoration: none;
            color: #4299e1;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            transition: all 0.3s;
        }
        .links a:hover {
            background: #ebf8ff;
            color: #2b6cb0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">🌰</div>
        <h1>Welcome to NutsShop</h1>
        <p>Your premium source for high-quality nuts and dried fruits.</p>
        
        <div class="links">
            <a href="/shop">Shop</a>
            <a href="/about">About Us</a>
            <a href="/contact">Contact</a>
            <a href="/admin">Admin Panel</a>
        </div>
    </div>
</body>
</html> 