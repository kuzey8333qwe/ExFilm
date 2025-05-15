<?php
session_start();

include 'db.php'; // Veritabanı bağlantısı

$tur = isset($_POST['tur']) ? $_POST['tur'] : ''; // Gelen tür parametresi

// Eğer tür parametresi varsa, o türe göre filtrele
if ($tur !== '') {
    $sql = "SELECT * FROM filmler WHERE tur LIKE ? ORDER BY yil ASC"; // türü filtrele
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $tur); // türü parametre olarak bağla
} else {
    // Eğer tür parametresi yoksa, tüm filmleri getir
    $sql = "SELECT * FROM filmler ORDER BY yil ASC";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

// Filmleri döndürme
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<a href='view_movie.php?id=" . $row['id'] . "'>"; // Tüm film kartını link yapıyoruz
        echo "<div class='film-card'>";
        echo "<img src='" . $row['afis'] . "' alt='" . $row['baslik'] . "'>";
        echo "<h3>" . $row['baslik'] . "</h3>";
        echo "<p>Yönetmen: " . $row['yonetmen'] . "</p>";
        echo "<p>Yıl: " . $row['yil'] . "</p>";
        echo "</div>";
        echo "</a>"; // Linki burada kapatıyoruz
    }
} else {
    echo "<p>Henüz eklenen film bulunmamaktadır.</p>";
}

$conn->close(); // Veritabanı bağlantısını kapat
?>
