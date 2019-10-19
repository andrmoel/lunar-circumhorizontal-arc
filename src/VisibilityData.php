<?php

namespace App;

use Andrmoel\AstronomyBundle\AstronomicalObjects\Moon;
use Andrmoel\AstronomyBundle\AstronomicalObjects\Sun;
use Andrmoel\AstronomyBundle\Location;
use Andrmoel\AstronomyBundle\TimeOfInterest;

class VisibilityData
{
    const STEP_SIZE_IN_MINUTES = 15;

    const CHA_MIN = 57.8;
    const ILLUMINATION_MIN = 0.5;
    const ILLUMINATION_BRIGHT = 0.75;

    private $location;
    private $sun;
    private $moon;

    public function __construct(Location $location)
    {
        $this->location = $location;
        $this->sun = new Sun();
        $this->moon = new Moon();
    }

    public function getVisibilityData(int $year): array
    {
        $result = [];

        $dateTime = new \DateTime($year . '-01-01 00:00:00');

        do {
            $dateStr = $dateTime->format('Y-m-d');
            $result[$dateStr] = $this->getDataForDay($dateTime);

            $dateTime->add(new \DateInterval('P1D'));
        } while (intval($dateTime->format('Y')) === $year);

        return $result;
    }

    private function getDataForDay(\DateTime $dateTime): array
    {
        $result = [];

        // Get lunar illumination for 00:00 to estimate if calculation is necessary.
        // If illumination of the moon is greater than 40%, we do the calculation
        $calculationNecessary = $this->getLunarIllumination($dateTime) > 0.4;

        for ($minuteOfDay = 0; $minuteOfDay < 1440; $minuteOfDay += self::STEP_SIZE_IN_MINUTES) {
            $dateTime->setTime(0, $minuteOfDay);

            $isVisible = false;
            if ($calculationNecessary) {
                $isVisible = $this->getLunarZhbVisibility($dateTime);
            }

            $result[$minuteOfDay] = $isVisible;

            echo 'Calculated visibility for ' . $dateTime->format('Y-m-d H:i') . ': ' . ($isVisible ? 'visible' : 'no') . "\n";
        }

        return $result;
    }

    private function getLunarZhbVisibility(\DateTime $dateTime): bool
    {
        $toi = TimeOfInterest::createFromDateTime($dateTime);
        $this->sun->setTimeOfInterest($toi);
        $this->moon->setTimeOfInterest($toi);

        $localHorizontalCoordinates = $this->sun->getLocalHorizontalCoordinates($this->location);
        $altitude = $localHorizontalCoordinates->getAltitude();

        // Must be astronomical twilight
        if ($altitude > -12) {
            return 0;
        }

        $localHorizontalCoordinates = $this->moon->getLocalHorizontalCoordinates($this->location);
        $altitude = $localHorizontalCoordinates->getAltitude();

        if ($altitude < self::CHA_MIN) {
            return 0;
        }

        $illumination = $this->moon->getIlluminatedFraction();

        if ($illumination < self::ILLUMINATION_MIN) {
            return 0;
        }

        return 2;
    }

    private function getLunarIllumination(\DateTime $dateTime): float
    {
        $toi = TimeOfInterest::createFromDateTime($dateTime);
        $this->moon->setTimeOfInterest($toi);

        return $this->moon->getIlluminatedFraction();
    }
}
