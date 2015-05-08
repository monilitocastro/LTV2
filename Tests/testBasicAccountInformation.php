<?php
/**
 * Created by PhpStorm.
 * User: mcastro
 * Date: 30/04/15
 * Time: 2:12 PM
 *
 * 
 */

include_once "../model.php";

$model = new Model("Unknown");
$model->Attributes['Username'] = "jsmith";
$model->Attributes['Password'] = "1";
$model->Attributes['UserID'] = "1";
$model->getAllStates();
$model->getAllUserAttributesFromDB();
$model->UseCase_ViewBasicAccountInformation();
print_r($model->dataViewBasicAccountInformation);
print_r($model->Attributes);

/**

 */

?>

