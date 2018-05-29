<?php

require('db-config.php');

/**
 * Функция подключения к базе данных
 * @return mysqli ресурс подключения
 */
function connect()
{
    $db = new mysqli(DB['server'], DB['username'], DB['password'], DB['db']);
    mysqli_set_charset($db, DB['charset']);
    return $db;
}

/**
 * Функция подсчета задач
 * @param string $projectCategory название проекта
 * @param array $tasks список всех задач в виде массива
 * @return integer число задач для переданного проекта
 */
function getTasks($tasks, $projectCategory)
{
    if ($projectCategory === 0) {
        return count($tasks);
    }

    if ($projectCategory === 'income') {
        return count(array_filter($tasks, function ($task) {
            return $task['project_id'] === NULL;
        }));
    }

    return count(array_filter($tasks, function ($task) use ($projectCategory) {
        return $task['project_id'] === $projectCategory;
    }));
}

/**
 * Функция отрисовки шаблона с данными
 * @param string $templatePath относительный путь к шаблону, например templates/index.php
 * @param array $data данные для шаблона
 * @return string html-код шаблона
 */
function renderTemplate($templatePath, $data)
{
    if (!file_exists($templatePath)) {
        return '';
    }

    ob_start();
    extract($data);
    require($templatePath);

    return ob_get_clean();
}

/**
 * Функция подсчета часов
 * @param string $taskDate дата завершения задачи
 * @return integer оставшееся количество часов до каждой из имеющихся дат
 */
function checkDeadline($taskDate)
{
    $currentTS = time();
    $taskTS = strtotime($taskDate);

    return $taskTS - $currentTS;
}

/**
 * Функция проверяет задачу на оставшееся время и приоритет важности
 * @param integer $taskDate дата завершения задачи
 * @param boolean $taskComplete статус выполнения задачи
 * @return boolean задача важна или нет
 */

function compareDate($taskDate, $taskComplete)
{
    return (checkDeadline($taskDate) / SEC_IN_HOUR < HOUR_IN_DAY) && !$taskComplete;
}

/**
 * Функция выполнения запроса
 * @param mysqli $db ресурс базы данных
 * @param string $sql строка запроза
 * @param array $formsData данные из формы
 * @return mysqli_stmt результат добавления задачи в базу данных
 */
function executeQuery($db, $sql, $formsData)
{
    $stmt = db_get_prepare_stmt($db, $sql, $formsData);
    mysqli_stmt_execute($stmt);
    return $stmt;
}

/**
 * Функция получения данных из базы данных
 * @param mysqli $db ресурс базы данных
 * @param string $sql строка запроза
 * @param array $condition условие для подстановки запроса
 * @return array массив с данными
 */
function getData($db, $sql, $condition)
{

    $resource = mysqli_stmt_get_result(executeQuery($db, $sql, $condition));
    $result = mysqli_fetch_all($resource, MYSQLI_ASSOC);

    return $result;
}

/**
 * Функция фильтрующая задачи
 * @param mysqli $db ресурс базы данных
 * @param integer $projectID идентификатор проекта
 * @param integer $userID идентификатор пользователя
 * @param integer $filterTask условие фильтрации
 * @return array массив с данными
 */
function getFilterData($db, $projectID, $userID, $filterTask)
{
    $sql = '';
    $condition = [];
    $today = date_create('today');
    $todayMidnight = date_create('tomorrow');
    $tomorrowMidnight = date_create('2 day midnight');

    switch ($filterTask) {
        case 'all':
            if ($projectID) {
                $sql = 'SELECT * FROM `tasks` WHERE project_id = ?';
                $condition = $projectID;
            } else {
                $sql = 'SELECT * FROM `tasks` WHERE `user_id` = ?';
                $condition = $userID;
            }
            break;
        case 'today':
            if ($projectID) {
                $sql = 'SELECT * FROM `tasks` WHERE `deadline` BETWEEN "' . $today->format(DATA_FORMAT) . '" AND "' . $todayMidnight->format(DATA_FORMAT) . '" AND project_id = ?';
                $condition = $projectID;
            } else {
                $sql = 'SELECT * FROM `tasks` WHERE `deadline` BETWEEN "' . $today->format(DATA_FORMAT) . '" AND "' . $todayMidnight->format(DATA_FORMAT) . '" AND user_id = ?';
                $condition = $userID;
            }
            break;
        case 'tomorrow':
            if ($projectID) {
                $sql = 'SELECT * FROM `tasks` WHERE `deadline` BETWEEN "' . $todayMidnight->format(DATA_FORMAT) . '" AND "' . $tomorrowMidnight->format(DATA_FORMAT) . '" AND project_id = ?';
                $condition = $projectID;
            } else {
                $sql = 'SELECT * FROM `tasks` WHERE `deadline` BETWEEN "' . $todayMidnight->format(DATA_FORMAT) . '" AND "' . $tomorrowMidnight->format(DATA_FORMAT) . '" AND `user_id` = ?';
                $condition = $userID;
            }
            break;
        case 'overdue':
            if ($projectID) {
                $sql = 'SELECT * FROM `tasks` WHERE (`deadline` < NOW() OR NOT `complete_date`) AND  project_id = ?';
                $condition = $projectID;

            } else {
                $sql = 'SELECT * FROM `tasks` WHERE (`deadline` < NOW() OR NOT `complete_date`) AND `user_id` = ?';
                $condition = $userID;
            }
            break;
    }

    return getData($db, $sql, [$condition]);
}

