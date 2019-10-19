<?php

namespace App;

class PlotGenerator
{
    const WIDTH = 4000;
    const HEIGHT = 4000;

    const PADDING = 10;
    const LEGEND_X_HEIGHT = 10;
    const LEGEND_Y_WIDTH = 60;

    const FONT_FILE = __DIR__ . '/../resources/arial.ttf';

    private $data = [];
    private $image;

    private $imageColorWhite;
    private $imageColorBlack;
    private $imageColorGray;

    public function generate(array $data)
    {
        $this->data = $data;

        $this->initialize();
        $this->initializeColors();
        $this->setImagebackground();
        $this->drawPlotData();
        $this->drawLegendX();
        $this->drawLegendY();
        $this->drawPlotGridX();
        $this->drawPlotGridY();

        return $this->image;
    }

    private function initialize(): void
    {
        $this->image = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
    }

    private function initializeColors(): void
    {
        $this->imageColorWhite = imagecolorallocate($this->image, 255, 255, 255);
        $this->imageColorBlack = imagecolorallocate($this->image, 0, 0, 0);
        $this->imageColorGray = imagecolorallocate($this->image, 127, 127, 127);
    }

    private function setImageBackground(): void
    {
        imagefilledrectangle($this->image, 0, 0, self::WIDTH, self::HEIGHT, $this->imageColorWhite);
    }

    private function drawPlotData(): void
    {
        $countY = count($this->data);
        $plotHeight = $this->getPlotHeight();

        $rowHeight = $plotHeight / $countY;

        $cntY = 0;
        foreach ($this->data as $date => $xData) {
            $y1 = $cntY * $rowHeight + self::PADDING + self::LEGEND_X_HEIGHT;
            $y2 = $y1 + $rowHeight;

            $countX = count($xData);
            $plotWidth = $this->getPlotWidth();

            $rowWidth = $plotWidth / $countX;

            $cntX = 0;
            foreach ($xData as $minuteOfDay => $isVisible) {
                $x1 = $cntX * $rowWidth + self::PADDING + self::LEGEND_Y_WIDTH;
                $x2 = $x1 + $rowWidth;

                if ($isVisible > 0) {
                    $color = $isVisible === 1 ? $this->imageColorGray : $this->imageColorBlack;
                    imagefilledrectangle($this->image, $x1, $y1, $x2, $y2, $color);

                    $string = $this->getTimeFromMinuteOfDay($minuteOfDay);

                    imagettftext($this->image, 8, 0, $x1 + 2, $y1 + 10, $this->imageColorWhite, self::FONT_FILE, $string);
                }

                $cntX++;
            }

            $cntY++;
        }
    }

    private function drawLegendX(): void
    {
        $dataCopy = $this->data;
        $xData = array_pop($dataCopy);

        $countX = count($xData);
        $plotWidth = $this->getPlotWidth();

        $rowWidth = $plotWidth / $countX;

        $cntX = 0;
        foreach ($xData as $minuteOfDay => $isVisible) {
            $x = $cntX * $rowWidth + self::PADDING + self::LEGEND_Y_WIDTH;
            $y = self::PADDING;

            $string = $this->getTimeFromMinuteOfDay($minuteOfDay);

            imagettftext($this->image, 8, 0, $x + 2, $y + 8, $this->imageColorBlack, self::FONT_FILE, $string);

            $cntX++;
        }
    }

    private function drawLegendY(): void
    {
        $count = count($this->data);
        $plotHeight = $this->getPlotHeight();

        $rowHeight = $plotHeight / $count;

        $cnt = 0;
        foreach ($this->data as $date => $entry) {
            $x = self::PADDING;
            $y = $cnt * $rowHeight + self::PADDING + self::LEGEND_X_HEIGHT;

            imagettftext($this->image, 8, 0, $x, $y + 10, $this->imageColorBlack, self::FONT_FILE, $date);

            $cnt++;
        }
    }

    private function drawPlotGridX(): void
    {
        $dataCopy = $this->data;
        $xData = array_pop($dataCopy);

        $countX = count($xData);
        $plotWidth = $this->getPlotWidth();

        $rowWidth = $plotWidth / $countX;

        for ($x = 0; $x < $countX; $x++) {
            $x1 = $x * $rowWidth + self::PADDING + self::LEGEND_Y_WIDTH;
            $y1 = self::PADDING;
            $x2 = $x1;
            $y2 = self::HEIGHT - self::PADDING;

            imageline($this->image, $x1, $y1, $x2, $y2, $this->imageColorGray);
        }
    }

    private function drawPlotGridY(): void
    {
        $countY = count($this->data);
        $plotHeight = $this->getPlotHeight();

        $rowHeight = $plotHeight / $countY;

        for ($y = 0; $y < $countY; $y++) {
            $x1 = self::PADDING;
            $y1 = $y * $rowHeight + self::PADDING + self::LEGEND_X_HEIGHT;
            $x2 = self::WIDTH - self::PADDING;
            $y2 = $y1;

            imageline($this->image, $x1, $y1, $x2, $y2, $this->imageColorGray);
        }
    }

    private function getPlotWidth(): float
    {
        return self::WIDTH - 2 * self::PADDING - self::LEGEND_Y_WIDTH;
    }

    private function getPlotHeight(): float
    {
        return self::HEIGHT - 2 * self::PADDING - self::LEGEND_X_HEIGHT;
    }

    private function getTimeFromMinuteOfDay(int $minuteOfDay): string
    {
        $dateTime = new \DateTime();
        $dateTime->setTime(0, $minuteOfDay, 0);

        return $dateTime->format('H:i');
    }
}
