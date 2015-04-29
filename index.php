<?php
require_once("model.php");
require_once("view.php");
require_once("controller.php");
$model = new Model("Unknown");
$controller = new Controller($model);
$view = new View($controller, $model);
?>

