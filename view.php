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


    public function showTemplate(){
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



    public function showPageContent(){

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