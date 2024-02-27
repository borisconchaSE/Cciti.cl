<?php
namespace Intouch\Framework\Widget\Definitions\AmChart5\Series;

class LabelSprite extends Sprite {

    public function __construct(
        public  string  $Text,
        public  bool    $PopulateText = true,
                ?int    $CenterXPercentage = null,
                ?int    $CenterYPercentage = null,
    ) {
        parent::__construct(CenterXPercentage: $CenterXPercentage, CenterYPercentage: $CenterYPercentage);
    }

}