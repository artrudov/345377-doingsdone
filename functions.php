<?php
/**
 * Функция подсчета задач
 * @param string $projectCategory название проекта
 * @param array $tasks список всех задач в виде массива
 * @return integer число задач для переданного проекта
 */
function getCountTasks($tasks, $projectCategory){
    if ($projectCategory === 0) {
        return count($tasks);
    }

    return count(array_filter($tasks, function ($task) use ($projectCategory) {
        return $task['category'] === $projectCategory;
    }));
};

/**
 * Функция отрисовки шаблона с данными
 * @param string $templatePath относительный путь к шаблону, например templates/index.php
 * @param array $data данные для шаблона
 * @return string html-код шаблона
 */
function renderTemplate($templatePath, $data) {
    if (!file_exists($templatePath)) {
        return '';
    }

    ob_start();
    extract($data);
    require($templatePath);

    return ob_get_clean();
};

/**
 * Функция подсчета часов
 * @param string $taskDate дата завершения задачи
 * @return integer оставшееся количество часов до каждой из имеющихся дат
 */
function checkDeadline($taskDate) {
    $currentTS = time();
    $taskTS = strtotime($taskDate);

    return $taskTS - $currentTS;
};

/**
 * Функция проверяет задачу на оставшееся время и приоритет важности
 * @param integer $taskDate дата завершения задачи
 * @param boolean $taskComplete статус выполнения задачи
 * @return boolean задача важна или нет
 */
function compareDate($taskDate, $taskComplete) {
    return checkDeadline($taskDate) / HOUR_IN_DAY < HOUR_IN_DAY && $taskDate !== 'Нет' && !$taskComplete;
}
