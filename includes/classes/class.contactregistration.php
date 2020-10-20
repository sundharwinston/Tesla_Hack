<?php

class ContactRegistration extends Base {

    private $db;
    private $IsRegistered;
    public $IsSubmitted = false;

    function __construct() {
        parent::__construct();
        $this->IsRegistered = false;
        $this->PrintImmediate = FALSE;
        $this->AddField('contactregname', 'Please enter your name.');
        $this->AddFieldValidation('contactregname', $this->CreateRegExValidation('The name contains unsupported characters', '/^[a-zA-Z0-9 ]+$/'));
        $this->AddField('contactregemail', 'Please enter email address.');
        $this->AddFieldValidation('contactregemail', $this->CreateEmailValidation('Please enter valid email'));
        $this->AddField('contactregphonenumber', 'Please enter phone number.');
        $this->AddFieldValidation('contactregphonenumber', $this->CreateRegExValidation('The phone number is not valid', '/^\+?[0-9 ]+$/'));
        $this->AddField('contactregcountry', 'Please select country.');
        $this->AddFieldValidation('contactregcountry', $this->CreateRegExValidation('The country is not allowed some special characters', '/^[a-zA-Z0-9.,!@#\- ]+$/'));
        $this->AddField('captcha', 'Please enter the code.');
        $this->AddFieldvalidation('captcha',$this->CreateSessionValidation("Please enter valid code",Constants::COMPANY.'contact'));
    }

    public function Register($skipmessage) {
        if (!$this->IsPost()) {
            return null;
        }
        if($skipmessage==0){
            $this->AddField('message', 'Please enter your message.');
            $this->AddFieldValidation('message', $this->CreateRegExValidation('The message is not allowed some special characters', '/^[a-zA-Z0-9.,!@#\- ]+$/'));
        }

        if ($_POST['contact_reg'] != 'contact_submitted') {
			return;
        }

        $this->IsSubmitted = true;
        if (!$this->Validate()) {
            return null;
        }
        $values = $this->ReadValues(array('contactregname', 'contactregemail', 'contactregphonenumber', 'contactregcountry','message','captcha'));

        $this->db = Database::getDB();

        if ($this->CheckDuplicate($values['contactregemail'])) {
            //return false;
        }

        $ipaddress = Utils::ReadIPAddress();
        $location = Utils::ReadIPLocation($ipaddress);
        $this->IsRegistered = $this->InsertContactRegistration($values['contactregname'], $values['contactregemail'], $values['contactregphonenumber'], $values['contactregcountry'],$values['message'], $ipaddress, $location);
        if ($this->IsRegistered) {

            $subject = "Contact Registration";

            $mail_content = '<table class="email-body-table" align="center" cellpadding="23" cellspacing="0" border="1">'
                    . '<tr><td>Name</td><td>' . $values['contactregname'] . '</td></tr>'
                    . '<tr><td>Email</td><td>' . $values['contactregemail'] . '</td></tr>'
                    . '<tr><td>Phone Number</td><td>' . $values['contactregphonenumber'] . '</td></tr>'
                    . '<tr><td>Country</td><td>' . $values['contactregcountry'] . '</td></tr>'
                    . '<tr><td>Message</td><td>' . $values['message'] . '</td></tr>'
                    . '<tr><td colspan="2">&nbsp;</td></tr>'
                    . '<tr><td>Client IP Address</td><td>' . $ipaddress . '</td></tr>'
                    . '<tr><td>Client Location</td><td>' . $location . '</td></tr>'
                    . '</table>';

                        Mailer::SendMail('oc',$mail_content, $subject, "info@al-shuaib.com", "Al-Shuaib International Financial Brokerage Co.", "backoffice@al-shuaib.com,graphics@kappsoft.com");

            $subject = "Contact Registration";

            $mail_content = '<p><b>Thank You for submitting the required information.</b></p>'
                    . '<p>You will be contacted soon from our customer support desk.</p>';

            Mailer::SendMail('uc',$mail_content, $subject, $values['contactregemail'], $values['contactregname'], "graphics@kappsoft.com");
            return true;
        } else {
            return false;
        }

        $this->db = null;
        Database::freeDB();
    }

    private function CheckDuplicate($email) {
        try {
            $stmt = $this->db->prepare(" SELECT id FROM webcontactregistration where email = :email");
            $result = $stmt->execute(array("email" => "$email"));
            $data = $stmt->fetchObject();
            if ($data->id) {
                unset($stmt);
                return TRUE;
            } else {
                unset($stmt);
                return FALSE;
            }
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return FALSE;
        }
    }

    private function InsertContactRegistration($name, $email, $phone, $country,$message,$ipaddress, $location) {
        try {
            $stmt = $this->db->prepare("INSERT INTO webcontactregistration(name,email,phone,country,message,ipaddress,location)VALUES(:name,:email,:phone,:country,:message,:ipaddress,:location)");
            $result = $stmt->execute(array(
                "name" => "$name",
                "email" => "$email",
                "phone" => "$phone",
                "country" => "$country",
                "message" => "$message",
                "ipaddress" => "$ipaddress",
                "location" => "$location"
            ));
            unset($stmt);
            return $result;
        } catch (PDOException $e) {
            //echo $e->getMessage();
            return FALSE;
        }
    }

    public function PrintContactError() {
        if (!$this->IsPost()) {
            return;
        }
        echo $this->Message;
    }

}

?>