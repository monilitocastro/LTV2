<?php
class Model
{
    public $attributes;                 //this member holds the permissions for UserType. relevant to sidebar
    public $messages;
    public $servername;
    private $conn;
    public $showThis;                   //messages for showing on main panel content
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
    public $dataDefinePatient;
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
    public $ViewStates;



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

        //Guard against undefined.
        if(!isset($this->Attributes['PatientName'])) {
            $this->Attributes['PatientName'] = 'Unknown';
        }
        if(!isset($this->Attributes['UserID'])){
            $this->Attributes['UserID']='Unknown';
        }
        if(!isset($this->Attributes['UserType'])){
            $this->Attributes['UserType'] == 'Unknown';
        }
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
        foreach ($_GET as $key=>$val)
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
            $this->ViewStates['Authenticate'] = "Sorry wrong username password combination.";
            return false;
        }else{
            $this->ViewStates['Authenticate'] = "Welcome to LifeThread";
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
CALL sign_up_patient( '$Name' ,  '$Username' ,  '$Password' , '$Address' );
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
        $userid = $this->Attributes['UserID'];
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
        $PatientID = $this->Attributes['PatientID'];
        $queryString = <<<EOT
SELECT Balance FROM Account NATURAL JOIN User WHERE UserID='$PatientID';
EOT;
        $resultString="";
        $Balance = "";
        $result = $this->conn->query($queryString);
        if(!$result){
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
        $PatientID = $this->Attributes['PatientID'];
        $this->connectToDB();
        $queryString = <<<EOT
SELECT Prescription.Name, MedicalRecord.Timestamp
        FROM Prescription, MedicalRecord
        WHERE MedicalRecord.PatientID='$PatientID' AND MedicalRecord.RxNumber = Prescription.RxNumber
        ORDER BY MedicalRecord.Timestamp DESC;
EOT;
        $result = $this->conn->query($queryString);
        $this->closeDB();

        if(!$result){
            $this->Attributes['opState']="FailedViewPrescription";
            $this->ViewState['ViewAccountBalance'] = 'No Prescription';
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
CALL schedule_appointment($patientID, $workerID, $time, '$freeform');
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
SELECT * FROM Appointment WHERE Appointment.PatientID=$PatientID
     ORDER BY Time;
EOT;
        $this->connectToDB();
        $result = $this->conn->query($queryString);
        $this->closeDB();

        foreach($result as $key => $value){

            $this->dataViewAppointments[$key] = $value;
            $this->ViewState['ViewAppointments'] = "Viewing Appointments.";
            $this->Attributes['opState']="PassedViewAppointments";
        }
    }

    public function UseCase_PrescribeMedication($drugName, $quantity, $refills, $freeform, $EmplID, $PatientID, $SymptID, $Timestamp){
        //for queryString first insert into PrescriptionTable then Description, then MedicalRecord
        $queryString =<<<EOT
START TRANSACTION;
INSERT INTO Prescription(Name, Quantity, Refills) VALUES('$drugName', $quantity, $refills);
SET @Rx_ID = LAST_INSERT_ID();
INSERT INTO Description(FreeFormText) VALUES('$freeform');
SET @Desc_ID = LAST_INSERT_ID();
INSERT INTO MedicalRecord(EmplID, PatientID, SymptID, Timestamp, DescriptionID,  RxNumber) VALUES ( $EmplID,  $PatientID,  $SymptID, NOW(), @Desc_ID,  @Rx_ID);
COMMIT;
EOT;
        $this->connectToDB();

        if(!$this->conn->query($queryString)){
            print "Errormessage: " . $this->conn->error;
            $this->closeDB();
            $this->ViewStates['PrescribeMedication'] = 'Error at attempting to enter prescription into system.';
            $this->Attributes['opState']="FailedPrescribeMedication";
            return false;
        }
        $this->closeDB();
        $this->ViewStates['PrescribeMedication'] = 'The System has prescribed the medicine to the patient.';
        $this->Attributes['opState']="PassedPrescribeMedication";
        return true;
    }
    public function UseCase_WritePhysiciansExam($freeform, $empl_id){
        //write_exam_note(IN note TEXT, IN empl_id INT, IN patient_id INT)
        $result = $this->private_WriteNotes($freeform, $empl_id);
        if($result==true){
            $this->ViewStates['WritePhysiciansExam'] = 'This note is now in the database: <br/>&nbsp;<br/>'.$freeform;
            $this->Attributes['opState']="PassedWritePhysiciansExam";
        }else{
            $this->ViewStates['WritePhysiciansExam'] = 'Unable to insert exam notes.';
            $this->Attributes['opState']="FailedWritePhysiciansExam";
        }
    }

    public function UseCase_WriteNursesNotes($freeform, $empl_id){
        //write_exam_note(IN note TEXT, IN empl_id INT, IN patient_id INT)
        $result = $this->private_WriteNotes($freeform, $empl_id);
        if($result==true){
            $this->ViewStates['WritePhysiciansExam'] = 'This note is now in the database: <br/>&nbsp;<br/>'.$freeform;
            $this->Attributes['opState']="PassedWriteNursesNotes";
        }else{
            $this->ViewStates['WritePhysiciansExam'] = 'Unable to insert exam notes.';
            $this->Attributes['opState']="FailedWriteNursesNotes";
        }
    }

    private function private_WriteNotes($freeform, $empl_id){
        //write_exam_note(IN note TEXT, IN empl_id INT, IN patient_id INT)
        $patient_id =  $this->Attributes['PatientID'];
        $queryString =<<<EOT
CALL write_exam_note( '$freeform' , $empl_id,  $patient_id );
EOT;
        $this->connectToDB();
        if(!$this->conn->query($queryString)){
            print "Errormessage: " . $this->conn->error;
            $this->closeDB();
            return false;
        }
        $this->closeDB();
        return true;
    }

    public function UseCase_CreateDisease($patient_id, $empl_id,$symptom_id,$treatment_id,$description){
        //create_disease_thread(IN patient_id INT, IN empl_id INT, IN symptom_id INT, IN treatment_id INT, IN description TEXT)
        $queryString =<<<EOT
CALL create_disease_thread($patient_id , $empl_id , $symptom_id , $treatment_id , '$description'  );
EOT;
        $this->connectToDB();
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
CALL create_disease_thread($patient_id , $empl_id , $symptom_id , $treatment_id ,'$description'  );
EOT;
        $result = $this->conn->query($queryString);
        if(!$this->conn->query($queryString)){
            print "Errormessage: " . $this->conn->error;
            $this->closeDB();
            $this->ViewStates['CreateDisease'] = 'Unable to insert symptom data.';
            $this->Attributes['opState']="FailedModifyDisease";
            return false;
        }
        $this->closeDB();
        $this->ViewStates['CreateDisease'] = 'This symptom is now in the database: <br/>&nbsp;<br/>';
        $this->Attributes['opState']="PassedModifyDisease";
        return true;

    }

    public function UseCase_ViewMedicalRecord(){
        //this function will retrieve an array
        $PatientID=$this->Attributes['PatientID'];
        $queryString=<<<EOT
SELECT * FROM MedicalRecord
WHERE MedicalRecord.PatientID=$PatientID;
EOT;
        $this->connectToDB();
        $result = $this->conn->query($queryString);
        $this->closeDB();

        foreach($result as $key => $value){
            //print_r($result);
            $this->dataViewMedicalRecord[$key] = $value;
            $this->ViewState['ViewMedicalRecord'] = "Viewing Medical Record.";
            $this->Attributes['opState']="PassedViewMedicalRecord";
        }
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


    public function UseCase_ViewLabHistory(){
        //this function will retrieve an array and is probably similar to view medical history (which uses MedicalRecord instead)
        $PatientID=$this->Attributes['PatientID'];
        $queryString=<<<EOT
SELECT *
FROM User AS PatientUser, User as PhysicianUser, Description, Appointment
WHERE Appointment.PatientID='$PatientID' AND Appointment.PatientID=PatientUser.UserID
AND Appointment.DescriptionID=Description.DescriptionID
AND PhysicianUser.UserID=Appointment.PhysicianID
AND PhysicianUser.UserType='Technician';
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
            //There are no records to display
            $ViewState['ViewLabHistory'] ="Error: Unable to view lab test appointments and history";
            $this->Attributes['opState']="FailedViewLabHistory";
            return false;
        }
        return true;
    }

    public function UseCase_CreateSpecialistReferral( $freeform, $PhysicianID, $patient_ID, $time ){
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

    public function UseCase_CreateEmergencyFirstContact(){
        //need to implement
        throw new Exception("Unimplemented method CreateEmergencyFirstContact");
    }

    public function UseCase_DefinePatient(){
        $PatientID = $this->Attributes['PatientID'];
        $queryString =<<<EOT
SELECT Name from User WHERE UserID='$PatientID';
EOT;
        $this->connectToDB();
        $result = $this->conn->query($queryString);
        $this->closeDB();
        if(!$result){
            $this->ViewStates['DefinePatient'] = 'Unable to define patient using the ID you provided. Message:' .  $this->conn->error;;
            $this->Attributes['opState'] = "FailedDefinePatient";
            return false;
        }else {
            foreach ($result as $key => $value) {
                //there is only one since PatientID is a UserID which is a primary key of User table, so we can do this ->
                $this->Attributes['PatientName'] = $this->dataDefinePatient[$key] = $value;
                //
                $this->ViewStates['DefinePatient'] = 'Patient is now defined.';
                $this->Attributes['opState'] = "PassedDefinePatient";
            }
            return true;
        }
    }

    public function UseCase_ScheduleLabTest($patient_id , $physician_id , $time , $description){

        /**
         * The time format for schedule_lab_test is datetime because of Appointment table. The current test files has a none-datetime format.
         * I try to put datetime format but it seems to reject it. It seems that the only datetime it accepts is the NOW() MySQL function.
         * Suffice it to say this is only useful some of the time.   MCC
         */

        $queryString=<<<EOT
CALL schedule_lab_test($patient_id, $physician_id, $time, "$description");
EOT;
        $this->connectToDB();
        if(!$this->conn->query($queryString)){
            print "Errormessage: " . $this->conn->error;
            $this->closeDB();
            $this->ViewStates['ScheduleLabTest'] = 'Unable to insert lab test into schedule.';
            $this->Attributes['opState'] = "FailedScheduleLabTest";
            return false;
        }
        $this->closeDB();
        $this->ViewStates['ScheduleLabTest'] = 'The Lab test has been scheduled';
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
                $this->Attributes['PatientName'] = $this->Attributes['Name'];
                $this->Attributes['PatientID'] = $this->Attributes['UserID'];
            }else{
                $this->Attributes['PatientName'] = "Please choose a Patient.<br/>";
                $this->Attributes['PatientID'] = $this->Attributes['UserID'];
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



    private function toCookie($cookie_name, $cookie_value){
        setcookie($cookie_name, $cookie_value, time() + (86400), '/'); // 86400 = 1 day
    }

    public function exists($ckname){
        return isset( $this->Attributes[$ckname] );
    }

    public function saveAllAttributesToCookies(){
        foreach($this->Attributes as $key => $value){
            $this->toCookie($key, $value);
        }
    }

    /**
     *
     * @param string $ctype
     */

    function __construct($ctype="Unknown"){
        //Guard against undefined.
        $this->Attributes['UserID']='Unknown';
        $this->Attributes['UserType'] == 'Unknown';
        $this->Attributes['PatientName'] = "_unknown";
        $this->Attributes['opState'] = "Initial";        //Stands for opState. For every screen place unique state here.
        $this->define($ctype);
        $this->attributesForCookiesSave = array(            //Used by SaveSelectedCookies method
            'UserID',
            'UserType',
            'Name',
            'Username',
            'Password',
            'Address');
        $this->setMessages();

    }

    private function setMessages(){
            $this->model->messages['Initial']='Welcome to your Hospital\'s LifeThread Electronic Medical Record system.';
            $this->model->messages['FailedAuthenticate']='Sorry, you entered the wrong username and password combination. Try again.';
            $this->model->messages['PassedAuthenticate'] =  'Welcome to your LifeThread Account.';
            $this->model->messages['FailedSignUpNewUser']='Sorry, please use different account information to register.';
            $this->model->messages['PassedSignUpNewUser'] = 'Welcome to your new LifeThread Account.';
            $this->model->messages['Logout'] = 'Good Bye';
            $this->model->messages['FailedUpdateUserInformation'] = 'Sorry, user update failed';
            $this->model->messages['PassedUpdateUserInformation'] = 'Your account has been update with the new information.';
            $this->model->messages['FailedViewAccountBalance'] = 'Sorry but your balance is not yet available.';
            $this->model->messages['PassedViewAccountBalance'] =  'Here is your new statement.';
            $this->model->messages['FailedViewPrescription'] = 'Sorry but your prescription history is not yet available.';
            $this->model->messages['PassedViewPrescription'] = 'Here is you history of prescription.';
            $this->model->messages['FailedScheduleAppointment'] = 'Sorry but your scheduled appointments are not yet available.';
            $this->model->messages['PassedScheduleAppointment'] = 'Your schedule.';
            $this->model->messages['FailedCancelAppointment'] = 'Sorry but we can not cancel that appointment. Please call the hospital.';
            $this->model->messages['PassedCancelAppointment'] = 'Your selected appointment has been canceled.';
            $this->model->messages['PassedViewAppointment'] = 'Here is your outstanding appointment.';
            $this->model->messages['FailedViewAppointment'] = 'There no appointments right now.';
            $this->model->messages['FailedPrescribeMedication'] = 'Sorry but medication cannot be prescribed right now.';
            $this->model->messages['PassedPrescribeMedication'] = 'Prescription has been made for the patient.';
            $this->model->messages['FailedWritePhysiciansExam'] = 'Sorry, your exam notes cannot be inserted right now.';
            $this->model->messages['PassedWritePhysiciansExam'] = 'Your Physician\'s exam has been inserted into the record';
            $this->model->messages['FailedWriteNursesNotes'] = 'Sorry, your notes cannot be inserted right now.';
            $this->model->messages['PassedWriteNursesNotes'] = 'Your nurse\'s notes have been inserted into the record.';
            $this->model->messages['FailedCreateDisease'] = 'Disease thread cannot be inserted';
            $this->model->messages['PassedCreateDisease'] = 'Disease thread has been inserted';
            $this->model->messages['FailedModifyDisease'] = 'Failed to modify disease thread';
            $this->model->messages['PassedModifyDisease'] = 'Modification of disease thread is updated';
            $this->model->messages['PassedViewMedicalRecord'] = 'Here is the patient\'s medical record';
            $this->model->messages['FailedViewMedicalRecord'] = 'Sorry the medical records cannot be viewed right now';
            $this->model->messages['FailedMakePayment'] = 'Payment cannot be made right now';
            $this->model->messages['PassedMakePayment'] = 'Transaction successful.';
            $this->model->messages['PassedViewLabHistory'] = 'Here is the patient\'s lab history.';
            $this->model->messages['FailedViewLabHistory'] = 'Patient\'s lab history is currently not viewable.';
            $this->model->messages['PassedScheduleLabTest'] = 'Here is the patient\'s lab test';
            $this->model->messages['FailedScheduleLabTest'] = 'Sorry lab tests cannot be viewed right now.';
            $this->model->messages['PassedCreateEmergencyFirstContact'] = 'Emergency first contact created.';
            $this->model->messages['FailedCreateEmergencyFirstContact'] = 'Emergency first contact cannot be created.';
            $this->model->messages['PassedDefinePatient'] = "Patient ID has been located and defined.";
            $this->model->messages['FailedDefinePatient'] = 'Sorry that Patient ID does not exist.';

    }

    public function define($type){
        $userType=$type;
        if(strcmp($type, "Unknown")==0){
            $this->attributes = array(
                "Authenticate" => 1,
                "Define Patient" => 0,
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
                "Define Patient" => 0,
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
                "Define Patient" => 1,
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
                "Define Patient" => 1,
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
                "Define Patient" => 1,
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
                "Define Patient" => 1,
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
                "Define Patient" => 1,
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
                "Define Patient" => 1,
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
                "Define Patient" => 1,
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