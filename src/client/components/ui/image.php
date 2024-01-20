<?php

function calculateAspectRatio($width, $height) {
    $gcd = function ($a, $b) use (&$gcd) {
        return ($b === 0) ? $a : $gcd($b, $a % $b);
    };

    $divisor = $gcd($width, $height);

    $aspectRatio = [
        'width' => $width / $divisor,
        'height' => $height / $divisor,
    ];

    return $aspectRatio;
}

function generateSrcSets($imageUrl, $width, $height, $step = 100, $limit = 100, $minWidth = 50) {
    $aspectRatio = calculateAspectRatio($width, $height);

    $srcSets = [];

    for ($i = $width; $i <= $width + $limit; $i += $step) {
        $newHeight = round(($i / $aspectRatio['width']) * $aspectRatio['height']);
        $srcSets[] = "$imageUrl&width=$i&height=$newHeight $i" . 'w ' . $newHeight . 'h';
    }

    for ($i = $width - $step; $i >= $minWidth; $i -= $step) {
        $newHeight = round(($i / $aspectRatio['width']) * $aspectRatio['height']);
        $srcSets[] = "$imageUrl&width=$i&height=$newHeight $i" . 'w ' . $newHeight . 'h';
    }

    return array_reverse($srcSets);
}


function Image($imageUrl, $width, $height, $imageAttributes = []) {
    $webpSrcSets = generateSrcSets("$imageUrl?type=webp", $width, $height);
    $avifSrcSets = generateSrcSets("$imageUrl?type=avif", $width, $height);

    $defaultImageAttributes = [
        'loading' => 'lazy',
        'decoding' => 'async',
        'alt' => 'image',
        'width'=>$width,
        'height'=>$height,
        'src' => $imageUrl,
    ];

    $imageAttributes = array_merge($defaultImageAttributes, $imageAttributes);
?>
    <picture>
        <source type="image/webp" srcset="<?= implode(', ', $webpSrcSets) ?>" />
        <source type="image/avif" srcset="<?= implode(', ', $avifSrcSets) ?>" />

        <!-- Main image -->
        <img <?= buildAttributes($imageAttributes) ?> />
    </picture>
<?php
}