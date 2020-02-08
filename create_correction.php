<?php

require './functions.php';

ini_set('memory_limit', '4G');

$channel_file = $argv[1];

$json = json_decode(file_get_contents($channel_file), true);

$width = $json['w'];
$height = $json['h'];

$channel = $json['channel']['data'];

// Split image into 114x76 blocks to find minimum average channel value from all blocks
// Let this minimal average channel value to be a neutral color in this channel
// Any deviation from this minimal average value considered as color shift and 
// stored in correction what will be printed out as json object

// Block size 114x76 because I assume what image is 5472x3648 (3:2, 19,961,856)

$block_w = 114;
$block_h = 76;
$min_summ = null;

for ($block_i = 0; $block_i < ($height / $block_h); $block_i++) {
    $offset_y = $block_i * $block_h;
    for ($block_j = 0; $block_j < ($width / $block_w); $block_j++) {
        $offset_x = $block_j * $block_w;
        $summ = 0;
        for ($y = $offset_y; $y < $offset_y + $block_h; $y++) {
            for ($x = $offset_x; $x < $offset_x + $block_w; $x++) {
                $summ += abs($channel[$y][$x]);
            }
        }
        if ($min_summ === null || $summ < $min_summ) {
            $min_summ = $summ;
        }
    }
}

$avg = (float) $min_summ / ((float) $block_w * (float) $block_h);
$threshold = ceil($avg);

fprintf(STDERR, "avg=$avg,threshold=$threshold\n");

$correction = [];

for ($y = 0; $y < $height; $y++) {
    for ($x = 0; $x < $width; $x++) {
        $correction[$y][$x] = $threshold - $channel[$y][$x];
    }
}

echo json_encode(['w' => $width, 'h' => $height, 'correction' => $correction]);