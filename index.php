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

if(isset($_GET)) {
    $stripGet = array_map(function ($item) {
        return strip_tags($item);
    }, $_GET);
}

$projectID = isset($stripGet['project_id']) ? intval($stripGet['project_id']) : 0;
$filterTask = isset($stripGet['filter']) ? $stripGet['filter'] : 0;
$completeTaskID = isset($stripGet['task_id']) ? intval($stripGet['task_id']) : 0;
$checkTask = isset($stripGet['check']) ? intval($stripGet['check']) : 0;
$show_complete_tasks = isset($stripGet['show_completed']) ? $stripGet['show_completed'] : 0;

$user = $_SESSION['user'];
$userID = $user['id'];
$userName = $user['name'];

$getProjects = 'SELECT * FROM `projects` WHERE `user_id` = ?';
$getProjectTasks = 'SELECT * FROM `tasks` WHERE `user_id` =' . $userID . ' AND `project_id` = ?';
$getAllTasks = 'SELECT *  FROM `tasks` WHERE `user_id` = ?';
$setNewProject = 'INSERT INTO `projects` (`name`, `user_id` ) VALUES (?, ' . $userID . ')';

if ($completeTaskID) {
    setCompleteDate($db, $checkTask, $completeTaskID);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task-form'])) {
    $newTask = array_map(function ($item) {return strip_tags($item);}, $_POST);
    $id = intval($newTask['project']);
    $errorsTask = [];

    var_dump($newTask);

    if (empty($newTask['name'])) {
        $errorsTask['name'] = 'Это поле надо заполнить';
    }

    if (!isProjectExists($id, getData($db, $getProjects, [$userID]))) {
        $errorsTask['project'] = 'Выбраный проект не найден';
    }

    if ($newTask['date']) {
        validateDate($newTask['date']) ? $newTask['date'] : $errorsTask['date'] = 'Введите дату и время в формате: ГГГГ-ММ-ДД ЧЧ:ММ';
    } else {
        $newTask['date'] = 'NULL';
    }

    if ($_FILES['preview']['size']) {
        $tmp_name = $_FILES['preview']['tmp_name'];
        $path = $_FILES['preview']['name'];

        if (!count($errorsTask)) {
            move_uploaded_file($tmp_name, 'uploads/' . $path);
            array_pop($newTask);
            $newTask['path'] = $path;
        }
    } else {
        array_pop($newTask);
        $newTask['path'] = 'NULL';
    }

    if (!count($errorsTask)) {
        addNewTask($db, $userID, $newTask);
        unset($newTask);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project-form'])) {
    $newProject = array_map(function ($item) {return strip_tags($item);}, $_POST);
    $errorsProject = [];

    if (empty($newProject['name'])) {
        $errorsProject['name'] = 'Это поле надо заполнить';
    }

    if (!count($errorsProject)) {
        array_pop($newProject);
        executeQuery($db, $setNewProject, $newProject);
        unset($newProject);
    }
}

$projects = getData($db, $getProjects, [$userID]);
$allTasks = getData($db, $getAllTasks, [$userID]);

$isProject = isProjectExists($projectID, $projects);

if ($isProject) {
    $tasks = getData($db, $getProjectTasks, [$projectID]);
} else if (!$projectID) {
    $tasks = $allTasks;
} else {
    header('Status: 404, not found');
    http_response_code(404);
    $errorMessage = 'Указанной категории нет';
}

if ($filterTask) {
    $tasks = getFilterDate($db, $projectID, $userID, $filterTask);
}

$pageHeader = renderTemplate('templates/header.php', ['userName' => $userName]);

$pageContent = renderTemplate('templates/index.php', [
    'show_complete_tasks' => $show_complete_tasks,
    'filterTask' => $filterTask,
    'projectID' => $projectID,
    'tasks' => $tasks ?? [],
]);

$modalProject = renderTemplate('templates/modal-project.php', [
    'newTask' => $newTask ?? [],
    'errors' => $errorsTask ?? []
]);

$modalTask = renderTemplate('templates/modal-task.php', [
    'projects' => $projects,
    'newTask' => $newTask ?? [],
    'errors' => $errorsTask ?? []
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
    'errors' => $errorsTask ?? [],
    'userName' => $userName
]);

print($layoutContent);
