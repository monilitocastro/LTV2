<?php
class Controller
{
    private $model;

    public function Initial(){
        $this->model->showThis = $this->model->messages['Initial'];
    }
    public function FailedAuthenticate(){
        $this->model->showThis = $this->model->messages['FailedAuthenticate'];
    }
    public function PassedAuthenticate(){
        $this->model->showThis = $this->model->messages['PassedAuthenticate'];
    }
    public function FailedSignUpNewUser(){
        $this->model->showThis = $this->model->messages['FailedSignUpNewUser'];
    }
    public function PassedSignUpNewUser(){
        $this->model->showThis = $this->model->messages['PassedSignUpNewUser'];
    }
    public function Logout(){
        $this->model->showThis = $this->model->messages['Logout'];
    }
    public function FailedUpdateUserInformation(){
        $this->model->showThis = $this->model->messages['FailedUpdateUserInformation'];
    }
    public function PassedUpdateUserInformation(){
        $this->model->showThis = $this->model->messages['PassedUpdateUserInformation'];
    }
    public function FailedViewAccountBalance(){
        $this->model->showThis = $this->model->messages['FailedViewAccountBalance'];
    }
    public function PassedViewAccountBalance(){
        $this->model->showThis = $this->model->messages['PassedViewAccountBalance'];
    }
    public function FailedViewPrescription(){
        $this->model->showThis = $this->model->messages['FailedViewPrescription'];
    }
    public function PassedViewPrescription(){
        $this->model->showThis = $this->model->messages['PassedViewPrescription'];
    }
    public function FailedScheduleAppointment(){
        $this->model->showThis = $this->model->messages['FailedScheduleAppointment'];
    }
    public function PassedScheduleAppointment(){
        $this->model->showThis = $this->model->messages['PassedScheduleAppointment'];
    }
    public function FailedCancelAppointment(){
        $this->model->showThis = $this->model->messages['FailedCancelAppointment'];
    }
    public function PassedCancelAppointment(){
        $this->model->showThis = $this->model->messages['PassedCancelAppointment'];
    }
    public function PassedViewAppointment(){
        $this->model->showThis = $this->model->messages['PassedViewAppointment'];
    }
    public function FailedViewAppointment(){
        $this->model->showThis = $this->model->messages['FailedViewAppointment'];
    }
    public function FailedPrescribeMedication(){
        $this->model->showThis = $this->model->messages['FailedPrescribeMedication'];
    }
    public function PassedPrescribeMedication(){
        $this->model->showThis = $this->model->messages['PassedPrescribeMedication'];
    }
    public function FailedWritePhysiciansExam(){
        $this->model->showThis = $this->model->messages['FailedWritePhysiciansExam'];
    }
    public function PassedWritePhysiciansExam(){
        $this->model->showThis = $this->model->messages['PassedWritePhysiciansExam'];
    }
    public function FailedWriteNursesNotes(){
        $this->model->showThis = $this->model->messages['FailedWriteNursesNotes'];
    }
    public function PassedWriteNursesNotes(){
        $this->model->showThis = $this->model->messages['PassedWriteNursesNotes'];
    }
    public function FailedCreateDisease(){
        $this->model->showThis = $this->model->messages['FailedCreateDisease'];
    }
    public function PassedCreateDisease(){
        $this->model->showThis = $this->model->messages['PassedCreateDisease'];
    }
    public function FailedModifyDisease(){
        $this->model->showThis = $this->model->messages['FailedModifyDisease'];
    }
    public function PassedModifyDisease(){
        $this->model->showThis = $this->model->messages['PassedModifyDisease'];
    }
    public function PassedViewMedicalRecord(){
        $this->model->showThis = $this->model->messages['PassedViewMedicalRecord'];
    }
    public function FailedViewMedicalRecord(){
        $this->model->showThis = $this->model->messages['FailedViewMedicalRecord'];
    }
    public function FailedMakePayment(){
        $this->model->showThis = $this->model->messages['FailedMakePayment'];
    }
    public function PassedMakePayment(){
        $this->model->showThis = $this->model->messages['PassedMakePayment'];
    }
    public function PassedViewLabHistory(){
        $this->model->showThis = $this->model->messages['PassedViewLabHistory'];
    }
    public function FailedViewLabHistory(){
        $this->model->showThis = $this->model->messages['FailedViewLabHistory'];
    }
    public function PassedScheduleLabTest(){
        $this->model->showThis = $this->model->messages['PassedScheduleLabTest'];
    }
    public function FailedScheduleLabTest(){
        $this->model->showThis = $this->model->messages['FailedScheduleLabTest'];
    }
    public function PassedCreateEmergencyFirstContact(){
        $this->model->showThis = $this->model->messages['PassedCreateEmergencyFirstContact'];
    }
    public function FailedCreateEmergencyFirstContact(){
        $this->model->showThis = $this->model->messages['FailedCreateEmergencyFirstContact'];
    }

    public function UserTypeIsNowKnown(){
        if(isset($this->model->Attributes['UserType'])) {
            $this->model->define($this->model->Attributes['UserType']);
        }
    }

    public function askUserToChooseDifferently(){
        $this->model->opState = "showThis";
        $this->model->showThis = "<br/>&nbsp;<br/><center>Sorry but that user name or password are taken. Please choose another</center>";
    }

    public function welcomeTheUser(){
        $Name = $this->model->Attributes['Name'];
        $Address = $this->model->Attributes['Address'];
        $Username = $this->model->Attributes['Username'];
        $Password = $this->model->Attributes['Password'];
        $this->model->opState = "showThis";
        $this->model->showThis = "<br/>&nbsp;<br/><center>Welcome to LifeThread, ".$Name.".<br/> You live at ". $Address .".</center>";
        $this->model->FromCookies['UserID'] = $this->model->get_UserID_fromDB($Username, $Password);
        $this->model->toCookie("UserID", $this->model->FromCookies['UserID'] );
    }

    public function registerTheUser(){
        $Username = $this->model->Attributes['Username'];
        $Name = $this->model->Attributes['Name'];
        $Address = $this->model->Attributes['Address'];
        $Password = $this->model->Attributes['Password'];
        $this->model->updateUserInformation($Name, $Username, $Password, $Address);
    }
    public function updateAccountInformation(){
        $Username = $this->model->Attributes['Username'];
        $Name = $this->model->Attributes['Name'];
        $Address = $this->model->Attributes['Address'];
        $Password = $this->model->Attributes['Password'];
        $this->model->signUpNewUser($Name, $Username, $Password, $Address);
    }

    public function __construct(&$model){
        $this->model = &$model;
    }

    /*      Example of what should be here.

    public function clicked() {
        $this->model->string = "Updated Data, thanks to MVC and PHP!";
    }
    */
}
?>