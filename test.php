<?php
/**
 * Created by PhpStorm.
 * User: mcastro
 * Date: 30/04/15
 * Time: 2:12 PM
 *
 * GOOD
 */
include_once "model.php";

$model = new Model("Unknown");
$model->Attributes['UserID'] = "1";
$model->Attributes['PatientID'] = "1";
$model->getAllStates();
$model->getAllUserAttributesFromDB();
$freeform = "This free form text should describe the type of lab test take.";
$PhysicianID = "108";
$patient_ID = "1";
$time = date('Y-m-d H:i:s','1299762201428');

$model->UseCase_ScheduleLabTest($freeform, $PhysicianID, $patient_ID, $time);


print_r($model->dataScheduleLabTest);
//print_r($model->Attributes);

/**
 *
 */

?>

