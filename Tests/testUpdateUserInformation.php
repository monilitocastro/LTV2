<?php
/**
 * Created by PhpStorm.
 * User: mcastro
 * Date: 30/04/15
 * Time: 2:12 PM
 */

include_once "../model.php";

$model = new Model("Unknown");
$model->Attributes['UserID'] = "45";
$model->getAllStates();
$model->getAllUserAttributesFromDB();
$model->UseCase_UpdateUserInformation('Rhonda K Smith', 'rsmith', '1', '123 Main St.');
//print_r($dataUpdateUserInformation);
//print_r($model->ViewStates);
print_r($model->Attributes);

/**
PHP Warning:  mysqli::close(): Couldn't fetch mysqli in /home/mcastro/Repos/LTV2/model.php on line 587
Array
(
    [PatientName] => Rhonda Smith
    [opState] => PassedUpdateUserInformation
    [UserID] => 45
    [UserType] => Patient
    [Name] => Rhonda Smith
    [Username] => rmsith
    [Password] => 1
    [Address] => 123 Main Street
    [AccountID] => 45
)


*/
?>
