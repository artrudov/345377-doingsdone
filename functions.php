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
