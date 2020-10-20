<?php
class Base{
    protected $Fields;
    public $Message;
    protected $PrintImmediate;

    function __construct(){
        $this->Fields=array();
        $this->Message='';
        $this->PrintImmediate=TRUE;
	}

    public function __get($name){
        if (method_exists($this, ($method = 'get'.$name)))
        {
            return $this->$method();
        }
        else return;
    }

    public function __set($name, $value){
        if (method_exists($this, ($method = 'set'.$name)))
        {
            $this->$method($value);
        }
    }

    public function IsPost(){
        return (strtolower($_SERVER['REQUEST_METHOD'])=='post');
    }

    public function IsGet(){
        return (strtolower($_SERVER['REQUEST_METHOD'])=='get');
    }

    public function AddField($name,$message)
    {
        $this->AddFieldValidation($name,$this->CreateMandatoryValidation($message));
    }
    public function AddFieldValidation($name,$validation)
    {
        if (!isset($this->Fields[$name][Constants::VALIDATION])){$this->Fields[$name][Constants::VALIDATION]=array();}
        $this->Fields[$name][Constants::VALIDATION]=array_merge($this->Fields[$name][Constants::VALIDATION],$validation);
    }
    
    public function CreateMandatoryValidation($message){
        return array(Constants::ISMANDATORY=>array(Constants::ERROR_MESSAGE=>$message));
    }
    public function CreateInvalidFieldValidation($message){
        return array(Constants::ISVALID=>array(Constants::ERROR_MESSAGE=>$message));
    }
    public function CreateEmailValidation($message){
        return array(Constants::ISEMAIL=>array(Constants::ERROR_MESSAGE=>$message));
    }
    public function CreateNumericValidation($message){
        return array(Constants::ISNUMERIC=>array(Constants::ERROR_MESSAGE=>$message));
    }
    public function CreateOffensiveValidation($message){
        return array(Constants::ISOFFENSIVE=>array(Constants::ERROR_MESSAGE=>$message));
    }
    public function CreateStringLengthValidation($message,$max=0,$min=0){
        $validation=array(Constants::ERROR_MESSAGE=>$message);
        if ($max>0){
            $validation[]=array(Constants::MAX=>$max);
        }
        if ($min>0){
            $validation[]=array(Constants::MIN=>$min);
        }
        return array(Constants::ISLENGTH=>$validation);
    }
    public function CreateRegExValidation($message,$pattern){
        return array(Constants::ISREGEX=>array(Constants::ERROR_MESSAGE=>$message,
                          Constants::REGEX=>$pattern));
    }
    public function CreateSessionValidation($message,$sessionkey){
        return array(Constants::ISSESSIONVALUE=>array(Constants::ERROR_MESSAGE=>$message,
                          Constants::SESSIONKEY=>$sessionkey));
    }

    public function PrintError($message,$canClose=true){
        $this->SetErrorMessage($message,$canClose);
    }
    public function PrintSuccess($message,$canClose=true){
        $this->SetSuccessMessage($message,$canClose);
    }
    
    public function ReadInput($fieldname){
        if(!isset($_REQUEST[$fieldname])) return '';
       return Utils::ReadInput($_REQUEST[$fieldname]);
    }
    
    public function ReadArray($fieldname){
        if(!isset($_REQUEST[$fieldname])) return '';
       return Utils::ReadInput(implode(", ",$_REQUEST[$fieldname]));
    }

    public function ConvertRadio($inputvalue,$value,$truevalue="Checked='checked'",$falsevalue="")
    {
        return (($inputvalue==$value)?$truevalue:$falsevalue);
    }

    public function ReadRadio($fieldname,$value,$truevalue="Checked='checked'",$falsevalue="")
    {
        $inputvalue=$this->ReadInput($fieldname);
        return $this->ConvertRadio($inputvalue,$value,$truevalue,$falsevalue);

    }

    public function ConvertChecked($inputvalue,$value,$truevalue="Checked='checked'",$falsevalue="")
    {
        return ((stripos(", ".$inputvalue.",",", ".$value.",")!==FALSE)?$truevalue:$falsevalue);
    }

    public function ReadChecked($fieldname,$value,$truevalue="Checked='checked'",$falsevalue="")
    {
        $inputvalue=$this->ReadInput($fieldname);
        return $this->ConvertChecked($inputvalue,$value,$truevalue,$falsevalue);
    }
    
    public function ReadCheckedArray($fieldname,$value,$truevalue="Checked='checked'",$falsevalue="")
    {
        $inputvalue=$this->ReadArray($fieldname);
        return $this->ConvertChecked($inputvalue,$value,$truevalue,$falsevalue);
    }

