<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.html" method="post">
    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>

<div class="tasks-controls">
    <nav class="tasks-switch">
        <a href="/<?= $projectID ? 'index.php?project_id=' . $projectID . '&' : '?' ?>filter=all"
           class="tasks-switch__item <?= $filterTask === 'all' || $filterTask === 0 ? 'tasks-switch__item--active' : '' ?>">Все
            задачи</a>
        <a href="/<?= $projectID ? 'index.php?project_id=' . $projectID . '&' : '?' ?>filter=today"
           class="tasks-switch__item <?= $filterTask === 'today' ? 'tasks-switch__item--active' : '' ?>">Повестка
            дня</a>
        <a href="/<?= $projectID ? 'index.php?project_id=' . $projectID . '&' : '?' ?>filter=tomorrow"
           class="tasks-switch__item <?= $filterTask === 'tomorrow' ? 'tasks-switch__item--active' : '' ?>">Завтра</a>
        <a href="/<?= $projectID ? 'index.php?project_id=' . $projectID . '&' : '?' ?>filter=overdue"
           class="tasks-switch__item <?= $filterTask === 'overdue' ? 'tasks-switch__item--active' : '' ?>">Просроченные</a>
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
                            <span class="checkbox__text"><?= htmlspecialchars($task['name']) ?></span>
                        </label>
                    </td>

                    <td class="task__file">
                        <? if ($task['file'] !== ''): ?>
                            <a class="download-link"
                               href="/uploads/<?= $task['file'] ?>"><?= htmlspecialchars($task['file']) ?></a>
                        <? endif; ?>                    </td>
                    <td class="task__date"><?= $task['deadline'] ?></td>
                </tr>
            <? endif; ?>
        <? else: ?>

            <tr class="tasks__item task
            <? if ($task['deadline'] !== NULL): ?>
                <?= compareDate($task['deadline'], $task['complete_date']) ? 'task--important' : '' ?>
            <? endif; ?>">
                <td class="task__select">
                    <label class="checkbox task__checkbox">
                        <input class="checkbox__input visually-hidden" type="checkbox"
                               value="<?= $task['id']?>">
                        <span class="checkbox__text"><?= htmlspecialchars($task['name']) ?></span>
                    </label>
                </td>

                <td class="task__file">
                    <? if ($task['file'] !== ''): ?>
                        <a class="download-link"
                           href="/uploads/<?= $task['file'] ?>"><?= htmlspecialchars($task['file']) ?></a>
                    <? endif; ?>
                </td>
                <td class="task__date"><?= $task['deadline'] !== NULL ? date("d-m-Y", strtotime($task['deadline'])) : '' ?></td>
            </tr>
        <? endif; ?>
    <? endforeach; ?>
</table>
