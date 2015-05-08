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
$model->Attributes['UserID'] = "1";
$model->Attributes['PatientID'] = "1";
$model->getAllStates();
$model->getAllUserAttributesFromDB();
$model->UseCase_ViewLabHistory();
print_r($model->dataViewLabHistory);
//print_r($model->Attributes);

/**
 *PHP Warning:  mysqli::close(): Couldn't fetch mysqli in /home/mcastro/Repos/LTV2/model.php on line 633
Array
(
    [UserID] => 107
    [UserType] => Technician
    [Name] => Morgan Jr John
    [Username] => mmjj
    [Password] => 1
    [Address] => 123 Morgan St
    [AccountID] => 107
    [DescriptionID] => 2
    [FreeFormText] => Testing Lab test viewing
    [AppointmentID] => 2
    [PhysicianID] => 107
    [PatientID] => 1
    [Time] => 2015-04-30 20:09:34
)

 */

?>

