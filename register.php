<?php
$name = $email = $phone = ''; 
$error = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    if ($password != $confirm_password) {
        $error = "Пароли не совпадают.";

    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $conn = new mysqli('localhost', 'root', '', 'job_test_db');
        if ($conn->connect_error) {
            die("Ошибка подключения: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM users WHERE email='$email' OR phone='$phone'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $error = "Пользователь с таким телефоном или почтой уже существует.";
        } else {
            
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);
            if ($stmt->execute()) {
             header("Location: profile.php");
                exit;
            } else {
                $error = "Ошибка при регистрации: " . $stmt->error;
            }
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
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

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #45a049;
        }

        .form-note {
            font-size: 12px;
            color: #666;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Регистрация</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="name">Имя:</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Телефон:</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Повторите пароль:</label>
                <input type="password" name="confirm_password" required>
            </div>

            <button type="submit" class="button">Зарегистрироваться</button>
        </form>
        
        <p class="form-note">Уже есть аккаунт? <a href="login.php">Войти</a></p>
    </div>

</body>
</html>