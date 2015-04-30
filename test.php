<?php
/**
 * Created by PhpStorm.
 * User: mcastro
 * Date: 30/04/15
 * Time: 2:12 PM
 */
include_once "model.php";

$model = new Model("Unknown");
$model->Attributes['UserID'] = "1";
$model->Attributes['PatientID'] = "1";
$model->getAllStates();
$model->getAllUserAttributesFromDB();
$model->UseCase_ViewMedicalRecord();
print_r($model->dataViewMedicalRecord);
print_r($model->Attributes);

?>