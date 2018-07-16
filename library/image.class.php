<?php
class Image {
    /**
     *
     * Saves an image
     * @param String $fl full path of the file
     * @param String $name name of file
     * @param String $response In case of success exact name of file will be returned in this else error message
     * @return Boolean
     */
    public static function saveImage($fl, $name, &$response){
        $size = getimagesize($fl);
        if ($size === false){
            $response = 'IMAGE_FORMAT_NOT_RECOGNIZED';
            return false;
        }
        $fname = preg_replace('/[^a-zA-Z0-9]/', '', $name);
        while (file_exists(CONF_INSTALLATION_PATH . 'user-uploads/' . $fname)){
            $fname .= '_' . rand(10, 99);
        }
    
        if (!copy($fl, CONF_INSTALLATION_PATH . 'user-uploads/' . $fname)){
            $response = 'COULD_NOT_SAVE_FILE';
            return false;
        }
    
        $response = $fname;
        return true;
    }
    
    public static function displayImage($img, $w=0, $h=0) {
        $w = intval($w);
        $h = intval($h);
        $pth = CONF_INSTALLATION_PATH . 'images/' . $img;
		
        if (!is_file($pth)) {
            $pth = CONF_INSTALLATION_PATH . 'public/images/no-image.jpg';
        }
        
        if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($pth))) {
            header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($pth)).' GMT', true, 304);
            exit;
        }
        
        header('Cache-Control: public');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($pth)).' GMT', true, 200);
        
        if ($w == 0 && $h == 0) {
            $size = getimagesize($pth);
            
            if ($size === false){
                echo 'INVALID_FILE';
                return false;
            }
            
            header("Content-Type: " . $size['mime']);
            
            readfile($pth);
        }
        
        $obj = new imageResize($pth);
        $obj->setMaxDimensions($w, $h);
        header("Content-Type: image/jpeg");
        $obj->displayImage();
    }
}