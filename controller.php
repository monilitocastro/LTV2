<?php
class Controller
{
    private $model;

    /**
     * This is the singular most important part of controller.php. It is the place where the logic starts.
     * It should be called from index.php after the Controller Class has been made.
     * It starts the decision making process.
     * Using $this->model->Attributes['opState'] it finds out where it can go next.
     */
    public function startTheLogic(){
        $opState = $this->model->Attributes['opState'];
        if($opState=='Initial'){

        }elseif($opState=='Initial'){}
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