<?php
require('functions.php');
require('mysql_helper.php');

session_start();

$db = connect();

$getUserName = 'SELECT COUNT(*) FROM `users` WHERE `name` = ?';
$getUserEmail = 'SELECT COUNT(*) FROM `users` WHERE `email` = ?';
$setNewUser = 'INSERT INTO `users` (`email`,`password`,`name`,`contacts`,`registration`) VALUES (?, ?, ?, NULL, NOW())';
$getUser = 'SELECT * FROM `users` WHERE `email` = ?';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registration-form'])) {
    $newUser = array_map('strip_tags', $_POST);
    $errorsRegistration = [];
    unset($newUser['registration-form']);

    if (!filter_var($newUser['email'], FILTER_VALIDATE_EMAIL)) {
        $errorsRegistration['email'] = empty($newUser['email']) ? 'Это поле надо заполнить' : 'Введите корректный E-mail';
    }

    if (empty($newUser['password'])) {
        $errorsRegistration['password'] = 'Это поле надо заполнить';
    }

    if (empty($newUser['name'])) {
        $errorsRegistration['name'] = 'Это поле надо заполнить';
    }

    if ($newUser['email'] && isEntriesExist($db, $getUserEmail, [$newUser['email']])) {
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
    $errorsLogin = [];
    unset($logInForm['login']);

    if (empty($logInForm['password'])) {
        $errorsLogin['password'] = 'Это поле надо заполнить';
    }

    if (!filter_var($logInForm['email'], FILTER_VALIDATE_EMAIL)) {
        $errorsLogin['email'] = empty($logInForm['email']) ? 'Это поле надо заполнить' : 'Введите корректный E-mail';
    }

    if (!count($errorsLogin)) {
        $user = getData($db, $getUser, [$logInForm['email']]);
        if ($user) {
            if (password_verify($logInForm['password'], $user[0]['password'])) {
                $_SESSION['user'] = $user[0];
                header("Location: /index.php");
                exit();
            } else {
                $errorsLogin['password'] = 'Неверный пароль';
            }
        } else {
            $errorsLogin['email'] = 'Пользователь с таким именем не найден';
        }
    }
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
