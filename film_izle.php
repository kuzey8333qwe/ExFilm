<?php
// Veritabanı bağlantısı dahil edilir
require_once 'db.php'; // Veritabanı bağlantısı dahil edilir

session_start();


// Film ID'sini al
$film_id = $_GET['film_id'] ?? 0;  // URL'den gelen film ID alınır, yoksa 0 varsayılır



// Film bilgilerini veritabanından çekmek için SQL sorgusu hazırlanır
$stmt = $conn->prepare("SELECT * FROM filmler WHERE id = ?");
$stmt->bind_param("i", $film_id); // ID değeri parametre olarak bağlanır
$stmt->execute();
$result = $stmt->get_result();
$film = $result->fetch_assoc(); // Film bilgileri dizi olarak alınır

// Film bulunamadıysa hata mesajı gösterilir
if (!$film) {
    die("Film bulunamadı.");
}

// Film bilgilerini ekrana yazdıralım
echo "<h1>" . htmlspecialchars($film['baslik']) . "</h1>";
echo "<p>" . htmlspecialchars($film['aciklama']) . "</p>";

// Video izleme alanı (eğer video dosyası varsa)
if (!empty($film['video_path'])) {
    echo "<h3>Video:</h3>";
    echo "<video controls>
            <source src='" . htmlspecialchars($film['video_path']) . "' type='video/mp4'>
            Tarayıcınız video öğesini desteklemiyor.
          </video>";
} else {
    echo "<p>Bu film için video dosyası mevcut değil.</p>";
}

?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($film['baslik']) ?> - İzle</title> <!-- Sayfa başlığına film adı yazılır -->
    <link rel="stylesheet" href="style.css"> <!-- Stil dosyası eklenir -->
</head>
<body>

    <!-- Sayfa içeriği -->
    <div class="container" style="text-align:center; margin-top: 30px;">
        <h1><?= htmlspecialchars($film['baslik']) ?></h1> <!-- Film başlığı ekrana yazdırılır -->

        <?php if (!empty($videoPath) && file_exists($videoPath)): ?> <!-- Video dosyası varsa ve sunucuda mevcutsa -->
            <video width="720" height="480" controls> <!-- HTML5 video oynatıcı -->
                <source src="<?= $videoPath ?>" type="video/mp4"> <!-- Video dosyası buradan alınır -->
                Tarayıcınız bu videoyu desteklemiyor. <!-- Desteklemeyen tarayıcılar için mesaj -->
            </video>
        <?php else: ?>
            <p style="color:red;">Bu filme ait bir video bulunamadı.</p> <!-- Dosya yoksa uyarı verilir -->
        <?php endif; ?>

        <br><br>
        <a class="btn btn-secondary" href="view_movie.php?id=<?= $film['id'] ?>">Film Sayfasına Geri Dön</a> <!-- Geri dönüş butonu -->
    </div>

</body>
</html>
