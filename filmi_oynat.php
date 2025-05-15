<?php
include 'db.php';

$film_id = $_GET['id'] ?? 0;

if ($film_id > 0) {
    $sql = "SELECT * FROM filmler WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $film_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $film = $result->fetch_assoc();

    if (!$film) {
        $film_icerik = "<p style='color:red;'>Film bulunamadı.</p>";
    } else {
        $film_icerik = "
            <div class='film-detaylari'>
                <h2>{$film['baslik']}</h2>
                <img src='{$film['afis']}' alt='{$film['baslik']}' style='max-width: 200px; height: auto;'>
                <p><strong>Yönetmen:</strong> {$film['yonetmen']}</p>
                <p><strong>Yıl:</strong> {$film['yil']}</p>
                <p><strong>Tür:</strong> {$film['tur']}</p>
                <p><strong>Açıklama:</strong> {$film['aciklama']}</p>
            </div>
            <div id='video-container'>
                <iframe width='560' height='315' src='https://www.youtube.com/embed/your_video_id' frameborder='0' allowfullscreen></iframe>
            </div>
        ";
    }
} else {
    $film_icerik = "<p style='color:red;'>Geçersiz Film ID.</p>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filmi Oynat</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .film-detaylari {
            color: #fff;
            margin-bottom: 20px;
        }
        #video-container {
            width: 80%;
            margin: 0 auto;
            background-color: #000;
        }
        iframe {
            width: 100%;
            height: 450px;
        }
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #556678;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .back-button:hover {
            background-color: #445567;
        }
    </style>
</head>
<body>
    <div class="container">
        <div id="film-icerik">
            <?php echo $film_icerik; ?>
        </div>
        <a href="filmler.php" class="back-button">Filmlere Geri Dön</a>
    </div>
</body>
</html>