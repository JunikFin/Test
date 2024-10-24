<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная страница</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            text-align: center;
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin-bottom: 20px;
            font-size: 32px;
            color: #333;
        }

        p {
            margin-bottom: 40px;
            font-size: 18px;
            color: #666;
        }

        .buttons {
            display: flex;
            justify-content: space-around;
        }

        .button {
            background-color: #4CAF50; 
            border: none;
            color: white;
            padding: 15px 30px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #45a049;
        }

        .button-secondary {
            background-color: #008CBA; 
        }

        .button-secondary:hover {
            background-color: #007BB5;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Добро пожаловать на наш сайт!</h1>
        <p>Пожалуйста, зарегистрируйтесь или войдите в профиль, чтобы продолжить.</p>
        <div class="buttons">
            <a href="register.php" class="button">Регистрация</a>
            <a href="login.php" class="button button-secondary">Войти</a>
        </div>
    </div>

</body>
</html>