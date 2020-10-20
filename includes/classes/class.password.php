<?php

class Password {

    private $pwdSalt;

    function __construct() {
        //pwd salt 128 char
        $this->pwdSalt = 'tD43lihpbbraNSFgYN5x6x5kUIUuhzzmB7oCuIgWMapZUcwimSGmBiV8BTtakL7VLORivFYazxfwTAW8svKz9rosl2Adg48bN3wcpZH1UiOgL4yuqlGZyMy0CyK3TB14';
    }

    public function generate_pwd_hash($pass) {
        $salt = $this->pwdSalt;
        $base64_pwd_str = base64_encode($pass . $salt);
        $pwd_hash = md5($base64_pwd_str) . sha1($base64_pwd_str);
        return $pwd_hash;
    }
    
    public function verify_pwd_hash($pass, $received_hash){
        $gen_hash = $this->generate_pwd_hash($pass);
        if($gen_hash === $received_hash){
            return true;
        } else {
            return false;
        }
    }
}
