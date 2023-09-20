<?php

namespace Rossjcooper\LaravelDuskVisualAssert;

use Imagick;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Assert;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/visual-assert.php' => config_path('visual-assert.php'),
        ], 'visual-assert-config');

        Browser::macro('assertScreenshot', function (string $name, float|null $threshold = null, int|null $metric = null) {
            /** @var Browser $this */

            $threshold = $threshold ?? config('visual-assert.default_threshold');
            $metric = $metric ?? config('visual-assert.default_metric');
            $width = config('visual-assert.screenshot_width');
            $height = config('visual-assert.screenshot_height');

            $filePath = sprintf('%s/references/%s.png', rtrim(Browser::$storeScreenshotsAt, '/'), $name);

            $diffName = sprintf('%s-diff', $name);
            $diffFilePath = sprintf('%s/diffs/%s.png', rtrim(Browser::$storeScreenshotsAt, '/'), $diffName);

            $directoryPath = dirname($filePath);

            if (! is_dir($directoryPath)) {
                mkdir($directoryPath, 0777, true);
            }

            if (! file_exists($filePath)) {
                $this->resize($width, $height);
                $this->driver->takeScreenshot($filePath);
                Assert::assertTrue(true, 'Reference screenshot stored successfully.');

                return $this;
            }


            $this->resize($width, $height);
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

        Browser::macro('assertResponsiveScreenshots', function (string $name, float|null $threshold = null, int|null $metric = null) {
            /** @var Browser $this */
            $threshold = $threshold ?? config('visual-assert.default_threshold');
            $metric = $metric ?? config('visual-assert.default_metric');

            if (substr($name, -1) !== '/') {
                $name .= '-';
            }

            foreach (Browser::$responsiveScreenSizes as $device => $size) {
                $this->resize($size['width'], $size['height'])
                    ->assertScreenshot("$name$device", $threshold, $metric);
            }

            return $this;
        });
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/visual-assert.php', 'visual-assert'
        );
    }

}
