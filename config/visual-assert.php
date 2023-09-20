<?php

return [

    // Keep the screenshots the same size for better comparison
    'screenshot_width' => 1920,
    'screenshot_height' => 1080,


    // For more info on how images are compared see
    // https://www.php.net/manual/en/imagick.compareimages.php
    'default_threshold' => 0.0001,
    'default_metric' => \Imagick::METRIC_MEANSQUAREERROR,
];