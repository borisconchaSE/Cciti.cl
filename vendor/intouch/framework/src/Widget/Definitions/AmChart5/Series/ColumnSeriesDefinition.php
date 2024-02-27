<?php
namespace Intouch\Framework\Widget\Definitions\AmChart5\Series;

class ColumnSeriesDefinition extends SeriesDefinition {
   
    public function __construct(
        public      ?bool   $Stacked = null,
                    ?string $FillColor = null,
                    ?float  $FillOpacity = null,
                    ?string $StrokeColor = null,
                    ?int    $StrokeWidth = 2,
        public      ?int    $TopLeftRadius = 5,
        public      ?int    $TopRightRadius = 5,
        public      ?int    $WidthPercentage = null,
        public      ?int    $WidthPixels = null,
        public              $DefinitionFunction = null,
        public      ?string $LegendLabelText = null,
        public      ?string $LegendRangeLabelText = null,
        public      ?string $ColumnTooltipText = null,
                    ?string $SeriesTooltipText = null,
    )
    {
        parent::__construct(
            FillOpacity             : $FillOpacity,
            FillColor               : $FillColor,
            StrokeWidth             : $StrokeWidth,
            StrokeColor             : $StrokeColor,
            LegendLabelText         : $LegendLabelText,
            LegendRangeLabelText    : $LegendRangeLabelText,
            SeriesTooltipText       : $SeriesTooltipText,
        );
    }

}