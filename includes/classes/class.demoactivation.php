<?php
class DemoActivation extends Base {

    private $db;
    private $IsRegistered;
    private $isDemoError;

    function __construct() {
        parent::__construct();
        $this->IsRegistered = false;
        $this->PrintImmediate = false;
        $this->isDemoError = False;
        $this->AddField('email', 'email address not found');
        $this->AddFieldValidation('email', $this->CreateEmailValidation('Please enter valid email'));
        $this->AddField('code', 'activation code not found');
    }

    public function getIsRegistered() {
        return $this->IsRegistered;
    }

    public function getDemoError() {
        return $this->isDemoError;
    }

    public function setRequest($value) {
        echo $value;
    }

    public function Register() {

        if ($this->Validate() !== TRUE) {
            return;
        }

        $values = $this->ReadValues(array('email', 'code'));

        $this->db = Database::getDB();

        //if ($this->SessionExists(Constants::COMPANY.'demoactivation',$values['email'].$values['code'])){
        //    $this->ResetRequest();
        //    return FALSE;
        //}
        $this->IsRegistered = $this->IsRegistered($values['email'], $values['code'], $isactivated, $name, $country, $phonenumber);
        if ($this->IsRegistered) {
            if (!$isactivated) {
                $xml = simplexml_load_file("http://demo1.reymount.com/WebService/DemoAccountCreation/DemoAccountCreationService.asmx/CreateDemoAccount?name=" . $name . "&country=" . $country . "&phone=" . $phonenumber . "&email=" . $values['email'] . "&baseCurrency=USD&subscribeNewsLetter=true&deposit=50000&server=127.0.0.1&port=8310&productName=ReymountTrader&version=2.1.0.25");
                //$xml=simplexml_load_string('<DemoUserCreationInfo><Success>True</Success><UserName>11111</UserName><Password>dgsgdshd</Password></DemoUserCreationInfo>');
                $this->isDemoError = !$xml->Success;
                if (!$this->isDemoError) {
                    $this->UpdateDemoAccountActivation($values['email'], $values['code']);
                    $this->SendAcknowledgement($values['email'], $name, $values['code'], $xml->UserName, $xml->Password);
                    //$_SESSION[Constants::COMPANY.'demoactivation']=$values['email'].$values['code'];
                }
            }
        }
        $this->db = null;
        Database::freeDB();
        if ($this->IsRegistered && !$this->isDemoError) {
            return !$isactivated;
        } else {
            return;
        }
    }

    private function UpdateDemoAccountActivation($email, $key) {
        try {
            $stmt = $this->db->prepare("UPDATE webdemoaccount SET activated=1 WHERE email=:email AND activationkey=:key");
            $result = $stmt->execute(array("key" => $key, "email" => $email));
            unset($stmt);
            return $result;
        } catch (PDOException $e) {
            unset($stmt);
            return FALSE;
        }
    }

    protected function IsRegistered($email, $activationkey, &$isactivated, &$name, &$country, &$phonenumber) {
        try {
            $stmt = $this->db->prepare("SELECT activated,name,country,phonenumber FROM webdemoaccount WHERE email=:email AND activationkey=:key");
            $params = array("email" => "$email", "key" => "$activationkey");
            $result = false;
            if ($stmt->execute($params)) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $result = ($row !== FALSE);
                if ($result) {
                    $isactivated = $row['activated'];
                    $name = $row['name'];
                    $country = $row['country'];
                    $phonenumber = $row['phonenumber'];
                }
            }
            unset($stmt);
            return $result;
        } catch (PDOException $e) {
            unset($stmt);
            return FALSE;
        }
    }

    private function SendAcknowledgement($email, $name, $key, $username, $password) {
        $subject = "Demo Account - Activation";

        $comment = '<p>Your demo account has been activated.</p>';
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment .= '<p>Login to Ax1 trader with your login information</p>';
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment .= '<p>User ID : <strong>' . $username . '</strong> </p>';
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment .= '<p>Password : <strong>' . $password . '</strong></p>';
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment .= '<p><strong>Download Platform</strong></p>';
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment .= '<p>Ax1 trader - A single platform transforming the world in to one Global Exchange !</p>';
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment .= "<p><table style='padding-top:14px; padding-bottom:14px;'><tbody><tr><td style='background:#f28841; padding-top:7px; padding-right:14px; padding-bottom:7px; padding-left:14px;'><p><a style='color:#fff;' href='http://live1.reymount.com/setups/reymount/trader/3.6.0/reymounttradersetup3.6.0.exe'>DOWNLOAD AX1 FOR PC</a></p></td></tr></tbody></table></p>";
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment .= '<p><strong>Ax1 trader for IPhone, IPad and Android</strong></p>';
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment .= '<p>Trade as you go. Access your trading account from anywhere.</p>';
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment .= '<p><a href="https://itunes.apple.com/us/app/reymountplus-for-ipad/id632016011?ls=1&mt=8"><img src="' . ABS_URL . 'img/istore.png"/></a>&nbsp;&nbsp;<a href="https://play.google.com/store/apps/details?id=com.trade.reymounttrader"><img src="' . ABS_URL . 'img/android.png"/></a></p>';
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
       
        Mailer::SendMail('uc', $comment, $subject, $email, $name, "graphics@kappsoft.com");
    }

}

?>