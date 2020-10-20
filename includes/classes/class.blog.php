<?php

class Blog {

    function __construct() {
        
    }

    public function Submit($data) {
        $blog_img = $this->UploadBlogIMG($data['title']);
        if ($blog_img) {
            $res = $this->InsertBlog($data, $blog_img);
            if ($res) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return NULL;
        }
    }
    
    public function Update($data) {
        if(isset($_FILES['blog_img']) && !empty($_FILES['blog_img']['tmp_name']) ){
            $blog_img = $this->UploadBlogIMG($data['title']);
        }
            
        $res = $this->UpdateBlog($data);
        if ($res) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function Delete($id) {
        $return = null;
        try {
            $db = Database::getDB();
            $strSQL = "UPDATE webblog SET blog_status=:blog_status WHERE blog_id=:blog_id";
            $stmt = $db->prepare($strSQL);
            $params = array(
                "blog_id" => $id,
                "blog_status" => 1
            );
            
            $result = $stmt->execute($params);
            $count = $stmt->rowCount();
            if($count > 0) {
                $return = TRUE;
            }else{
                $return = FALSE;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            unset($stmt);
        }

        return $return;
    }

    private function UploadBlogIMG($title) {
        $return_url = '';
        $allowed_type = array('image/jpeg', 'image/png');
        $img_type = $_FILES['blog_img']['type'];

        if (in_array($img_type, $allowed_type)) {
            $ext = strtolower(pathinfo(basename($_FILES['blog_img']['name']), PATHINFO_EXTENSION));
            $blog_img_url = $this->GenerateURL($title) . '.' . $ext;
            $blog_img_path = ABS_PATH . 'img/blog/' . $blog_img_url;

            if (move_uploaded_file($_FILES['blog_img']['tmp_name'], $blog_img_path)) {
                $return_url = $blog_img_url;
            }
        }
        return $return_url;
    }

    private function InsertBlog($data, $blog_img) {
        $return = null;
        try {

            $db = Database::getDB();
            if (isset($data['pub_date'])) {
                $pub_date = $data['pub_date'];
            } else {
                $pub_date = date('m/d/Y');
            }

            $pub_date = strtotime($pub_date);
            $pub_date = date('Y-m-d', $pub_date);
            if( isset($data['blog_url']) && $data['blog_url'] !=''){
                $blog_url = $data['blog_url'];
            }else{
                $blog_url = $this->GenerateURL($data['title']);
            }
            

            $strSQL = "INSERT INTO webblog(blog_title, blog_meta_title, blog_meta_desc, blog_seo_header, blog_short_desc, blog_content, blog_url, blog_alt, blog_img, blog_language, blog_publish_date) values(:blog_title, :blog_meta_title, :blog_meta_desc, :blog_seo_header, :blog_short_desc, :blog_content, :blog_url, :blog_alt, :blog_img, :blog_language, :blog_publish_date)";
            $stmt = $db->prepare($strSQL);
            $params = array(
                "blog_title" => $data['title'],
                "blog_meta_title" => $data['meta_title'],
                "blog_meta_desc" => $data['meta_desc'],
                "blog_seo_header" => $data['seo_header'],
                "blog_content" => $data['blog_content'],
                "blog_img" => $blog_img,
                "blog_language" => $data['blog_language'],
                "blog_short_desc" => $data['short_desc'],
                "blog_publish_date" => $pub_date,
                "blog_url" => $blog_url,
                "blog_alt" => $data['blog_alt']
            );

            $result = $stmt->execute($params);
            $return = $result;
        } catch (PDOException $e) {
            //echo $e->getMessage();
            unset($stmt);
        }

        return $return;
    }
    
    private function UpdateBlog($data) {
        $return = null;
        try {

            $db = Database::getDB();
            if (isset($data['pub_date'])) {
                $pub_date = $data['pub_date'];
            } else {
                $pub_date = date('m/d/Y');
            }

            $pub_date = strtotime($pub_date);
            $pub_date = date('Y-m-d', $pub_date);

            $strSQL = "UPDATE webblog SET blog_title=:blog_title, blog_meta_title=:blog_meta_title, blog_meta_desc=:blog_meta_desc, blog_seo_header=:blog_seo_header, blog_short_desc=:blog_short_desc, blog_alt=:blog_alt, blog_content=:blog_content, blog_publish_date=:blog_publish_date WHERE blog_id=:id";
            $stmt = $db->prepare($strSQL);
            $params = array(
                "id" => $data['blog_id'],
                "blog_title" => $data['title'],
                "blog_meta_title" => $data['meta_title'],
                "blog_meta_desc" => $data['meta_desc'],
                "blog_seo_header" => $data['seo_header'],
                "blog_content" => $data['blog_content'],
                "blog_short_desc" => $data['short_desc'],
                "blog_alt" => $data['blog_alt'],
                "blog_publish_date" => $pub_date
            );

            $result = $stmt->execute($params);
            $return = $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
            unset($stmt);
        }

        return $return;
    }

    public function GetLiveBlog($start,$limit) {
        try {
            $db = Database::getDB();
            $date_today = date('Y-m-d');
            $strSQL = "SELECT * from  webblog WHERE blog_status=0 AND blog_language='en' AND blog_publish_date <=  :today ORDER BY blog_publish_date DESC LIMIT $start,$limit";
            $stmt = $db->prepare($strSQL);
            $params = array("today" => $date_today);
            $result = $stmt->execute($params);
            $blog = array();
            while ($row = $stmt->fetchObject()) {
                $temp['id'] = $row->blog_id;
                $temp['title'] = $row->blog_title;
                $temp['url'] = ABS_URL . 'blog/' . $row->blog_url;
                $temp['url_with_text'] = rawurlencode($row->blog_title) . ':' . ABS_URL . 'blog/' . $row->blog_url;
                $temp['img_url'] = $row->blog_img;
                $temp['desc'] = $row->blog_short_desc;
                $date = date_create($row->blog_publish_date);
                $temp['date'] = date_format($date, 'dS F Y');
                $blog[] = $temp;
            }
 

            Database::freeDB();
            return $blog;
        } catch (PDOException $e) {
            //echo $e->getMessage();
        }
    }
    
    public function GetBlogCount() {
        $count = 0;        
        try {
            $db = Database::getDB();
            $date_today = date('Y-m-d');
            $strSQL = "SELECT * from  webblog WHERE blog_status=0 AND blog_language='en' ORDER BY blog_publish_date DESC";
            $stmt = $db->prepare($strSQL);
            $params = array("today" => $date_today);
            $result = $stmt->execute($params);
            $count = $stmt->rowCount();
            Database::freeDB();
        } catch (PDOException $e) {
            //echo $e->getMessage();
        }
        return $count;
    }
    
    public function ViewAllBlogs($start,$limit) {
        try {
            $db = Database::getDB();
            $date_today = date('Y-m-d');
            $strSQL = "SELECT * from  webblog WHERE blog_status=0 AND blog_language='en' ORDER BY blog_publish_date DESC LIMIT $start,$limit";
            $stmt = $db->prepare($strSQL);
            $params = array("today" => $date_today);
            $result = $stmt->execute($params);
            $blog = array();
            while ($row = $stmt->fetchObject()) {
                $temp['id'] = $row->blog_id;
                $temp['title'] = $row->blog_title;
                $temp['url'] = ABS_URL . 'blog/' . $row->blog_url;
                $temp['img_url'] = $row->blog_img;
                $temp['desc'] = $row->blog_short_desc;
                $date = date_create($row->blog_publish_date);
                $temp['date'] = date_format($date, 'dS F Y');
                $blog[] = $temp;
            }

            Database::freeDB();
            return $blog;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    public function GetBlogById($id) {
        try {
            $db = Database::getDB();
            $date_today = date('Y-m-d');
            $strSQL = "SELECT * from  webblog WHERE  blog_status=0 AND blog_id=:id";
            $stmt = $db->prepare($strSQL);
            $params = array("id"=> $id);
            $result = $stmt->execute($params);
            $count = $stmt->rowCount();
            if($count>0){
                $blog = $stmt->fetchObject();                
                $db = NULL;
                Database::freeDB();                
                return $blog;
            }else{
                return FALSE;
            }
        } catch (PDOException $e) {
            //echo $e->getMessage();
        }
    }
    
    public function GetBlogByURL($url) {
        try {
            $db = Database::getDB();
            $date_today = date('Y-m-d');
            $strSQL = "SELECT * from  webblog WHERE  blog_status=0 AND blog_language='en' AND blog_publish_date <=  :today AND blog_url=:url";
            $stmt = $db->prepare($strSQL);
            $params = array("today" => $date_today,"url"=> $url);
            $result = $stmt->execute($params);
            $count = $stmt->rowCount();
            if($count>0){
                $blog = array();
                $row = $stmt->fetchObject();            
                $blog['title'] = $row->blog_title;
                $blog['meta_title'] = $row->blog_meta_title;
                $blog['meta_desc'] = $row->blog_meta_desc;
				$blog['seo_header'] = $row->blog_seo_header;
                $blog['content'] = $row->blog_content;
                $blog['url'] = ABS_URL . 'blog/' . $row->blog_url;
                $blog['blog_alt'] = $row->blog_alt;
                $blog['url_with_text'] = rawurlencode($row->blog_title) . ':' . ABS_URL . 'blog/' . $row->blog_url;
                $blog['img_url'] = $row->blog_img;
                $blog['desc'] = $row->blog_short_desc;
                $date = date_create($row->blog_publish_date);
                $blog['date'] = date_format($date, 'dS F Y');
                Database::freeDB();
                return $blog;
            }else{
                return FALSE;
            }
        } catch (PDOException $e) {
            //echo $e->getMessage();
        }
    }
    
    public function GetSelectedTheme(){
        $return =  array('id'=>1,'name' => 'default', 'path' => 'blog-post/theme/default/','count' => 10);
        try {
            $db = Database::getDB();
            $strSQL = "SELECT * from  webblogtheme WHERE  theme_selected=1";
            $stmt = $db->prepare($strSQL);
            $params = array();
            $result = $stmt->execute($params);
            $count = $stmt->rowCount();
            if($count>0){
                $data = $stmt->fetchObject();
                $return['id'] = $data->theme_id;
                $return['path'] = $data->theme_path;
                $return['count'] = $data->theme_blogcount;
                $return['name'] = $data->theme_name;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        
        return $return;
    }
    public function GetAllTheme(){
        $return =  array();
        try {
            $db = Database::getDB();
            $strSQL = "SELECT * from  webblogtheme";
            $stmt = $db->prepare($strSQL);
            $result = $stmt->execute();
            $count = $stmt->rowCount();
            if($count>0){
                while( $data = $stmt->fetchObject()){
                    $temp = array();
                    $temp['id'] = $data->theme_id;
                    $temp['path'] = $data->theme_path;
                    $temp['count'] = $data->theme_blogcount;
                    $temp['name'] = $data->theme_name;
                    $temp['selected'] = $data->theme_selected;
                    $return[] = $temp;
                }
            }
        } catch (PDOException $e) {
            //echo $e->getMessage();
        }
        
        return $return;
    }
    
    public function ChangeTheme($theme_id) {
        $return = NULL;
        try {
            $db = Database::getDB();
            $strSQL = "update webblogtheme SET theme_selected=0 WHERE theme_selected=1";
            $stmt = $db->prepare($strSQL);
            $result = $stmt->execute();
            $count = $stmt->rowCount();
            if($count>0){
                $strSQL2 = "update webblogtheme SET theme_selected=1 WHERE theme_id=:theme_id";
                $stmt2 = $db->prepare($strSQL2);
                $params = array("theme_id"=>$theme_id);
                $result2 = $stmt2->execute($params);
                $count2 = $stmt2->rowCount();
                if($count>0){
                    $return = TRUE;
                }
            }
        } catch (PDOException $e) {
            //echo $e->getMessage();
        }
        return $return;
    }
    

    private function GenerateURL($text) {

        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public static function uri() {
        $uri = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        echo $uri;
    }

}

?>