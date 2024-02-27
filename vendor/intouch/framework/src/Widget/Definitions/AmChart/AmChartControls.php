<?php
namespace Intouch\Framework\Widget\Definitions\AmChart;

class AmChartControls {

    private $_ScrollBarX = false;

    private $_ScrollBarY = false;

    public function __construct(
        public bool $ScrollBarX     = false,
        public bool $ScrollBarY     = false,
        public bool $Cursor    = false
    )
    {
        $this->_ScrollBarX  = $ScrollBarX;
        $this->_ScrollBarY  = $ScrollBarY; 
        $this->_GraphCursor = $Cursor;       
    }


    public function BuildAmChartControls($ChartKey,$ChartType){

        $TemporalScript = "";

        if(
            $this->_ScrollBarX AND
            $ChartType != AmChartTypeEnum::PIE_CHART AND
            $ChartType != AmChartTypeEnum::PIE_DRILLDOWN AND
            $ChartType != AmChartTypeEnum::DONUTH_CHART
        ):
            $TemporalScript .= " $ChartKey.scrollbarX = new am4core.Scrollbar(); ";
        endif;

        if(
            $this->_ScrollBarY AND
            $ChartType != AmChartTypeEnum::PIE_CHART AND
            $ChartType != AmChartTypeEnum::PIE_DRILLDOWN AND
            $ChartType != AmChartTypeEnum::DONUTH_CHART
            ):
            $TemporalScript .= " $ChartKey.scrollbarY = new am4core.Scrollbar(); ";
        endif;

        if($this->_GraphCursor):
            switch($ChartType){                 
                case AmChartTypeEnum::BAR_CHART:
                case AmChartTypeEnum::BAR_CHART_HORIZONTAL:
                    $TemporalScript .= " $ChartKey.cursor = new am4charts.XYCursor(); ";                    
                break;
                case AmChartTypeEnum::LINE_CHART:
                    $TemporalScript .= "$ChartKey.cursor = new am4charts.XYCursor(); $ChartKey.cursor.xAxis = dateAxis; $ChartKey.cursor.maxTooltipDistance = 20;";
                    break;
            }
        endif;

        return $TemporalScript;

    }   
}