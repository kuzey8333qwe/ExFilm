<?php
include 'db.php';

session_start();

$errorMessage = ""; // Hata mesajı için değişken

// Form verileri POST ile gönderildi mi kontrol et
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];

    // Kullanıcı adı ve şifreyi kontrol et
    $inputUsername = $conn->real_escape_string($inputUsername); // Basit SQL kaçış
    $sql = "SELECT * FROM users WHERE username = '$inputUsername'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Düz metin şifre kontrolü
        if ($inputPassword == $user['password']) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['rol'] = $user['rol']; // Rol bilgisini de session'a ekle
            header("Location: index.php"); // Ana sayfaya yönlendir
            exit();
        } else {
            $errorMessage = "Yanlış şifre."; // Hatalı şifre mesajı
        }
    } else {
        $errorMessage = "Kullanıcı bulunamadı."; // Hatalı kullanıcı adı mesajı
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - Beyaz Perde</title>
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
            background-image: url('arkaplan.jpg'); /* Arkaplan resmi ekleyebilirsiniz */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        
        /* LOGIN CONTAINER */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-box {
            background-color: rgba(15, 23, 42, 0.9); /* Saydam koyu arkaplan */
            border-radius: 12px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
            backdrop-filter: blur(5px); /* Arkaplanı bulanıklaştırır */
        }
        
        .login-title {
            font-size: 28px;
            text-align: center;
            margin-bottom: 30px;
            color: var(--lighter-text);
            font-family: 'Sharp Grotesk SmBold 20 Regular', sans-serif;
        }
        
        .login-form .form-group {
            margin-bottom: 20px;
        }
        
        .login-form label {
            display: block;
            font-size: 14px;
            color: var(--light-text);
            margin-bottom: 8px;
        }
        
        .login-form input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            background-color: var(--darker-bg);
            color: var(--lighter-text);
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }
        
        .login-form input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.3);
        }
        
        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: var(--lighter-text);
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }
        
        .login-btn:hover {
            background-color: var(--primary-hover);
        }
        
        .error-message {
            color: #ff6b6b;
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--light-text);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .back-link:hover {
            color: var(--primary-color);
        }
        
        /* RESPONSIVE STYLES */
        @media (max-width: 768px) {
            .login-box {
                padding: 30px;
            }
            
            .login-title {
                font-size: 24px;
            }
        }
        
        @media (max-width: 480px) {
            .login-box {
                padding: 25px;
            }
        }
    </style>

</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1 class="login-title">Giriş Yap</h1>
            
            <form method="POST" action="giris_yap.php" class="login-form">
                <div class="form-group">
                    <label for="username">Kullanıcı Adı</label>
                    <input type="text" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Şifre</label>
                    <input type="password" name="password" required>
                </div>
                
                <button type="submit" class="login-btn">Giriş Yap</button>
                
                <?php if ($errorMessage): ?>
                    <p class="error-message"><?php echo $errorMessage; ?></p>
                <?php endif; ?>
                
                <a href="index.php" class="back-link">Ana Sayfaya Dön</a>
            </form>
        </div>
    </div>
</body>
</html>
