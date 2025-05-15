<?php
// Veritabanı bağlantısını dahil et
require_once 'db.php'; // db.php MySQLi bağlantısı sağlıyor ($conn)
session_start();

// Formdan gelen verileri al
$baslik = $_POST['baslik'] ?? '';
$yonetmen = $_POST['yonetmen'] ?? '';
$yil = $_POST['yil'] ?? '';
$tur = $_POST['tur'] ?? '';
$aciklama = $_POST['aciklama'] ?? '';
$imdb = $_POST['imdb'] ?? 'N/A';

// Video dosyasını işle
$video_path = '';
if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
    $video_name = $_FILES['video']['name'];
    $video_path = 'Uploads/' . $video_name;
    if (!move_uploaded_file($_FILES['video']['tmp_name'], $video_path)) {
        die("Video yüklenirken hata oluştu.");
    }
}

// Afiş dosyasını işle
$afis = '';
if (isset($_FILES['afis']) && $_FILES['afis']['error'] === UPLOAD_ERR_OK) {
    $afis_name = $_FILES['afis']['name'];
    $afis = 'afisler/' . $afis_name;
    if (!move_uploaded_file($_FILES['afis']['tmp_name'], $afis)) {
        die("Afiş yüklenirken hata oluştu.");
    }
}

// Veritabanına ekleme işlemi
$baslik = $conn->real_escape_string($baslik);
$yonetmen = $conn->real_escape_string($yonetmen);
$tur = $conn->real_escape_string($tur);
$afis = $conn->real_escape_string($afis);
$aciklama = $conn->real_escape_string($aciklama);
$imdb = $conn->real_escape_string($imdb);
$video_path = $conn->real_escape_string($video_path);
$sql = "INSERT INTO filmler (baslik, yonetmen, yil, tur, afis, aciklama, imdb, video_path) 
        VALUES ('$baslik', '$yonetmen', $yil, '$tur', '$afis', '$aciklama', '$imdb', '$video_path')";

if ($conn->query($sql)) {
    header("Location: filmler.php?success=Film başarıyla eklendi.");
    exit;
} else {
    die("Film eklenirken hata oluştu.");
}

// Bağlantıyı kapat
$conn->close();
?>