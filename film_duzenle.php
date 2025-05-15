<?php
session_start();
require_once 'db.php';

// Admin kontrolü
if (!isset($_SESSION['username'])) {
    header("Location: giris_yap.php");
    exit;
}
$username = $_SESSION['username'];
$username = $conn->real_escape_string($username);
$sql = "SELECT rol FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
if ($user['rol'] !== 'admin') {
    header("Location: filmler.php?error=Bu işlem için yetkiniz yok.");
    exit;
}

// Film ID'sini alalım
$film_id = $_GET['id'] ?? 0;

// Film bilgilerini veritabanından çekmek için SQL sorgusu
$film_id = (int)$film_id; // SQL Injection için basit sanitizasyon
$sql = "SELECT * FROM filmler WHERE id = $film_id";
$result = $conn->query($sql);
$film = $result->fetch_assoc();

// Film bulunamadıysa hata mesajı gösterilir
if (!$film) {
    echo "Film bulunamadı.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Film Düzenle</title>
    <link rel="stylesheet" href="style.css">
    <style>
       body {
    font-family: 'Arial', sans-serif;
    background: linear-gradient(to right, #ece9e6, #ffffff);
    color: #444;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 600px;
    margin: 50px auto;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    padding: 25px;
}

h1 {
    font-size: 2.4rem;
    color: #222;
    text-align: center;
    margin-bottom: 25px;
    font-weight: bold;
}

label {
    font-size: 1rem;
    margin-bottom: 5px;
    font-weight: 600;
    display: block;
    color: #333;
}

input[type="text"], textarea, select, input[type="file"] {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border: 2px solid #ddd;
    border-radius: 6px;
    font-size: 1rem;
    box-sizing: border-box;
    transition: all 0.3s ease;
}

input[type="text"]:focus, select:focus, textarea:focus {
    border-color: #0073e6;
    box-shadow: 0 0 8px rgba(0, 115, 230, 0.25);
    outline: none;
}

textarea {
    resize: vertical;
}

button {
    width: 100%;
    padding: 12px;
    background: #0073e6;
    color: white;
    font-size: 1.2rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.1s ease;
}

button:hover {
    background-color: #005bb5;
    transform: translateY(-3px);
}

.back-link {
    display: block;
    text-align: center;
    margin-top: 15px;
    color: #0073e6;
    font-weight: bold;
    text-decoration: none;
    transition: color 0.3s ease;
}

.back-link:hover {
    color: #004a99;
}

@media (max-width: 768px) {
    .container {
        padding: 20px;
    }
    h1 {
        font-size: 2rem;
    }
    button {
        font-size: 1rem;
    }
}
</style>
</head>
<body>
    <div class="container">
        <h1>Film Düzenle</h1>
        <form action="film_duzenle_islem.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $film['id']; ?>">

            <label for="baslik">Film Başlığı:</label>
            <input type="text" id="baslik" name="baslik" value="<?php echo $film['baslik']; ?>" required>

            <label for="yonetmen">Yönetmen:</label>
            <input type="text" id="yonetmen" name="yonetmen" value="<?php echo $film['yonetmen']; ?>" required>

            <label for="yil">Yıl:</label>
            <input type="text" id="yil" name="yil" value="<?php echo $film['yil']; ?>" required>

            <label for="tur">Film Türü:</label>
            <input type="text" id="tur" name="tur" value="<?php echo $film['tur']; ?>" required>

            <label for="afis">Afiş Yükle (Opsiyonel):</label>
            <input type="file" name="afis">
        
            <label for="video">Film Dosyası (Opsiyonel):</label>
            <input type="file" name="video" accept="video/mp4">

            <label for="aciklama">Açıklama:</label>
            <textarea id="aciklama" name="aciklama" rows="4" required><?php echo $film['aciklama']; ?></textarea>

            <button type="submit">Güncelle</button>
        </form>
        <a class="back-link" href="index.php">Geri Dön</a>
    </div>
</body>
</html>