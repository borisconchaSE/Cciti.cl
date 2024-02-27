<?php
namespace Intouch\Framework\Widget\Definitions\AmChart5\Series;

class LineSeriesDefinition extends SeriesDefinition {
   
    public function __construct(
                    ?string $FillColor = null,
                    ?float  $FillOpacity = null,
                    ?string $StrokeColor = null,
                    ?int    $StrokeWidth = 2,
        public      ?array  $StrokeDash = null,
        public      ?bool    $AreaVisible = null,
        public      ?string $LegendLabelText = null,
        public      ?string $LegendRangeLabelText = null,
    )
    {
        parent::__construct(
            FillOpacity             : $FillOpacity,
            FillColor               : $FillColor,
            StrokeWidth             : $StrokeWidth,
            StrokeColor             : $StrokeColor,
            LegendLabelText         : $LegendLabelText,
            LegendRangeLabelText    : $LegendRangeLabelText
        );
    }

}