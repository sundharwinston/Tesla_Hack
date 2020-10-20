<?php
class GetACall extends Base{

    private $db;
    private $IsRegistered;

	function __construct(){
        parent::__construct();
        $this->IsRegistered=false;
        $this->AddField('getacallname','Please enter your name.');
        $this->AddFieldValidation('getacallname', $this->CreateRegExValidation('The name contains unsupported characters', '/^[a-zA-Z0-9 ]+$/'));
        $this->AddField('getacallemail','Please enter email address.');
        $this->AddFieldValidation('getacallemail', $this->CreateEmailValidation('Please enter valid email'));
        $this->AddField('getacallphonenumber','Please enter contact number.');
        $this->AddFieldValidation('getacallphonenumber', $this->CreateRegExValidation('The phone number is not valid', '/^\+?[0-9 ]+$/'));
        $this->AddFieldValidation('getacalltimetocall', $this->CreateRegExValidation('The time to call is not allowed some special characters', '/^[a-zA-Z0-9.,!@#\- ]+$/'));
        $this->AddFieldValidation('getacallcomments', $this->CreateRegExValidation('The comment is not allowed some special characters', '/^[a-zA-Z0-9.,!@#\- ]+$/'));
        $this->AddField('getacallcaptcha', 'Please enter the code.');
        $this->AddFieldvalidation('getacallcaptcha',$this->CreateSessionValidation("Please enter valid code",Constants::COMPANY.'getacall'));
	}

    public function getIsRegistered() {
        return $this->IsRegistered;
    }

    public function setRequest($value) {
        echo $value;
    }

	public function Register(){	
        if (!$this->IsPost())
        {
            return;
        }

        if ($_POST['getacall_reg'] != 'getacall_submitted') {
			return;
        }

        if (!$this->Validate())
        {
            return;
        }
        $values=$this->ReadValues(array('getacallname','getacallemail','getacallphonenumber','getacallphoneprefix','getacalltimetocall','getacallcomments','getacallcaptcha'));

        if ($this->SessionExists(Constants::COMPANY.'getacall',$values['getacallname'].$values['getacallphonenumber'].$values['getacallemail']))
        {
            $this->ResetRequest();
            $this->PrintError('<p>Your message has been already forwarded.</p><p>Thank you for your interest in Al Shuaib International Financial Brokerage Co.<p>');
            return;
        }

        $this->db=Database::getDB();
        $ipaddress=Utils::ReadIPAddress();
		$location=Utils::ReadIPLocation($ipaddress);
        $this->IsRegistered=$this->InsertGetACall($values['getacallname'],$values['getacallemail'],$values['getacallphonenumber'],$values['getacalltimetocall'],$values['getacallcomments'],$ipaddress, $location);
        if ($this->IsRegistered){
            $_SESSION[Constants::COMPANY.'getacall']=$values['getacallname'].$values['getacallphonenumber'].$values['getacallemail'];
            $this->ResetRequest();

            $subject="Get A Call";
         
            $comment = '<table style="margin:0px auto;" class="email-body-table" align="center" class="" cellpadding="23" cellspacing="0" border="1">';
            $comment .= '<tr><td>Name</td><td>'  . $values['getacallname'] . '</td></tr>';
            $comment .= '<tr><td>Email</td><td>'  . $values['getacallemail'] . '</td></tr>';
            $comment .= '<tr><td>Phone Number</td><td>'  . $values['getacallphonenumber'] . '</td></tr>';
            $comment .= '<tr><td>Convenient time to call</td><td>'  . $values['getacalltimetocall'] . '</td></tr>';
            $comment .= '<tr><td>Comments</td><td>'  . $values['getacallcomments'] . '</td></tr>';
            $comment .= '<tr><td colspan="2">&nbsp;</td></tr>';
            $comment .= '<tr><td>Client IP Address</td><td>'  . $ipaddress . '</td></tr>';
            $comment .= '<tr><td>Client Location</td><td>'  . $location . '</td></tr>';
            $comment .= '</table>';

            Mailer::SendMail('oc',$comment,$subject,"notifications@al-shuaib.com","Al Shuaib International Financial Brokerage Co.","backoffice@al-shuaib.com,graphics@kappsoft.com");

            Utils::PrintSuccess('<p>Your message has been forwarded.</p><p>Thank you for your interest in Al Shuaib International Financial Brokerage Co.<p>');

            $comment = '<p>Your message has been forwarded.</p>'
                    . '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>'
                    . '<p>Thank you for your interest in Al Shuaib International Financial Brokerage Co.</p>'
                    . '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
            
            Mailer::SendMail('uc',$comment,$subject,$values['getacallemail'],$values['getacallname'],"graphics@kappsoft.com");
        }
        $this->db=null;
        Database::freeDB();
	}
    
    private function InsertGetACall($name,$email,$phonenumber,$timetocall,$comments,$ipaddress,$location)
    {
        try
        {
            $stmt = $this->db->prepare("INSERT INTO webgetacall(name,email,phonenumber,timetocall,comments,ipaddress,location)VALUES(:name,:email,:phonenumber,:timetocall,:comments,:ipaddress,:location)");
            $result = $stmt->execute(array(
                "name"=>"$name",
                "email"=>"$email",
                "phonenumber"=>"$phonenumber",
                "timetocall"=>"$timetocall",
                "comments"=>"$comments",
                "ipaddress"=>"$ipaddress",
                "location"=>"$location"
            ));
            unset($stmt);
            return $result;
        }
        catch(PDOException $e)
        {
            unset($stmt);
            return FALSE;
        }
    }
}
?>