<?php
class Controller
{
    private $model;
    public $makeThese;
    public function Authenticate(){
        $this->model->showThis =  "Please fill in the form to login to the secure server.";
        $this->makeThese[] = array('IB', 'Username: ', 'Username');
        $this->makeThese[] = array('IB', 'Password: ', 'Password');
        $this->makeThese[] = array('BR');
        $this->makeThese[] = array('BTN', 'Reset','Reset', 'Reset', '');
        $this->makeThese[] = array('BTN', 'Submit','Submit', 'Login', 'CheckCredentials');  //arglist type, value, label

    }


    public function SignUpNewUser(){
        $this->model->showThis =  "Signup TESTING THIS STRING NEEDS TO BE DELETED. EACH OF THE USE CASE FIRST STEPS MUST HAVE THEIR OWN METHOD HERE!";
    }

    //public function Logout(){}
    public function ViewAccountBalance(){}
    public function ViewPrescription(){}
    //public function ScheduleAppointment(){}
    public function CancelAppointment(){}
    public function ViewAppointments(){}
    public function PrescribeMedication(){}
    public function WritePhysiciansExam(){}
    public function WriteNursesNotes(){}
    public function CreateDisease(){}
    public function ModifyDiseaseThread(){}
    public function ViewMedicalRecord(){}
    public function MakePayment(){}
    public function ViewLabHistory(){}
    public function CreateSpecialistReferral(){}
    public function CreateEmergencyFirstContact(){}
    public function DefinePatient(){}
    public function ScheduleLabTest(){}

    public function CheckCredentials(){
        if(!$this->model->UseCase_Authenticate()){
            $this->redirect('FailedAuthenticate');
            return "AUTH FAILED";
        }else{
            //print"AUTH GOOD";
            $this->redirect('PassedAuthenticate');
            return $this->model->ViewStates['Authenticate'];
        }
    }

    public function redirect($opState){
        $this->model->Attributes['opState']=$opState;
        $this->model->saveAllAttributesToCookies();
        header('Location: index.php');
        die();
    }

    public function ScheduleAppointment(){
        return "";
    }

    public function UpdateAccountInformation(){
    }


    public function Initial(){
        $this->model->showThis = $this->model->messages['Initial'];
    }
    public function FailedAuthenticate(){
        $this->model->showThis = $this->model->messages['FailedAuthenticate'];
    }
    public function PassedAuthenticate(){
        $this->model->showThis = $this->model->messages['PassedAuthenticate'];
    }
    public function FailedDefinePatient(){
        $this->model->showThis = $this->model->messages['FailedDefinePatient'];
    }
    public function PassedDefinePatient(){
        $this->model->showThis = $this->model->messages['PassedDefinePatient'];
    }
    public function FailedSignUpNewUser(){
        $this->model->showThis = $this->model->messages['FailedSignUpNewUser'];
    }
    public function PassedSignUpNewUser(){
        $this->model->showThis = $this->model->messages['PassedSignUpNewUser'];
    }
    public function Logout(){
        $this->model->showThis =  $this->model->messages['Logout'];
        $this->model->UseCase_Logout();
        $this->model->redefine();
        //print $this->model->messages['Logout'];
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

    public function UpdateAccountInformation2(){
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