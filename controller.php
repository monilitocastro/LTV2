<?php
class Controller
{
    private $model;

    public function Initial(){
        
    }
    public function FailedAuthenticate(){}
    public function PassedAuthenticate(){}
    public function FailedSignUpNewUser(){}
    public function PassedSignUpNewUser(){}
    public function Logout(){}
    public function FailedUpdateUserInformation(){}
    public function PassedUpdateUserInformation(){}
    public function FailedViewAccountBalance(){}
    public function PassedViewAccountBalance(){}
    public function FailedViewPrescription(){}
    public function PassedViewPrescription(){}
    public function FailedScheduleAppointment(){}
    public function PassedScheduleAppointment(){}
    public function FailedCancelAppointment(){}
    public function PassedCancelAppointment(){}
    public function PassedViewAppointment(){}
    public function FailedViewAppointment(){}
    public function FailedPrescribeMedication(){}
    public function PassedPrescribeMedication(){}
    public function FailedWritePhysiciansExam(){}
    public function PassedWritePhysiciansExam(){}
    public function FailedWriteNursesNotes(){}
    public function PassedWriteNursesNotes(){}
    public function FailedCreateDisease(){}
    public function FailedModifyDisease(){}
    public function PassedModifyDisease(){}
    public function PassedViewMedicalRecord(){}
    public function FailedViewMedicalRecord(){}
    public function FailedMakePayment(){}
    public function PassedMakePayment(){}
    public function PassedViewLabHistory(){}
    public function FailedViewLabHistory(){}
    public function PassedScheduleLabTest(){}
    public function FailedScheduleLabTest(){}
    public function PassedCreateEmergencyFirstContact(){}
    public function FailedCreateEmergencyFirstContact(){}

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