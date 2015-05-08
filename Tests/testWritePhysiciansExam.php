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
$freeform = 'THIS IS A TEST FOR WRITE PHYSICIANS EXAM.';
$EmplID = '108';
$model->UseCase_WritePhysiciansExam($freeform, $EmplID);
//print_r($dataUpdateUserInformation);
//print_r($model->ViewStates);
print "$model->Attributes['PatientID'] = ".$model->Attributes['PatientID'];
print_r($model->Attributes);

/**
PHP Warning:  mysqli::close(): Couldn't fetch mysqli in /home/mcastro/Repos/LTV2/model.php on line 590
PHP Notice:  Array to string conversion in /home/mcastro/Repos/LTV2/Tests/testWritePhysiciansExam.php on line 20
Array['PatientID'] = 1Array
(
    [PatientName] => John Smith
    [opState] => PassedWritePhysiciansExam
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
