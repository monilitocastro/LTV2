<!DOCTYPE html>

<?php
class View
{
    private $model;
    private $controller;
    private $showThis;
    private $refShowThis;
    private $jsCodeCookie;

    public function __construct(&$controller,&$model) {
        $this->controller = &$controller;
        $this->model =& $model;
        ///The following is taken from http://www.pontikis.net/blog/create-cookies-php-javascript
        $this->jsCodeCookie =<<<EOT
/**
 * Create cookie with javascript
 *
 * @param {string} name cookie name
 * @param {string} value cookie value
 * @param {int} days2expire
 * @param {string} path
 */
function create_cookie(name, value, days2expire, path) {
  var date = new Date();
  date.setTime(date.getTime() + (days2expire * 24 * 60 * 60 * 1000));
  var expires = date.toUTCString();
  document.cookie = name + '=' + value + ';' +
                   'expires=' + expires + ';' +
                   'path=' + path + ';';
}
EOT;

    }

    public function SINGLEVALUE($arg){
        //print($arg);
        return "\$".$this->model->{$arg[0]};
    }

    public function TABLE($arg){
        //print('\''.$this->model->{$arg[0]}.'\'');
        //print $this->model->dataViewAccountBalance;
        $numRow = 0;
        $result = "<table>";
        $arr = $this->model->{$arg[0]};
        //print ($arr);
        foreach($arr as $key => $value){
            $numRow++;
            $color = ($numRow % 2 == 1)? "#BBFFBB": "#FFFFFF";
            if($numRow==1){
                $result = $result . "<tr style='background-color:black;color:white;'>";
            }else{
                $result = $result . "<tr style='background-color:$color;color:black;'>";
            }
            foreach($value as $item){
                $result = $result . "<td>".$item."</td>";
            }
            $result = $result. "</tr>";
        }
        if($numRow==0){$result=$result."No Prescription";}
        $result = $result . "</table>";

        return $result;
    }

    public function CheckCredentials(){
        //$Username = $this->model->Attributes['Username'];
        //$Password = $this->model->Attributes['Password'];
        return $this->controller->CheckCredentials();
    }

    public function BTN($arg){
        if(!isset($arg[3])){
            print 'BTN requires 4 inputs.';
        }
        $insertOrNot = ($arg[3] != '')?'onclick="create_cookie(\'opState\', \''.$arg[3].'\', 1, \'/\');" ' : "";
        $result='<button type=\''.$arg[0].'\' value=\''.$arg[1].'\' '.$insertOrNot.' style=\'padding:5px;color:white;background-color:black;border-radius:2px\'>'.$arg[2].'</button>&nbsp;&nbsp;';
        return $result;
    }

    public function IB($arg){
        //print "INSIDE IB!";
        $arg2= "";
        if(!isset($arg[2])){
            $arg2="";
        }else{
            $arg2= $arg[2];
        }
        $result=<<<EOT
<p class='formlabel'>$arg[0]</p>
<input type='textbox' name='$arg[1]' style='border:2px;'/ value='$arg2'><br/>
EOT;
        return $result;
    }
    public function BR(){
        return "<BR/>";
    }
    public function ConcatenationWithDynamicFunctionCalls(){

        $result = "<div style='margin:auto;padding-left:100px;'><form method='POST' action='index.php'><h3>"
            .$this->model->showThis
        ."</h3><br/>&nbsp;<br/>";
        foreach($this->controller->makeThese as $key => $value){
            //print_r($value[0]);
            $argArray = array_splice($value, 1);
            //print_r($argArray);
            $result = $result . $this->{$value[0]}($argArray);
        }
        return $result.'</form></div>';
    }

/**    Frustrated with this method
    public function ConcatenationWithDynamicFunctionCalls(){
        $result = 'RESULT VAR';
        foreach($this->controller->makeThese as $key => $value){
            $functName = $value[0];//substr($row[0], 0, strlen($row[0]) );
            $argArray = array_splice($value, 1);
            //IMPORTANT: each function provided by $functName must return a string.
            print_r($value[0]);
            print_r($argArray);
            //print "Is callable: ". is_callable($value) ? 'True':'False';
           $result = $result . call_user_func_array(array($this->controller, $value), $argArray);
        }
        //$this->{call_user_func_array(array('IB','Value', 'Label'))};
        return $result;
    }
*/
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
        $result = '';
        if($this->model->Attributes['PatientName']=='Unknown'){
            $result = $result. 'Patient unknown. Please Authenticate.';
        }else{
            $result = $result . $this->model->Attributes['PatientName'];
        }

        return $result;
    }

    public function createSideBar(){
        //use javascript to set opState as $key
        $result = "Patient: ".$this->PatientNameView()."<br><br>";
        foreach($this->model->attributes as $key => $value){
            if($value==1){
                $url = str_replace(' ', '', $key);
                //$result = $result . "<li><a href='index.php' onClick='create_cookie('opState', '$url', 1, '/')'>".$key."</a></li>";
                $result = $result . "<li><a href='index.php' onClick=\"create_cookie('opState', '$url', 1, '/');\">".$key."</a></li>";
            }
        }

        return $result;
    }

    /**
     * This function should be the only one called by index.php that is by view.php
     * @return string
     */

    public function outputWebPage(){
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
        return "<body><script>"
                .$this->jsCodeCookie
                . "</script><div class=\"blended_grid\">"
                . $this->showPageHeader()
                . $this->showPageLeftMenu()
                .$this->showPageContent()
                . $this->showPageFooter()
                ."</div>" . "</body>";
    }
    public function showPageHeader(){
        return "<div class=\"pageHeader\"><img src=\"logo.png\"></div>";
    }
    public function showPageLeftMenu(){
        return "<div class=\"pageLeftMenu\">
	    <ul>". $this->createSideBar() ."</ul>" ."</div>";
    }



    public function showPageContent(){
        return "<div class=\"pageContent\"><div style='margin:0 auto;padding-left:80px;'>"
        ."<br/>&nbsp;<br/>"
        . $this->ConcatenationWithDynamicFunctionCalls()
        ."</div></div>";
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