/**
 * Функция проверки наличия запрашиваемых данных в базе
 * @param mysqli $db ресурс базы данных
 * @param string $sql строка запроза
 * @param array $condition условие для подстановки запроса
 * @return integer возвращает количество записей в базе
 */
function isEntriesExist($db, $sql, $condition)
{
    $resource = mysqli_stmt_get_result(executeQuery($db, $sql, $condition));
    $result = mysqli_fetch_all($resource, MYSQLI_ASSOC);

    return $result['0']['COUNT(*)'];
}

/**
 * Функция добавления новой задачи у активного пользователя
 * @param mysqli $db ресурс базы данных
 * @param array $formsData данные из формы
 * @param int $idProject задачи без проекта
 * @return mysqli_stmt результат добавления задачи в базу данных
 */
function addNewTask($db, $formsData, $idProject)
{
    if ($idProject) {
        if ($formsData['date']) {
            $sql = 'INSERT INTO `tasks` (`name`,`create_date`,`complete_date`,`project_id`,`deadline`,`file`, `user_id` )
        VALUES (?, NOW(), NULL, ?, ?, ?, ?)';
        } else {
            $sql = 'INSERT INTO `tasks` (`name`,`create_date`,`complete_date`,`project_id`,`deadline`,`file`, `user_id` )
        VALUES (?, NOW(), NULL, ?, NULL, ?, ?)';
            unset($formsData['date']);
        }
    } else {
        if ($formsData['date']) {
            $sql = 'INSERT INTO `tasks` (`name`,`create_date`,`complete_date`,`project_id`,`deadline`,`file`, `user_id` )
        VALUES (?, NOW(), NULL, NULL, ?, ?, ?)';
        } else {
            $sql = 'INSERT INTO `tasks` (`name`,`create_date`,`complete_date`,`project_id`,`deadline`,`file`, `user_id` )
        VALUES (?, NOW(), NULL, NULL, NULL, ?, ?)';
            unset($formsData['date']);
        }
        unset($formsData['project']);
    }

    return executeQuery($db, $sql, $formsData);
}

/**
 * Функция изменения статуса задачи
 * @param mysqli $db ресурс базы данных
 * @param integer $checkTask текущий статус задачи
 * @param integer $taskID идентификатор задачи
 * @return mysqli_stmt результат добавления задачи в базу данных
 */
function setCompleteDate($db, $checkTask, $taskID)
{
    if ($checkTask) {
        $sql = 'UPDATE `tasks` SET `complete_date` = NOW() WHERE `id` = ?';

    } else {
        $sql = 'UPDATE `tasks` SET `complete_date` = NULL WHERE `id` = ?';
    }

    return executeQuery($db, $sql, [$taskID]);
}

/**
 * Функция проверки id проекта
 * @param integer $id искомый идентификатор
 * @param array $projects массив проектов
 * @return boolean результат проверки
 */
function isProjectExists($id, $projects)
{
    if ($id === 'all') {
        return true;
    }

    $isProject = in_array($id, array_column($projects, 'id'));
    return $isProject && $isProject !== -1 ? true : false;
}

/**
 * Функция валидации даты
 * @param string $date дата
 * @param string $format формат даты
 * @return boolean
 */
function validateDate($date, $format = 'Y-m-d H:i')
{
    $receivedDate = DateTime::createFromFormat($format, $date);
    return $receivedDate && $receivedDate->format($format) == $date;
}

/**
 * Функция входа пользователя
 * @param array $loginData данные для входа
 * @param mysqli $db соединение с базой данных
 * @return array Массив ошибок
 */
function login($loginData, $db)
{
    $getUser = 'SELECT * FROM `users` WHERE `email` = ?';

    $errorsLogin = [];

    if (empty($loginData['password'])) {
        $errorsLogin['password'] = 'Это поле надо заполнить';
    }

    if (!isset($loginData['email'])) {
        $errorsLogin['email'] = 'Это поле надо заполнить';
    } elseif (!filter_var($loginData['email'], FILTER_VALIDATE_EMAIL)) {
        $errorsLogin['email'] = 'Введите корректный E-mail';
    }

    if (!count($errorsLogin)) {
        $user = getData($db, $getUser, [$loginData['email']]);
        if ($user) {
            if (password_verify($loginData['password'], $user[0]['password'])) {
                $_SESSION['user'] = $user[0];
                header("Location: /index.php");
                exit();
            } else {
                $errorsLogin['password'] = 'Неверный пароль';
            }
        } else {
            $errorsLogin['email'] = 'Пользователь с таким именем не найден';
        }
    }

    return $errorsLogin;
}
