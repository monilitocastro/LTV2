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

    //public function Logout(){}
    public function ViewAccountBalance(){
        $this->model->UseCase_ViewAccountBalance();
        $this->model->showThis = "Your Account Balance";
        $this->makeThese[] = array("SINGLEVALUE", "dataViewAccountBalance");
    }
    public function ViewPrescription(){
        //$this->dataViewPrescription[$index] = array($RxName, $RxTimestamp);
        $this->model->UseCase_ViewPrescription();
        $this->model->showThis = "View Prescription";
        array_unshift($this->model->dataViewPrescription, array(0=>"Drug name",1=>"Date and Time") );
        $this->makeThese[] = array("TABLE", "dataViewPrescription");

    }
    public function DefinePatient(){
        $this->model->Attributes['NameToSearchFor'] = '';
        $this->redirect('SearchFor');
    }
    public function SearchFor(){
        if(isset($this->model->Attributes['PatientID'])){
            $this->model->getPatientName();
        }
        $createTable = ($this->model->Attributes['NameToSearchFor'] != '');
        $this->model->UseCase_SearchFor();
        $this->makeThese[] = array("IB", "Name (% wildcard)","NameToSearchFor", $this->model->Attributes['NameToSearchFor']);
        $this->makeThese[] = array('BTN', 'Submit','Submit', 'Search', 'SearchFor');
        if($createTable==true ){
            $this->showThis = 'Found accounts: <br/>&nbsp;<br/>';
            array_unshift($this->model->dataSearchFor, array("Select","Name","Username","Address") );
            $this->makeThese[] = array("RADIOTABLE", "dataSearchFor");
        }else{
            $this->showThis = 'Please enter name';
        }
        $this->makeThese[] = array('BTN', 'Submit','Button', 'Set Patient', '');
    }
    public function SetPatient(){

    }
    public function CancelAppointment(){}
    public function ViewAppointments(){

    }
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
    public function ScheduleLabTest(){}

    public function CheckCredentials(){
        if(!$this->model->UseCase_Authenticate()){
            $this->redirect('FailedAuthenticate');
            //return "AUTH FAILED";
        }else{
            $this->redirect('PassedAuthenticate');
            //return $this->model->ViewStates['Authenticate'];
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

    public function SignUpNewUser(){
        $this->model->showThis = "Please enter information";
        $this->makeThese[] = array('IB', 'Name: ', 'Name');
        $this->makeThese[] = array('IB', 'User name: ', 'Username');
        $this->makeThese[] = array('IB', 'Password: ', 'Password');
        $this->makeThese[] = array('IB', 'Address: ', 'Address');
        $this->makeThese[] = array('BTN', 'Reset','Reset', 'Reset', '');
        $this->makeThese[] = array('BTN', 'Submit','Submit', 'Sign Up', 'CheckSignUpNewUser');  //arglist type, value, label


    }
    public function CheckSignUpNewUser(){
        $Name=$_POST['Name'];
        $Username=$_POST['Username'];
        $Password=$_POST['Password'];
        $Address=$_POST['Address'];
        if($this->model->UseCase_SignUpNewUser($Name, $Username, $Password, $Address)){
            $this->redirect('PassedSignUpNewUser');
        }else{
            $this->redirect('FailedSignUpNewUser');
        }
    }

    public function UpdateAccountInformation(){
        $this->model->UseCase_ViewBasicAccountInformation();
        $info = $this->model->dataViewBasicAccountInformation;
        $this->model->showThis = "Please enter information";
        $this->makeThese[] = array('IB', 'Name: ', 'Name', $info['Name']);
        $this->makeThese[] = array('IB', 'User name: ', 'Username', $info['Username']);
        $this->makeThese[] = array('IB', 'Password: ', 'Password', $info['Password']);
        $this->makeThese[] = array('IB', 'Address: ', 'Address', $info['Address']);
        $this->makeThese[] = array('BTN', 'Reset','Reset', 'Reset', '');
        $this->makeThese[] = array('BTN', 'Submit','Submit', 'Update', 'CheckUpdateAccountInformation');  //arglist type, value, label


    }
    public function CheckUpdateAccountInformation(){
        $Name=$_POST['Name'];
        $Username=$_POST['Username'];
        $Password=$_POST['Password'];
        $Address=$_POST['Address'];
        if($this->model->UseCase_UpdateUserInformation($Name, $Username, $Password, $Address)){
            $this->redirect('PassedUpdateUserInformation');
        }else{
            $this->redirect('FailedUpdateUserInformation');
        }
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
        //redirect("PassedAuthenticate");
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