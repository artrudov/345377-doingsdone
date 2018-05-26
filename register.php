<?php
require('functions.php');
require('db-config.php');
require('mysql_helper.php');

$db = new mysqli(DB['server'], DB['username'], DB['password'], DB['db']);
$getUserName = 'SELECT COUNT(*) FROM `users` WHERE `name` = ?';
$getUserEmail = 'SELECT COUNT(*) FROM `users` WHERE `email` = ?';
$setNewUser = 'INSERT INTO `users` (`email`,`password`,`name`,`contacts`,`registration`) VALUES (?, ?, ?, NULL, NOW())';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUser = $_POST;
    $errors = [];

    if (!filter_var($newUser['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = empty($newUser['email']) ? 'Это поле надо заполнить' : 'Введите корректный E-mail';
    }

    if (empty($newUser['password'])) {
        $errors['password'] = 'Это поле надо заполнить';
    }

    if (empty($newUser['name'])) {
        $errors['name'] = 'Это поле надо заполнить';
    }

    if ($newUser['email'] && getEntries($db, $getUserEmail, [$newUser['email']])) {
        $errors['email'] = 'Пользователь с таким email уже существует';
    }

    if ($newUser['name'] && getEntries($db, $getUserName, [$newUser['name']])) {
        $errors['name'] = 'Пользователь с таким именем уже существует';
    }

    $newUser['password'] = password_hash($newUser['password'], PASSWORD_DEFAULT);

    if (!count($errors)) {
        if (!addNewEntry($db, $setNewUser, $newUser)) {
            $errors['connect'] = 'Что-то пошло не так, попробуйте еще раз';
        } else {
            header("Location: /index.php");
            exit();
        }
    }
}

$layoutContent = renderTemplate('templates/register.php', [
    'titlePage' => 'Дела в порядке | Регистрация',
    'newUser' => $newUser ?? [],
    'errors' => $errors ?? []
]);

print($layoutContent);
