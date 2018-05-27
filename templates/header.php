<header class="main-header">
    <a href="#">
        <img src="img/logo.png" width="153" height="42" alt="Логотип Дела в порядке">
    </a>

    <div class="main-header__side">
        <?php if ($userName !== NULL): ?>
            <a class="main-header__side-item button button--plus open-modal" target="task_add">Добавить задачу</a>

            <div class="main-header__side-item user-menu">
            <div class="user-menu__image">
                <img src="img/user-pic.jpg" width="40" height="40" alt="Пользователь">
            </div>
            <div class="user-menu__data">
                <p><?= $userName ?></p>
                <a href="logout.php">Выйти</a>
            </div>
        <?php else: ?>

            <a class="main-header__side-item button button--transparent open-modal"
               target="user_login">Войти</a>
            </div>
        <?php endif; ?>
    </div>
</header>
