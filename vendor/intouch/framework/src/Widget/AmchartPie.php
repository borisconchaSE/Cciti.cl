<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\AmChart\AmChartTypeEnum;
use Intouch\Framework\Widget\ScriptDirect;

#[Widget(Template: 'ChartContainer')]

class AmchartPie extends AmChartGeneric{

    //Variable interna que almacena el script construido
    private         $_Script_    = ""; 
    private string  $_Key        = "";
    private         $_Data       = [];
    private         $_Settings;

    //INICIO DEL CONSTRUCTOR
    public function __construct(
        public string  $Key,
        public array   $Classes = [],
        public array   $Styles = [],
        public array   $Attributes = [],
        public array   $Properties = [],
        public string  $Titulo = '',
        public         $Datos,  
        public array   $PieConfig   = [   
            "LabelName"     => "",
            "ValueName"     => "",   
            "DonutMode"     => false, 
            "ColorName"     => null,
            "Legend"        => true                         
        ]
        
    )
    {   
        //Seteo el key
        $this->_Key = trim($Key);
        //ME ASEGURO QUE LA VARIABLE PieConfig SEA UN OBJETO
        $this->_Settings = json_decode(json_encode($PieConfig));

        $this->_Data = $this->format_simple_data($Datos,$this->_Settings);

        //Se construye el pie
        $this->_Script_ = $this->BuildPie();

        //SE MANDA TODA LA INFORMACIÓN AL CONSTRUCTOR PADRE
        parent::__construct(
            $Key,
            $Classes,
            $Styles,
            $Attributes,
            $Properties,
            $Titulo,
            $this->_Script_
        );
    }

    private function BuildPie(){
        //Converción de los datos del PIE a formato JSON
        $datos = str_replace('"',"'", json_encode($this->_Data));       

        //Generación del script
        $Script = 
        "
       
            setTimeout(function(){   
                ". $this->ChartHeader(AmChartTypeEnum::PIE_CHART,$this->_Key) ."
                ". $this->PieHeader() ." 
                ". $this->DonutMode() ."
                ". $this->ColorInObject($this->_Settings,$this->_Key) ."
                ". $this->SetLegend($this->_Settings,$this->_Key) ."

                chart_$this->_Key.data = $datos;
                
                ". $this->ChartFooter() ."
            }, 1000);

       
        ";

        //Se genera el ScriptDirect
        $ScriptReturn =  (new ScriptDirect(
            Scripts: [ (string)$Script]
        ));
        return $ScriptReturn;

    }

    private function PieHeader(){
        
        $labelname = $this->_Settings->LabelName;
        if(empty($labelname) OR is_null($labelname)):
            $labelname = "Label";
        endif;

        $Valuename = $this->_Settings->ValueName;

        return 
        "
        var pieSeries_$this->_Key = chart_$this->_Key.series.push(new am4charts.PieSeries());
        pieSeries_$this->_Key.dataFields.value      = '$Valuename';
        pieSeries_$this->_Key.dataFields.category   = '$labelname';

        pieSeries_$this->_Key.slices.template.stroke = am4core.color('#fff');
        pieSeries_$this->_Key.slices.template.strokeWidth = 2;
        pieSeries_$this->_Key.slices.template.strokeOpacity = 1;

        pieSeries_$this->_Key.labels.template.adapter.add('radius', function(radius, target) {
            if (target.dataItem && (target.dataItem.values.value.percent < 10)) {
              return 0;
            }
            return radius;
          });
          
          pieSeries_$this->_Key.labels.template.adapter.add('fill', function(color, target) {
            if (target.dataItem && (target.dataItem.values.value.percent < 10)) {
              return am4core.color('#000');
            }
            return color;
          }); 
        ";
    }

    private function DonutMode(){

        $valuename = $this->_Settings->ValueName;
        if($this->_Settings->DonutMode == true):
            $SumAll = 0;
            foreach($this->_Data as $item){
                $SumAll = $SumAll + $item[$valuename];
            }
            return " 

            chart_$this->_Key.innerRadius       = 50; 

            var label_$this->_Key               = chart_$this->_Key.seriesContainer.createChild(am4core.Label); 
     
            label_$this->_Key.horizontalCenter  = 'middle';
            label_$this->_Key.verticalCenter    = 'middle';
            label_$this->_Key.fontSize          = 45;
            label_$this->_Key.text              = $SumAll;

            

            ";

        
        else:
            //SI NO SE ESTA SOLICITANDO EL MODO DONUT SE CONFIGURA EL SETTINGS
            return " 
            pieSeries_$this->_Key.ticks.template.disabled = true;
            pieSeries_$this->_Key.alignLabels = false;
            pieSeries_$this->_Key.labels.template.text = '{value}';
            pieSeries_$this->_Key.labels.template.radius = am4core.percent(-40);
            pieSeries_$this->_Key.labels.template.fill = am4core.color('white');
            pieSeries_$this->_Key.legendSettings.valueText = '{value}';
            ";

        endif;

    }

     

}