<?php
session_start();
unset($_SESSION['user'][0]);

header('Location: /guest.php');
exit();
