<?php
class Model
{
    public $attributes;

    public $servername;
    private $conn;
    public $showThis;
    public $Attributes;                  //NOTE: $this->Attributes['opState'] is strictly used for pages presented to user.
    public $AttributesForCookiesSave;

    /**
    public $dataSignUpNewUser;
    public $dataScheduleAppointment;
    public $dataCancelAppointment;
     * The following public variables are data stores for the use case methods.
     * A use case method can have a dedicated public variable.
     * The types are all different and not all use cases will have one.
     * For example dataAuthenticated is a boolean and dataViewPrescription is a 2-D array.
     */
    public $dataAuthenticate;
    //public $dataLogout; //There shouldn't be a need for this.
    public $dataPrescribeMedication;
    public $dataWritePhysiciansExam;
    public $dataCreateDisease;
    public $dataModifyDiseaseThread;
    public $dataViewMedicalRecord;
    public $dataViewPrescription;
    public $dataViewAccountBalance;
    public $dataMakePayment;
    public $dataScheduleLabTest;
    public $dataViewLabHistory;
    public $dataCreateSpecialistReferral;
    public $dataUpdateAccountInformation;
    public $dataCreateEmergencyFirstContact;
    public $dataWriteNursesNotes;
    public $dataViewAppointments;


    /**
     *   The following array holds the view states.
     * Each variable holds strings such names of buttons, links, component labels and so on
     * The idea is that one particular part of the screen may have a single variable dedicated towards keeping a particular text,
     * label, button states and so on.
     * The view will use these variables to concatenate with other strings which define the html.
     * It is made private so that it must be accessed by setters and getters.
     * If a particular value is not set, an error message will printed.
     */
    private $ViewStates;



    public function state($varname){
        if(!in_array($varname, $this->ViewStates)){
            print "ERROR: $varname is not in the ViewState.";
            return "ERROR: $varname is not in the ViewState.";
        }
        return $this->ViewStates[$varname];
    }

    public function setState($key, $value){
        $this->ViewStates[$key]=$value;
    }

    public function getAllStates(){
        $this->getAllCookies();
        $this->getAllPOST();
        $this->getAllGET();
    }

    private function getAllCookies(){
        foreach ($_COOKIE as $key=>$val)
        { 
            $this->Attributes[$key] = $val;
        }
    }

    private function getAllPOST(){
        foreach ($_POST as $key=>$val)
        {
            $this->Attributes[$key] = $val;
        }
    }
    private function getAllGET(){
        foreach ($_POST as $key=>$val)
        {
            $this->Attributes[$key] = $val;
        }
    }

    public function UseCase_Authenticate(){
        $Username = $this->Attributes['Username'];
        $Password = $this->Attributes['Password'];
        if(!$this->get_UserID_fromDB($Username, $Password)){
            $this->dataAuthenticate=false;
            $this->Attributes['opState']="FailedAuthenticate";
        }else{
            $this->dataAuthenticate=true;
            $this->Attributes['opState']="PassedAuthenticate";
        }

        if($this->Attributes['UserID'] =='ERROR'){
            $this->ViewStates['PassFailAuthenticate'] = "Sorry wrong username password combination.";
            return false;
        }else{
            $this->ViewStates['PassFailAuthenticate'] = "Welcome to LifeThread";
        }
        $this->toCookie("UserID",$this->UserAttributes['UserID'] );
        $this->getAllUserAttributesFromDB();
        $this->define($this->Attributes['UserType']);
        return true;
    }

    public function UseCase_SignUpNewUser($Name, $Username, $Password, $Address){
        //sign up new user method
        $this->connectToDB();
        $queryString =<<<EOT
CALL sign_up_patient( $Name ,  $Username ,  $Password , $Address );
EOT;

        if(!$this->conn->query($queryString)){
            print "Errormessage: " . $this->conn->error;
            $this->closeDB();
            $this->ViewStates['SignUpNewUser'] = 'Sorry please try a different username and password combination';
            $this->Attributes['opState']="FailedSignUpNewUser";
            return false;
        }
        $this->closeDB();
        $this->ViewStates['SignUpNewUser'] = 'Welcome to your hospital\'s LifeThread Electronic Medical Record System';
        $this->Attributes['opState']="PassedSignUpNewUser";
        return true;

    }

