<?php
require_once __DIR__ . '/CONTROLLER/usercontroller.php';
$ctrl = new UserController();
$ctrl->logout();
header('Location: index.php');
exit;
