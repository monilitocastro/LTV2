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
$model->Attributes['UserID'] = "1";
$model->Attributes['NameToSearchFor']='smith';
$model->getAllStates();
$model->getAllUserAttributesFromDB();
$model->UseCase_SearchFor();
print_r($model->dataSearchFor);

/**

 */

?>

