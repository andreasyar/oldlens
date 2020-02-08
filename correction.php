<?php

require './functions.php';

ini_set('memory_limit', '2G');

$image_name = $argv[1];
$image_ext = $argv[2];
$correction_name = $argv[3];
$channel_name = $argv[4];

switch ($image_ext) {
    case 'jpeg':
        $img = imagecreatefromjpeg($image_name);
        break;
    case 'png':
        $img = imagecreatefrompng($image_name);
        break;
    case 'bmp':
        $img = imagecreatefrombmp($image_name);
        break;
}

$width = imagesx($img);
$height = imagesy($img);

$json = json_decode(file_get_contents($correction_name), true);

$correction = $json['correction'];

$img_corrected = imagecreatetruecolor($width, $height);

// Get every pixel of image to be corrected
// Convert it from RGB to Lab
// Apply correction
// Convert back from Lab to RGB and store into new image

for ($y = 0; $y < $height; $y++) {
    for ($x = 0; $x < $width; $x++) {
        $color = imagecolorat($img, $x, $y);
        $rgb_r = ($color >> 16) & 0xFF;
        $rgb_g = ($color >> 8) & 0xFF;
        $rgb_b =  $color & 0xFF;
        rgb2xyz($rgb_r, $rgb_g, $rgb_b, $xyz_x, $xyz_y, $xyz_z);
        xyz2lab($xyz_x, $xyz_y, $xyz_z, $lab_l, $lab_a, $lab_b);
        switch ($channel_name) {
            case 'a':
                $lab_a += $correction[$y][$x];
                if ($lab_a > 127) $lab_a = 127;
                if ($lab_a < -128) $lab_a = -128;
                break;
            case 'b':
                $lab_b += $correction[$y][$x];
                if ($lab_b > 127) $lab_b = 127;
                if ($lab_b < -128) $lab_b = -128;
                break;
        }
        lab2xyz($lab_l, $lab_a, $lab_b, $xyz_x, $xyz_y, $xyz_z);
        xyz2rgb($xyz_x, $xyz_y, $xyz_z, $rgb_r, $rgb_g, $rgb_b);
        $color_new = imagecolorallocate($img_corrected, $rgb_r, $rgb_g, $rgb_b);
        imagesetpixel($img_corrected, $x, $y, $color_new);
    }
}

imagebmp($img_corrected, "corrected_$channel_name.bmp");

imagedestroy($img_corrected);