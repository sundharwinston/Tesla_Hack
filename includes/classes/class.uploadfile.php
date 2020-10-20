<?php
class UploadFile{
    private $types;
    private $basedir;
    public $Error;
    public $FileName;

	function __construct($basedir='download/files/',$types='jpg,png,jpeg,gif,pdf'){
        $this->basedir=$basedir;
        $this->types=explode(",",strtolower($types));
        $this->Error='';
        $this->FileName='';
	}
	
	public function Upload($name){
        
        if (empty($_FILES[$name])){
            $this->Error= "Sorry, select a valid file to upload.";
            return;
        }
        $filebasename=basename($_FILES[$name]["name"]);
        $imageFileType = strtolower(pathinfo($filebasename,PATHINFO_EXTENSION));
        $filename= date("YmdHis").".".$imageFileType;
        $targetSuffix=$this->basedir;
        $target_dir = ABS_PATH .$targetSuffix;
        $folderExists=is_dir($target_dir);

        if (!$folderExists) {
            $folderExists=mkdir($target_dir, 0777,true);
        }
        if (!$folderExists)
        {
            $this->Error= "Sorry, there was an error uploading your file.";
            return;
        }
        $target_file = $target_dir.$filename ;
        $uploadOk = 1;

        // Check if file already exists
        if (file_exists($target_file)) {
            $this->Error= "Sorry, file already exists.";
            return;
        }
        // Check file size
        if ($_FILES[$name]["size"] > 1048576) {
            $this->Error= "Sorry, your file is too large (Maximum 1MB).";
            return;
        }
        // Allow certain file formats
        if(!in_array(strtolower($imageFileType),$this->types)){
            $this->Error= "Sorry, only JPG, JPEG, PNG, GIF & PDF files are allowed.";
            return;
        }
        if (move_uploaded_file($_FILES[$name]["tmp_name"], $target_file)) {
            $this->FileName=$filename;
            return TRUE;
        }
        else {
            $this->Error= "Sorry, there was an error uploading your file.";
            return;
        }
	}
}
?>