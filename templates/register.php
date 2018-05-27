<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title><?= $titlePage?></title>
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
<h1 class="visually-hidden">Дела в порядке</h1>

<div class="page-wrapper">
    <div class="container container--with-sidebar">
        <header class="main-header">
            <a href="guest.php">
                <img src="../img/logo.png" width="153" height="42" alt="Логитип Дела в порядке">
            </a>
        </header>

        <div class="content">
            <section class="content__side">
                <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>

                <a class="button button--transparent content__side-button" href="#">Войти</a>
            </section>

            <main class="content__main">
                <h2 class="content__main-heading">Регистрация аккаунта</h2>
                <p class="error-message"><?= $errors['connect'] ?? ''?></p>

                <form class="form" action="register.php" method="post">

                    <div class="form__row">
                        <label class="form__label" for="email">E-mail <sup>*</sup></label>

                        <input class="form__input <?= $errors['email'] ? 'form__input--error' : ''?>" type="text" name="email" id="email" value="<?= $newUser['email'] ?? ''?>" placeholder="Введите e-mail">

                        <p class="form__message"><?= $errors['email'] ?? ''?></p>
                    </div>

                    <div class="form__row">
                        <label class="form__label" for="password">Пароль <sup>*</sup></label>

                        <input class="form__input <?= $errors['password'] ? 'form__input--error' : ''?>" type="password" name="password" id="password" value="" placeholder="Введите пароль">

                        <p class="form__message"><?= $errors['password'] ?? ''?></p>
                    </div>

                    <div class="form__row">
                        <label class="form__label" for="name">Имя <sup>*</sup></label>

                        <input class="form__input <?= $errors['name'] ? 'form__input--error' : ''?>" type="text" name="name" id="name" value="<?= $newUser['name'] ?? ''?>" placeholder="Введите имя">
                        <p class="form__message"><?= $errors['name'] ?? ''?></p>

                    </div>

                    <div class="form__row form__row--controls">
                        <p class="error-message"><?= count($errors) ? 'Пожалуйста, исправьте ошибки в форме' : ''?></p>

                        <input class="button" type="submit" name="" value="Зарегистрироваться">
                    </div>
                </form>
            </main>
        </div>
    </div>
</div>
