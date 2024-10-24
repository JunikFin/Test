<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход в профиль</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            color: #333;
        }

        .form-group input {
            width: 95%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .button {
            background-color: #008CBA;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #007BB5;
        }

        .form-note {
            font-size: 12px;
            color: #666;
            margin-top: 10px;
        }
    </style>
    <script src="https://smartcaptcha.yandexcloud.net/captcha.js" defer></script>
</head>
<body>

    <div class="container">
        <h2>Вход в профиль</h2>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="login">Email или телефон:</label>
                <input type="text" name="login" required>
            </div>

            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" name="password" required>
            </div>

            <div class="smart-captcha" data-sitekey="ysc1_fwCuAx0dzFkZvlp5et7blHyuFRn4FySXD6lIOHnzc2e3eb90"></div>

            <button type="submit" class="button">Войти</button>
        </form>
        <p class="form-note">Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
    </div>

</body>
</html>

<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];

    if (isset($_POST['smart-token'])) {
        $captcha_token = $_POST['smart-token']; 
    } else {
        echo "Токен капчи не был получен. Попробуйте снова.";
        exit;
    }

    $secret_key = 'ysc2_fwCuAx0dzFkZvlp5et7bOBqFwHMykYzrLRQViobM8696faa5';  

    $response = file_get_contents("https://smartcaptcha.yandexcloud.net/validate?secret=$secret_key&token=$captcha_token");
    $captcha_result = json_decode($response, true);

    if ($captcha_result['status'] !== 'ok') {
        echo "Проверка капчи не пройдена. Попробуйте ещё раз.";
        exit;
    }

    $conn = new mysqli('localhost', 'root', '', 'job_test_db');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM users WHERE email = ? OR phone = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            header("Location: profile.php");
            exit;
        } else {
            echo "Неверный пароль.";
        }
    } else {
        echo "Пользователь не найден.";
    }

    $stmt->close();
    $conn->close();
}
?>