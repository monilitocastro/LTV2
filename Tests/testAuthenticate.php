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
$model->getAllStates();
$model->getAllUserAttributesFromDB();
$model->UseCase_Authenticate();
print_r($model->dataAuthenticate);
print_r($model->Attributes);

/**
PHP Warning:  mysqli::close(): Couldn't fetch mysqli in /home/mcastro/Repos/LTV2/model.php on line 589
1Array
(
    [PatientName] => John Smith
    [opState] => PassedAuthenticate
    [Username] => jsmith
    [Password] => 1
    [UserID] => 1
    [UserType] => Patient
    [Name] => John Smith
    [Address] => 123 Main St
    [AccountID] => 1
)


)

 */

?>

