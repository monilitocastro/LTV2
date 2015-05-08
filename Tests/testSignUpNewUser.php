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
//$model->Attributes['Username'] = "jsmith";
//$model->Attributes['Password'] = "1";
$Name='John Test'; 
$Username='jjtest';
$Password='1';
$Address = '123 Test Ave';
$model->UseCase_SignUpNewUser($Name, $Username, $Password, $Address);
print_r($model->dataSignUpNewUser);
print_r($model->Attributes);

/**
PHP Notice:  Undefined property: Model::$dataSignUpNewUser in /home/mcastro/Repos/LTV2/Tests/testSignUpNewUser.php on line 21
Array
(
    [PatientName] => _unknown
    [opState] => PassedSignUpNewUser
)


 */

?>

