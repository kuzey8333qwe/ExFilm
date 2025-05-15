<?php 
session_start();
require_once 'db.php';

// Hata görüntülemeyi aç
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Yeni eklenen filmleri veritabanından çekelim
$sql = "SELECT * FROM filmler ORDER BY eklenme_tarihi DESC LIMIT 5";
$result = $conn->query($sql);
if (!$result) {
    die("Sorgu başarısız: " . $conn->error);
}

// Kullanıcı rolünü kontrol et
$rol = 'misafir';
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $sql = "SELECT rol FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result_rol = $stmt->get_result();
        if ($result_rol->num_rows > 0) {
            $user = $result_rol->fetch_assoc();
            $rol = $user['rol'] ?? 'kullanici';
        }
        $stmt->close();
    }
}

// Kategorileri veritabanından çekelim
$kategoriler = [];
$sql_kategori = "SELECT DISTINCT kategori FROM filmler LIMIT 8";
$result_kategori = $conn->query($sql_kategori);
if ($result_kategori && $result_kategori->num_rows > 0) {
    while ($row = $result_kategori->fetch_assoc()) {
        $kategoriler[] = $row['kategori'];
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EK-Ana Sayfa</title>
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
            padding: 0 20px;
            flex: 1;
            width: 100%;
        }
        
        .welcome-message {
            text-align: center;
            margin: 40px 0 30px;
            font-size: 20px;
            color: var(--lighter-text);
            font-weight: 300;
        }
        
        .welcome-message strong {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        section {
            margin: 30px 0;
            width: 100%;
        }
        
        .section-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: var(--lighter-text);
            position: relative;
            display: inline-block;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--primary-color);
        }
        
        .film-list {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            justify-content: center;
            width: 100%;
        }
        
        .film-item {
            background: var(--dark-bg);
            border-radius: 12px;
            overflow: hidden;
            width: 200px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
        }
        
        .film-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(37, 99, 235, 0.2);
            border-color: var(--primary-color);
        }
        
        .film-item img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-bottom: 1px solid var(--border-color);
        }
        
        .film-info {
            padding: 15px;
        }
        
        .film-item h3 {
            font-size: 16px;
            margin-bottom: 8px;
            color: var(--lighter-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .film-item p {
            font-size: 13px;
            color: var(--light-text);
            margin-bottom: 5px;
        }
        
        .categories {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            margin: 30px 0;
            width: 100%;
        }
        
        .category-item {
            background-color: var(--dark-bg);
            color: var(--lighter-text);
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }
        
        .category-item:hover {
            background-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.3);
            border-color: var(--primary-color);
        }
        
        /* FOOTER */
        footer {
            background-color: var(--dark-bg);
            padding: 40px 0;
            margin-top: 50px;
            border-top: 1px solid var(--border-color);
            width: 100%;
        }
        
        .footer-container {
            max-width: var(--max-content-width);
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 30px;
        }
        
        .footer-section {
            flex: 1;
            min-width: 250px;
        }
        
        .footer-section h3 {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-size: 18px;
        }
        
        .footer-section ul {
            list-style: none;
        }
        
        .footer-section ul li {
            margin-bottom: 10px;
        }
        
        .footer-section ul li a {
            color: var(--light-text);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-section ul li a:hover {
            color: var(--primary-color);
        }
        
        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-icons a {
            color: var(--light-text);
            font-size: 20px;
            transition: color 0.3s;
        }
        
        .social-icons a:hover {
            color: var(--primary-color);
        }
        
        .copyright {
            text-align: center;
            padding-top: 20px;
            margin-top: 20px;
            border-top: 1px solid var(--border-color);
            color: var(--light-text);
            font-size: 14px;
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
            .film-item {
                width: 160px;
            }
            
            .film-item img {
                height: 240px;
            }
            
            .footer-content {
                flex-direction: column;
                gap: 20px;
            }
        }
        
        @media (max-width: 576px) {
            .film-item {
                width: 140px;
            }
            
            .film-item img {
                height: 210px;
            }
            
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
                            <?php if ($rol === 'admin'): ?>
                                <li><a href="film_ekle.php">EKLE</a></li>
                            <?php endif; ?>
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
        <div class="welcome-message">
            <p id="welcomeMessage">
                <?php
                if (isset($_SESSION['username'])) {
                    echo "Hoş geldiniz <strong>" . htmlspecialchars($_SESSION['username']) . "</strong>, keyifli seyirler dileriz!";
                } else {
                    echo "EK ya Hoş Geldiniz. Film Dünyasının Kapıları Sizin için Açık!";
                }
                ?>
            </p>
        </div>

        <section>
            <h2 class="section-title">Yeni Eklenen Filmler</h2>
            <div class="film-list">
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<a href='view_movie.php?id=" . $row['id'] . "' class='film-item'>";
                        echo "<img src='" . htmlspecialchars($row['afis']) . "' alt='" . htmlspecialchars($row['baslik']) . "'>";
                        echo "<div class='film-info'>";
                        echo "<h3>" . htmlspecialchars($row['baslik']) . "</h3>";
                        echo "<p>Yönetmen: " . htmlspecialchars($row['yonetmen']) . "</p>";
                        echo "<p>Yıl: " . htmlspecialchars($row['yil']) . "</p>";
                        echo "</div></a>";
                    }
                } else {
                    echo "<p>Henüz eklenen film bulunmamaktadır.</p>";
                }
                ?>
            </div>
        </section>

        

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
    </div>

    <footer>
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Hakkımızda</h3>
                    <p>EK, film tutkunları için en güncel ve kaliteli içerikleri sunar.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Bağlantılar</h3>
                    <ul>
                        <li><a href="index.php">Ana Sayfa</a></li>
                        <li><a href="filmler.php">Filmler</a></li>
                        <li><a href="#">Yakında</a></li>
                        <li><a href="#">İletişim</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Yardım</h3>
                    <ul>
                        <li><a href="#">SSS</a></li>
                        <li><a href="#">Gizlilik Politikası</a></li>
                        <li><a href="#">Kullanım Şartları</a></li>
                        <li><a href="#">Bize Ulaşın</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2023 EK. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>

    <script>
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

<?php
$conn->close();
?>