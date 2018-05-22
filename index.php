<?php

require('functions.php');
require('db-config.php');
require('mysql_helper.php');

const SEC_IN_HOUR = 3600;
const HOUR_IN_DAY = 24;

$db = new mysqli(DB['server'], DB['username'], DB['password'], DB['db']);

$projectID = $_GET ? intval($_GET['project_id']) : 0;
$userID = 1;

$getProjects = 'SELECT * FROM projects WHERE user_id = ?';
$getProjectTasks = 'SELECT * FROM tasks WHERE user_id ='. $userID .' AND project_id = ?';
$getAllTasks = 'SELECT * FROM tasks WHERE user_id = ?';

$projects = getData($db, $getProjects, [$userID]);
$tasks = $projectID ? getData($db, $getProjectTasks, [$projectID]) : getData($db, $getAllTasks, [$userID]);
$allTasks = getData($db, $getAllTasks, [$userID]);

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$pageContent = renderTemplate('templates/index.php', [
    'show_complete_tasks' => $show_complete_tasks,
    'tasks' => $tasks,
]);

$layoutContent = renderTemplate('templates/layout.php', [
    'titlePage' => 'Дела в порядке',
    'content' => $pageContent,
    'tasks' => $allTasks,
    'projects' => $projects
]);

print($layoutContent);
