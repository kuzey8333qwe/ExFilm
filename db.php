<?php
// Veritabanı bağlantısı için gerekli bilgiler
$servername = "localhost"; // Sunucu adı (genellikle localhost)
$username = "root";        // Veritabanı kullanıcı adı
$password = "";            // Şifre (XAMPP'ta genelde boştur)
$dbname = "sinema";        // Kullanılacak veritabanının adı

// Veritabanına bağlantı kurar
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantı sırasında bir hata olursa işlemi durdurur ve hata mesajı gösterir
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}
?>
