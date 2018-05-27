<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: /guest.php");
    exit();
}

require('functions.php');
require('db-config.php');
require('mysql_helper.php');

const SEC_IN_HOUR = 3600;
const HOUR_IN_DAY = 24;

$db = new mysqli(DB['server'], DB['username'], DB['password'], DB['db']);
$projectID = intval($_GET['project_id'] ?? 0);
$user = $_SESSION['user'];
$userID = $user['id'];
$userName = $user['name'];

$getProjects = 'SELECT * FROM `projects` WHERE `user_id` = ?';
$getProjectTasks = 'SELECT * FROM `tasks` WHERE `user_id` =' . $userID . ' AND `project_id` = ?';
$getAllTasks = 'SELECT * FROM `tasks` WHERE `user_id` = ?';
$setNewTask = 'INSERT INTO `tasks` (`name`,`create_date`,`complete_date`,`project_id`,`deadline`,`file`, `user_id` )
        VALUES (?, NOW(), NULL, ?, ?, ?, ' . $userID . ')';

$projects = getData($db, $getProjects, [$userID]);
$allTasks = getData($db, $getAllTasks, [$userID]);

$isProject = isProjectExists($projectID, $projects);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project'])) {
    $newTask = $_POST;
    $id = intval($newTask['project']);
    $errors = [];

    if (empty($newTask['name'])) {
        $errors['name'] = 'Это поле надо заполнить';
    }

    if (!isProjectExists($id, $projects)) {
        $errors['project'] = 'Выбраный проект не найден';
    }

    if ($newTask['date']) {
        validateDate($newTask['date']) ? $newTask['date'] : $errors['date'] = 'Введите дату и время в формате: ГГГГ-ММ-ДД ЧЧ:ММ';
    } else {
        $newTask['date'] = 'NULL';
    }

    if ($_FILES['preview']['size']) {
        $tmp_name = $_FILES['preview']['tmp_name'];
        $path = $_FILES['preview']['name'];

        if (!count($errors)) {
            move_uploaded_file($tmp_name, 'uploads/' . $path);
            $newTask['path'] = $path;
        }
    } else {
        $newTask['path'] = 'NULL';
    }

    if (!count($errors)) {
        addNewEntry($db, $setNewTask, $newTask);
    }
}

if ($isProject) {
    $tasks = getData($db, $getProjectTasks, [$projectID]);
} else if (!$projectID) {
    $tasks = $allTasks;
} else {
    header('Status: 404, not found');
    http_response_code(404);
    $errorMessage = 'Указанной категории нет';
}

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$pageHeader = renderTemplate('templates/header.php', ['userName' => $userName]);

$pageContent = renderTemplate('templates/index.php', [
    'show_complete_tasks' => $show_complete_tasks,
    'tasks' => $tasks ?? [],
]);

$modalProject = renderTemplate('templates/modal-project.php', [
    'newTask' => $newTask ?? [],
    'errors' => $errors ?? []
]);

$modalTask = renderTemplate('templates/modal-task.php', [
    'projects' => $projects,
    'newTask' => $newTask ?? [],
    'errors' => $errors ?? []
]);

$layoutContent = renderTemplate('templates/layout.php', [
    'titlePage' => 'Дела в порядке',
    'pageHeader' => $pageHeader,
    'content' => $pageContent,
    'errorMessage' => $errorMessage ?? '',
    'tasks' => $allTasks,
    'projectID' => $projectID,
    'projects' => $projects,
    'modalTask' => $modalTask,
    'modalProject' => $modalProject,
    'errors' => $errors ?? [],
    'userName' => $userName
]);

print($layoutContent);
