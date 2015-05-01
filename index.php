<?php
require_once("model.php");
require_once("view.php");
require_once("controller.php");
$model = new Model("Unknown");

$model->getAllStates();
if($model->Attributes['UserID'] != 'Unknown'){
    $model->getAllUserAttributesFromDB();
}
$controller = new Controller($model);
$controller->{$model->Attributes['opState']}();
$view = new View($controller, $model);
$model->saveAllAttributesToCookies();
echo $view->outputWebPage();
?>

