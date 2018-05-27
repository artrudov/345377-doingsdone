<div class="modal" <?= count($errors) ? '' : 'hidden'?> id="user_login">
    <button class="modal__close" type="button" name="button">Закрыть</button>

    <h2 class="modal__heading">Вход на сайт</h2>

    <form class="form" action="" method="post">
        <div class="form__row">
            <label class="form__label" for="email">E-mail <sup>*</sup></label>
            <input class="form__input <?= count($errors) ? 'form__input--error' : ''?>" type="text" name="email" id="email" value="" placeholder="Введите e-mail">

        </div>

        <div class="form__row">
            <label class="form__label" for="password">Пароль <sup>*</sup></label>
            <input class="form__input <?= count($errors) ? 'form__input--error' : ''?>" type="password" name="password" id="password" value="" placeholder="Введите пароль">

        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="login" value="Войти">
        </div>

        <p class="error-message"><?= count($errors) ? 'Вы ввели неверный email/пароль' : ''?></p>
    </form>
</div>
