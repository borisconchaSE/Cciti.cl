<?php

declare(strict_types=1);

namespace Endroid\QrCode\ImageData;

use Endroid\QrCode\Label\LabelInterface;

class LabelImageData
{
    private int $width;
    private int $height;

    private function __construct(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public static function createForLabel(LabelInterface $label,int $margin = 0): self
    {
        // if (false !== strpos($label->getText(), "\n")) {
        //     throw new \Exception('Label does not support line breaks');
        // }

            if (!function_exists('imagettfbbox')) {
            throw new \Exception('Function "imagettfbbox" does not exist: check your FreeType installation');
        }

        $labelBox = imagettfbbox($label->getFont()->getSize(), 0, $label->getFont()->getPath(), $label->getText());

        if (!is_array($labelBox)) {
            throw new \Exception('Unable to generate label image box: check your FreeType installation');
        }   

        $w = intval($labelBox[2] - $labelBox[0]);
        $h = intval($labelBox[0] - $labelBox[7]) + $margin;

        return new self(
            $w,
            $h
            
        );
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }
}