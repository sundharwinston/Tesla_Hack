<?php

class Utils {

    public static function ReadInput($name) {
        $data = trim($name);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public static function ReadIPAddress() {
        $ip = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ip = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ip = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ip = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ip = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ip = getenv('REMOTE_ADDR');
        else
            $ip = 'UNKNOWN';

        return self::ReadInput($ip);
    }

/*    public static function ReadIPLocation($ip) {
        $location = '';
        $client_ip_location_details = json_decode(@file_get_contents("http://ip-api.com/json/" . $ip, true));
        if ($client_ip_location_details->status == 'success') {
            $location = $client_ip_location_details->city . ' - ' . $client_ip_location_details->country;
        } else {
            $location = "UNKNOWN";
        }
        return $location;
    } */
	
    public static function ReadIPLocation($ip,$cn_code_only=false) {
        $location = '';
        $client_ip_location_details = json_decode( file_get_contents("http://ip-api.com/json/" . $ip, true) , true );
        
        if( isset($client_ip_location_details['status']) ){
            $api_sts = $client_ip_location_details['status'];
        }else{
            $api_sts = '';
        }
        
        if ($api_sts == 'success') {
            $location = $client_ip_location_details['city'] . ' - ' . $client_ip_location_details['country'];
            $cn_code = $client_ip_location_details['countryCode'];
        } else {
            $cn_code = $location = "Private Range";
        }
        
        if($cn_code_only == true ){
            return strtoupper($cn_code);
        }else{
            return $location;
        }
    }	

    public static function ReadFileSize($url) {
        $head = array_change_key_case(get_headers($url, TRUE));
        $filesize = $head['content-length'];
        return $filesize;
    }

    public static function GetAbsoluteURL($name = '') {
        return ABS_URL . $name;
    }

    public static function RandomString($length = 10) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $str = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++)
            $str .= $chars[rand(0, $max)];

        return $str;
    }

    public static function ReadUserAgent() {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        return self::ReadInput($useragent);
    }

    function myErrorHandler($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting
            return;
        }

        switch ($errno) {
            case E_USER_ERROR:
                echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
                echo "  Fatal error on line $errline in file $errfile";
                echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
                echo "Aborting...<br />\n";
                exit(1);
                break;

            case E_USER_WARNING:
                echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
                break;

            case E_USER_NOTICE:
                echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
                break;

            default:
                echo "Unknown error type: [$errno] $errstr<br />\n";
                break;
        }

        /* Don't execute PHP internal error handler */
        return true;
    }

