<?php
/**
 * Created by PhpStorm.
 * User: mcastro
 * Date: 30/04/15
 * Time: 2:12 PM
 *
 * GOOD
 */
include_once "../model.php";

$model = new Model("Unknown");
$model->Attributes['UserID'] = "1";
$model->Attributes['PatientID'] = "1";
$model->getAllStates();
$model->getAllUserAttributesFromDB();
$freeform = "FROM TEST PHP LABTEST";
$PhysicianID = "107";
$patient_ID = "2";

$year = '2014';
$month= '02';
$days  = '21';
$hour = '11';
$minutes='18';
$seconds='56';

$time = $year.'-'.$month.'-'.$days.' '.
        $hour.':'.$minutes.':'.$seconds;
print "***************".$time;
$model->UseCase_ScheduleLabTest($patient_ID , $PhysicianID , "NOW()" , $freeform);
print_r($model->ViewStates);
//($freeform, $PhysicianID, $patient_ID, $time);

print_r($model->dataScheduleLabTest);
//print_r($model->Attributes);

/**
 *
 */

?>

