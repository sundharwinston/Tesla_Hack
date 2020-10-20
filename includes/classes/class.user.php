<?php

class User extends Password {

    private $db;
    public $result_sts;

    function __construct() {
        parent::__construct();
        $this->result_sts = NULL;
    }

    public function UserLogin($uname, $pwd) {
        $this->db = Database::getDB();
        $rst_ary = $this->CheckUser($uname);        
        $hash = $rst_ary['hash'];
        $user_id = $rst_ary['id'];
        $_SESSION['uname'] = null;
        $_SESSION['key'] = null;
        
        if (is_array($rst_ary) && $hash ) {
            $res = $this->verify_pwd_hash($pwd, $hash);
            if ($res) {                
                //set session
                $_SESSION['uname'] = $uname;   
                $_SESSION['key'] = Utils::RandomString(5);
                $_SESSION['uid'] = $user_id;
                $_SESSION['name'] = $rst_ary['name'];
                $_SESSION['usertype'] = $rst_ary['usertype'];
                
                //set login time
                $useragent = $_SERVER['HTTP_USER_AGENT'];
                $ipaddress = Utils::ReadIPAddress();
                $location = Utils::ReadIPLocation($ipaddress);
                $this->InsertLoginHistory($user_id, $useragent, $ipaddress, $location);
                $this->result_sts = 1;
            } else {
                $this->result_sts = 0;
            }
        } else {
            $this->result_sts = 0;
        }

        $this->db = NULL;
        Database::freeDB();
        return $this->result_sts;
    }

    public function RegisterUser($uname, $pwd) {
        $this->db = Database::getDB();
        
        $rst_ary = $this->CheckUser($uname);
        $user_id = $rst_ary['id'];
        
        if (is_array($rst_ary) && $user_id ) {
            $this->result_sts = 2;
        } else {
            $password = $this->generate_pwd_hash($pwd);
            if ($this->InsertUser($uname, $password)) {
                $this->result_sts = 1;
            } else {
                $this->result_sts = 0;
            }
        }

        $this->db = NULL;
        Database::freeDB();
        return $this->result_sts;
    }

    private function CheckUser($username) {
        try {
            $return = NULL;
            $stmt = $this->db->prepare("SELECT * FROM webuser WHERE username=:username");
            $result = $stmt->execute(array("username" => "$username"));
            $row = $stmt->fetch();
            if (isset($row["id"])) {
                $temp = array();
                $temp['id'] = $row["id"];
                $temp['hash'] = $row["password"];
                $temp['name'] = $row["name"];
                $temp['usertype'] = $row["usertype"];
                $return = $temp;
            } else {
                $return = FALSE;
            }
            unset($stmt);
        } catch (PDOException $e) {
            unset($stmt);
            $return = FALSE;
            //echo $e->getMessage();
        }
        return $return;
    }
    
    public function ChangePassword($uname,$oldpwd,$newpwd) {
        
        $this->db = Database::getDB();
        
        $rst_ary = $this->CheckUser($uname);
        $user_id = $rst_ary['id'];
        $pwd_hash = $rst_ary['hash'];
        if($user_id){
            //check old pwd 
            $res = $this->verify_pwd_hash($oldpwd, $pwd_hash);
            if($res){
                $new_pwd_hash = $this->generate_pwd_hash($newpwd);
                
                if($pwd_hash === $new_pwd_hash){
                    //pwd can not be same
                    $this->result_sts = 2;
                }else{
                    try {
                        $stmt = $this->db->prepare("UPDATE webuser SET password=:password WHERE id=:id");
                        $result = $stmt->execute(array("password" => "$new_pwd_hash", "id" => "$user_id"));
                        unset($stmt);
                        if($result){
                            //success
                            $this->result_sts = 1;
                        }else{
                            //failed
                            $this->result_sts = 0;
                        }
                    } catch (PDOException $e) {
                        unset($stmt);
                        //failed
                        $this->result_sts = 0;
                    }
                }
            }else{
                //Old pwd mismatch
                $this->result_sts = 3;
            }
        }else{
            //no user name found
            $this->result_sts = 4;
        }
        
        $this->db = NULL;
        Database::freeDB();
        return $this->result_sts;
    }

    private function InsertUser($username, $password, $ipaddress = "127.0.0.1", $location = "server") {
        try {
            $stmt = $this->db->prepare("INSERT INTO webuser(username,password,ipaddress,location) VALUES(:username,:password,:ipaddress,:location)");
            $result = ($stmt->execute(array("username" => "$username", "password" => "$password", "ipaddress" => "$ipaddress", "location" => "$location")));
            unset($stmt);
            return $result;
        } catch (PDOException $e) {
            unset($stmt);
            //echo 'test'.PHP_EOL;
            return FALSE;
        }
    }

    public function InsertLoginHistory($id,$useragent, $ipaddress, $region) {
        try {
            $stmt = $this->db->prepare("INSERT INTO webloginhistory(userid,useragent,ipaddress,location)VALUES(:userid,:useragent,:ipaddress,:location)");
            $result = $stmt->execute(array(
                "userid" => "$id",
                "useragent" => "$useragent",
                "ipaddress" => "$ipaddress",
                "location" => "$region"
            ));
        } catch (PDOException $e) {
            //echo $e->getMessage();
        }
        unset($stmt);
    }

    public function UpdateLogoutHistory($userid) {
        if($userid){
            try {
                $this->db = Database::getDB();
                $stmt = $this->db->prepare("SELECT id FROM webloginhistory WHERE userid=:userid ORDER by id DESC LIMIT 1");
                $result = $stmt->execute(array(
                    "userid" => "$userid"
                ));
                
                $data = $stmt->fetchObject();
                $his_id = $data->id;
                
                $stmt2 = $this->db->prepare("UPDATE webloginhistory SET logouttime=NOW() WHERE id=:id");
                $result2 = $stmt2->execute(array(
                    "id" => "$his_id"
                ));
                unset($stmt);
                unset($stmt2);
            } catch (PDOException $e) {
                //echo $e->getMessage();
                //exit();
            }
            
            $this->db = NULL;
            Database::freeDB();
        }
    }
}

?>