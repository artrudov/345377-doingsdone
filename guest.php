<?php

require('functions.php');
require('mysql_helper.php');

$db = connect();

$getUser = 'SELECT * FROM `users` WHERE `email` = ?';

session_start();

if (isset($_SESSION['user'])) {
    header("Location: /index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $logInForm = $_POST;
    unset($logInForm['login']);

    $errorsLogin = login($logInForm, $db);
}

$pageHeader = renderTemplate('templates/header.php', ['userName' => NULL]);

$modalAuthorization = renderTemplate('templates/modal-authorization.php', [
    'user' => $user ?? [],
    'errors' => $errorsLogin ?? [],
    'logInForm' => $logInForm ?? []
]);

$layoutContent = renderTemplate('templates/guest.php', [
    'titlePage' => 'Дела в порядке | Регистрация',
    'pageHeader' => $pageHeader,
    'modalAuthorization' => $modalAuthorization,
    'errors' => $errorsLogin ?? []
]);

print($layoutContent);
