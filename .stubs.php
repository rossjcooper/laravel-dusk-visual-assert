<?php

namespace Laravel\Dusk {

    use Imagick;

    class Browser {
        public function assertScreenshot(string $name, float $threshold = 0.0001, int $metric = Imagick::METRIC_MEANSQUAREERROR): static {}

        public function assertResponsiveScreenshots(string $name, float $threshold = 0.0001, int $metric = Imagick::METRIC_MEANSQUAREERROR): static {}

    }
}
