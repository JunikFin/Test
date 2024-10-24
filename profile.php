<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'job_test_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$message = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE (email = ? OR phone = ?) AND id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $email, $phone, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "Email или телефон уже используются другим пользователем.";
    } else {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql = "UPDATE users SET name = ?, email = ?, phone = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $name, $email, $phone, $hashed_password, $user_id);
        } else {
            $sql = "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $name, $email, $phone, $user_id);
        }

        if ($stmt->execute()) {
            $message = "Данные успешно обновлены!";
            $_SESSION['name'] = $name;
        } else {
            $message = "Ошибка при обновлении данных: " . $stmt->error;
        }
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль пользователя</title>
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
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            margin-top: 15px;
        }

        .button:hover {
            background-color: #45a049;
        }

        .button-logout {
            background-color: #f44336;
            margin-top: 10px;
        }

        .button-logout:hover {
            background-color: #e53935;
        }

        .message {
            margin-bottom: 20px;
            font-size: 14px;
            color: #333;
            background-color: #e7f3fe;
            padding: 10px;
            border: 1px solid #b3d4fc;
            border-radius: 5px;
        }

        .error {
            background-color: #fce4e4;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Ваш профиль</h2>

        <?php if ($message != ''): ?>
            <div class="message <?php echo strpos($message, 'Ошибка') !== false ? 'error' : ''; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="profile.php" method="POST">
            <div class="form-group">
                <label for="name">Имя:</label>
                <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Телефон:</label>
                <input type="text" id="phone" name="phone" value="<?php echo $user['phone']; ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Новый пароль (оставьте пустым, если не хотите менять):</label>
                <input type="password" id="password" name="password">
            </div>

            <button type="submit" class="button">Обновить данные</button>
        </form>
        <form action="logout.php" method="POST">
            <button type="submit" class="button button-logout">Выйти</button>
        </form>
    </div>

</body>
</html>
