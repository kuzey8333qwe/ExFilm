<?php
include 'db.php'; // Veritabanı bağlantısı
// Veritabanından toplam film sayısını çekme

session_start(); // Oturum başlatma

// Kullanıcı rolünü kontrol et
$rol = 'misafir';
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $sql_rol = "SELECT rol FROM users WHERE username = ?";
    $stmt_rol = $conn->prepare($sql_rol);
    if ($stmt_rol) {
        $stmt_rol->bind_param("s", $username);
        $stmt_rol->execute();
        $result_rol = $stmt_rol->get_result();
        if ($result_rol->num_rows > 0) {
            $user = $result_rol->fetch_assoc();
            $rol = $user['rol'] ?? 'kullanici';
        }
        $stmt_rol->close();
    }
}

$sql_total_films = "SELECT COUNT(*) AS total FROM filmler";
$result_total_films = $conn->query($sql_total_films);
$total_films = $result_total_films->fetch_assoc()['total'];

// URL'den tür parametresini alıyoruz
$selected_tur = isset($_GET['tur']) ? $_GET['tur'] : '';  // Varsayılan olarak tüm filmler

// Tür filtresine göre sorgu yazıyoruz
if ($selected_tur != '') {
    // SQL Injection'a karşı güvenlik için prepared statement kullanıyoruz
    $sql = "SELECT * FROM filmler WHERE tur = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $selected_tur);  // Tür parametresini bağlıyoruz
} else {
    // Eğer tür seçilmemişse tüm filmleri getiriyoruz
    $sql = "SELECT * FROM filmler ORDER BY eklenme_tarihi DESC"; // Yeni eklenen filmler önce
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();  // Sorguyu çalıştırıp sonuçları alıyoruz

// Hata mesajını kontrol edelim
if ($result === false) {
    echo "Veritabanı sorgusu hatalı: " . $conn->error;
    exit;
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filmler - Beyaz Perde</title>
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
        
        /* FILM GRID STYLES */
        .film-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 25px;
            width: 100%;
        }

        .film-card {
            background: var(--dark-bg);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
        }

        .film-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(37, 99, 235, 0.2);
            border-color: var(--primary-color);
        }

        .film-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-bottom: 1px solid var(--border-color);
        }

        .film-info {
            padding: 15px;
        }

        .film-card h3 {
            font-size: 16px;
            margin-bottom: 8px;
            color: var(--lighter-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .film-card p {
            font-size: 13px;
            color: var(--light-text);
            margin-bottom: 5px;
        }
        
        /* FILTER STYLES */
        .filter-container {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
        }

        .filter-title {
            background-color: var(--primary-color);
            color: var(--lighter-text);
            padding: 10px 15px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s;
        }

        .filter-title:hover {
            background-color: var(--primary-hover);
        }

        .filter-title::after {
            content: "▼";
            font-size: 11px;
            margin-left: 8px;
        }

        .filter-dropdown {
            display: none;
            position: absolute;
            background-color: var(--dark-bg);
            width: 180px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 5px;
            margin-top: 5px;
            padding: 5px 0;
            border: 1px solid var(--border-color);
        }

        .filter-dropdown a {
            display: block;
            color: var(--light-text);
            padding: 8px 12px;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.3s;
        }

        .filter-dropdown a:hover {
            background-color: var(--primary-color);
            color: var(--lighter-text);
        }

        .filter-dropdown.show {
            display: block;
        }
        
        .active {
            background-color: var(--primary-color);
            color: var(--lighter-text) !important;
        }
        
        .film-count {
            background-color: var(--dark-bg);
            color: var(--light-text);
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Arial', sans-serif;
            border: 1px solid var(--border-color);
            margin-bottom: 20px;
            display: inline-block;
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
            .film-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 15px;
            }
            
            .film-card img {
                height: 240px;
            }
        }
        
        @media (max-width: 576px) {
            .film-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            }
            
            .film-card img {
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
        <div class="filter-container">
            <h2 class="filter-title" onclick="toggleDropdown()"><?php echo $selected_tur ? htmlspecialchars($selected_tur) : 'Tüm Filmler'; ?></h2>
            <div class="filter-dropdown" id="filterDropdown">
                <a href="filmler.php" class="filter-btn <?php echo ($selected_tur == '') ? 'active' : ''; ?>">Tüm Filmler</a>
                <a href="filmler.php?tur=Aksiyon" class="filter-btn <?php echo ($selected_tur == 'Aksiyon') ? 'active' : ''; ?>">Aksiyon</a>
                <a href="filmler.php?tur=Bilim Kurgu" class="filter-btn <?php echo ($selected_tur == 'Bilim Kurgu') ? 'active' : ''; ?>">Bilim Kurgu</a>
                <a href="filmler.php?tur=Komedi" class="filter-btn <?php echo ($selected_tur == 'Komedi') ? 'active' : ''; ?>">Komedi</a>
                <a href="filmler.php?tur=Dram" class="filter-btn <?php echo ($selected_tur == 'Dram') ? 'active' : ''; ?>">Dram</a>
                <a href="filmler.php?tur=Gerilim" class="filter-btn <?php echo ($selected_tur == 'Gerilim') ? 'active' : ''; ?>">Gerilim</a>
                <a href="filmler.php?tur=Suç" class="filter-btn <?php echo ($selected_tur == 'Suç') ? 'active' : ''; ?>">Suç</a>
                <a href="filmler.php?tur=Savaş" class="filter-btn <?php echo ($selected_tur == 'Savaş') ? 'active' : ''; ?>">Savaş</a>
                <a href="filmler.php?tur=Romantik" class="filter-btn <?php echo ($selected_tur == 'Romantik') ? 'active' : ''; ?>">Romantik</a>
                <a href="filmler.php?tur=Korku" class="filter-btn <?php echo ($selected_tur == 'Korku') ? 'active' : ''; ?>">Korku</a>
                <a href="filmler.php?tur=Müzikal" class="filter-btn <?php echo ($selected_tur == 'Müzikal') ? 'active' : ''; ?>">Müzikal</a>
                <a href="filmler.php?tur=Animasyon" class="filter-btn <?php echo ($selected_tur == 'Animasyon') ? 'active' : ''; ?>">Animasyon</a>
                <a href="filmler.php?tur=Fantastik" class="filter-btn <?php echo ($selected_tur == 'Fantastik') ? 'active' : ''; ?>">Fantastik</a>
                <a href="filmler.php?tur=Gizem" class="filter-btn <?php echo ($selected_tur == 'Gizem') ? 'active' : ''; ?>">Gizem</a>
                <a href="filmler.php?tur=Aile" class="filter-btn <?php echo ($selected_tur == 'Aile') ? 'active' : ''; ?>">Aile</a>
            </div>
        </div>
        
        <div class="film-count">
            Toplam Film Sayısı: <?php echo $total_films; ?>
            <?php if ($selected_tur): ?>
                - Seçilen Tür: <?php echo htmlspecialchars($selected_tur); ?>
            <?php endif; ?>
        </div>
        
        <div class="film-grid">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<a href='view_movie.php?id=" . $row['id'] . "'>";
                    echo "<div class='film-card'>";
                    echo "<img src='" . htmlspecialchars($row['afis']) . "' alt='" . htmlspecialchars($row['baslik']) . "'>";
                    echo "<div class='film-info'>";
                    echo "<h3>" . htmlspecialchars($row['baslik']) . "</h3>";
                    echo "<p>Yönetmen: " . htmlspecialchars($row['yonetmen']) . "</p>";
                    echo "<p>Yıl: " . htmlspecialchars($row['yil']) . "</p>";
                    echo "</div></div></a>";
                }
            } else {
                echo "<p>Henüz eklenen film bulunmamaktadır.</p>";
            }
            ?>
        </div>
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
        function toggleDropdown() {
            const dropdown = document.getElementById('filterDropdown');
            dropdown.classList.toggle('show');
        }
        
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


<?php
$conn->close(); // Veritabanı bağlantısını kapatma
?>