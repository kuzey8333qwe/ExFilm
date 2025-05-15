<?php
session_start(); // Oturum işlemlerini başlatır (varsa devam eder)

// Kullanıcının oturumuna ait tüm verileri siler (örneğin: kullanıcı adı, id vs.)
session_unset(); 

// Oturumu tamamen sona erdirir (kullanıcı artık sistemde oturum açmış sayılmaz)
session_destroy(); 

// Kullanıcıyı ana sayfaya (index.php) yönlendirir
header("Location: index.php");

// PHP kodunu burada sonlandırır (bundan sonrası çalışmaz)
exit();
?>
