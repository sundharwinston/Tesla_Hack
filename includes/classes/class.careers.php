<?php

class Careers extends Base {

    private $db;
    private $IsRegistered;
    public $IsSubmitted = false;

    function __construct() {
        parent::__construct();
        $this->IsRegistered = false;
        $this->AddField('name', 'Please enter your full name.');
        $this->AddFieldValidation('name', $this->CreateRegExValidation('The name contains unsupported characters', '/^[a-zA-Z0-9 ]+$/'));
        $this->AddFieldValidation('post', $this->CreateInvalidFieldValidation('Please enter valid position.'));
        $this->AddFieldValidation('post', $this->CreateRegExValidation('The post is not allowed some special characters', '/^[a-zA-Z0-9.,!@#\- ]+$/'));
        $this->AddField('phonenumber', 'Please enter your phone number.');
        $this->AddFieldValidation('phonenumber', $this->CreateRegExValidation('The phone number is not valid', '/^\+?[0-9 ]+$/'));
        $this->AddField('email', 'Please enter email address.');
        $this->AddFieldValidation('email', $this->CreateEmailValidation('Please enter valid email'));

        $this->AddFieldValidation('details', $this->CreateInvalidFieldValidation('Please enter valid details.'));
        $this->AddFieldValidation('details', $this->CreateRegExValidation('The detail is not allowed some special characters', '/^[a-zA-Z0-9.,!@#\- ]+$/'));
        $this->AddField('captcha', 'Please enter the code.');
        $this->AddFieldvalidation('captcha',$this->CreateSessionValidation("Please enter valid code",Constants::COMPANY.'career'));
    }

    public function getIsRegistered() {
        return $this->IsRegistered;
    }

    public function setRequest($value) {
        echo $value;
    }

    public function Register() {
        if (!$this->IsPost()) {
            return;
        }

        if ($_POST['career_reg'] != 'career_submitted') {
			return;
        }

        $this->IsSubmitted = true;

        if (!$this->Validate()) {
            return;
        }

        $values = $this->ReadValues(array('name', 'email', 'phonenumber', 'post', 'details', 'captcha'));

        if ($this->SessionExists(Constants::COMPANY . 'careers', $values['name'] . $values['phonenumber'] . $values['email'])) {
            $this->ResetRequest();
            Utils::PrintSuccess('<p><strong>Your information already has been successfully submitted.</strong></p><hr><p>We will get back to you at the earliest.</p>');
            return;
        }

        $fileupload = new UploadFile('download/resumes/', 'doc,docx,pdf');
        if ($fileupload->Upload('resume') !== TRUE) {
            $this->PrintError('<p>' . $fileupload->Error . '</p>');
            return;
        }
        $filename = $fileupload->FileName;
        $this->db = Database::getDB();
        $ipaddress = Utils::ReadIPAddress();
        $location = Utils::ReadIPLocation($ipaddress);
        $this->IsRegistered = $this->InsertCareers($values['name'], $values['email'], $values['phonenumber'], $values['post'], $values['details'], $filename, $ipaddress, $location);
        if ($this->IsRegistered) {
            $_SESSION[Constants::COMPANY . 'careers'] = $values['name'] . $values['phonenumber'] . $values['email'];
            $this->ResetRequest();

            $subject = "Careers";

            $url = ABS_URL . 'download/resumes/' . $filename;

            $comment =  '<table style="margin:0px auto;" class="email-body-table" align="center" class="" cellpadding="23" cellspacing="0" border="1">';
            $comment .= '<tr><td>Name</td><td>' . $values['name'] . '</td></tr>';
            $comment .= '<tr><td>Position</td><td>' . $values['post'] . '</td></tr>';
            $comment .= '<tr><td>Phone Number</td><td>' . $values['phonenumber'] . '</td></tr>';
            $comment .= '<tr><td>Email</td><td>' . $values['email'] . '</td></tr>';
            $comment .= '<tr><td>Details</td><td>' . $values['details'] . '</td></tr>';
            $comment .= '<tr><td colspan="2"><p><a href="' . $url . '"><strong>Click Here</strong></a> to download Resume</p><p>or copy &amp; paste the following link</p><p><a href="' . $url . '">' . $url . '</a></p></td></tr>';
            $comment .= '<tr><td colspan="2">&nbsp;</td></tr>';
            $comment .= '<tr><td>Client IP Address</td><td>' . $ipaddress . '</td></tr>';
            $comment .= '<tr><td>Client Location</td><td>' . $location . '</td></tr>';
            $comment .= '</table>';

            Mailer::SendMail('oc',$comment, $subject, "info@al-shuaib.com", "Al Shuaib International Financial Brokerage Co.", "backoffice@al-shuaib.com,graphics@kappsoft.com");

            Utils::PrintSuccess('<p><strong>Thank you for your interest in Al Shuaib International Financial Brokerage Co.</strong></p><hr><p>Your information has been successfully submitted. We will get back to you at the earliest.</p>');

            $comment = '<p>Thank you for your interest in Al Shuaib International Financial Brokerage Co.</p>'
                    . '<p>Your information has been successfully submitted.</p>'
                    . '<p>We will get back to you at the earliest.</p>';

           Mailer::SendMail('uc',$comment, $subject, $values['email'], $values['name'], "graphics@kappsoft.com");
        } else {
            $this->PrintError('<p>Unexpected Error!!</p><p>Code :' . Constants::CAREERSERROR . '</p>');
        }
        $this->db = null;
        Database::freeDB();
    }

    private function InsertCareers($name, $email, $phonenumber, $post, $details, $filename, $ipaddress, $location) {
        try {
            $stmt = $this->db->prepare("INSERT INTO webcareers(name,email,phonenumber,post,details,filename,ipaddress,location)VALUES(:name,:email,:phonenumber,:post,:details,:filename,:ipaddress,:location)");
            $result = $stmt->execute(array(
                "name" => "$name",
                "email" => "$email",
                "phonenumber" => "$phonenumber",
                "post" => "$post",
                "details" => "$details",
                "filename" => "$filename",
                "ipaddress" => "$ipaddress",
                "location" => "$location"
            ));
            unset($stmt);
            return $result;
        } catch (PDOException $e) {
            unset($stmt);
            return FALSE;
        }
    }

}

?>