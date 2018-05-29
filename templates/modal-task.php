<div class="modal" id="task_add" <?= count($errorsTask) ?: 'hidden' ?>>
    <button class="modal__close" type="button" name="button">Закрыть</button>

    <h2 class="modal__heading">Добавление задачи</h2>

    <form class="form" action="index.php" method="post" enctype="multipart/form-data">
        <div class="form__row">
            <label class="form__label" for="name">Название <sup>*</sup></label>

            <input class="form__input <?= $errorsTask['name'] ? 'form__input--error' : '' ?>" type="text" name="name"
                   id="name" value="<?= isset($newTask['name']) ? $newTask['name'] : '' ?>"
                   placeholder="Введите название">
            <p class="form__message">
                <?= isset($errorsTask['name']) ? $errorsTask['name'] : '' ?>
            </p>
        </div>

        <div class="form__row">
            <label class="form__label" for="project">Проект <sup>*</sup></label>

            <select class="form__input form__input--select <?= $errorsTask['project'] ? 'form__input--error' : '' ?>"
                    name="project" id="project">
                <? if (isset($projects)): ?>
                    <? foreach ($projects as $project): ?>
                        <option value="<?= $project['id'] ?>">
                            <?= $project['name'] ?></option>
                    <? endforeach; ?>
                <? endif; ?>
            </select>
            <p class="form__message">
                <?= isset($errorsTask['project']) ? $errorsTask['project'] : '' ?>
            </p>
        </div>

        <div class="form__row">
            <label class="form__label" for="date">Срок выполнения</label>

            <input class="form__input form__input--date" type="text" name="date" id="date"
                   placeholder="Введите дату и время" value="<?= isset($newTask['date']) ? $newTask['date'] : '' ?>">

            <p class="form__message">
                <?= isset($errorsTask['date']) ? $errorsTask['date'] : '' ?>
            </p>
        </div>

        <div class="form__row">
            <label class="form__label" for="preview">Файл</label>

            <div class="form__input-file">
                <input class="visually-hidden" type="file" name="preview" id="preview"
                       value="<?= isset($newTask['path']) ? $newTask['path'] : '' ?>">

                <label class="button button--transparent" for="preview">
                    <span>Выберите файл</span>
                </label>
            </div>
        </div>

        <p class="form__message">
            <?= count($errorsTask) ? 'Пожалуйста, исправьте ошибки в форме' : '' ?>
        </p>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="task-form" value="Добавить">
        </div>
    </form>
</div>
