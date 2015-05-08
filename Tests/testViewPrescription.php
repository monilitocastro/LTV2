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
$model->Attributes['PatientID'] = "1";
$model->getAllStates();
$model->getAllUserAttributesFromDB();
$model->UseCase_ViewPrescription();
print_r($model->dataViewPrescription);
print_r($model->Attributes);

/**PHP Warning:  mysqli::close(): Couldn't fetch mysqli in /home/mcastro/Repos/LTV2/model.php on line 597
Array
(
    [0] => Array
        (
            [0] => Ibuprofen
            [1] => 2015-04-30 22:52:27
        )

    [1] => Array
        (
            [0] => Tylenol
            [1] => 2015-04-26 21:39:56
        )

)
Array
(
    [PatientName] => John Smith
    [opState] => PassedViewPrescription
    [UserID] => 1
    [PatientID] => 1
    [UserType] => Patient
    [Name] => John Smith
    [Username] => jsmith
    [Password] => 1
    [Address] => 123 Main St
    [AccountID] => 1
)


*/
?>