    public function UseCase_Logout(){
        unset($this->Attributes);
        $this->Attributes = array('UserType'=> 'Unknown',
            'UserID'  => 'Unknown');
        $this->Attributes['opState'] = 'Logout';
        $this->ViewState['LoggedOut'] = 'Thank you for coming.';
        return true;
    }
    public function UseCase_UpdateUserInformation($Name, $Username, $Password, $Address){
        //this will be alot like sign up new user method
        $this->connectToDB();
        $userid = $this->UserAttributes['UserID'];
        $queryString =<<<EOT
UPDATE User
SET Name='$Name', Username='$Username', Password='$Password', Address='$Address'
WHERE User.UserID='$userid';
EOT;

        if(!$this->conn->query($queryString)){
            print "Errormessage: " . $this->conn->error;
            $this->closeDB();
            $this->Attributes['opState']="FailedUpdateUserInformation";
            return false;
        }
        $this->closeDB();
        $this->Attributes['opState']="PassedUpdateUserInformation";
        return true;
    }

    public function UseCase_ViewAccountBalance(){
        $resultString = "";
        $this->connectToDB();
        $queryString = <<<EOT
SELECT Balance FROM Account NATURAL JOIN User WHERE UserID='$this->PatientID';
EOT;
        $resultString="";
        $Balance = "";
        $result = $this->conn->query($queryString);
        if(!result){
            $this->Attributes['opState']="FailedViewAccountBalance";
            $this->ViewState['ViewAccountBalance'] = 'No balance.';
            $this->dataViewAccountBalance = 'ERROR';
            print "ERROR: null result in UseCase_ViewAccountBalance";
            return false;
        }
        while($row = $result->fetch_assoc())
        {
            $this->UserAttributes=$row;
            $Balance=$row['Balance'];
        }
        $resultString = $resultString."</div>";
        $this->Attributes['opState']="PassedViewAccountBalance";
        $this->ViewState['ViewAccountBalance'] = 'Balance is ';
        $this->dataViewAccountBalance = $Balance;
        return true;
    }

    public function UseCase_ViewPrescription(){
        $resultString = "";
        $this->connectToDB();
        $queryString = <<<EOT
SELECT Prescription.Name, MedicalRecord.Timestamp
        FROM Prescription, MedicalRecord
        WHERE MedicalRecord.PatientID='$this->PatientID' AND MedicalRecord.RxNumber = Prescription.RxNumber
        ORDER BY MedicalRecord.Timestamp DESC;
EOT;
        $result = $this->conn->query($queryString);
        $this->closeDB();

        if(!result){
            $this->Attributes['opState']="FailedViewPrescription";
            $this->ViewState['ViewAccountBalance'] = 'No Prescription';
            print "ERROR: null result in UseCase_ViewPrescription";
            return false;
        }
        $index = 0;
        while($row = $result->fetch_assoc())
        {
            $RxName=$row['Name'];
            $RxTimestamp=$row['Timestamp'];
            $this->dataViewPrescription[$index] = array($RxName, $RxTimestamp);
            $index++;
        }
        $this->Attributes['opState']="PassedViewPrescription";
        $this->ViewState['ViewAccountBalance'] = 'Prescription History';
        return true;
    }

    public function UseCase_ScheduleAppointment($freeform, $workerID, $patientID, $time){
        $queryString =<<<EOT
CALL schedule_appointment($patientID, $workerID, $time, $freeform);
EOT;
        $this->connectToDB();
        $result = $this->conn->query($queryString);
        $this->closeDB();
        if(!$result){
            $ViewState['ScheduleAppointment'] ="Error: Unable to schedule";
            $this->Attributes['opState']="FailedScheduleAppointment";
            return false;
        }
        $this->ViewState['ScheduleAppointment'] = "Scheduling appointment for physician.";
        $this->Attributes['opState']="PassedScheduleAppointment";
        return true;
    }

    public function UseCase_CancelAppointment($apptID){
        $queryString =<<<EOT
CALL cancel_appointment($apptID);
EOT;
        $this->connectToDB();
        $result = $this->conn->query($queryString);
        $this->closeDB();
        if(!$result){
            $ViewState['CancelAppointment'] ="Error: Unable to cancel";
            $this->Attributes['opState']="FailedCancelAppointment";
            return false;
        }
        $this->ViewState['CancelAppointment'] = "Canceling appointment for physician.";
        $this->Attributes['opState']="PassedCancelAppointment";
        return true;
    }

