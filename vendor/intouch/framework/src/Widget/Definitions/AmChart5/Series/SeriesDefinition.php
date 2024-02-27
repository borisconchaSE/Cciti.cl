<?php
namespace Intouch\Framework\Widget\Definitions\AmChart5\Series;

abstract class SeriesDefinition {
   
    public function __construct(
        public      ?string     $FillColor = null,
        public      ?float      $FillOpacity = null,
        public      ?string     $StrokeColor = null,
        public      ?int        $StrokeWidth = 2,
        public      ?string     $LegendLabelText = null,
        public      ?string     $LegendRangeLabelText = null,
        public      ?string     $SeriesTooltipText = null,
        public      ?Bullet     $BulletDefinition = null,
    )
    {}

}