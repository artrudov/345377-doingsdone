<?php

require('functions.php');

const SEC_IN_HOUR = 3600;
const HOUR_IN_DAY = 24;

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
        'deadline' => '22.04.2018',
        'category' => 1,
        'isComplete' => false
    ],
    [
        'task' => 'Купить корм для кота',
        'deadline' => '12.05.2018',
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

$pageContent = renderTemplate('templates/index.php', [
    'show_complete_tasks' => $show_complete_tasks,
    'tasks' => $tasks,
    'dateCheck' => 'dateCheck'
]);

$layoutContent = renderTemplate('templates/layout.php', [
    'titlePage' => 'Дела в порядке',
    'content' => $pageContent,
    'tasks' => $tasks,
    'projects' => $projects,
    'hourInDay' => 24
]);

print($layoutContent);
