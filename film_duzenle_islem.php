<?php
include 'db.php'; // Veritabanı bağlantısını ekleyin

// Form gönderildi mi kontrol et
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Bu sayfaya doğrudan erişilemez.");
}

// Formdan gelen verileri güvenli al
$film_id = $_POST['id'] ?? 0;
$baslik = $_POST['baslik'] ?? '';
$yonetmen = $_POST['yonetmen'] ?? '';
$yil = $_POST['yil'] ?? '';
$tur = $_POST['tur'] ?? '';
$aciklama = $_POST['aciklama'] ?? '';

// Gerekli alanların dolu olduğunu kontrol et
if (empty($film_id) || empty($baslik) || empty($yonetmen) || empty($yil) || empty($tur)) {
    die("Lütfen tüm zorunlu alanları doldurun.");
}

// Afiş yüklendi mi kontrol et
$afis_dosya = '';
if (!empty($_FILES['afis']['name']) && $_FILES['afis']['error'] === UPLOAD_ERR_OK) {
    if ($_FILES['afis']['size'] > 5 * 1024 * 1024) { // 5MB sınırı
        die("Afiş dosyası çok büyük, maksimum 5MB olmalı.");
    }
    $afis_dizin = "afisler/";
    $afis_dosya = $afis_dizin . basename($_FILES["afis"]["name"]);
    if (!move_uploaded_file($_FILES["afis"]["tmp_name"], $afis_dosya)) {
        die("Afiş yüklenirken hata oluştu.");
    }
}

// Video yüklendi mi kontrol et
$video_dosya = '';
if (!empty($_FILES['video']['name']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
    if ($_FILES['video']['size'] > 1600 * 1024 * 1024) { // 1.6GB sınırı
        die("Video dosyası çok büyük, maksimum 1.6GB olmalı.");
    }
    $video_dizin = "Uploads/";
    $video_dosya = $video_dizin . basename($_FILES["video"]["name"]);
    if (!move_uploaded_file($_FILES["video"]["tmp_name"], $video_dosya)) {
        die("Video yüklenirken hata oluştu.");
    }
}

// Veritabanı güncelleme işlemi
$baslik = $conn->real_escape_string($baslik);
$yonetmen = $conn->real_escape_string($yonetmen);
$tur = $conn->real_escape_string($tur);
$aciklama = $conn->real_escape_string($aciklama);
$film_id = (int)$film_id;
$yil = (int)$yil;
$afis_dosya = $afis_dosya ? $conn->real_escape_string($afis_dosya) : '';
$video_dosya = $video_dosya ? $conn->real_escape_string($video_dosya) : '';

if ($afis_dosya && $video_dosya) {
    $sql = "UPDATE filmler SET baslik = '$baslik', yonetmen = '$yonetmen', yil = $yil, tur = '$tur', afis = '$afis_dosya', aciklama = '$aciklama', video_path = '$video_dosya' WHERE id = $film_id";
} elseif ($afis_dosya) {
    $sql = "UPDATE filmler SET baslik = '$baslik', yonetmen = '$yonetmen', yil = $yil, tur = '$tur', afis = '$afis_dosya', aciklama = '$aciklama' WHERE id = $film_id";
} elseif ($video_dosya) {
    $sql = "UPDATE filmler SET baslik = '$baslik', yonetmen = '$yonetmen', yil = $yil, tur = '$tur', aciklama = '$aciklama', video_path = '$video_dosya' WHERE id = $film_id";
} else {
    $sql = "UPDATE filmler SET baslik = '$baslik', yonetmen = '$yonetmen', yil = $yil, tur = '$tur', aciklama = '$aciklama' WHERE id = $film_id";
}

if ($conn->query($sql)) {
    header("Location: view_movie.php?id=$film_id");
    exit;
} else {
    echo "Film güncellenirken hata oluştu: " . $conn->error;
}

// Bağlantıyı kapat
$conn->close();
?>