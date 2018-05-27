<div class="modal" hidden id="project_add">
    <button class="modal__close" type="button" name="button">Закрыть</button>

    <h2 class="modal__heading">Добавление проекта</h2>

    <form class="form" action="index.php" method="post">
        <div class="form__row">
            <label class="form__label" for="project_name">Название <sup>*</sup></label>

            <input class="form__input <?= count($errors) ? 'form__input--error' : '' ?>" type="text" name="name"
                   id="project_name" value="" placeholder="Введите название проекта">
            <?= isset($errors['name']) ? $errors['name'] : '' ?>
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="project-form" value="Добавить">
        </div>

        <p class="form__message">
            <?= count($errors) ? 'Пожалуйста, исправьте ошибки в форме' : '' ?>
        </p>
    </form>
</div>
