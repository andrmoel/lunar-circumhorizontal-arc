# What does this tool do?
This tool generates plots to visualize a possible occuration of a lunar circumhorizontal arc for a given location.

## Requirements
* PHP > 7.2
* PHP-GD Extension
* composer

## Installation
1. Check out the repository
2. Run `composer install`

## Run the app
1. Adapt the desired parameters* in `/public/generate.php` (e.g. Locations)
2. Run `php /public/generate.php`
3. Wait, the tool is generating the plots
4. The result can be found in the `/plots` folder

\* Default parameters are given:

```
$yearFrom = 2019;
$yearTo = 2030;

$locations = [
    'flensburg' => Location::create(54.78, 9.44),
    'hamburg' => Location::create(53.55, 9.99),
    'berlin' => Location::create(52.52, 13.41),
    'leipzig' => Location::create(51.34, 12.38),
    'frankfurt-main' => Location::create(50.11, 8.68),
    'nuernberg' => Location::create(49.56, 11.08),
    'stuttgart' => Location::create(48.78, 9.18),
    'muenchen' => Location::create(48.14, 11.58),
];
```