    public function UseCase_ViewAppointments(){
        //This can be used to view upcoming lab tests as well as view appointments with doctors
        $PatientID = $this->Attributes['PatientID'];
        $queryString =<<<EOT
CALL SELECT * FROM Appointments WHERE Appointment.PatientID=$PatientID
     ORDER BY Time;
EOT;
        $this->connectToDB();
        $result = $this->conn->query($queryString);
        $this->closeDB();
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            foreach($row as $key => $value){
                $this->dataViewAppointments[$key] = $value;
            }
            $this->ViewState['ViewAppointments'] = "Viewing appointment for physician.";
            $this->Attributes['opState']="PassedViewAppointments";

        }else{
            print "ERROR: model->UseCase_ViewAppointments";
            $ViewState['ViewAppointment'] ="Error: Unable to view appointments";
            $this->Attributes['opState']="FailedViewAppointments";
            return false;
        }
        return true;
    }

    public function UseCase_PrescribeMedication($drugName, $quantity, $refills, $freeform, $EmplID, $PatientID, $SymptID, $Timestamp){
        //for queryString first insert into PrescriptionTable then MedicalRecord
        $queryString =<<<EOT
START TRANSACTION;
INSERT INTO Prescription(Name, Quantity, Refills) VALUES($drugName, $quantity, $refills);
SET @Rx_ID = LAST_INSERT_ID();
INSERT INTO Description(FreeFormText) VALUEs($freeform);
SET @Desc_ID = LAST_INSERT_ID();
INSERT INTO MedicalRecord ( $EmplID,  $PatientID, $SymptID,  $Timestamp, @Desc_ID,  @Rx_ID);
COMMIT;
EOT;
        $this->connectToDB();

        if(!$this->conn->query($queryString)){
            print "Errormessage: " . $this->conn->error;
            $this->closeDB();
            $this->ViewStates['SignUpNewUser'] = 'Error at attempting to enter prescription into system.';
            $this->Attributes['opState']="FailedPrescribeMedication";
            return false;
        }
        $this->closeDB();
        $this->ViewStates['SignUpNewUser'] = 'The System has prescribed the medicine to the patient.';
        $this->Attributes['opState']="PassedPrescribeMedication";
        return true;


    }

    public function UseCase_WritePhysiciansExam($freeform, $empl_id, $patient_id){
        //write_exam_note(IN note TEXT, IN empl_id INT, IN patient_id INT)
        $queryString =<<<EOT
CALL write_exam_note( $freeform ,  $empl_id ,  $patient_id );
EOT;
        $result = $this->conn->query($queryString);
        if(!$this->conn->query($queryString)){
            print "Errormessage: " . $this->conn->error;
            $this->closeDB();
            $this->ViewStates['WritePhysiciansExam'] = 'Unable to insert exam notes.';
            $this->Attributes['opState']="FailedWritePhysiciansExam";
            return false;
        }
        $this->closeDB();
        $this->ViewStates['WritePhysiciansExam'] = 'This note is now in the database: <br/>&nbsp;<br/>'.$freeform;
        $this->Attributes['opState']="PassedWritePhysiciansExam";
        return true;
    }

    public function UseCase_CreateDisease($patient_id, $empl_id,$symptom_id,$treatment_id,$description){
        //create_disease_thread(IN patient_id INT, IN empl_id INT, IN symptom_id INT, IN treatment_id INT, IN description TEXT)
        $queryString =<<<EOT
CALL create_disease_thread($patient_id , $empl_id , $symptom_id , $treatment_id ,$description  );
EOT;
        $result = $this->conn->query($queryString);
        if(!$result){
            print "Errormessage: " . $this->conn->error;
            $this->closeDB();
            $this->ViewStates['CreateDisease'] = 'Unable to insert symptom data.';
            $this->Attributes['opState']="FailedCreateDisease";
            return false;
        }
        $this->closeDB();
        $this->ViewStates['CreateDisease'] = 'This symptom is now in the database: <br/>&nbsp;<br/>';
        $this->Attributes['opState']="PassedCreateDisease";
        return true;

    }

    public function UseCase_ModifyDisease($patient_id, $empl_id,$symptom_id,$treatment_id,$description){
        //create_disease_thread(IN patient_id INT, IN empl_id INT, IN symptom_id INT, IN treatment_id INT, IN description TEXT)
        $queryString =<<<EOT
CALL create_disease_thread($patient_id , $empl_id , $symptom_id , $treatment_id ,$description  );
EOT;
        $result = $this->conn->query($queryString);
        if(!$this->conn->query($queryString)){
            print "Errormessage: " . $this->conn->error;
            $this->closeDB();
            $this->ViewStates['CreateDisease'] = 'Unable to insert symptom data.';
            $this->Attributes['opState']="FailedCreateDisease";
            return false;
        }
        $this->closeDB();
        $this->ViewStates['CreateDisease'] = 'This symptom is now in the database: <br/>&nbsp;<br/>';
        $this->Attributes['opState']="PassedCreateDisease";
        return true;

    }

    public function UseCase_ViewMedicalRecord($PatientID){
        //this function will retrieve an array
        $queryString=<<<EOT
SELECT * FROM MedicalRecord
WHERE MedicalRecord.PatientID=$PatientID;
EOT;
        $this->connectToDB();
        $result = $this->conn->query($queryString);
        $this->closeDB();
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            foreach($row as $key => $value){
                $this->dataViewAppointments[$key] = $value;
            }
            $this->ViewState['ViewAppointments'] = "Canceling appointment for physician.";
            $this->Attributes['opState']="PassedViewAppointments";

        }else{
            print "ERROR: model->UseCase_ViewAppointments";
            $ViewState['ViewAppointment'] ="Error: Unable to view appointments";
            $this->Attributes['opState']="FailedViewAppointments";
            return false;
        }
        return true;

    }

