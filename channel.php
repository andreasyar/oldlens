<?php

require './functions.php';

ini_set('memory_limit', '2G');

$reference_image_name = $argv[1];
$channel_name = $argv[2];

$img = imagecreatefrompng($reference_image_name);

$width = imagesx($img);
$height = imagesy($img);

$channel = [];

// Convert each pixel of image from
// RGB to Lab and print desired channel
// as json object

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
                $channel[$y][$x] = $lab_a;
                break;
            case 'b':
                $channel[$y][$x] = $lab_b;
                break;
        }
    }
}

echo json_encode(['w' => $width, 'h' => $height, 'channel' => ['name' => $argv[2], 'data' => $channel]]);

imagedestroy($img);