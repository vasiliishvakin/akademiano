<?php

return [
    'square150' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $initWidth = 150;
        $initHeight = 150;

        $width =  ($image->width() < $initWidth) ? $image->width() : $initWidth;
        $height =  ($image->height() < $initHeight) ? $image->height() : $initHeight;
        $image->fill($width, $height);

        return $image->save($newPath, $format, 80);
    },
    '999X630' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $initWidth = 999;
        $initHeight = 630;

        $width =  ($image->width() < $initWidth) ? $image->width() : $initWidth;
        $height =  ($image->height() < $initHeight) ? $image->height() : $initHeight;
        $image->fill($width, $height);

        return $image->save($newPath, $format, 80);
    },
    'l250' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $long = 250;
        $image->resize($long, $long, false);
        return $image->save($newPath, $format, 80);
    },
    'l1200' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $long = 1200;
        $image->resize($long, $long, false);
        return $image->save($newPath, $format, 80);
    },
];
