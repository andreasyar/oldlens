<?php

function rgb2xyz($rgb_r, $rgb_g, $rgb_b, &$xyz_x, &$xyz_y, &$xyz_z)
{
    // sRGB matrix from http://www.brucelindbloom.com/index.html?Eqn_RGB_XYZ_Matrix.html
    $xyz_x = 0.4124564 * $rgb_r + 0.3575761 * $rgb_g + 0.1804375 * $rgb_b;
    $xyz_y = 0.2126729 * $rgb_r + 0.7151522 * $rgb_g + 0.0721750 * $rgb_b;
    $xyz_z = 0.0193339 * $rgb_r + 0.1191920 * $rgb_g + 0.9503041 * $rgb_b;
}

function xyz2rgb($xyz_x, $xyz_y, $xyz_z, &$rgb_r, &$rgb_g, &$rgb_b)
{
    // sRGB matrix from http://www.brucelindbloom.com/index.html?Eqn_RGB_XYZ_Matrix.html
    $rgb_r =   3.2404542 * $xyz_x  + (-1.5371385 * $xyz_y) + (-0.4985314 * $xyz_z);
    $rgb_g = (-0.9692660 * $xyz_x) +   1.8760108 * $xyz_y  +   0.0415560 * $xyz_z;
    $rgb_b =   0.0556434 * $xyz_x  + (-0.2040259 * $xyz_y) +   1.0572252 * $xyz_z;
}

function xyz2lab($xyz_x, $xyz_y, $xyz_z, &$lab_l, &$lab_a, &$lab_b)
{
    // See http://www.brucelindbloom.com/index.html?Eqn_XYZ_to_Lab.html

    $epsilon = 0.008856;
    $kappa = 903.3;

    // See https://en.wikipedia.org/wiki/CIELAB_color_space
    $xyz_white_x = 95.0489;  // Illuminant D65
    $xyz_white_y = 100;      // Illuminant D65
    $xyz_white_z = 108.8840; // Illuminant D65

    $xyz_x_ref = $xyz_x / $xyz_white_x;
    $xyz_y_ref = $xyz_y / $xyz_white_y;
    $xyz_z_ref = $xyz_z / $xyz_white_z;

    if ($xyz_x_ref > $epsilon) {
        $f_x = $xyz_x_ref ** (1/3);
    } else {
        $f_x = ($kappa * $xyz_x_ref + 16.0) / 116.0;
    }

    if ($xyz_y_ref > $epsilon) {
        $f_y = $xyz_y_ref ** (1/3);
    } else {
        $f_y = ($kappa * $xyz_y_ref + 16.0) / 116.0;
    }

    if ($xyz_z_ref > $epsilon) {
        $f_z = $xyz_z_ref ** (1/3);
    } else {
        $f_z = ($kappa * $xyz_z_ref + 16.0) / 116.0;
    }

    $lab_l = (int) (116.0 * $f_y - 16.0);
    $lab_a = (int) (500.0 * ($f_x - $f_y));
    $lab_b = (int) (200.0 * ($f_y - $f_z));
}

function lab2xyz($lab_l, $lab_a, $lab_b, &$xyz_x, &$xyz_y, &$xyz_z)
{
    // See http://www.brucelindbloom.com/index.html?Eqn_Lab_to_XYZ.html

    $epsilon = 0.008856;
    $kappa = 903.3;

    // See https://en.wikipedia.org/wiki/CIELAB_color_space
    $xyz_white_x = 95.0489;  // Illuminant D65
    $xyz_white_y = 100;      // Illuminant D65
    $xyz_white_z = 108.8840; // Illuminant D65

    $f_y = ($lab_l + 16.0) / 116.0;
    $f_x = $lab_a / 500.0 + $f_y;
    $f_z = $f_y - ($lab_b / 200.0);

    $f_x_3 = $f_x ** 3;
    $f_z_3 = $f_z ** 3;

    if ($f_x_3 > $epsilon) {
        $xyz_x_ref = $f_x_3;
    } else {
        $xyz_x_ref = (116.0 * $f_x - 16.0) / $kappa;
    }

    if ($lab_l > $epsilon * $kappa) {
        $xyz_y_ref = $f_y ** 3;
    } else {
        $xyz_y_ref = $lab_l / $kappa;
    }

    if ($f_z_3 > $epsilon) {
        $xyz_z_ref = $f_z_3;
    } else {
        $xyz_z_ref = (116.0 * $f_z - 16.0) / $kappa;
    }

    $xyz_x = $xyz_x_ref * $xyz_white_x;
    $xyz_y = $xyz_y_ref * $xyz_white_y;
    $xyz_z = $xyz_z_ref * $xyz_white_z;
}

function gettransparency($lab_a, $threshold) {
    // "a" is 0,1,2,...,126,127 total 128 values (in case of green negative "a" shift "a" to 128 before func call)
    // Convert range of "a" [threshold, 127] to transparency range [127, 0]
    $lab_a_range        = 127.0 - (float) $threshold;
    $transparency_range = 127.0 - 0.0;
    $transparency_float = ((((float) $lab_a - (float) $threshold) * $transparency_range) / $lab_a_range) + 0.0;
    // transparency_float will be [0,127] but we need [127,0] so convert it
    $transparency = 127 - (int) $transparency_float;
    if ($transparency > 127) { // Not sure need it or not
        fprintf(STDERR, "transparency > 127 for transparency_float = $transparency_float\n");
        $transparency = 127;
    }
    return $transparency;
}
