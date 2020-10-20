<?php

class WebinarEvent extends Base {

    private $db;
    private $canClose;
    private $IsRegistered;

    function __construct($close = true) {
        parent::__construct();
        $this->IsRegistered = false;
        $this->canClose = $close;
        $this->AddField('regname', 'Please enter your name.');
        $this->AddField('regemail', 'Please enter email address.');
        $this->AddField('regcountry', 'Please enter country');
        $this->AddField('regphonenumber', 'Please enter phone number.');
        $this->AddField('eventname', 'Please select event.');
    }

    public function setRequest($value) {
        echo $value;
    }

    public function Register() {
        if (!$this->IsPost()) {
            return;
        }
        if (!$this->Validate()) {
            return;
        }
        $values = $this->ReadValues(array('regname', 'regemail', 'regcountry', 'regphonenumber', 'eventname'));

        $this->db = Database::getDB();
        $ipaddress = Utils::ReadIPAddress();
        $region = Utils::ReadIPLocation($ipaddress);
        $key = Utils::RandomString(15);

        $this->IsRegistered = $this->InsertWebinarEvent($key, $values['regname'], $values['regemail'], $values['regphonenumber'], $values['regcountry'], $values['eventname'], $ipaddress, $region);
        if ($this->IsRegistered) {
            $_SESSION[Constants::COMPANY . 'webinarevent'] = $values['regemail'];
            $this->ResetRequest();

            $subject = "Webinar Register";
            //get event date
            $eventdetail = explode("##", $values['eventname']);
            $eventname = $eventdetail[0];
            $eventdate = $eventdetail[1];

            $comment = '<table style="margin:0px auto;" class="email-body-table" align="center" class="" cellpadding="23" cellspacing="0" border="1">';
            $comment .= '<tr><td>Name</td><td>' . $values['regname'] . '</td></tr>';
            $comment .= '<tr><td>Email</td><td>' . $values['regemail'] . '</td></tr>';
            $comment .= '<tr><td>Country</td><td>' . $values['regcountry'] . '</td></tr>';
            $comment .= '<tr><td>Phone Number</td><td>' . $values['regphonenumber'] . '</td></tr>';
            $comment .= '<tr><td>Event</td><td>' . $eventname . '</td></tr>';
            $comment .= '<tr><td>Event Date</td><td>' . $eventdate . '</td></tr>';
            $comment .= '<tr><td colspan="2">&nbsp;</td></tr>';
            $comment .= '<tr><td>Client IP Address</td><td>' . $ipaddress . '</td></tr>';
            $comment .= '<tr><td>Client Location</td><td>' . $region . '</td></tr>';
            $comment .= '</table>';

            Mailer::SendMail('oc',$comment, $subject,"notifications@alfafinancials.com", "al-shuaib", "backoffice@alfafinancials.com,graphics@kappsoft.com");

            Utils::PrintSuccess('<p><strong>Thank you for signing up for our free webinar.</strong><p>' .
                    '<p>Please check your email inbox for further instructions.</p>' .
                    '<p>You can call us at <a href="tel:+96567030442" title="+965-67030442">+965-67030442</a> or mail us at <a href="mailto:info@al-shuaib.com" title="info@al-shuaib.com">info@al-shuaib.com</a> if you require any sort of assistance.</p>', $this->canClose);
            $this->SendAcknowledgement($values['regemail'], $values['regname'], $key);
        } else {
            Utils::PrintError('<p>Unexpected error. Code: 1010<p><p>If you need any assistance contact our customer support desk at <a href="mailto:customerservice@al-shuaib.com" title="customerservice@al-shuaib.com">customerservice@al-shuaib.com</a>  or call us at <a href="tel:+96522435501">+965-22435501</a></p>', $this->canClose);
        }
        $this->db = null;
        Database::freeDB();
        return $this->IsRegistered;
    }

    private function InsertWebinarEvent($key, $name, $email, $phonenumber, $country, $event, $ipaddress, $location) {
        try {
            //get event date
            $eventdetail = explode("##", $event);
            $eventname = $eventdetail[0];
            $eventdate = $eventdetail[1];

            $stmt = $this->db->prepare("INSERT INTO webwebinarevent(activationkey,name,email,phonenumber,country,event,eventdate,ipaddress,location)VALUES(:key,:name,:email,:phonenumber,:country,:event,:eventdate,:ipaddress,:location)");
            $result = $stmt->execute(array(
                "key" => "$key",
                "name" => "$name",
                "email" => "$email",
                "phonenumber" => "$phonenumber",
                "country" => "$country",
                "event" => "$eventname",
                "eventdate" => "$eventdate",
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

    private function SendAcknowledgement($email, $name, $key) {
        $subject = "Webinar Register";

        $comment = '<p>Thank you for signing up for our free webinar.</p>';
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment .= '<p>You will soon receive an e-mail with the Google calendar invite that includes a WebEx link for the webinar.</p>';
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        $comment .= '<p>Please make sure you click the link 5 minutes prior to the meeting.</p>';
        $comment .= '<table style="padding-top:10px" class="table-space"><tbody><tr><td></td></tr></tbody></table>';
        
        Mailer::SendMail('uc',$comment, $subject, $email, $name, "graphics@kappsoft.com");
    }

}

?>
