<?php
session_start();
include('db.php'); // Veritabanı bağlantısı

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kullanıcı adı daha önce alınmış mı kontrol ediliyor
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error_message'] = 'Bu kullanıcı adı zaten alınmış!';
    } else {
        // Düz metin şifreyi veritabanına kaydediyoruz
        $query = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $username, $password); // Şifreyi düz metin olarak kaydediyoruz
        $stmt->execute();

        $_SESSION['success_message'] = 'Kayıt başarılı, giriş yapabilirsiniz!';
        header('Location: giris_yap.php'); // Kayıt sonrası giriş sayfasına yönlendirme
    }
}

?>

<!-- Form -->
<form method="POST" action="">
    <input type="text" name="username" placeholder="Kullanıcı Adı" required>
    <input type="password" name="password" placeholder="Şifre" required>
    <button type="submit">Kayıt Ol</button>
</form>

<?php
if (isset($_SESSION['error_message'])) {
    echo "<p style='color:red;'>".$_SESSION['error_message']."</p>";
    unset($_SESSION['error_message']);
}

if (isset($_SESSION['success_message'])) {
    echo "<p style='color:green;'>".$_SESSION['success_message']."</p>";
    unset($_SESSION['success_message']);
}
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EK - Kayıt Ol</title>
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
            padding: 40px 20px;
            flex: 1;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .register-form {
            background-color: var(--dark-bg);
            padding: 40px;
            border-radius: 12px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
        }
        
        .register-form h2 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--lighter-text);
            font-size: 28px;
            position: relative;
            padding-bottom: 15px;
        }
        
        .register-form h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: var(--primary-color);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--light-text);
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background-color: var(--darker-bg);
            color: var(--lighter-text);
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.3);
        }
        
        .submit-btn {
            width: 100%;
            padding: 14px;
            background-color: var(--primary-color);
            color: var(--lighter-text);
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }
        
        .submit-btn:hover {
            background-color: var(--primary-hover);
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: var(--light-text);
            font-size: 14px;
        }
        
        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .message {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
            font-size: 14px;
        }
        
        .error {
            background-color: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }
        
        .success {
            background-color: rgba(25, 135, 84, 0.2);
            color: #198754;
            border: 1px solid rgba(25, 135, 84, 0.3);
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
        
        /* RESPONSIVE STYLES */
        @media (max-width: 768px) {
            .register-form {
                padding: 30px;
            }
        }
        
        @media (max-width: 576px) {
            .register-form {
                padding: 20px;
            }
            
            .register-form h2 {
                font-size: 24px;
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
                            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                                <li><a href="film_ekle.php">EKLE</a></li>
                            <?php endif; ?>
                            <li><a href="cikis.php">ÇIKIŞ YAP</a></li>
                        <?php else: ?>
                            <li><a href="giris.php">GİRİŞ YAP</a></li>
                            <li><a href="kayit.php">KAYIT OL</a></li>
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
        <div class="register-form">
            <h2>Kayıt Ol</h2>
            
            <?php if (isset($error_message)): ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($success_message)): ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Kullanıcı Adı</label>
                    <input type="text" id="username" name="username" placeholder="Kullanıcı adınızı girin" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Şifre</label>
                    <input type="password" id="password" name="password" placeholder="Şifrenizi girin" required>
                </div>
                
                <button type="submit" class="submit-btn">Kayıt Ol</button>
            </form>
            
            <div class="login-link">
                Zaten bir hesabınız var mı? <a href="giris.php">Giriş yapın</a>
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
</body>
</html>