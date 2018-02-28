<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ApplicationHelpers
{
    
    public static function uploadMedia($image_string, $extension, $entity_id, $old_file = false) {

        if($old_file)
            self::deleteMedia($old_file);
        $image_name = self::uploadImage($image_string, $extension);
        
        return $image_name;

    }
    public static function deleteMedia($file_name) {
        
        $file = storage_path().'/'. $file_name;
        File::delete($file);
    }
    
    public static function uploadImage($image_string,$extension)
    {
        $decode_string = str_replace(' ', '+', $image_string);

        $decode_string = base64_decode($decode_string);

        $name =  uniqid();
        $image = self::createImageOnFly($name, $extension, $decode_string);                                   
        $uploaded_image = $name.".".$extension;
        return $uploaded_image;
    }
    
    public static function createImageOnFly($name,$extension,$string)
    {
        $file = storage_path().'/'. $name . '.'.$extension;
        
        $extension = strtolower($extension);

        switch($extension)
        {
            case 'jpg':
            case 'jpeg':
                $create_image = 'imagecreatefromjpeg';
                $save_image   = 'imagejpeg';
                break;
            case 'gif':
                $create_image = 'imagecreatefromgif';
                $save_image   = 'imagegif';
                break;
            case 'png':
                $create_image = 'imagecreatefrompng';
                $save_image   = 'imagepng';
                break;
           
        }
        
        $image = imagecreatefromstring($string);
        if($image != false)
        {
            imagesavealpha($image, true);
            $save_image($image, $file);
            return TRUE;
        }         
        return FALSE;
    }

}
