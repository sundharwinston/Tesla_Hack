<?php

class Capcha {

    private $sessionKey;

    function __construct($key) {
        $this->sessionKey = $key;
    }

    public function Create($format,$width = 200, $height = 34, $length = 6,$letters = "ABCDEFGHIJKLMNPQRSTUVWXYZ", $textColor = '#000', $backgroundColor = '#fff', $noiceLines = 0, $noiceDots = 10, $noiceColor = '#404040') {
        

        $fontSize = $height * 0.75;
        $image = imagecreatetruecolor($width, $height);

        $textColor = $this->hexToRGB($textColor);
        $textColor = imagecolorallocate($image, $textColor['r'], $textColor['g'], $textColor['b']);

        $backgroundColor = $this->hexToRGB($backgroundColor);
        $backgroundColor = imagecolorallocate($image, $backgroundColor['r'], $backgroundColor['g'], $backgroundColor['b']);

        imagefilledrectangle($image, 0, 0, $width, $height, $backgroundColor);
        if ($noiceLines > 0 && $format>0) {
            $noiceColor = $this->hexToRGB($noiceColor);
            $noiceColor = imagecolorallocate($image, $noiceColor['r'], $noiceColor['g'], $noiceColor['b']);
            for ($i = 0; $i < $noiceLines; $i++) {
                imageline($image, 0, rand() % $height, $width, rand() % $height, $noiceColor);
            }
        }
        if ($noiceDots > 0) {
            for ($i = 0; $i < $noiceDots; $i++) {
                imagesetpixel($image, rand() % $width, rand() % $height, $textColor);
            }
            for ($i = 0; $i < $noiceDots; $i++) {
                imagesetpixel($image, rand() % $width, rand() % $height, $textColor);
            }
//            for ($i = 0; $i < $noiceDots; $i++) {
//                imagefilledellipse($image, mt_rand(0, $width), mt_rand(0, $height), 3, 3, $textColor);
//            }
        }
        $word = $this->random($length,$letters);
        $font = ABS_PATH . 'fonts/capcha.otf';
        list($x, $y) = $this->ImageTTFCenter($image, $word, $font, $fontSize);
        imagettftext($image, $fontSize, 0, $x, $y, $textColor, $font, $word);
        if (isset($_SESSION)) {
            $_SESSION[Constants::COMPANY . $this->sessionKey] = $word;
        }
        //echo $word;
        //exit;
        ob_clean();
        header('Content-type: image/png');
        imagepng($image);
        ImageDestroy($image);
    }

    /* for random string */
    protected function random($characters = 6, $letters = '23456789bcdfghjkmnpqrstvwxyz') {
        $str = '';
        for ($i = 0; $i < $characters; $i++) {
            $str .= substr($letters, mt_rand(0, strlen($letters) - 1), 1);
        }
        return $str;
    }
    
    /* function to convert hex value to rgb array */
    protected function hexToRGB($colour) {
        if ($colour[0] == '#') {
            $colour = substr($colour, 1);
        }
        if (strlen($colour) == 6) {
            list( $r, $g, $b ) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
        } elseif (strlen($colour) == 3) {
            list( $r, $g, $b ) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
        } else {
            return false;
        }
        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);
        return array('r' => $r, 'g' => $g, 'b' => $b);
    }

    /* function to get center position on image */

    protected function ImageTTFCenter($image, $text, $font, $size, $angle = 8) {
        $xi = imagesx($image);
        $yi = imagesy($image);
        $box = imagettfbbox($size, $angle, $font, $text);
        $xr = abs(max($box[2], $box[4])) + 5;
        $yr = abs(max($box[5], $box[7]));
        $x = intval(($xi - $xr) / 2);
        $y = intval(($yi + $yr) / 2);
        return array($x, $y);
    }

    public function Valid($code) {
        return (strlen($code) > 0 && !empty($this->sessionKey) && !empty($_SESSION[Constants::COMPANY . $this->sessionKey]) && $_SESSION[Constants::COMPANY . $this->sessionKey] === $code);
    }
}
?>