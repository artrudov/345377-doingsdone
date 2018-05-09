<?php

require('functions.php');

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$projects = ['Все', 'Входящие', 'Учеба', 'Работа', 'Домашние дела', 'Авто'];
$tasks = [
    [
        'task' => 'Собеседование в IT компании',
        'deadline' => '01.06.2018',
        'category' => 2,
        'isComplete' => false
    ],
    [
        'task' => 'Выполнить тестовое задание',
        'deadline' => '25.05.2018',
        'category' => 3,
        'isComplete' => false
    ],
    [
        'task' => 'Сделать задание первого раздела',
        'deadline' => '21.04.2018',
        'category' => 2,
        'isComplete' => true
    ],
    [
        'task' => 'Встреча с другом',
        'deadline' => '	22.04.2018',
        'category' => 1,
        'isComplete' => false
    ],
    [
        'task' => 'Купить корм для кота',
        'deadline' => 'Нет',
        'category' => 4,
        'isComplete' => false
    ],
    [
        'task' => 'Заказать пиццу',
        'deadline' => 'Нет',
        'category' => 4,
        'isComplete' => false
    ]
];

function getCountTasks($tasks, $projectCategory){
    if ($projectCategory === 0) {
        return count($tasks);
    }

    return count(array_filter($tasks, function ($task) use ($projectCategory) {
        return $task['category'] === $projectCategory;
    }));
};

$pageContent = renderTemplate('templates/index.php', [
    'show_complete_tasks' => $show_complete_tasks,
    'tasks' => $tasks
]);

$layoutContent = renderTemplate('templates/layout.php', [
    'titlePage' => 'Дела в порядке',
    'content' => $pageContent,
    'tasks' => $tasks,
    'projects' => $projects,
    'getCountTasks' => 'getCountTasks'
]);

print($layoutContent);
