# Laravel Dusk Visual Assert

This package adds assertions to compare screenshots taken during [Laravel Dusk](https://laravel.com/docs/10.x/dusk#taking-a-screenshot) tests using the Imagick extension.

## Installation

You can install the package via composer:

```bash
composer require --dev rossjcooper/laravel-dusk-visual-assert
```

## Configuration

Publish the config file to control default settings:

```bash
php artisan vendor:publish --tag=visual-assert-config
```


## Usage

The Dusk Browser class now has access to some new methods:

### assertScreenshot()

This method will take a screenshot of the current page and compare it to a reference image (generated the first time the test is run). 

If the images are different, the test will fail and save the image diff so you can inspect the differences.

```php
$browser->assertScreenshot(string $name, float|null $threshold = null, int|null $metric = null)
```

Example: 

```php
$this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->assertScreenshot('home');
    });
```

### assertResponsiveScreenshots()

This method is similar to the `assertScreenshot` as above but it screenshots the page at different screen sizes.

```php
$browser->assertResponsiveScreenshots(string $name, float|null $threshold = null, int|null $metric = null)
```

Example:

```php
$this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->assertResponsiveScreenshots('home');
    });
```

## Updating reference images

If you want to update the reference images simply delete them from the `tests/Browser/screenshots/references` directory and re-run your tests to generate new ones.

I would recommend committing the reference images to your repository so you can track changes to them over time.