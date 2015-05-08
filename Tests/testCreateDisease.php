<?php
/**
 * Created by PhpStorm.
 * User: mcastro
 * Date: 30/04/15
 * Time: 2:12 PM
 */

include_once "../model.php";

$model = new Model("Unknown");
$model->Attributes['UserID'] = "1";
$model->getAllStates();
$model->getAllUserAttributesFromDB();
$patient_id = '1';
$empl_id   = '108';
$symptom_id = '1';
$treatment_id = '1';
$description = 'Need to ask CDC about this one.';
$model->UseCase_CreateDisease($patient_id, $empl_id,$symptom_id,$treatment_id,$description);
//print_r($dataUpdateUserInformation);
//print_r($model->ViewStates);
print_r($model->Attributes);

/**
PHP Warning:  mysqli::close(): Couldn't fetch mysqli in /home/mcastro/Repos/LTV2/model.php on line 591
Array
(
    [PatientName] => John Smith
    [opState] => PassedCreateDisease
    [UserID] => 1
    [UserType] => Patient
    [Name] => John Smith
    [Username] => jsmith
    [Password] => 1
    [Address] => 123 Main St
    [AccountID] => 1
    [PatientID] => 1
)

*/
?>
