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
    'square200' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $initWidth = 200;
        $initHeight = 200;

        $width =  ($image->width() < $initWidth) ? $image->width() : $initWidth;
        $height =  ($image->height() < $initHeight) ? $image->height() : $initHeight;
        $image->fill($width, $height);

        return $image->save($newPath, $format, 85);
    },
    'square250' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $initWidth = 250;
        $initHeight = 250;

        $width =  ($image->width() < $initWidth) ? $image->width() : $initWidth;
        $height =  ($image->height() < $initHeight) ? $image->height() : $initHeight;
        $image->fill($width, $height);

        return $image->save($newPath, $format, 90);
    },
    'square300' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $initWidth = 300;
        $initHeight = 300;

        $width =  ($image->width() < $initWidth) ? $image->width() : $initWidth;
        $height =  ($image->height() < $initHeight) ? $image->height() : $initHeight;
        $image->fill($width, $height);

        return $image->save($newPath, $format, 90);
    },
    '999X630' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $initWidth = 999;
        $initHeight = 630;

        $width =  ($image->width() < $initWidth) ? $image->width() : $initWidth;
        $height =  ($image->height() < $initHeight) ? $image->height() : $initHeight;
        $image->fill($width, $height);

        return $image->save($newPath, $format, 90);
    },
    '1129x250' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $initWidth = 1129;
        $initHeight = 250;

        $width =  ($image->width() < $initWidth) ? $image->width() : $initWidth;
        $height =  ($image->height() < $initHeight) ? $image->height() : $initHeight;
        $image->fill($width, $height);

        return $image->save($newPath, $format, 90);
    },
    'l250' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $long = 250;
        $image->resize($long, $long, false);
        return $image->save($newPath, $format, 90);
    },
    'l300' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $long = 300;
        $image->resize($long, $long, false);
        return $image->save($newPath, $format, 90);
    },
    'l400' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $long = 400;
        $image->resize($long, $long, false);
        return $image->save($newPath, $format, 90);
    },
    'l500' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $long = 500;
        $image->resize($long, $long, false);
        return $image->save($newPath, $format, 90);
    },
    'l600' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $long = 600;
        $image->resize($long, $long, false);
        return $image->save($newPath, $format, 90);
    },
    'l700' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $long = 700;
        $image->resize($long, $long, false);
        return $image->save($newPath, $format, 90);
    },
    'l900' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $long = 900;
        $image->resize($long, $long, false);
        return $image->save($newPath, $format, 90);
    },
    'l1200' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $long = 1200;
        $image->resize($long, $long, false);
        return $image->save($newPath, $format, 90);
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
    'facebook' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $width = 1200;
        $height = 630;

        $image->fill($width, $height, false);
        return $image->save($newPath, $format, 100);
    },
    'twitter' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $width = 1024;
        $height = 512;

        $image->fill($width, $height, false);
        return $image->save($newPath, $format, 100);
    },
    'socials' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $width = 968;
        $height = 504;

        $image->fill($width, $height, false);
        return $image->save($newPath, $format, 100);
    },
];
