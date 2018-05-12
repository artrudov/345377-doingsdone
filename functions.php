<?php
function renderTemplate($templatePath, $data) {
    if (!file_exists($templatePath)) {
        return '';
    }

    ob_start();

    extract($data);

    require($templatePath);
    return ob_get_clean();
};

function dateCheck($taskDate) {
    if ($taskDate == 'Нет') {
        return '';
    }

    $secInHour = 3600;
    $hourInDay = 24;
    $currentTS = time();
    $taskTS = strtotime($taskDate);

    $differenceHour = ($taskTS - $currentTS) / $secInHour;

    return ($differenceHour < $hourInDay) ? 'task--important' : '';
};
