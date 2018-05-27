<?php

require('functions.php');
require('db-config.php');
require('mysql_helper.php');

$db = new mysqli(DB['server'], DB['username'], DB['password'], DB['db']);
$getUser = 'SELECT * FROM `users` WHERE `email` = ?';

session_start();

if (isset($_SESSION['user'])) {
    header("Location: /index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'  ) {
    $logInForm = $_POST;
    $errors = [];

    if (empty($logInForm['password'])) {
        $errors['password'] = 'Это поле надо заполнить';
    }

    if (!filter_var($logInForm['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = empty($logInForm['email']) ? 'Это поле надо заполнить' : 'Введите корректный E-mail';
    }

    if (!count($errors)) {
        $user = getData($db, $getUser, [$logInForm['email']]);
        if ($user) {
            if (password_verify($logInForm['password'], $user[0]['password'])) {
                $_SESSION['user'] = $user[0];
                header("Location: /index.php");
                exit();
            } else {
                $errors['password'] = 'Неверный пароль';
            }
        } else {
            $errors['email'] = 'Пользователь с таким именем не найден';
        }
    }
}

$pageHeader = renderTemplate('templates/header.php', ['userName' => NULL]);

$modalAuthorization = renderTemplate('templates/modal-authorization.php', [
    'user' => $user ?? [],
    'errors' => $errors ?? [],
    'logInForm' => $logInForm ?? []
]);

$layoutContent = renderTemplate('templates/guest.php', [
    'titlePage' => 'Дела в порядке | Регистрация',
    'pageHeader' => $pageHeader,
    'modalAuthorization' => $modalAuthorization,
    'errors' => $errors ?? []
]);

print($layoutContent);
