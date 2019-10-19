<?php

use Andrmoel\AstronomyBundle\Location;
use App\PlotGenerator;
use App\VisibilityData;

require __DIR__ . '/../app/bootstrap.php';

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

foreach ($locations as $locationName => $location) {
    for ($year = $yearFrom; $year <= $yearTo; $year++) {
        $fileName = __DIR__ . '/../plots/' . $locationName . '_' . $year . '.png';

        if (file_exists($fileName)) {
            continue;
        }

        $vData = new VisibilityData($location);
        $data = $vData->getVisibilityData($year);

        $plotGenerator = new PlotGenerator();
        $image = $plotGenerator->generate($data);

        imagepng($image, $fileName);
    }
}
