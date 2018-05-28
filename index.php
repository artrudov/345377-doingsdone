<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: /guest.php");
    exit();
}

const SEC_IN_HOUR = 3600;
const HOUR_IN_DAY = 24;
const DATA_FORMAT = 'Y-m-d H-i';

require('functions.php');
require('mysql_helper.php');

$db = connect();

if(isset($_GET)) {
    $stripGet = array_map('strip_tags', $_GET);
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
$getProjectTasks = 'SELECT * FROM `tasks` WHERE `user_id` = ? AND `project_id` = ?';
$getAllTasks = 'SELECT *  FROM `tasks` WHERE `user_id` = ?';
$setNewProject = 'INSERT INTO `projects` (`name`, `user_id` ) VALUES (?, ?)';

if ($completeTaskID) {
    setCompleteDate($db, $checkTask, $completeTaskID);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task-form'])) {
    $newTask = array_map('strip_tags', $_POST);
    unset($newTask['task-form']);

    $id = isset($newTask['project']) ? intval($newTask['project']) : 0;
    $errorsTask = [];

    if (empty($newTask['name'])) {
        $errorsTask['name'] = 'Это поле надо заполнить';
    }

    if (!isProjectExists($id, getData($db, $getProjects, [$userID]))) {
        $errorsTask['project'] = 'Выбраный проект не найден';
    }

    if (isset($newTask['date'])) {
        validateDate($newTask['date']) ? : $errorsTask['date'] = 'Введите дату и время в формате: ГГГГ-ММ-ДД ЧЧ:ММ';
    } else {
        $newTask['date'] = 'NULL';
    }

    if (isset($_FILES['preview'])) {
        $tmp_name = $_FILES['preview']['tmp_name'];
        $path = $_FILES['preview']['name'];

        if (!count($errorsTask)) {
            move_uploaded_file($tmp_name, 'uploads/' . $path);
            $newTask['path'] = $path;
        }
    } else {
        $newTask['path'] = '';
    }

    if (!count($errorsTask)) {
        $newTask['user'] = $userID;
        addNewTask($db, $newTask);
        unset($newTask);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project-form'])) {
    $newProject = array_map('strip_tags', $_POST);
    unset($newProject['project-form']);
    $errorsProject = [];

    if (empty($newProject['name'])) {
        $errorsProject['name'] = 'Это поле надо заполнить';
    }

    if (!count($errorsProject)) {
        $newProject['userID'] = $userID;
        executeQuery($db, $setNewProject, $newProject);
        unset($newProject);
    }
}

$projects = getData($db, $getProjects, [$userID]);
$allTasks = getData($db, $getAllTasks, [$userID]);

$isProject = isProjectExists($projectID, $projects);

if ($isProject) {
    $tasks = getData($db, $getProjectTasks, [$userID, $projectID]);
} else if (!$projectID) {
    $tasks = $allTasks;
} else {
    header('Status: 404, not found');
    http_response_code(404);
    $errorMessage = 'Указанной категории нет';
}

if ($filterTask) {
    $tasks = getFilterData($db, $projectID, $userID, $filterTask);
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
    'errorsProject' => $errorsProject ?? []
]);

$modalTask = renderTemplate('templates/modal-task.php', [
    'projects' => $projects,
    'newTask' => $newTask ?? [],
    'errorsTask' => $errorsTask ?? []
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
    'errorsTask' => $errorsTask ?? [],
    'errorsProject' => $errorsProject ?? [],
    'userName' => $userName
]);

print($layoutContent);