public function UseCase_MakePayment( $amount, $patient_ID ){
        //create_disease_thread(IN patient_id INT, IN empl_id INT, IN symptom_id INT, IN treatment_id INT, IN description TEXT)
        $queryString=<<<EOT
CALL make_payment($amount, $patient_ID);
EOT;
        if(!$this->conn->query($queryString)){
            print "Errormessage: " . $this->conn->error;
            $this->closeDB();
            $this->ViewStates['MakePayment'] = 'Unable to insert payment data.';
            $this->Attributes['opState']="FailedMakePayment";
            return false;
        }
        $this->closeDB();
        $this->ViewStates['MakePayment'] = 'The payment has been entered. <br/>&nbsp;<br/>';
        $this->Attributes['opState']="PassedMakePayment";
        return true;
    }


    public function UseCase_ViewLabHistory($PatientID){
        //this function will retrieve an array
        $queryString=<<<EOT
SELECT User.Name, PhysicianUser.Name ,Description.FreeFormText
FROM User AS PatientUser, User AS PhysicianUser, Appointment, Description
WHERE Appointment.PatientID='$PatientID' AND PatientUser.UserID=Appointment.PatientID AND Appointment.DescriptionID=Description.DescriptionID
AND PhysicianUser.UserID=Appointment.PhysicianID AND PhysicianUser.UserType='Technician';
EOT;
        $this->connectToDB();
        $result = $this->conn->query($queryString);
        $this->closeDB();
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            foreach($row as $key => $value){
                $this->dataViewLabHistory[$key] = $value;
            }
            $this->ViewState['ViewLabHistory'] = "Lab test history: ";
            $this->Attributes['opState']="PassedViewLabHistory";

        }else{
            print "ERROR: model->UseCase_ViewAppointments";
            $ViewState['ViewLabHistory'] ="Error: Unable to view lab test appointments and history";
            $this->Attributes['opState']="FailedViewLabHistory";
            return false;
        }
        return true;
    }

    public function UseCase_CreateSpecialistReferral( $freeform, $PhysicianID, $patient_ID, $time ){
        //first call DB to find out if employee with physicianID is a technician If so then use method schedule appointment
        $this->connectToDB();

        $queryString = "SELECT UserType FROM User WHERE UserID='" .$PhysicianID."';";

        $result = $this->conn->query($queryString);
        $this->closeDB();
        if($result->num_rows == 1){
            $this->closeDB();
            //$row = $result->fetch_assoc();
            $row = $result->fetch_assoc();
            foreach($row as $key => $value){
                $this->Attributes[$key] = $value;
            }


            if($this->Attributes['UserType']=='Technician'){
                //call schedule patient
                return $this->UseCase_ScheduleAppointment($freeform, $PhysicianID,$patient_ID,$time);
            }else{
                //The DB should be confirm that it is a technician. If not then expected value is not set up in HTML code
                return false;
            }

        }else{

            print "ERROR: model->CreateSpecialistReferral";
            $this->dataCreateSpecialistReferral="ERROR";
            return false;
        }
    }

    public function UseCase_Create( $freeform, $PhysicianID, $patient_ID, $time ){
        // similar to create specialist referral. First checks to see if it is a technician. if so it calls schedule lab test
        $this->connectToDB();

        $queryString = "SELECT UserType FROM User WHERE UserID='" .$PhysicianID."';";

        $result = $this->conn->query($queryString);
        $this->closeDB();
        if($result->num_rows == 1){
            $this->closeDB();
            //$row = $result->fetch_assoc();
            $row = $result->fetch_assoc();
            foreach($row as $key => $value){
                $this->Attributes[$key] = $value;
            }


            if($this->Attributes['UserType']=='Technician'){
                //call schedule patient
                //private_ScheduleLabTest($patient_id , $physician_id , $time , $description)
                return $this->UseCase_ScheduleAppointment($freeform, $PhysicianID,$patient_ID,$time);
            }else{
                //The DB should be confirm that it is a technician. If not then expected value is not set up in HTML code
                return false;
            }

        }else{

            print "ERROR: model->CreateSpecialistReferral"; 
            $this->dataCreateSpecialistReferral="ERROR";
            return false;
        }
    }

    private function private_ScheduleLabTest($patient_id , $physician_id , $time , $description){
        $queryString=<<<EOT
CALL schedule_lab_test($patient_id , $physician_id , $time , $description);
EOT;
        if(!$this->conn->query($queryString)){
            print "Errormessage: " . $this->conn->error;
            $this->closeDB();
            $this->ViewStates['ScheduleLabTest'] = 'Unable to insert lab test into schedule.';
            $this->Attributes['opState']="FailedScheduleLabTest";
            return false;
        }
        $this->closeDB();
        $this->ViewStates['MakeLabTest'] = 'The Lab test has been scheduled';
        $this->Attributes['opState']="PassedScheduleLabTest";
        return true;
    }


    public function getAllUserAttributesFromDB(){
        $this->connectToDB();

        $queryString = "SELECT * FROM User WHERE UserID='" .$this->Attributes['UserID']."';";

        $result = $this->conn->query($queryString);
        $this->closeDB();
        if($result->num_rows == 1){
            $this->closeDB();
            //$row = $result->fetch_assoc();
            $row = $result->fetch_assoc();
            foreach($row as $key => $value){
                $this->Attributes[$key] = $value;
            }


            if($this->Attributes['UserType']=='Patient'){
                $this->Attributes['PatientName'] = $this->UserAttributes['Name'];
            }else{
                $this->Attributes['PatientName'] = "Please choose a Patient.<br/>";
            }

        }else{

            print "ERROR: model->getAllUserAttributesFromDB";
            $this->Attributes['UserID']="ERROR";
        }
    }

    /**
     * Very important method used to unlock user information
     * @param $Username
     * @param $Password
     * @return string
     */
    public function get_UserID_fromDB($Username, $Password){
        $this->connectToDB();
        $queryString = "SELECT UserID FROM User WHERE Username='" .$Username."' AND Password='".$Password."';";
        $result = $this->conn->query($queryString);
        $this->closeDB();
        if($result->num_rows == 1){
            $row = $result->fetch_assoc();
            $this->Attributes['UserID'] = $row['UserID'];
            return true;
        }else{
            print "ERROR: get_UserID_fromDB";
            return false;
        }
    }

    public function isPostBack(){
        if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'){
            return TRUE;
        }
        return FALSE;
    }





    public function connectToDB($servername='localhost',$username='root', $password=''){
        $dbname = "LifeThread";
        //Create connection
        $this->conn = new mysqli($this->servername, $username, $password, $dbname);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error );
            return false;
        }
        return true;
    }

    public function closeDB(){
        $this->conn->close();
        return true;
    }

    /**/
    public function saveSelectedAttributesToCookie(){
        foreach($this->AttributesForCookiesSave as $key => $attribName){
            $this->toCookie( $attribName, $this->Attributes[$attribName]);
        }
    }



    public function toCookie($cookie_name, $cookie_value){
        setcookie($cookie_name, $cookie_value, time() + (86400), '/'); // 86400 = 1 day
    }

    public function exists($ckname){
        return isset( $this->Attributes[$ckname] );
    }

    /**
     *
     * @param string $ctype
     */

    function __construct($ctype="Unknown"){
        $this->PatientName = "_unknown";
        $this->Attributes['opState'] = "Initial";        //Stands for opState. For every screen place unique state here.
        $this->define($ctype);
        $this->UserID = $this->fromCookie("UserID");
        $this->attributesForCookiesSave = array(            //Used by SaveSelectedCookies method
            'UserID',
            'UserType',
            'Name',
            'Username',
            'Password',
            'Address');

    }

    public function define($type){
        $userType=$type;
        if(strcmp($type, "Unknown")==0){
            $this->attributes = array(
                "Authenticate" => 1,
                "Logout"       => 0,
                "Sign Up New User" => 1,
                "Schedule Appointment" => 0,
                "Cancel Appointment" => 0,
                "Prescribe Medication" => 0,
                "Write Physicians Exam" => 0,
                "Create Disease" => 0,
                "Modify Disease Thread" => 0,
                "View Medical Record" => 0,
                "View Prescription" => 0,
                "View Account Balance" => 0,
                "Make Payment" => 0,
                "Schedule Lab Test" => 0,
                "View Lab History" => 0,
                "Create Specialist Referral" => 0,
                "Update Account Information" => 0,
                "Create Emergency First Contact" => 0,
                "Write Nurses Notes" => 0
            );
        }
        elseif(strcmp($type, "Patient")==0){
            $this->attributes = array(
                "Authenticate" => 0,
                "Logout"       => 1,
                "Sign Up New User" => 0,
                "Schedule Appointment" => 1,
                "Cancel Appointment" => 1,
                "Prescribe Medication" => 0,
                "Write Physicians Exam" => 0,
                "Create Disease" => 0,
                "Modify Disease Thread" => 0,
                "View Medical Record" => 0,
                "View Prescription" => 1,
                "View Account Balance" => 1,
                "Make Payment" => 0,
                "Schedule Lab Test" => 0,
                "View Lab History" => 0,
                "Create Specialist Referral" => 0,
                "Update Account Information" => 1,
                "Create Emergency FirstContact" => 0,
                "Write Nurses Notes" => 0
            );
        }
        elseif(strcmp($type, "Nurse")==0){
            $this->attributes = array(
                "Authenticate" => 0,
                "Logout"       => 1,
                "Sign Up New User" => 1,
                "Schedule Appointment" => 1,
                "Cancel Appointment" => 1,
                "Prescribe Medication" => 0,
                "Write Physicians Exam" => 0,
                "Create Disease" => 0,
                "Modify Disease Thread" => 0,
                "View Medical Record" => 1,
                "View Prescription" => 1,
                "View Account Balance" => 0,
                "Make Payment" => 0,
                "Schedule Lab Test" => 0,
                "View Lab History" => 1,
                "Create Specialist Referral" => 0,
                "Update Account Information" => 0,
                "Create Emergency First Contact" => 0,
                "Write Nurses Notes" => 1
            );
        }
        elseif(strcmp($type, "Nurse Practitioner")==0){
            $this->attributes = array(
                "Authenticate" => 0,
                "Logout"       => 1,
                "Sign Up New User" => 1,
                "Schedule Appointment" => 1,
                "Cancel Appointment" => 1,
                "Prescribe Medication" => 1,
                "Write Physicians Exam" => 1,
                "Create Disease" => 1,
                "Modify Disease Thread" => 1,
                "View Medical Record" => 1,
                "View Prescription" => 1,
                "View Account Balance" => 0,
                "Make Payment" => 0,
                "Schedule LabTest" => 1,
                "View Lab History" => 1,
                "Create Specialist Referral" => 1,
                "Update Account Information" => 0,
                "Create Emergency First Contact" => 0,
                "Write Nurses Notes" => 0
            );
        }
        elseif(strcmp($type, "Physician")==0){
            /*Keep in synch with Nurse Practitioner*/
            $this->attributes = array(
                "Authenticate" => 0,
                "Logout"       => 1,
                "Sign Up New User" => 1,
                "Schedule Appointment" => 1,
                "Cancel Appointment" => 1,
                "Prescribe Medication" => 1,
                "Write Physicians Exam" => 1,
                "Create Disease" => 1,
                "Modify Disease Thread" => 1,
                "View Medical Record" => 1,
                "View Prescription" => 1,
                "View AccountBalance" => 0,
                "Make Payment" => 0,
                "Schedule Lab Test" => 1,
                "View Lab History" => 1,
                "Create Specialist Referral" => 1,
                "Update Account Information" => 0,
                "Create Emergency First Contact" => 0,
                "Write Nurses Notes" => 0
            );
        }
        elseif(strcmp($type, "Specialist")==0){
            $this->attributes = array(
                "Authenticate" => 0,
                "Logout"       => 1,
                "Sign Up New User" => 0,
                "Schedule Appointment" => 1,
                "Cancel Appointment" => 1,
                "Prescribe Medication" => 1,
                "Write Physicians Exam" => 0,
                "Create Disease" => 1,
                "Modify Disease Thread" => 1,
                "View Medical Record" => 0,
                "View Prescription" => 1,
                "View Account Balance" => 0,
                "Make Payment" => 0,
                "Schedule Lab Test" => 1,
                "View Lab History" => 1,
                "Create Specialist Referral" => 1,
                "Update Account Information" => 0,
                "Create Emergency First Contact" => 0,
                "Write Nurses Notes" => 0
            );
        }
        elseif(strcmp($type, "Admin")==0){
            $this->attributes = array(
                "Authenticate" => 0,
                "Logout"       => 1,
                "Sign Up New User" => 1,
                "Schedule Appointment" => 1,
                "Cancel Appointment" => 1,
                "Prescribe Medication" => 0,
                "Write Physicians Exam" => 0,
                "Create Disease" => 0,
                "Modify Disease Thread" => 0,
                "View Medical Record" => 0,
                "View Prescription" => 0,
                "View Account Balance" => 1,
                "Make Payment" => 1,
                "Schedule Lab Test" => 0,
                "View Lab History" => 0,
                "Create Specialist Referral" => 0,
                "Update Account Information" => 1,
                "Create Emergency First Contact" => 0,
                "Write Nurses Notes" => 0
            );
        }
        elseif(strcmp($type, "EMT")==0){
            $this->attributes = array(
                "Authenticate" => 0,
                "Logout"       => 1,
                "Sign Up New User" => 0,
                "Schedule Appointment" => 0,
                "Cancel Appointment" => 0,
                "Prescribe Medication" => 0,
                "Write Physicians Exam" => 0,
                "Create Disease" => 0,
                "Modify Disease Thread" => 0,
                "View Medical Record" => 1,
                "View Prescription" => 1,
                "View Account Balance" => 0,
                "Make Payment" => 0,
                "Schedule Lab Test" => 0,
                "View Lab History" => 0,
                "Create Specialist Referral" => 0,
                "Update Account Information" => 0,
                "Create Emergency First Contact" => 1,
                "Write Nurses Notes" => 0
            );
        }
        elseif(strcmp($type, "Technician")==0){
            $this->attributes = array(
                "Authenticate" => 0,
                "Logout"       => 1,
                "Sign Up New User" => 0,
                "Schedule Appointment" => 0,
                "Cancel Appointment" => 0,
                "Prescribe Medication" => 0,
                "Write Physicians Exam" => 0,
                "Create Disease" => 0,
                "Modify Disease Thread" => 0,
                "View Medical Record" => 0,
                "View Prescription" => 0,
                "View Account Balance" => 0,
                "Make Payment" => 0,
                "Schedule Lab Test" => 1,
                "View Lab History" => 1,
                "Create Specialist Referral" => 0,
                "Update Account Information" => 0,
                "Create Emergency First Contact" => 0,
                "Write Nurses Notes" => 0
            );
        }
        else{
            print "ERROR: UKNOWN USER TYPE IN CONSTRUCTION OF BaseUser: '".$type."'";
        }
    }

}
?>