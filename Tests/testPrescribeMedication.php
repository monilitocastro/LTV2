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
$apptID = '8';
$model->getAllStates();
$model->getAllUserAttributesFromDB();
$drugName = 'Druganedrine';
$quantity = 1;
$refills = 2;
$freeform = 'This is a test';
$EmplID = '108';
$PatientID = '1';
$SymptID='1';
$Timestamp = "02-22-2015 10:23:15";
$model->UseCase_PrescribeMedication($drugName, $quantity, $refills, $freeform, $EmplID, $PatientID, $SymptID, $Timestamp);
print_r($model->ViewStates);
print_r($model->Attributes);

/**
NOT PASSED
This SQL works:
START TRANSACTION;
INSERT INTO Prescription(Name, Quantity, Refills) VALUES("Drugazine", 2, 1);
SET @Rx_ID = LAST_INSERT_ID();
INSERT INTO Description(FreeFormText) VALUES('Take this every 24 hours.');
SET @Desc_ID = LAST_INSERT_ID();
INSERT INTO MedicalRecord(EmplID, PatientID, SymptID, Timestamp, DescriptionID,  RxNumber) VALUES ( 108,  1, 1, NOW(), @Desc_ID,  @Rx_ID);
COMMIT;
)

*/
?>
