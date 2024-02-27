<?php
namespace Intouch\Framework\Widget\Definitions\AmChart5\Chart;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\Widget\Definitions\AmChart5\Axis\Axis;
use Intouch\Framework\Widget\Definitions\AmChart5\Axis\XAxis;
use Intouch\Framework\Widget\Definitions\AmChart5\Series\Series;

#[Widget(Template: 'ChartXY', Path: '../Templates', Extension: '.js')]
class ChartXY extends Chart {

    public function OnBeforeDraw()
    {
        // Asignar las llaves a los ejes y series

        // XAxis
        //
        $this->XAxis->RootKey  = $this->RootKey;
        $this->XAxis->ChartKey = $this->Key;

        // YAxes
        //
        foreach($this->YAxis as $item) {
            // Asignar el RootKey
            $item->RootKey  = $this->RootKey;
            $item->ChartKey = $this->Key;
        }

        // Series
        //
        foreach($this->Series as $serie) {            
            $serie->RootKey  = $this->RootKey;
            $serie->ChartKey = $this->Key;
        }

        parent::OnBeforeDraw();
    }

    public function __construct(
        string  $Key,
        public XAxis   $XAxis,
        public array   $YAxis,
        public array   $Series,
        ?array  $Data = null,
        ?bool   $ShowLegend = null,
    ) {
        // Verificar YAxis
        foreach($YAxis as $item) {
            if (!($item instanceof Axis)) {                
                throw new \Exception("Un elemento de los ejes Y no es una instancia de Axis o sus clases derivadas");
            }
        }

        // Verificar Series
        // Series
        foreach($Series as $serie) {
            if (!($serie instanceof Series)) {
                throw new \Exception("Un elemento de las series no es una instancia de Series o sus clases derivadas");
            }
        }

        $dataset = "";
        $dataset .= "\t\t\t\txAxis_" . $XAxis->Key . ".data.setAll([[ROOTKEY]]_data);\n";

        // foreach ($YAxis as $item) {
        //     $dataset .= "\t\t\t\tyAxis_" . $item->Key . ".data.setAll([[ROOTKEY]]_data);\n";
        // }

        foreach ($Series as $child) {            
            $dataset .= "\t\t\t\t[[ROOTKEY]]_series_" . $child->Key . ".data.setAll([[ROOTKEY]]_data);\n";
            $dataset .= "\t\t\t\t[[ROOTKEY]]_series_" . $child->Key . ".appear(1000);\n";
            $dataset .= "\t\t\t\tchart_" . $Key . ".appear(1000, 100);\n";
        }

        $legend = '';
        if (isset($ShowLegend) && $ShowLegend) {
            $legend = "\t\t\t\tvar legend_$Key = chart_$Key.children.unshift(am5.Legend.new([[ROOTKEY]], {
                layout: [[ROOTKEY]].horizontalLayout,
                y: am5.percent(100),
                x: am5.percent(50),
                centerX: am5.percent(50),
                marginTop: 10,
                marginBottom: 10,
                paddingTop: 20
            }));\n";
        }

        parent::__construct(
            Key         : $Key,
            Category    : $XAxis->PropertyName,
            Replace     : [
                'LEGEND'            => $legend,
                'KEY'               => $Key,
                'XAXIS'             => $XAxis,
                'YAXIS'             => $YAxis,
                'SERIES'            => $Series,
                'DATACOLLECTION'    => function () { return $this->Data; },
                'DATASET'           => $dataset,
                'ROOTKEY'           => function () { return $this->RootKey; },
            ],
            Data: $Data
        );
    }

}