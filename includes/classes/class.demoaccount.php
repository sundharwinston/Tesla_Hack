<?php
class DemoAccount extends Base{

    private $db;
    private $canClose;
    private $IsRegistered;
    private $CaptchaKey;
    public $IsSubmitted = false;

	function __construct($close = true,$captchakey){
        parent::__construct();
        $this->IsRegistered=False;
        $this->PrintImmediate=False;
        $this->CaptchaKey = $captchakey;
        
        
	}

    public function getIsRegistered() {
        return $this->IsRegistered;
    }

    public function setRequest($value) {
        echo $value;
    }
	public function Register($skipcaptcha){	
        if (!$this->IsPost()){
            return;
        }
        $this->AddField('demoname','Please enter your name.');
        $this->AddFieldValidation('demoname', $this->CreateRegExValidation('The name contains unsupported characters', '/^[a-zA-Z0-9 ]+$/'));
        $this->AddField('demoemail','Please enter email address.');
        $this->AddFieldValidation('demoemail', $this->CreateEmailValidation('Please enter valid email'));
        $this->AddField('democountry','Please enter country');
        $this->AddFieldValidation('democountry', $this->CreateRegExValidation('The country is not allowed some special characters', '/^[a-zA-Z0-9.,!@#\- ]+$/'));
        $this->AddField('demophonenumber','Please enter phone number.');
        $this->AddFieldValidation('demophonenumber', $this->CreateRegExValidation('The phone number is not valid', '/^\+?[0-9 ]+$/'));
        if($skipcaptcha==0){
        $this->AddField('captcha', 'Please enter the code.');
        $this->AddFieldvalidation('captcha',$this->CreateSessionValidation("Please enter valid code",Constants::COMPANY . $this->CaptchaKey));
        }

        if ($_POST['demo_acc_reg'] != 'demo_acc_submitted') {
			return;
        }
        
        $this->IsSubmitted = true;
        if (!$this->Validate()){
            return;
        }
        $values=$this->ReadValues(array('demoname','demoemail','democountry','demophonenumber','demophoneprefix','captcha'));
        
        $this->db=Database::getDB();
        $ipaddress=Utils::ReadIPAddress();
		$location = Utils::ReadIPLocation($ipaddress);
        $key=Utils::RandomString(15);

        if ($this->IsRegistered($values['demoemail'],$isactivated,$activationkey)){
            $_SESSION[Constants::COMPANY.'demoaccount']=$values['demoemail'];
            if ($isactivated){
                $this->PrintError('<p><strong>You have tried to sign-up for al-shuaib demo account using an email address that has already been registered with us.</strong></p>'.
                    '<p>Please log-in to your account with your existing al-shuaib id delivered to you.</p>'.
                    '<p>If you have difficulty in logging to your free demo account please feel free to contact our customer support desk at <a href="mailto:info@al-shuaib.com" title="info@al-shuaib.com">info@al-shuaib.com</a>  or call us at <a href="tel:+96522435501">+965-22435501</a></p>',true);
            }
            else{
                $this->PrintSuccess('<p><strong>Your demo account has already been created and the activation link has been sent to your email id.</strong></p>'.
                    '<p>If you have not received the email with activation link yet or trouble logging to your demo account feel free to contact our customer support desk at <a href="mailto:info@al-shuaib.com" title="info@al-shuaib.com">info@al-shuaib.com</a>  or call us at <a href="tel:+96522435501" title="+965-22435501">+965-22435501</a></p>',true);
                $this->SendAcknowledgement($values['demoemail'],$values['demoname'],$activationkey);
            }
            $this->ResetRequest();
            return;
        }
        if ($this->SessionExists(Constants::COMPANY.'demoaccount',$values['demoemail'])){
            //Utils::PrintSuccess('<p><strong>Your demo account has already been created and the activation link has been sent to your email id.</strong></p>'.
            //        '<p>If you have not received the email with activation link yet or trouble logging to your demo account feel free to contact our customer support desk at <a href="mailto:info@al-shuaib.com" title="info@al-shuaib.com">info@al-shuaib.com</a>  or call us at <a href="tel:+96522435501" title="+965-22435501">+965-22435501</a></p>',$this->canClose);
            $this->ResetRequest();
            return TRUE;
        }
        $this->IsRegistered=$this->InsertDemoAccount($key,$values['demoname'],$values['demoemail'],$values['demophonenumber'],$values['democountry'],$ipaddress, $location);
        if ($this->IsRegistered){
            $_SESSION[Constants::COMPANY.'demoaccount']=$values['demoemail'];
            $this->ResetRequest();

            $subject="Demo Account Registration";
         
            $comment = '<table style="margin:0px auto;" class="email-body-table" align="center" class="" cellpadding="23" cellspacing="0" border="1">';
            $comment .= '<tr><td>Name</td><td>'  . $values['demoname'] . '</td></tr>';
            $comment .= '<tr><td>Email</td><td>'  . $values['demoemail'] . '</td></tr>';
            $comment .= '<tr><td>Country</td><td>'  . $values['democountry'] . '</td></tr>';
            $comment .= '<tr><td>Phone Number</td><td>'  . $values['demophonenumber'] . '</td></tr>';
            $comment .= '<tr><td colspan="2">&nbsp;</td></tr>';
            $comment .= '<tr><td>Client IP Address</td><td>'  . $ipaddress . '</td></tr>';
            $comment .= '<tr><td>Client Location</td><td>'  . $location . '</td></tr>';
            $comment .= '</table>';

            Mailer::SendMail('oc',$comment,$subject,"demosupport@al-shuaib.com","al-shuaib","backoffice@al-shuaib.com,graphics@kappsoft.com");
            $this->SendAcknowledgement($values['demoemail'],$values['demoname'],$key);
        }
        else{
            $this->PrintError('<p>Unexpected error. Code: 1010<p><p>If you need any assistance contact our customer support desk at <a href="mailto:info@al-shuaib.com" title="info@al-shuaib.com">info@al-shuaib.com</a>  or call us at <a href="tel:+96522435501">+965-22435501</a></p>',true);
        }
        $this->db=null;
        Database::freeDB();
        return $this->IsRegistered;
	}
    