    public function Validate($showallerrros=false){
        $errors=array();
        //var_dump($this->Fields);
        foreach($this->Fields as $key=>$value){
            if (isset($this->Fields[$key][Constants::VALIDATION]) && count($this->Fields[$key][Constants::VALIDATION])>0){
                $fieldvalue=$this->ReadInput($key);
                foreach($this->Fields[$key][Constants::VALIDATION] as $validationkey=>$validationvalue){
                    
                    //echo "<BR>";
                    //echo "KEY: $validationkey";
                    //echo "<BR>";
                    //var_dump($validationvalue);
                    //continue;
                    switch ($validationkey){
                        case Constants::ISMANDATORY:{
                            if (empty($fieldvalue)){
                                $errors[]=$validationvalue[Constants::ERROR_MESSAGE];
                                if ($showallerrros===FALSE) break 3;
                            }
                            break;
                        }
                        case Constants::ISVALID:{
                            if (!empty($fieldvalue) && $this->CheckKeyExists($fieldvalue,array("%",".com","http","www"))){
                                $errors[]=$validationvalue[Constants::ERROR_MESSAGE];
                                if ($showallerrros===FALSE) break 3;
                            }
                            break;
                        }
                        case Constants::ISOFFENSIVE:{
                            if (!empty($fieldvalue) && $this->CheckKeyExists($fieldvalue,array("impressed","cheap","nudist","teen","debt","free","adult","lesb","nude","porn","url","ahref","dear"))){
                                $errors[]=$validationvalue[Constants::ERROR_MESSAGE];
                                if ($showallerrros===FALSE) break 3;
                            }
                            break;
                        }
                        case Constants::ISNUMERIC:{
                            if (!empty($fieldvalue) && !is_numeric($fieldvalue)){
                                $errors[]=$validationvalue[Constants::ERROR_MESSAGE];
                                if ($showallerrros===FALSE) break 3;
                            }
                            break;
                        }
                        case Constants::ISEMAIL:{
                            if (!empty($fieldvalue) && !filter_var($fieldvalue, FILTER_VALIDATE_EMAIL)){
                                $errors[]=$validationvalue[Constants::ERROR_MESSAGE];
                                if ($showallerrros===FALSE) break 3;
                            }
                            break;
                        }
                        case Constants::ISLENGTH:{
                            if (!empty($fieldvalue)){
                                if (isset($validationvalue[Constants::MAX])){
                                    $max=$validationvalue[Constants::MAX];
                                    if ($max>0 && strlen($fieldvalue)>$max){
                                        $errors[]=$validationvalue[Constants::ERROR_MESSAGE];
                                        if ($showallerrros===FALSE) break 3;    
                                    }
                                }
                                if (isset($validationvalue[Constants::MIN])){
                                    $min=$validationvalue[Constants::MIN];
                                    if ($min>0 && strlen($fieldvalue)<$min){
                                        $errors[]=$validationvalue[Constants::ERROR_MESSAGE];
                                        if ($showallerrros===FALSE) break 3;    
                                    }
                                }
                            }
                            break;
                        }
                        case Constants::ISREGEX:{
                            if (!empty($fieldvalue) && isset($validationvalue[Constants::REGEX]) && !preg_match($validationvalue[Constants::REGEX],$fieldvalue)){
                                $errors[]=$validationvalue[Constants::ERROR_MESSAGE];
                                if ($showallerrros===FALSE) break 3;
                            }
                            break;
                        }
                        case Constants::ISSESSIONVALUE:{
                            if (!empty($fieldvalue) && isset($validationvalue[Constants::SESSIONKEY]) && !empty($_SESSION[$validationvalue[Constants::SESSIONKEY]]) && $_SESSION[$validationvalue[Constants::SESSIONKEY]]!==$fieldvalue){
                                $errors[]=$validationvalue[Constants::ERROR_MESSAGE];
                                if ($showallerrros===FALSE) break 3;
                            }
                            break;
                        }
                    }
                }
            }
        }
        if (count($errors)==1){
            $this->SetErrorMessage('<p>'.$errors[0].'</p>');
            return FALSE;
        }
        else if(count($errors)>0){
            $errormsg="<p>Please clear the below listed error to continue.</p><ul>".PHP_EOL;
            foreach($errors as $e)
            {
                $errormsg.="<li>$e</li>".PHP_EOL;
            }
            $errormsg.="</ul>".PHP_EOL;
            $this->SetErrorMessage($errormsg);
            return FALSE;
        }
        else{
            return TRUE;
        }
    }

    public function CheckKeyExists($haystack, $needles, $offset=0) {
        foreach($needles as $needle) {
            If (stripos($haystack, $needle, $offset)!==FALSE){
                return TRUE;
            }
        }
        return FALSE;
    }

    public function SessionExists($sessionkey,$sessionvalue)
    {
        if (empty($_SESSION[$sessionkey])) return FALSE;
        return ($_SESSION[$sessionkey]===$sessionvalue);
    }

    public function ReadValues($fields)
    {
        $values=array();
        foreach($fields as $value)
        { 
            $values[$value]=$this->ReadInput($value);
        } 
        return $values;
    }

    public function SetErrorMessage($message,$canClose=true)
    {
        $this->Message=Utils::GenerateErrorMessage($message,$canClose);
        if ($this->PrintImmediate){
            echo $this->Message;
        }
    }

    public function SetSuccessMessage($message,$canClose=true)
    {
        $this->Message=Utils::GenerateSuccessMessage($message,$canClose);
        if ($this->PrintImmediate){
            echo $this->Message;
        }
    }
    
    public function ResetRequest(){
       $_REQUEST=array();
    }
}
?>