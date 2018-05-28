<?php
require('functions.php');
require('mysql_helper.php');

session_start();

$db = connect();

$getUserName = 'SELECT COUNT(*) FROM `users` WHERE `name` = ?';
$getUserEmail = 'SELECT COUNT(*) FROM `users` WHERE `email` = ?';
$setNewUser = 'INSERT INTO `users` (`email`,`password`,`name`,`contacts`,`registration`) VALUES (?, ?, ?, NULL, NOW())';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registration-form'])) {
    $newUser = array_map('strip_tags', $_POST);
    $errorsRegistration = [];
    unset($newUser['registration-form']);

    if (!isset($newUser['email'])) {
        $errorsRegistration['email'] = 'Это поле надо заполнить';
    } elseif (!filter_var($newUser['email'], FILTER_VALIDATE_EMAIL)){
        $errorsRegistration['email'] = 'Введите корректный E-mail';
    }

    if (empty($newUser['password'])) {
        $errorsRegistration['password'] = 'Это поле надо заполнить';
    }

    if (empty($newUser['name'])) {
        $errorsRegistration['name'] = 'Это поле надо заполнить';
    }

    if (isset($newUser['email']) && isEntriesExist($db, $getUserEmail, [$newUser['email']])) {
        $errorsRegistration['email'] = 'Пользователь с таким email уже существует';
    }

    if (!count($errorsRegistration)) {
        $newUser['password'] = password_hash($newUser['password'], PASSWORD_DEFAULT);
        if (executeQuery($db, $setNewUser, $newUser)->error) {
            $errorsRegistration['connect'] = 'Что-то пошло не так, попробуйте еще раз';
        } else {
            header("Location: /index.php");
            exit();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $logInForm = array_map('strip_tags', $_POST);
    unset($logInForm['login']);

    $errorsLogin = login($logInForm, $db);
}

$modalAuthorization = renderTemplate('templates/modal-authorization.php', [
    'user' => $user ?? [],
    'errors' => $errorsLogin ?? [],
    'logInForm' => $logInForm ?? []
]);

$layoutContent = renderTemplate('templates/register.php', [
    'titlePage' => 'Дела в порядке | Регистрация',
    'modalAuthorization' => $modalAuthorization,
    'newUser' => $newUser ?? [],
    'errors' => $errorsRegistration ?? [],
    'errorsLogin' => $errorsLogin ?? []
]);

print($layoutContent);
