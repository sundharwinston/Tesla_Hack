<?php

class MT4Demo extends Base {

    private $db;

    function __construct() {
        parent::__construct();
        $this->PrintImmediate = FALSE;
    }

    public function getIsCreateDemo() {
        if ($this->IsGet())
            return FALSE;
        return $this->IsCreate();
    }

    public function getIsCreate() {
        if (!isset($_POST['btnsubmit']))
            return;
        return ($_POST['btnsubmit'] === "submit");
    }

    public function Register() {
        if (!$this->IsPost()) {
            return;
        }
        If ($this->IsCreate === TRUE) {
            $this->RegisterDemo();
        } else if ($this->IsCreate === FALSE) {
            $this->ResendDemo();
        }
    }

    public function RegisterDemo() {

        $this->AddField('mt4name', 'Please enter your name.');
        $this->AddFieldValidation('mt4name', $this->CreateInvalidFieldValidation('Please enter valid name.'));
        $this->AddFieldValidation('mt4name', $this->CreateRegExValidation('The name contains unsupported characters', '/^[a-zA-Z0-9 ]+$/'));
        $this->AddField('mt4email', 'Please enter email address.');
        $this->AddFieldValidation('mt4email', $this->CreateEmailValidation('Please enter valid email'));
        $this->AddField('mt4country', 'Please enter country');
        $this->AddField('mt4phonenumber', 'Please enter phone number.');
        $this->AddFieldValidation('mt4phonenumber', $this->CreateRegExValidation('The contact number is not valid', '/^\+?[0-9 ]+$/'));

        $this->AddField('code', 'Please enter code.');
        $this->AddFieldValidation('code', $this->CreateSessionValidation('Please enter valid code.', Constants::COMPANY . 'mt4democapcha'));

        if (!$this->Validate()) {
            return;
        }

        $values = $this->ReadValues(array('mt4name', 'mt4email', 'mt4country', 'mt4phonenumber', 'mt4phoneprefix'));

        if ($this->SessionExists(Constants::COMPANY . 'mt4demo', $values['mt4email'])) {
            $this->ResetRequest();
            $this->ClearCapcha();
            header('Location:meta-demo-success.php');
            exit;
        }

        $this->db = Database::getDB();
        $ipaddress = Utils::ReadIPAddress();
        $location = Utils::ReadIPLocation($ipaddress);
        $key = Utils::RandomString(15);

        $registered = $this->IsRegistered($values['mt4email'], $name);
        If ($registered === TRUE) {
            $this->SetErrorMessage('<p><strong>You have tried to sign-up for Al-shuaib Meta trader demo account using an email address that has already been registered with us.</strong></p>' .
                    '<p>If you need any assistance contact our customer support desk at <a href="mailto:info@al-shuaib.com" title="info@al-shuaib.com">info@al-shuaib.com</a>  or call us at <a href="tel:+96522435501">+965-22435501</a></p>');
            $this->ResetRequest();
            $_REQUEST['mt4resendemail'] = $values['mt4email'];
            $this->ClearCapcha();
            return;
        }

        $isregistered = $this->InsertMT4DemoAccount($key, $values['mt4name'], $values['mt4email'], $values['mt4country'], $values['mt4phonenumber'], $ipaddress, $location);
        if ($isregistered) {
            $_SESSION[Constants::COMPANY . 'mt4demo'] = $values['mt4email'];
            $this->ResetRequest();

            $subject = "MT 4 Demo Account";

            $comment = '<table style="margin:0px auto;" class="email-body-table" align="center" class="" cellpadding="23" cellspacing="0" border="1">';
            $comment .= '<tr><td>Name</td><td>' . $values['mt4name'] . '</td></tr>';
            $comment .= '<tr><td>Email</td><td>' . $values['mt4email'] . '</td></tr>';
            $comment .= '<tr><td>Country</td><td>' . $values['mt4country'] . '</td></tr>';
            $comment .= '<tr><td>Phone Number</td><td>' . $values['mt4phonenumber'] . '</td></tr>';
            $comment .= '<tr><td colspan="2">&nbsp;</td></tr>';
            $comment .= '<tr><td>Client IP Address</td><td>' . $ipaddress . '</td></tr>';
            $comment .= '<tr><td>Client Location</td><td>' . $location . '</td></tr>';
            $comment .= '</table>';

            Mailer::SendMail('oc', $comment, $subject, "demosupport@al-shuaib.com", "Al-shuaib", "backoffice@al-shuaib.com,graphics@kappsoft.com");
            $this->SendAcknowledgement($values['mt4email'], $values['mt4name']);
        } else {
            $this->SetErrorMessage('<strong>Unexpected error. Code: ' . Constants::MT4DEMOERROR . '</strong><hr/><p>If you need any assistance contact our customer support desk at <a href="mailto:info@al-shuaib.com" title="info@al-shuaib.com">info@al-shuaib.com</a>  or call us at <a href="tel:+96522435501">+965-22435501</a></p>');
        }
        $this->ClearCapcha();
        $this->db = null;
        Database::freeDB();
        if ($isregistered) {
            header('Location:meta-demo-success.php');
            exit;
        }
    }

