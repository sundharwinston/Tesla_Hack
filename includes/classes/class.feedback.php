<?php
class Feedback extends Base{

    private $db;
    private $IsRegistered;

	function __construct(){
        parent::__construct();
        $this->IsRegistered=false;
        $this->AddField('feedbackname','Please enter your name.');
        $this->AddFieldValidation('feedbackname', $this->CreateRegExValidation('The name contains unsupported characters', '/^[a-zA-Z0-9 ]+$/'));
        $this->AddField('feedbackemail','Please enter email address.');
        $this->AddFieldValidation('feedbackemail', $this->CreateEmailValidation('Please enter valid email'));
        $this->AddField('feedbacksubject','Please enter subject.');
        $this->AddFieldValidation('feedbacksubject', $this->CreateRegExValidation('The subject is not allowed some special characters', '/^[a-zA-Z0-9.,!@#\- ]+$/'));
        $this->AddFieldValidation('feedbackphonenumber', $this->CreateRegExValidation('The phone number is not valid', '/^\+?[0-9 ]+$/'));
        $this->AddFieldValidation('feedbackcomments', $this->CreateRegExValidation('The comment is not allowed some special characters', '/^[a-zA-Z0-9.,!@#\- ]+$/'));
        $this->AddField('feedbackcaptcha', 'Please enter the code.');
        $this->AddFieldvalidation('feedbackcaptcha',$this->CreateSessionValidation("Please enter valid code",Constants::COMPANY.'feedback'));
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

        if ($_POST['feedback_reg'] != 'feedback_submitted') {
			return;
        }

        if (!$this->Validate())
        {
            return;
        }
        $values=$this->ReadValues(array('feedbackname','feedbacksubject','feedbackemail','feedbackphonenumber','feedbackphoneprefix','feedbackcomments','feedbackcaptcha'));

        if ($this->SessionExists(Constants::COMPANY.'feedback',$values['feedbackname'].$values['feedbacksubject'].$values['feedbackemail']))
        {
            $this->ResetRequest();
            $this->PrintError('<p>Your message has been already forwarded.</p><p>Thank you for having taken your time to provide us with your valuable feedback.<p>');
            return;
        }

        $this->db=Database::getDB();
        $ipaddress=Utils::ReadIPAddress();
		$location=Utils::ReadIPLocation($ipaddress);
        $this->IsRegistered=$this->InsertFeedback($values['feedbackname'],$values['feedbackemail'],$values['feedbacksubject'],$values['feedbackphonenumber'],$values['feedbackcomments'],$ipaddress, $location);
        if ($this->IsRegistered){
            $_SESSION[Constants::COMPANY.'feedback']=$values['feedbackname'].$values['feedbacksubject'].$values['feedbackemail'];
            $this->ResetRequest();

            $subject="Feedback";
         
            $comment = '<table style="margin:0px auto;" class="email-body-table" align="center" class="" cellpadding="23" cellspacing="0" border="1">';
            $comment .= '<tr><td>Name</td><td>'  . $values['feedbackname'] . '</td></tr>';
            $comment .= '<tr><td>Email</td><td>'  . $values['feedbackemail'] . '</td></tr>';
            $comment .= '<tr><td>Subject</td><td>'  . $values['feedbacksubject'] . '</td></tr>';
            $comment .= '<tr><td>Phone Number</td><td>'  . $values['feedbackphonenumber'] . '</td></tr>';
            $comment .= '<tr><td>Comments</td><td>'  . $values['feedbackcomments'] . '</td></tr>';
            $comment .= '<tr><td colspan="2">&nbsp;</td></tr>';
            $comment .= '<tr><td>Client IP Address</td><td>'  . $ipaddress . '</td></tr>';
            $comment .= '<tr><td>Client Location</td><td>'  . $location . '</td></tr>';
            $comment .= '</table>';

            Mailer::SendMail('oc',$comment,$subject,"notifications@al-shuaib.com","Al Shuaib International Financial Brokerage Co.","backoffice@al-shuaib.com,graphics@kappsoft.com");

            Utils::PrintSuccess('<p>Your message has been forwarded.</p><p>Thank you for having taken your time to provide us with your valuable feedback.<p>');

            $comment = '<p>Your message has been forwarded.</p>'
                    . '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>'
                    . '<p>Thank you for having taken your time to provide us with your valuable feedback. </p>'
                    . '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
            
            
            Mailer::SendMail('uc',$comment,$subject,$values['feedbackemail'],$values['feedbackname'],"graphics@kappsoft.com");
        }
        $this->db=null;
        Database::freeDB();
	}
    
    private function InsertFeedback($name,$email,$subject,$phonenumber,$comments,$ipaddress,$location)
    {
        try
        {
            $stmt = $this->db->prepare("INSERT INTO webfeedback(name,email,subject,phonenumber,comments,ipaddress,location)VALUES(:name,:email,:subject,:phonenumber,:comments,:ipaddress,:location)");
            $result = $stmt->execute(array(
                "name"=>"$name",
                "email"=>"$email",
                "subject"=>"$subject",
                "phonenumber"=>"$phonenumber",
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