<?php 
include 'db.php'; // Veritabanı bağlantısını dahil eder
session_start();

// Kullanıcı rolünü kontrol et
$rol = 'misafir';
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $sql_rol = "SELECT rol FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql_rol);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result_rol = $stmt->get_result();
    if ($result_rol->num_rows > 0) {
        $user = $result_rol->fetch_assoc();
        $rol = $user['rol'] ?? 'kullanici';
    }
    $stmt->close();
}

// Arama kutusundan gelen değeri alır, yoksa boş string kullanır
$query = isset($_GET['query']) ? $_GET['query'] : '';

// SQL sorgusu: "filmler" tablosunda başlık içinde aranan kelime geçenleri seç
$sql = "SELECT * FROM filmler WHERE baslik LIKE '%$query%' ORDER BY eklenme_tarihi DESC";
// Sorguyu çalıştırmak için mysqli kullanılır
$result = $conn->query($sql); // Sorguyu çalıştır ve sonucu al

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film Arama Sonuçları - Beyaz Perde</title>
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
        
        .film-item a {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 16px;
            background-color: var(--primary-color);
            color: var(--lighter-text);
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .film-item a:hover {
            background-color: var(--primary-hover);
        }
        
        .no-results {
            text-align: center;
            color: var(--light-text);
            margin-top: 40px;
            font-size: 18px;
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
        }
        
        @media (max-width: 576px) {
            .film-item {
                width: 140px;
            }
            
            .film-item img {
                height: 210px;
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
                        <input type="text" name="query" placeholder="Film ara..." class="search-input" value="<?php echo htmlspecialchars($query); ?>" required>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <h1 class="section-title">Arama Sonuçları: "<?php echo htmlspecialchars($query); ?>"</h1>
        
        <div class="film-list">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='film-item'>";
                    echo "<img src='" . htmlspecialchars($row['afis']) . "' alt='" . htmlspecialchars($row['baslik']) . "'>";
                    echo "<div class='film-info'>";
                    echo "<h3>" . htmlspecialchars($row['baslik']) . "</h3>";
                    echo "<p>Yönetmen: " . htmlspecialchars($row['yonetmen']) . "</p>";
                    echo "<p>Yıl: " . htmlspecialchars($row['yil']) . "</p>";
                    echo "<a href='view_movie.php?id=" . $row['id'] . "'>Detayları Gör</a>";
                    echo "</div></div>";
                }
            } else {
                echo "<p class='no-results'>Aradığınız kritere uygun film bulunamadı.</p>";
            }
            ?>
        </div>
    </div>

    <script>
        // Eğer modal butonları kullanılacaksa
        var loginBtn = document.getElementById("loginBtn");
        var registerBtn = document.getElementById("registerBtn");
        
        if (loginBtn) {
            loginBtn.onclick = function() {
                window.location.href = "giris_yap.php";
            }
        }
        
        if (registerBtn) {
            registerBtn.onclick = function() {
                window.location.href = "kayit_ol.php";
            }
        }
    </script>
</body>
</html>

<?php
$conn->close(); // Veritabanı bağlantısını kapat
?>