    public function ResendDemo() {
        $this->AddField('mt4resendemail', 'Please enter email address.');
        $this->AddFieldValidation('mt4resendemail', $this->CreateEmailValidation('Please enter valid email'));

        $this->AddField('code', 'Please enter code.');
        $this->AddFieldValidation('code', $this->CreateSessionValidation('Please enter valid code.', Constants::COMPANY . 'mt4resendcapcha'));

        if (!$this->Validate()) {
            return;
        }

        $values = $this->ReadValues(array('mt4resendemail'));

        $this->db = Database::getDB();
        $ipaddress = Utils::ReadIPAddress();
        $location = Utils::ReadIPLocation($ipaddress);

        if ($this->SessionExists(Constants::COMPANY . 'mt4resend', $values['mt4resendemail'])) {
            $this->ClearCapcha();
            header('Location:meta-demo-success.php');
            exit;
        }

        $isregistered = $this->IsRegistered($values['mt4resendemail'], $name);
        if ($isregistered === TRUE) {
            $this->ResetRequest();
            $this->ClearCapcha();
            $this->SendAcknowledgement($values['mt4resendemail'], $name);
            header('Location:meta-demo-success.php');
            exit;
        } elseif ($isregistered === FALSE) {
            $this->SetErrorMessage('<p>You have tried to download the Al-shuaib Meta trader demo setup using an email address that has not been registered with us.</p>' .
                    '<p>Please register your email to get Al-shuaib MT 4 Demo download.</p><hr/>' .
                    '<p>If you need any assistance contact our customer support desk at <a href="mailto:info@al-shuaib.com" title="info@al-shuaib.com">info@al-shuaib.com</a>  or call us at <a href="tel:+96522435501">+965-22435501</a></p>');
            $this->ResetRequest();
            $_REQUEST['mt4email'] = $values['mt4resendemail'];
        } else {
            $this->SetErrorMessage('<strong>Unexpected error. Code: ' . Constants::MT4RESENDERROR . '</strong><hr/><p>If you need any assistance contact our customer support desk at <a href="mailto:info@al-shuaib.com" title="info@al-shuaib.com">info@al-shuaib.com</a>  or call us at <a href="tel:+96522435501">+965-22435501</a></p>');
        }
        $this->ClearCapcha();
        $this->db = null;
        Database::freeDB();
    }

    private function InsertMT4DemoAccount($key, $name, $email, $phonenumber, $country, $ipaddress, $location) {
        try {
            $stmt = $this->db->prepare("INSERT INTO webmt4demoaccount(activationkey,name,email,phonenumber,country,ipaddress,location)VALUES(:key,:name,:email,:phonenumber,:country,:ipaddress,:location)");
            $result = $stmt->execute(array(
                "key" => "$key",
                "name" => "$name",
                "email" => "$email",
                "phonenumber" => "$phonenumber",
                "country" => "$country",
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

    protected function IsRegistered($email, &$name) {
        try {
            $stmt = $this->db->prepare("SELECT name FROM webmt4demoaccount WHERE email=:email");
            $params = array("email" => "$email");
            $result = false;
            if ($stmt->execute($params)) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $result = ($row !== FALSE);
                if ($result) {
                    $name = $row['name'];
                }
            }
            unset($stmt);
            return $result;
        } catch (PDOException $e) {
            unset($stmt);
            return;
        }
    }

    private function SendAcknowledgement($email, $name) {
        $subject = "Al-shuaib - MT 4 Demo Download";

        $comment = '<p><strong>Thank you for signing up for a free demo account!</strong></p>';
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment .= '<p>You\'re all set and ready to try out your trading strategies in a risk free environment.</p>';
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment .= '<p>Click on the Download Now button and follow the instructions to install the platform on your PC.</p>';        
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment .= '<h2><strong>Trade with MT4</strong></h2>';
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment .= '<p>Trade with the most popular Forex trading platform.</p>';
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment .= '<p><a style="line-height: 1.33;letter-spacing: 0.08em;padding:10px 16px;background: #f28841;font-size: 16px;border-color: #f28841;color: #fff !important;" href="http://www.kerford.co.uk/metasoftware/kerforduk4setup.exe"><strong>DOWNLOAD NOW !</strong></a></p>';
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        
        
        Mailer::SendMail('uc',$comment, $subject,$email, $name, "graphics@kappsoft.com");
    }

    public function PrintResendError() {
        if ($this->IsGet()) {
            return;
        }

        if ($this->IsCreate === FALSE) {
            echo $this->Message;
        }
    }

    public function PrintDemoError() {
        if ($this->IsGet()) {
            return;
        }
        if ($this->IsCreate === TRUE) {
            echo $this->Message;
        }
    }

    private function ClearCapcha() {
        unset($_SESSION[Constants::COMPANY . 'mt4democapcha']);
        unset($_SESSION[Constants::COMPANY . 'mt4resendcapcha']);
    }

}

?>