    private function InsertDemoAccount($key,$name,$email,$phonenumber,$country,$ipaddress,$location)
    {
        try{
            $stmt = $this->db->prepare("INSERT INTO webdemoaccount(activationkey,name,email,phonenumber,country,ipaddress,location)VALUES(:key,:name,:email,:phonenumber,:country,:ipaddress,:location)");
            $result = $stmt->execute(array(
                "key"=>"$key",
                "name"=>"$name",
                "email"=>"$email",
                "phonenumber"=>"$phonenumber",
                "country"=>"$country",
                "ipaddress"=>"$ipaddress",
                "location"=>"$location"
            ));
            unset($stmt);
            return $result;
        }
        catch(PDOException $e){
            unset($stmt);
            return FALSE;
        }
    }
    
    protected function IsRegistered($email,&$isactivated,&$activationkey){
        try
        {
            $stmt = $this->db->prepare("SELECT activated,activationkey FROM webdemoaccount WHERE email=:email");
            $params=array("email"=>"$email");
            $result=false;
            if ($stmt->execute($params)){
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $result=($row!==FALSE);
                if ($result){
                    $isactivated = $row['activated'];
                    $activationkey = $row['activationkey'];
                }
            }
            unset($stmt);
            return $result;
        }
        catch(PDOException $e)
        {
            unset($stmt);
            return FALSE;
        }
    }

    private function SendAcknowledgement($email,$name,$key){
        $subject="Demo Account Registration";

        //Utils::PrintSuccess('<p>Your message has been forwarded.</p><p>Thank you for having taken your time to provide us with your valuable feedback.<p>');
        $url= ABS_URL . 'demoactivation/?email='.$email.'&code='.$key;
        
        $comment = '<p>Thank you for submitting the required information.</p>';
        $comment.= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment.= '<p>You have successfully registered for al-shuaib demo trading account.</p>';
        $comment.= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment.= '<p><a href="'.$url.'"><strong>Click here</strong></a> to activate your account.</p>';
        $comment.= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment.= '<p><strong>Or</strong></p>';
        $comment.= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment.= '<p>copy & paste the below link to activate it</p>';
        $comment.= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment.= '<p><a href="'.$url.'">'.$url.'</a></p>';
        
       Mailer::SendMail('uc',$comment,$subject,$email,$name,"graphics@kappsoft.com");
    }

    public function PrintDemoError() {
        if (!$this->IsPost()) {
            return;
        }
        echo $this->Message;
    }
}
?>