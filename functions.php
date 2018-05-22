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
        return $task['project_id'] === $projectCategory;
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

/**
 * Функция получения данных из баззы даннх
 * @param mysqli $db ресурс базы данны
 * @param string $sql строка запроза
 * @param array $condition условие для подстановки запроса
 * @return array задача важна или нет
 */
function getData($db, $sql, $condition) {

    $resource = mysqli_prepare($db, $sql);
    $stmt = db_get_prepare_stmt($db, $sql, $condition);
    mysqli_stmt_execute($stmt);

    $resource = mysqli_stmt_get_result($stmt);

    $result = mysqli_fetch_all($resource, MYSQLI_ASSOC);

    return $result;
}

