<?php
require_once 'db.php';
session_start();

// Giriş ve admin kontrolü
if (!isset($_SESSION['username'])) {
    header("Location: giris_yap.php");
    exit;
}
$username = $_SESSION['username'];
$sql = "SELECT rol FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if ($user['rol'] !== 'admin') {
    header("Location: filmler.php?error=Bu işlem için yetkiniz yok.");
    exit;
}
$stmt->close();

// Film silme işlemi
$film_id = $_GET['id'] ?? 0;
if ($film_id <= 0) {
    header("Location: filmler.php?error=Geçersiz film ID.");
    exit;
}

$sql = "DELETE FROM filmler WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $film_id);
if ($stmt->execute()) {
    header("Location: filmler.php?success=Film silindi.");
} else {
    header("Location: filmler.php?error=Film silinemedi.");
}
$stmt->close();
$conn->close();
exit;
?>