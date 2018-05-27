<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.html" method="post">
    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>

<div class="tasks-controls">
    <nav class="tasks-switch">
        <a href="/<?= $projectID ? 'index.php?project_id=' . $projectID . '&' : '?' ?>filter=all"
           class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
        <a href="/<?= $projectID ? 'index.php?project_id=' . $projectID . '&' : '?' ?>filter=today"
           class="tasks-switch__item">Повестка дня</a>
        <a href="/<?= $projectID ? 'index.php?project_id=' . $projectID . '&' : '?' ?>filter=tomorrow"
           class="tasks-switch__item">Завтра</a>
        <a href="/<?= $projectID ? 'index.php?project_id=' . $projectID . '&' : '?' ?>filter=overdue"
           class="tasks-switch__item">Просроченные</a>
    </nav>

    <label class="checkbox">
        <input class="checkbox__input visually-hidden show_completed" type="checkbox"
            <?= $show_complete_tasks ? "checked" : '' ?>>
        <span class="checkbox__text">Показывать выполненные</span>
    </label>
</div>

<table class="tasks">
    <?php foreach ($tasks as $task): ?>
        <? if ($task['complete_date']): ?>
            <? if ($show_complete_tasks): ?>
                <tr class="tasks__item task task--completed">
                    <td class="task__select">
                        <label class="checkbox task__checkbox">
                            <input class="checkbox__input visually-hidden" value="<?= $task['id'] ?>"
                                   type="checkbox" checked>
                            <span class="checkbox__text"><?= strip_tags($task['name']) ?></span>
                        </label>
                    </td>

                    <td class="task__file">
                        <a class="download-link"
                           href="<?= isset($task['file']) !== NULL ? $task['file'] : '' ?>"><?= isset($task['file']) !== NULL ? $task['file'] : '' ?></a>
                    </td>
                    <td class="task__date"><?= strip_tags($task['deadline']) ?></td>

                    <td class="task__controls"></td>
                </tr>
            <? endif; ?>
        <? else: ?>
            <tr class="tasks__item task
        <?= compareDate(strip_tags($task['deadline']), $task['complete_date']) ? 'task--important' : '' ?>">
                <td class="task__select">
                    <label class="checkbox task__checkbox">
                        <input class="checkbox__input visually-hidden" type="checkbox" value="<?= $task['id'] ?>">
                        <span class="checkbox__text"><?= strip_tags($task['name']) ?></span>
                    </label>
                </td>

                <td class="task__file">
                    <a class="download-link"
                       href="<?= isset($task['file']) !== NULL ? $task['file'] : '' ?>"><?= isset($task['file']) !== NULL ? $task['file'] : '' ?></a>
                </td>
                <td class="task__date"><?= strip_tags($task['deadline']) ?></td>

                <td class="task__controls"></td>
            </tr>
        <? endif; ?>
    <? endforeach; ?>
</table>
