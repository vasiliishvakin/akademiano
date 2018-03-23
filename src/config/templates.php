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
    'l300' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $long = 300;
        $image->resize($long, $long, false);
        return $image->save($newPath, $format, 80);
    },
    'l400' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $long = 400;
        $image->resize($long, $long, false);
        return $image->save($newPath, $format, 80);
    },
    'l500' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $long = 500;
        $image->resize($long, $long, false);
        return $image->save($newPath, $format, 80);
    },
    'l600' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $long = 600;
        $image->resize($long, $long, false);
        return $image->save($newPath, $format, 80);
    },
    'l700' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $long = 700;
        $image->resize($long, $long, false);
        return $image->save($newPath, $format, 80);
    },
    'l800' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $long = 800;
        $image->resize($long, $long, false);
        return $image->save($newPath, $format, 80);
    },
    'l1200' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $long = 1200;
        $image->resize($long, $long, false);
        return $image->save($newPath, $format, 80);
    },
    'orig1200' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $long = 1200;
        $resize = false;
        if (($image->width()/$long) - 1 <= 0.1) {
            $width = $image->width();
            $resize = false;
        } else {
            $width = $long;
            $resize = true;
        }

        if (($image->height()/$long) - 1 <= 0.1) {
            $heigth = $image->width();
            $resize = false;
        } else {
            $heigth = $long;
            $resize = true;
        }
        $image->resize($width, $heigth, false);
        return $image->save($newPath, $format, 100);
    },
];