/*    public static function LoadCountry($selected = '', $selectcaption = '-- Select --') {
        echo '<option value="">' . $selectcaption . '</option>';
        echo '<option ' . self::selected($selected, "Afghanistan") . 'value="Afghanistan"> Afghanistan </option>';
        echo '<option ' . self::selected($selected, "Albania") . 'value="Albania"> Albania </option>';
        echo '<option ' . self::selected($selected, "Algeria") . 'value="Algeria"> Algeria </option>';
        echo '<option ' . self::selected($selected, "Andorra") . 'value="Andorra"> Andorra </option>';
        echo '<option ' . self::selected($selected, "Angola") . 'value="Angola"> Angola </option>';
        echo '<option ' . self::selected($selected, "Antigua and Barbuda") . 'value="Antigua and Barbuda"> Antigua and Barbuda </option>';
        echo '<option ' . self::selected($selected, "Argentina") . 'value="Argentina"> Argentina </option>';
        echo '<option ' . self::selected($selected, "Armenia") . 'value="Armenia"> Armenia </option>';
        echo '<option ' . self::selected($selected, "Aruba") . 'value="Aruba"> Aruba </option>';
        echo '<option ' . self::selected($selected, "Australia") . 'value="Australia"> Australia </option>';
        echo '<option ' . self::selected($selected, "Austria") . 'value="Austria"> Austria </option>';
        echo '<option ' . self::selected($selected, "Azerbaijan") . 'value="Azerbaijan"> Azerbaijan </option>';
        echo '<option ' . self::selected($selected, "Bahamas") . 'value="Bahamas"> Bahamas </option>';
        echo '<option ' . self::selected($selected, "Bahrain") . 'value="Bahrain"> Bahrain </option>';
        echo '<option ' . self::selected($selected, "Bangladesh") . 'value="Bangladesh"> Bangladesh </option>';
        echo '<option ' . self::selected($selected, "Barbados") . 'value="Barbados"> Barbados </option>';
        echo '<option ' . self::selected($selected, "Belarus") . 'value="Belarus"> Belarus </option>';
        echo '<option ' . self::selected($selected, "Belgium") . 'value="Belgium"> Belgium </option>';
        echo '<option ' . self::selected($selected, "Belize") . 'value="Belize"> Belize </option>';
        echo '<option ' . self::selected($selected, "Benin") . 'value="Benin"> Benin </option>';
        echo '<option ' . self::selected($selected, "Bermuda") . 'value="Bermuda"> Bermuda </option>';
        echo '<option ' . self::selected($selected, "Bhutan") . 'value="Bhutan"> Bhutan </option>';
        echo '<option ' . self::selected($selected, "Bolivia") . 'value="Bolivia"> Bolivia </option>';
        echo '<option ' . self::selected($selected, "Bosnia and Herzegovina") . 'value="Bosnia and Herzegovina"> Bosnia and Herzegovina </option>';
        echo '<option ' . self::selected($selected, "Botswana") . 'value="Botswana"> Botswana </option>';
        echo '<option ' . self::selected($selected, "Brazil") . 'value="Brazil"> Brazil </option>';
        echo '<option ' . self::selected($selected, "Brunei") . 'value="Brunei"> Brunei </option>';
        echo '<option ' . self::selected($selected, "Bulgaria") . 'value="Bulgaria"> Bulgaria </option>';
        echo '<option ' . self::selected($selected, "Burkina Faso") . 'value="Burkina Faso"> Burkina Faso </option>';
        echo '<option ' . self::selected($selected, "Burundi") . 'value="Burundi"> Burundi </option>';
        echo '<option ' . self::selected($selected, "Cambodia") . 'value="Cambodia"> Cambodia </option>';
        echo '<option ' . self::selected($selected, "Cameroon") . 'value="Cameroon"> Cameroon </option>';
        echo '<option ' . self::selected($selected, "Canada") . 'value="Canada"> Canada </option>';
        echo '<option ' . self::selected($selected, "Cape Verde") . 'value="Cape Verde"> Cape Verde </option>';
        echo '<option ' . self::selected($selected, "Central African Republic") . 'value="Central African Republic"> Central African Republic </option>';
        echo '<option ' . self::selected($selected, "Chad") . 'value="Chad"> Chad </option>';
        echo '<option ' . self::selected($selected, "Chile") . 'value="Chile"> Chile </option>';
        echo '<option ' . self::selected($selected, "China") . 'value="China"> China </option>';
        echo '<option ' . self::selected($selected, "Colombia") . 'value="Colombia"> Colombia </option>';
        echo '<option ' . self::selected($selected, "Comoros") . 'value="Comoros"> Comoros </option>';
        echo '<option ' . self::selected($selected, "Costa Rica") . 'value="Costa Rica"> Costa Rica </option>';
        echo '<option ' . self::selected($selected, "Côte d’Ivoire") . 'value="Côte d’Ivoire"> Côte d’Ivoire </option>';
        echo '<option ' . self::selected($selected, "Croatia") . 'value="Croatia"> Croatia </option>';
        echo '<option ' . self::selected($selected, "Cuba") . 'value="Cuba"> Cuba </option>';
        echo '<option ' . self::selected($selected, "Cyprus") . 'value="Cyprus"> Cyprus </option>';
        echo '<option ' . self::selected($selected, "Czech Republic") . 'value="Czech Republic"> Czech Republic </option>';
        echo '<option ' . self::selected($selected, "Democratic Republic of the Congo") . 'value="Democratic Republic of the Congo"> Democratic Republic of the Congo </option>';
        echo '<option ' . self::selected($selected, "Denmark") . 'value="Denmark"> Denmark </option>';
        echo '<option ' . self::selected($selected, "Djibouti") . 'value="Djibouti"> Djibouti </option>';
        echo '<option ' . self::selected($selected, "Dominica") . 'value="Dominica"> Dominica </option>';
        echo '<option ' . self::selected($selected, "Dominican Republic") . 'value="Dominican Republic"> Dominican Republic </option>';
        echo '<option ' . self::selected($selected, "East Timor") . 'value="East Timor"> East Timor </option>';
        echo '<option ' . self::selected($selected, "Ecuador") . 'value="Ecuador"> Ecuador </option>';
        echo '<option ' . self::selected($selected, "Egypt") . 'value="Egypt"> Egypt </option>';
        echo '<option ' . self::selected($selected, "El Salvador") . 'value="El Salvador"> El Salvador </option>';
        echo '<option ' . self::selected($selected, "Equatorial Guinea") . 'value="Equatorial Guinea"> Equatorial Guinea </option>';
        echo '<option ' . self::selected($selected, "Eritrea") . 'value="Eritrea"> Eritrea </option>';
        echo '<option ' . self::selected($selected, "Estonia") . 'value="Estonia"> Estonia </option>';
        echo '<option ' . self::selected($selected, "Ethiopia") . 'value="Ethiopia"> Ethiopia </option>';
        echo '<option ' . self::selected($selected, "Fiji") . 'value="Fiji"> Fiji </option>';
        echo '<option ' . self::selected($selected, "Finland") . 'value="Finland"> Finland </option>';
        echo '<option ' . self::selected($selected, "France") . 'value="France"> France </option>';
        echo '<option ' . self::selected($selected, "Gabon") . 'value="Gabon"> Gabon </option>';
        echo '<option ' . self::selected($selected, "Gambia") . 'value="Gambia"> Gambia </option>';
        echo '<option ' . self::selected($selected, "Georgia") . 'value="Georgia"> Georgia </option>';
        echo '<option ' . self::selected($selected, "Germany") . 'value="Germany"> Germany </option>';
        echo '<option ' . self::selected($selected, "Ghana") . 'value="Ghana"> Ghana </option>';
        echo '<option ' . self::selected($selected, "Greece") . 'value="Greece"> Greece </option>';
        echo '<option ' . self::selected($selected, "Grenada") . 'value="Grenada"> Grenada </option>';
        echo '<option ' . self::selected($selected, "Guatemala") . 'value="Guatemala"> Guatemala </option>';
        echo '<option ' . self::selected($selected, "Guinea") . 'value="Guinea"> Guinea </option>';
        echo '<option ' . self::selected($selected, "Guinea-Bissau") . 'value="Guinea-Bissau"> Guinea-Bissau </option>';
        echo '<option ' . self::selected($selected, "Guyana") . 'value="Guyana"> Guyana </option>';
        echo '<option ' . self::selected($selected, "Haiti") . 'value="Haiti"> Haiti </option>';
        echo '<option ' . self::selected($selected, "Honduras") . 'value="Honduras"> Honduras </option>';
        echo '<option ' . self::selected($selected, "Hungary") . 'value="Hungary"> Hungary </option>';
        echo '<option ' . self::selected($selected, "Iceland") . 'value="Iceland"> Iceland </option>';
        echo '<option ' . self::selected($selected, "India") . 'value="India"> India </option>';
        echo '<option ' . self::selected($selected, "Indonesia") . 'value="Indonesia"> Indonesia </option>';
        echo '<option ' . self::selected($selected, "Iran") . 'value="Iran"> Iran </option>';
        echo '<option ' . self::selected($selected, "Iraq") . 'value="Iraq"> Iraq </option>';
        echo '<option ' . self::selected($selected, "Ireland") . 'value="Ireland"> Ireland </option>';
        echo '<option ' . self::selected($selected, "Israel") . 'value="Israel"> Israel </option>';
        echo '<option ' . self::selected($selected, "Italy") . 'value="Italy"> Italy </option>';
        echo '<option ' . self::selected($selected, "Jamaica") . 'value="Jamaica"> Jamaica </option>';
        echo '<option ' . self::selected($selected, "Japan") . 'value="Japan"> Japan </option>';
        echo '<option ' . self::selected($selected, "Jordan") . 'value="Jordan"> Jordan </option>';
        echo '<option ' . self::selected($selected, "Kazakhstan") . 'value="Kazakhstan"> Kazakhstan </option>';
        echo '<option ' . self::selected($selected, "Kenya") . 'value="Kenya"> Kenya </option>';
        echo '<option ' . self::selected($selected, "Kiribati") . 'value="Kiribati"> Kiribati </option>';
        echo '<option ' . self::selected($selected, "Kuwait") . 'value="Kuwait"> Kuwait </option>';
        echo '<option ' . self::selected($selected, "Kyrgyzstan") . 'value="Kyrgyzstan"> Kyrgyzstan </option>';
        echo '<option ' . self::selected($selected, "Laos") . 'value="Laos"> Laos </option>';
        echo '<option ' . self::selected($selected, "Latvia") . 'value="Latvia"> Latvia </option>';
        echo '<option ' . self::selected($selected, "Lebanon") . 'value="Lebanon"> Lebanon </option>';
        echo '<option ' . self::selected($selected, "Lesotho") . 'value="Lesotho"> Lesotho </option>';
        echo '<option ' . self::selected($selected, "Liberia") . 'value="Liberia"> Liberia </option>';
        echo '<option ' . self::selected($selected, "Libya") . 'value="Libya"> Libya </option>';
        echo '<option ' . self::selected($selected, "Liechtenstein") . 'value="Liechtenstein"> Liechtenstein </option>';
        echo '<option ' . self::selected($selected, "Lithuania") . 'value="Lithuania"> Lithuania </option>';
        echo '<option ' . self::selected($selected, "Luxembourg") . 'value="Luxembourg"> Luxembourg </option>';
        echo '<option ' . self::selected($selected, "Madagascar") . 'value="Madagascar"> Madagascar </option>';
        echo '<option ' . self::selected($selected, "Malawi") . 'value="Malawi"> Malawi </option>';
        echo '<option ' . self::selected($selected, "Malaysia") . 'value="Malaysia"> Malaysia </option>';
        echo '<option ' . self::selected($selected, "Maldives") . 'value="Maldives"> Maldives </option>';
        echo '<option ' . self::selected($selected, "Mali") . 'value="Mali"> Mali </option>';
        echo '<option ' . self::selected($selected, "Malta") . 'value="Malta"> Malta </option>';
        echo '<option ' . self::selected($selected, "Marshall Islands") . 'value="Marshall Islands"> Marshall Islands </option>';
        echo '<option ' . self::selected($selected, "Mauritania") . 'value="Mauritania"> Mauritania </option>';
        echo '<option ' . self::selected($selected, "Mauritius") . 'value="Mauritius"> Mauritius </option>';
        echo '<option ' . self::selected($selected, "Mexico") . 'value="Mexico"> Mexico </option>';
        echo '<option ' . self::selected($selected, "Micronesia") . 'value="Micronesia"> Micronesia </option>';
        echo '<option ' . self::selected($selected, "Moldova") . 'value="Moldova"> Moldova </option>';
        echo '<option ' . self::selected($selected, "Monaco") . 'value="Monaco"> Monaco </option>';
        echo '<option ' . self::selected($selected, "Mongolia") . 'value="Mongolia"> Mongolia </option>';
        echo '<option ' . self::selected($selected, "Montenegro") . 'value="Montenegro"> Montenegro </option>';
        echo '<option ' . self::selected($selected, "Morocco") . 'value="Morocco"> Morocco </option>';
        echo '<option ' . self::selected($selected, "Mozambique") . 'value="Mozambique"> Mozambique </option>';
        echo '<option ' . self::selected($selected, "Myanmar") . 'value="Myanmar"> Myanmar </option>';
        echo '<option ' . self::selected($selected, "Namibia") . 'value="Namibia"> Namibia </option>';
        echo '<option ' . self::selected($selected, "Nauru") . 'value="Nauru"> Nauru </option>';
        echo '<option ' . self::selected($selected, "Nepal") . 'value="Nepal"> Nepal </option>';
        echo '<option ' . self::selected($selected, "Netherlands") . 'value="Netherlands"> Netherlands </option>';
        echo '<option ' . self::selected($selected, "New Zealand") . 'value="New Zealand"> New Zealand </option>';
        echo '<option ' . self::selected($selected, "Nicaragua") . 'value="Nicaragua"> Nicaragua </option>';
        echo '<option ' . self::selected($selected, "Niger") . 'value="Niger"> Niger </option>';
        echo '<option ' . self::selected($selected, "Nigeria") . 'value="Nigeria"> Nigeria </option>';
        echo '<option ' . self::selected($selected, "North Korea") . 'value="North Korea"> North Korea </option>';
        echo '<option ' . self::selected($selected, "Norway") . 'value="Norway"> Norway </option>';
        echo '<option ' . self::selected($selected, "Oman") . 'value="Oman"> Oman </option>';
        echo '<option ' . self::selected($selected, "Pakistan") . 'value="Pakistan"> Pakistan </option>';
        echo '<option ' . self::selected($selected, "Palau") . 'value="Palau"> Palau </option>';
        echo '<option ' . self::selected($selected, "Panama") . 'value="Panama"> Panama </option>';
        echo '<option ' . self::selected($selected, "Papua New Guinea") . 'value="Papua New Guinea"> Papua New Guinea </option>';
        echo '<option ' . self::selected($selected, "Paraguay") . 'value="Paraguay"> Paraguay </option>';
        echo '<option ' . self::selected($selected, "Peru") . 'value="Peru"> Peru </option>';
        echo '<option ' . self::selected($selected, "Philippines") . 'value="Philippines"> Philippines </option>';
        echo '<option ' . self::selected($selected, "Poland") . 'value="Poland"> Poland </option>';
        echo '<option ' . self::selected($selected, "Portugal") . 'value="Portugal"> Portugal </option>';
        echo '<option ' . self::selected($selected, "Qatar") . 'value="Qatar"> Qatar </option>';
        echo '<option ' . self::selected($selected, "Republic of Macedonia") . 'value="Republic of Macedonia"> Republic of Macedonia </option>';
        echo '<option ' . self::selected($selected, "Republic of the Congo") . 'value="Republic of the Congo"> Republic of the Congo </option>';
        echo '<option ' . self::selected($selected, "Romania") . 'value="Romania"> Romania </option>';
        echo '<option ' . self::selected($selected, "Russia") . 'value="Russia"> Russia </option>';
        echo '<option ' . self::selected($selected, "Rwanda") . 'value="Rwanda"> Rwanda </option>';
        echo '<option ' . self::selected($selected, "Saint Kitts and Nevis") . 'value="Saint Kitts and Nevis"> Saint Kitts and Nevis </option>';
        echo '<option ' . self::selected($selected, "Saint Lucia") . 'value="Saint Lucia"> Saint Lucia </option>';
        echo '<option ' . self::selected($selected, "Saint Vincent and the Grenadines") . 'value="Saint Vincent and the Grenadines"> Saint Vincent and the Grenadines </option>';
        echo '<option ' . self::selected($selected, "Samoa") . 'value="Samoa"> Samoa </option>';
        echo '<option ' . self::selected($selected, "San Marino") . 'value="San Marino"> San Marino </option>';
        echo '<option ' . self::selected($selected, "Sao Tome and Principe") . 'value="Sao Tome and Principe"> Sao Tome and Principe </option>';
        echo '<option ' . self::selected($selected, "Saudi Arabia") . 'value="Saudi Arabia"> Saudi Arabia </option>';
        echo '<option ' . self::selected($selected, "Senegal") . 'value="Senegal"> Senegal </option>';
        echo '<option ' . self::selected($selected, "Serbia") . 'value="Serbia"> Serbia </option>';
        echo '<option ' . self::selected($selected, "Seychelles") . 'value="Seychelles"> Seychelles </option>';
        echo '<option ' . self::selected($selected, "Sierra Leone") . 'value="Sierra Leone"> Sierra Leone </option>';
        echo '<option ' . self::selected($selected, "Singapore") . 'value="Singapore"> Singapore </option>';
        echo '<option ' . self::selected($selected, "Slovakia") . 'value="Slovakia"> Slovakia </option>';
        echo '<option ' . self::selected($selected, "Slovenia") . 'value="Slovenia"> Slovenia </option>';
        echo '<option ' . self::selected($selected, "Solomon Islands") . 'value="Solomon Islands"> Solomon Islands </option>';
        echo '<option ' . self::selected($selected, "Somalia") . 'value="Somalia"> Somalia </option>';
        echo '<option ' . self::selected($selected, "South Africa") . 'value="South Africa"> South Africa </option>';
        echo '<option ' . self::selected($selected, "South Korea") . 'value="South Korea"> South Korea </option>';
        echo '<option ' . self::selected($selected, "South Sudan") . 'value="South Sudan"> South Sudan </option>';
        echo '<option ' . self::selected($selected, "Spain") . 'value="Spain"> Spain </option>';
        echo '<option ' . self::selected($selected, "Sri Lanka") . 'value="Sri Lanka"> Sri Lanka </option>';
        echo '<option ' . self::selected($selected, "Sudan") . 'value="Sudan"> Sudan </option>';
        echo '<option ' . self::selected($selected, "Suriname") . 'value="Suriname"> Suriname </option>';
        echo '<option ' . self::selected($selected, "Swaziland") . 'value="Swaziland"> Swaziland </option>';
        echo '<option ' . self::selected($selected, "Sweden") . 'value="Sweden"> Sweden </option>';
        echo '<option ' . self::selected($selected, "Switzerland") . 'value="Switzerland"> Switzerland </option>';
        echo '<option ' . self::selected($selected, "Syria") . 'value="Syria"> Syria </option>';
        echo '<option ' . self::selected($selected, "Tajikistan") . 'value="Tajikistan"> Tajikistan </option>';
        echo '<option ' . self::selected($selected, "Tanzania") . 'value="Tanzania"> Tanzania </option>';
        echo '<option ' . self::selected($selected, "Thailand") . 'value="Thailand"> Thailand </option>';
        echo '<option ' . self::selected($selected, "Togo") . 'value="Togo"> Togo </option>';
        echo '<option ' . self::selected($selected, "Tonga") . 'value="Tonga"> Tonga </option>';
        echo '<option ' . self::selected($selected, "Trinidad and Tobago") . 'value="Trinidad and Tobago"> Trinidad and Tobago </option>';
        echo '<option ' . self::selected($selected, "Tunisia") . 'value="Tunisia"> Tunisia </option>';
        echo '<option ' . self::selected($selected, "Turkey") . 'value="Turkey"> Turkey </option>';
        echo '<option ' . self::selected($selected, "Turkmenistan") . 'value="Turkmenistan"> Turkmenistan </option>';
        echo '<option ' . self::selected($selected, "Tuvalu") . 'value="Tuvalu"> Tuvalu </option>';
        echo '<option ' . self::selected($selected, "Uganda") . 'value="Uganda"> Uganda </option>';
        echo '<option ' . self::selected($selected, "Ukraine") . 'value="Ukraine"> Ukraine </option>';
        echo '<option ' . self::selected($selected, "United Arab Emirates") . 'value="United Arab Emirates"> United Arab Emirates </option>';
        echo '<option ' . self::selected($selected, "United Kingdom") . 'value="United Kingdom"> United Kingdom </option>';
        echo '<option ' . self::selected($selected, "United States of America") . 'value="United States of America"> United States of America </option>';
        echo '<option ' . self::selected($selected, "Uruguay") . 'value="Uruguay"> Uruguay </option>';
        echo '<option ' . self::selected($selected, "Uzbekistan") . 'value="Uzbekistan"> Uzbekistan </option>';
        echo '<option ' . self::selected($selected, "Vanuatu") . 'value="Vanuatu"> Vanuatu </option>';
        echo '<option ' . self::selected($selected, "Venezuela") . 'value="Venezuela"> Venezuela </option>';
        echo '<option ' . self::selected($selected, "Vietnam") . 'value="Vietnam"> Vietnam </option>';
        echo '<option ' . self::selected($selected, "Yemen") . 'value="Yemen"> Yemen </option>';
        echo '<option ' . self::selected($selected, "Zambia") . 'value="Zambia"> Zambia </option>';
        echo '<option ' . self::selected($selected, "Zimbabwe") . 'value="Zimbabwe"> Zimbabwe </option>';
    } */
	
    public static function LoadCountry($selected = '', $selectcaption = '-- Select --') {
        echo '<option value="">' . $selectcaption . '</option>';
        $db = Database::getDB();
        try {
            $stmt = $db->prepare("SELECT * FROM webcountrylist");
            $result = $stmt->execute();
            while ( $data = $stmt->fetchObject() ) {
                echo '<option data-dial-code="'.$data->dial_code.'" data-iso-code="'.$data->iso_code.'" ' . self::selected($selected, $data->country_en) . ' value="' . $data->country_en . '">' . $data->country_en . '</option>';
            }
            unset($stmt);
            $db = null;
            Database::freeDB();
        } catch (PDOException $e) {
            echo $e->getMessage();
            unset($stmt);
        }
    }

    public static function LoadExperience($selected = '', $selectcaption = '-- Select --') {
        echo '<option value="">' . $selectcaption . '</option>';
        echo '<option ' . self::selected($selected, "Fresher") . ' value="Fresher">Fresher</option>' . PHP_EOL;
        echo '<option ' . self::selected($selected, "0 - 1 years") . ' value="0 - 1 years">0 - 1 years</option>' . PHP_EOL;
        echo '<option ' . self::selected($selected, "1 - 2 years") . ' value="1 - 2 years">1 - 2 years</option>' . PHP_EOL;
        echo '<option ' . self::selected($selected, "2 - 3 years") . ' value="2 - 3 years">2 - 3 years</option>' . PHP_EOL;
        echo '<option ' . self::selected($selected, "3 - 4 years") . ' value="3 - 4 years">3 - 4 years</option>' . PHP_EOL;
        echo '<option ' . self::selected($selected, "4 - 5 years") . ' value="4 - 5 years">4 - 5 years</option>' . PHP_EOL;
        echo '<option ' . self::selected($selected, "5 - 6 years") . ' value="5 - 6 years">5 - 6 years</option>' . PHP_EOL;
        echo '<option ' . self::selected($selected, "6 - 7 years") . ' value="6 - 7 years">6 - 7 years</option>' . PHP_EOL;
        echo '<option ' . self::selected($selected, "7 - 8 years") . ' value="7 - 8 years">7 - 8 years</option>' . PHP_EOL;
        echo '<option ' . self::selected($selected, "8 - 9 years") . ' value="8 - 9 years">8 - 9 years</option>' . PHP_EOL;
        echo '<option ' . self::selected($selected, "9 - 10 years") . ' value="9 - 10 years">9 - 10 years</option>' . PHP_EOL;
        echo '<option ' . self::selected($selected, "10 - 11 years") . ' value="10 - 11 years">10 - 11 years</option>' . PHP_EOL;
        echo '<option ' . self::selected($selected, "11 - 12 years") . ' value="11 - 12 years">11 - 12 years</option>' . PHP_EOL;
        echo '<option ' . self::selected($selected, "12 - 13 years") . ' value="12 - 13 years">12 - 13 years</option>' . PHP_EOL;
        echo '<option ' . self::selected($selected, "13 - 14 years") . ' value="13 - 14 years">13 - 14 years</option>' . PHP_EOL;
        echo '<option ' . self::selected($selected, "14 - 15 years") . ' value="14 - 15 years">14 - 15 years</option>' . PHP_EOL;
    }

    private static function selected($selected, $value) {
        return ((strcasecmp($selected, $value) == 0) ? 'selected ' : '');
    }

    public static function PrintSuccess($message = '', $candismiss = true) {
        echo self::GenerateSuccessMessage($message, $candismiss);
    }

    public static function PrintError($message = '', $candismiss = true) {
        echo self::GenerateErrorMessage($message, $candismiss);
    }

    public static function GenerateSuccessMessage($message = '', $candismiss = true) {
        $str = '<div class="alert alert-success' . ($candismiss ? ' alert-dismissable' : '') . '" role="alert">' . PHP_EOL;
        if ($candismiss) {
            $str .= '<a href="#" class="close" data-dismiss="alert">&times;</a>' . PHP_EOL;
        }
        $str .= $message . PHP_EOL;
        $str .= '</div>' . PHP_EOL;
        return $str;
    }

    public static function GenerateErrorMessage($message = '', $candismiss = true) {
        $str = '<div class="alert alert-danger' . ($candismiss ? ' alert-dismissable' : '') . '" role="alert">' . PHP_EOL;
        if ($candismiss) {
            $str .= '<a href="#" class="close" data-dismiss="alert">&times;</a>' . PHP_EOL;
        }
        $str .= $message . PHP_EOL;
        $str .= '</div>' . PHP_EOL;
        return $str;
    }

    public static function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    public static function endsWith($haystack, $needle) {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }

    public static function LoadWebinarEvent($selectcaption = '-- Select --') {
        //loads webinar events
        echo '<option value="">' . $selectcaption . '</option>';
        $file = fopen(ABS_PATH . "json/data/event_final.csv", "r");
        if ($file) {
            fgetcsv($file); // skips first line == titles
            $curdate = date("m/d/Y");
            while ($temp_ary = fgetcsv($file)) {
                $event_date = $temp_ary[2];
                $event_date = date("m/d/Y", strtotime($event_date));
                if ($event_date >= $curdate) {
                    $event_id = $temp_ary[0];
                    $event_title = $temp_ary[1];
                    $event_time = $temp_ary[3];
                    echo '<option data-event-id="' . $event_id . '" value="' . $event_title . '##' . $event_date . '">' . $event_date . ' - ' . $event_title . ' (' . $event_time . ')</option>';
                }
            }
        }
    }
    
    public static function pagination($base_url, $page, $total_pages) {


        $first_label = "First";
        $prev_label = "Prev";
        $next_label = "Next";
        $last_label = "Last";

        $paginate_htm = '<nav><ul class="pagination">';

        // first
        if ($page > 1) {
            $paginate_htm .= '<li class="page-item"><a class="page-link" href="' . $base_url . '">' . $first_label . '</a></li>';
        } else {
            $paginate_htm .= '<li class="page-item disabled"><a class="page-link" >' . $first_label . '</a></li>';
        }

        // previous
        if ($page == 1) {
            $paginate_htm .= '<li class="page-item disabled"><a class="page-link" >' . $prev_label . '</a></li>';
        } elseif ($page == 2) {
            $paginate_htm .= '<li class="page-item"><a class="page-link" href="' . $base_url . '">' . $prev_label . '</a></li>';
        } else {
            $paginate_htm .= '<li class="page-item"><a class="page-link" href="' . $base_url . 'page-' . ($page - 1) . '">' . $prev_label . '</a></li>';
        }

        // current
        $paginate_htm .= '<li class="page-item"><span class="page-link current"> Page ' . $page . ' of ' . $total_pages . ' </span></li>';

        // next
        if ($page < $total_pages) {
            $paginate_htm .= '<li class="page-item"><a class="page-link" href="' . $base_url . 'page-' . ($page + 1) . '">' . $next_label . '</a></li>';
        } else {
            $paginate_htm .= '<li class="page-item disabled"><a class="page-link">' . $next_label . '</a></li>';
        }

        // last
        if ($page < $total_pages) {
            $paginate_htm .= '<li class="page-item"><a class="page-link" href="' . $base_url . 'page-' . $total_pages . '">' . $last_label . '</a></li>';
        } else {
            $paginate_htm .= '<li class="page-item disabled"><a class="page-link">' . $last_label . '</a></li>';
        }

        $paginate_htm .= '</ul></nav>';

        echo $paginate_htm;
    }

}

?>