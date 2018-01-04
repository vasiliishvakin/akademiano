<?php

return [
    'square150' => function (\PHPixie\Image\Drivers\Driver\Resource $image, $newPath, $format) {
        $initWidth = 150;
        $initHeight = 150;

        $width =  ($image->width() < $initWidth) ? $image->width() : $initWidth;
        $height =  ($image->height() < $initHeight) ? $image->height() : $initHeight;
        $image->fill($width, $height);

        return $image->save($newPath, $format, 80);
    }
];
