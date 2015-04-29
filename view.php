<!DOCTYPE html>

<?php
class View
{
    private $model;
    private $controller;
    private $showThis;
    private $refShowThis;

    public function __construct(&$controller,&$model) {
        $this->controller = &$controller;
        $this->model =& $model;
    }

    public function UserInformationForm($HeadingText, $ActionText,$SubmissionText){
        $Username = "";
        $Name = "";
        $Password = "";
        $Address = "";
        if( !isset($this->model->FromCookies['Name']) &&
            !isset($this->model->FromCookies['Username']) &&
            !isset($this->model->FromCookies['Password']) &&
            !isset($this->model->FromCookies['Address']) ) {
        }else{
            if($_GET['action']=='SignUpNow'){
                $Username = "";
                $Name = "";
                $Password = "";
                $Address = "";
            }elseif($_GET['action']=='UpdateAccountInformation'){
                $Name = $this->model->FromCookies['Name'];
                $Username = $this->model->FromCookies['Username'];
                $Password = $this->model->FromCookies['Password'];
                $Address = $this->model->FromCookies['Address'];
            }

        }
        $result =<<<EOT
<br/><br/><div style='width:300px;margin:0 auto;'>
<form action='index.php?action=$ActionText' method='POST'>$HeadingText<BR/><BR/>
Name: <input type='textbox' name='Name' value='$Name'><br/>
Username: <input type='textbox' name='Username' value='$Username'><br/>
Password: <input type='password' name='Password' value='$Password'><br/>
Address:  <input type='textbox' name='Address' value='$Address'><br/><input type='reset' value='Reset'>
<input type='submit' value='$SubmissionText'></form></div>
EOT;
        return $result;
    }
    public function PatientNameView(){
        $result = "";
        if( isset($this->model->FromCookies['UserType']) && ($this->model->FromCookies['UserType'] != 'Patient')   ){
            //For everyone else
            $result = $result . "<li><a href=\"index.php?action=DefinePatient\" target=\"_top\">Define Patient</a></li>";
        }else if($this->model->FromCookies['UserType'] == 'Patient'){
            //just return patient name
            $result = $result . $this->model->PatientName;
            $this->model->PatientID = $this->model->FromCookies['UserID'];
        }
        return $result;
    }

    public function createSideBar(){
        $result = "Patient: ".$this->PatientNameView()."<br><br>";
        foreach($this->model->attributes as $key => $value){
            if($value==1){
                $linkTitle = $key;
                $url = str_replace(' ', '', $key);
                $result = $result . "<li><a href=\"index.php?action=".$url."\" target=\"_top\">".$linkTitle."</a></li>";
            }
        }

        return $result;
    }


    public function showTemplate($usecase){
        $this->model->useCaseContent = $usecase;
        $concatString = "<html>". $this->showPreBody() . $this->showBody() . "</html>";
        return $concatString;
    }
    public function showPreBody(){

        return "<head>
	 <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">
	 <link rel=\"stylesheet\" type=\"text/css\" href=\"blended_layout.css\">
	 <title>LifeThread Electronic Medical Record Software</title>
	 <meta name=\"description\" content=\"Write some words to describe your html page\">
         </head>";
    }
    public function showBody(){
        return "<body>" . "<div class=\"blended_grid\">". $this->showPageHeader(). $this->showPageLeftMenu(). $this->showPageContent(). $this->showPageFooter() ."</div>" . "</body>";
    }
    public function showPageHeader(){
        return "<div class=\"pageHeader\"><img src=\"logo.png\"></div>";
    }
    public function showPageLeftMenu(){
        return "<div class=\"pageLeftMenu\">
	    <ul>". $this->createSideBar() ."</ul>" ."</div>";
    }

    public function GuardAgainstHacking(){

        $tempArr1 = $this->model->attributes;
        foreach($tempArr1 as $key => $value){
            $tempArr1[str_replace(' ', '', $key)] = $value;
        }
        if( $_GET['action'] != 'DefinePatient' && $tempArr1[$_GET['action']]==0 ){
            return true;
        }
        return false;
    }

    public function showPageContent(){
        $this->showThis=&$this->model->showThis;
        $operation = & $this->model->opState;
        if($operation=='trunk'){
            if($this->GuardAgainstHacking()){       //TRUE if an attempt is made to see use cases with permission
                return "&nbsp;<br/>&nbsp;<br/><center><h2>Welcome</h2></center>";
            }
            switch($this->model->useCaseContent){
                case "Authenticate":
                    $showThis=
                        "<br/>&nbsp;<br/><div style='width:300px;margin:0 auto;'><form action='index.php?action=Authenticate' method='POST'><input type=\"textbox\" name='Username'><br/>
         <input type='password' name='Password'><br/>
         <input type='reset'><input type='submit' value='Log in'></div>";
                    break;
                case "SignUpNewUser":
                    $showThis=$this->UserInformationForm("Please fill in the information: ","SignUpNewUser","Sign Up");
                    break;
                case "DefinePatient":
                    $this->showThis = $this->showSearchTool("DefinePatient", "Name");
                    break;
                case "Logout":
                    $showThis=$this->model->toCookie('UserID','Unknown');
                    $this->model->define('Unknown');
                    header("Location: /"); /* Redirect browser */
                    exit();
                case "ViewPrescription":
                    $showThis = $this->showThis . $this->model->ViewPrescription();
                    break;
                case "ViewAccountBalance":
                    $showThis = $this->model->ViewAccountBalance();
                    break;
                case "UpdateAccountInformation":
                    $showThis = $this->UserInformationForm("Please modify the necessary fields: ", "UpdateAccountInformation", "Update");
                    break;
                default:
                    break;
            }
        }elseif($operation=='showThis'){

        }elseif($operation=='loggedin'){


        };
        return "<div class=\"pageContent\">".$showThis."</div>";
    }

    public function showSearchTool($action, $attribute){
        return
            "<br/>&nbsp;<br/><div style='width:300px;margin:0 auto;'><form action='index.php?action=".$action."' method='POST'>Search ".$attribute.": <input type=\"textbox\" name='Name'><br/>
         <input type='reset'>&nbsp;<input type='submit' value='Search'></div>";
    }

    public function showPageFooter(){
        return "<div class=\"pageFooter\">"."</div>";
    }

    /**
     *
    $resultString="<div style='text-align:center;margin:auto;'><br/><br/><h2>Prescription history:</h2><br/>
    <div style='text-align:center;margin:0 auto;'><table><tr><td>Drug Name</td><td>Date and Time</td></tr>
    ";
    $result = $this->conn->query($queryString);
    while($row = $result->fetch_assoc())
    {
    $this->UserAttributes=$row;
    $RxName=$row['Name'];
    $RxTimestamp=$row['Timestamp'];
    $resultString = $resultString . "<tr><td>$RxName</td><td>$RxTimestamp</td><tr></div></div><br/>";
    }
    $resultString = $resultString."</table>";
     */
}
?>