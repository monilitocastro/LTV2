<?php
/**
 * Created by PhpStorm.
 * User: mcastro
 * Date: 30/04/15
 * Time: 2:12 PM
 */
 $PatientID = '1';
 $queryString=<<<EOT
SELECT * FROM Appointment WHERE Appointment.PatientID=$PatientID
     ORDER BY Time;
EOT;

include_once "../model.php";

$model = new Model("Unknown");
$model->Attributes['UserID'] = "1";
$apptID = '8';
$model->getAllStates();
$model->getAllUserAttributesFromDB();
$model->UseCase_CancelAppointment($apptID);
print_r($model->ViewState);
print_r($model->Attributes);

/**
PHP Warning:  mysqli::close(): Couldn't fetch mysqli in /home/mcastro/Repos/LTV2/model.php on line 589
Array
(
    [0] => Array
        (
            [AppointmentID] => 7
            [PhysicianID] => 107
            [PatientID] => 1
            [Time] => 2015-04-30 21:16:33
            [DescriptionID] => 7
        )

    [1] => Array
        (
            [AppointmentID] => 8
            [PhysicianID] => 107
            [PatientID] => 1
            [Time] => 2015-04-30 21:16:53
            [DescriptionID] => 8
        )

)

*/
?>
