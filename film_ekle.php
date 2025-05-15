<?php
session_start();
require_once 'db.php';

// Admin kontrolü
if (!isset($_SESSION['username'])) {
    header("Location: giris_yap.php");
    exit;
}

$username = $_SESSION['username'];
$username = $conn->real_escape_string($username); // Basit SQL kaçış
$sql = "SELECT rol FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

if ($user['rol'] !== 'admin') {
    header("Location: filmler.php?error=Bu işlem için yetkiniz yok.");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film Ekle - Beyaz Perde</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://db.onlinewebfonts.com/c/629ed6829f706958b9bdf4f6300dfca0?family=Sharp+Grotesk+SmBold+20+Regular" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --dark-bg: #0f172a;
            --darker-bg: #020617;
            --light-text: #e2e8f0;
            --lighter-text: #f8fafc;
            --border-color: #1e293b;
            --pink-accent: rgb(255, 0, 119);
            --max-content-width: 1200px;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--darker-bg);
            color: var(--light-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.6;
        }
        
        /* HEADER STYLES */
        .header-wrapper {
            width: 100%;
            background-color: var(--dark-bg);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-container {
            max-width: var(--max-content-width);
            margin: 0 auto;
            padding: 15px 20px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            width: 100%;
            gap: 20px;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }
        
        .logo {
            font-family: 'Sharp Grotesk SmBold 20 Regular', sans-serif;
            font-size: 45px;
            color: var(--lighter-text);
            text-decoration: none;
            transition: color 0.3s;
            line-height: 1;
        }
        
        .logo span {
            color: var(--pink-accent);
        }
        
        .logo:hover {
            color: var(--primary-color);
        }
        
        nav {
            flex: 1;
            min-width: 0;
        }
        
        nav ul {
            display: flex;
            list-style: none;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        nav ul li a {
            color: var(--light-text);
            text-decoration: none;
            font-weight: 600;
            padding: 8px 15px;
            border-radius: 20px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            white-space: nowrap;
        }
        
        nav ul li a:hover {
            color: var(--lighter-text);
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .search-container {
            position: relative;
            flex-shrink: 0;
            min-width: 220px;
        }
        
        .search-input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border-radius: 25px;
            border: 1px solid var(--border-color);
            background-color: var(--dark-bg);
            color: var(--lighter-text);
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.3);
        }
        
        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--light-text);
        }
        
        /* MAIN CONTENT */
        .container {
            max-width: var(--max-content-width);
            margin: 0 auto;
            padding: 20px;
            flex: 1;
            width: 100%;
        }
        
        .form-section {
            background-color: var(--dark-bg);
            padding: 30px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            margin-top: 30px;
        }
        
        .form-title {
            font-size: 24px;
            margin-bottom: 25px;
            color: var(--lighter-text);
            position: relative;
            padding-bottom: 10px;
        }
        
        .form-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--primary-color);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--light-text);
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            background-color: var(--darker-bg);
            color: var(--lighter-text);
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.3);
        }
        
        .form-group input[type="file"] {
            padding: 8px;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .submit-btn {
            background-color: var(--primary-color);
            color: var(--lighter-text);
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        
        .submit-btn:hover {
            background-color: var(--primary-hover);
        }
        
        /* MODAL STYLES */
        .modal {
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            display: none;
            background-color: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            position: relative;
            background-color: var(--dark-bg);
            color: var(--lighter-text);
            margin: 10% auto;
            padding: 30px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            width: 400px;
            max-width: 90%;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.3);
            text-align: center;
            animation: fadeIn 0.3s ease-in-out;
        }
        
        .modal-content h3 {
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
            color: var(--lighter-text);
        }
        
        .modal-content label {
            font-size: 14px;
            color: var(--light-text);
            display: block;
            margin-bottom: 8px;
            text-align: left;
        }
        
        .modal-content input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            background-color: var(--dark-bg);
            color: var(--lighter-text);
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }
        
        .modal-content input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.3);
        }
        
        .modal-content button {
            background-color: var(--primary-color);
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s ease;
            color: var(--lighter-text);
            font-weight: 600;
        }
        
        .modal-content button:hover {
            background-color: var(--primary-hover);
        }
        
        .close {
            position: absolute;
            top: 15px;
            right: 15px;
            color: var(--light-text);
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        .close:hover {
            color: var(--primary-color);
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        /* RESPONSIVE STYLES */
        @media (max-width: 992px) {
            .header-content {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
            }
            
            .logo-container {
                justify-content: center;
                margin-bottom: 10px;
            }
            
            nav ul {
                justify-content: center;
            }
            
            .search-container {
                width: 100%;
                min-width: auto;
            }
        }
        
        @media (max-width: 768px) {
            .form-section {
                padding: 20px;
            }
        }
        
        @media (max-width: 576px) {
            .modal-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="header-wrapper">
        <div class="header-container">
            <div class="header-content">
                <div class="logo-container">
                    <a href="index.php" class="logo">E<span>K</span></a>
                </div>
                <nav>
                    <ul>
                        <?php if (isset($_SESSION['username'])): ?>
                            <li><a href="filmler.php">FİLMLER</a></li>
                            <li><a href="film_ekle.php">EKLE</a></li>
                            <li><a href="cikis.php">ÇIKIŞ YAP</a></li>
                        <?php else: ?>
                            <li><a href="#" id="loginBtn">GİRİŞ YAP</a></li>
                            <li><a href="#" id="registerBtn">KAYIT OL</a></li>
                            <li><a href="filmler.php">FİLMLER</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <form method="GET" action="film_ara.php">
                        <input type="text" name="query" placeholder="Film ara..." class="search-input" required>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <section class="form-section">
            <h2 class="form-title">Yeni Film Ekle</h2>
            
            <form action="film_ekle_islem.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="baslik">Film Başlığı:</label>
                    <input type="text" name="baslik" required>
                </div>

                <div class="form-group">
                    <label for="yonetmen">Yönetmen:</label>
                    <input type="text" name="yonetmen" required>
                </div>

                <div class="form-group">
                    <label for="yil">Yıl:</label>
                    <input type="number" name="yil" required>
                </div>

                <div class="form-group">
                    <label for="tur">Tür:</label>
                    <input type="text" name="tur" required>
                </div>

                <div class="form-group">
                    <label for="imdb">IMDb Puanı:</label>
                    <input type="text" name="imdb" required>
                </div>

                <div class="form-group">
                    <label for="afis">Afiş Yükle:</label>
                    <input type="file" name="afis" accept="image/*" required>
                </div>

                <div class="form-group">
                    <label for="video">Video Dosyası:</label>
                    <input type="file" name="video" accept="video/mp4" required>
                </div>

                <div class="form-group">
                    <label for="aciklama">Açıklama:</label>
                    <textarea name="aciklama" required></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="submit-btn">Filmi Ekle</button>
                </div>
            </form>
        </section>
    </div>
    
    <!-- Giriş Modalı -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close">×</span>
            <h3>Giriş Yap</h3>
            <form method="POST" action="giris_yap.php">
                <label for="username">Kullanıcı Adı:</label>
                <input type="text" name="username" required>
                <label for="password">Şifre:</label>
                <input type="password" name="password" required>
                <button type="submit">Giriş Yap</button>
            </form>
        </div>
    </div>

    <!-- Kayıt Ol Modalı -->
    <div id="registerModal" class="modal">
        <div class="modal-content">
            <span class="close">×</span>
            <h3>Kayıt Ol</h3>
            <form method="POST" action="kayit_ol.php">
                <label for="new_username">Kullanıcı Adı:</label>
                <input type="text" name="username" required>
                <label for="new_password">Şifre:</label>
                <input type="password" name="password" required>
                <button type="submit">Kayıt Ol</button>
            </form>
        </div>
    </div>

    <script>
        // Modal açma ve kapama işlemleri
        var loginModal = document.getElementById("loginModal");
        var registerModal = document.getElementById("registerModal");
        var loginBtn = document.getElementById("loginBtn");
        var registerBtn = document.getElementById("registerBtn");
        var closeBtns = document.getElementsByClassName("close");

        if (loginBtn) {
            loginBtn.onclick = function() {
                loginModal.style.display = "flex";
                registerModal.style.display = "none";
            }
        }

        if (registerBtn) {
            registerBtn.onclick = function() {
                registerModal.style.display = "flex";
                loginModal.style.display = "none";
            }
        }

        for (var i = 0; i < closeBtns.length; i++) {
            closeBtns[i].onclick = function() {
                loginModal.style.display = "none";
                registerModal.style.display = "none";
            }
        }

        window.onclick = function(event) {
            if (event.target == loginModal) {
                loginModal.style.display = "none";
            }
            if (event.target == registerModal) {
                registerModal.style.display = "none";
            }
        }
    </script>
</body>
</html>