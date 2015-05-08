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
$model->UseCase_ViewMedicalRecord();
print_r($model->dataViewMedicalRecord);
//
/**
 *Array
(
    [0] => Array
        (
            [MR_ID] => 1
            [EmplID] => 108
            [PatientID] => 1
            [SymptID] => 
            [Trtmt_ID] => 
            [Folder_path] => 
            [Timestamp] => 2015-04-26 21:39:56
            [DescriptionID] => 
            [RxNumber] => 1
        )

    [1] => Array
        (
            [MR_ID] => 2
            [EmplID] => 108
            [PatientID] => 1
            [SymptID] => 
            [Trtmt_ID] => 
            [Folder_path] => 
            [Timestamp] => 2015-04-30 22:30:49
            [DescriptionID] => 
            [RxNumber] => 
        )

)

 */

?>

