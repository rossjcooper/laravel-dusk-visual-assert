<?php

namespace Rossjcooper\LaravelDuskVisualAssert;

use Imagick;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Assert;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        Browser::macro('assertScreenshot', function (string $name, float $threshold = 0.0001, int $metric = Imagick::METRIC_MEANSQUAREERROR) {
            /** @var Browser $this */

            $filePath = sprintf('%s/%s.png', rtrim(Browser::$storeScreenshotsAt, '/'), $name);

            $diffName = sprintf('%s-diff', $name);
            $diffFilePath = sprintf('%s/diffs/%s.png', rtrim(Browser::$storeScreenshotsAt, '/'), $diffName);

            $directoryPath = dirname($filePath);

            if (! is_dir($directoryPath)) {
                mkdir($directoryPath, 0777, true);
            }

            if (! file_exists($filePath)) {
                $this->driver->takeScreenshot($filePath);
            }

            $this->driver->takeScreenshot($diffFilePath);

            $originalImage =  new Imagick($filePath);
            $diffImage =  new Imagick($diffFilePath);

            $result = $originalImage->compareImages($diffImage, $metric);

            if ($result[1] > $threshold) {
                $result[0]->setImageFormat("png");
                $result[0]->writeImage($diffFilePath);
            } else {
                unlink($diffFilePath);
            }

            Assert::assertLessThanOrEqual($threshold, $result[1], sprintf('Screenshots are not the same. Difference can be viewed at: %s', $diffFilePath));

            return $this;
        });

        Browser::macro('assertResponsiveScreenshots', function (string $name, float $threshold = 0.0001, int $metric = Imagick::METRIC_MEANSQUAREERROR) {
            if (substr($name, -1) !== '/') {
                $name .= '-';
            }

            foreach (Browser::$responsiveScreenSizes as $device => $size) {
                $this->resize($size['width'], $size['height'])
                    ->assertScreenshot("$name$device");
            }

            return $this;
        });
    }

}