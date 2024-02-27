<?php
namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\GenericWidget;

#[Widget(Template: 'ChartContainer')]
class AmChartGeneric extends GenericWidget{

    protected string $_script   = "";
    protected        $Settings  = "";

    public function __construct(
        public string  $Key,
        public array   $Classes = [],
        public array   $Styles = [],
        public array   $Attributes = [],
        public array   $Properties = [],
        public string  $Titulo = '',
        public $script = null
        
    )
    {
        //DEFINICION DEL GENERIC WIDGET
        $this->AddClasses($Classes);
        $this->AddStyles($Styles);
        $this->AddAttributes($Attributes);
        $this->AddAttribute('id', $Key);
        $this->AddProperties($Properties);

        parent::__construct(Replace: [
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties(),
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'SCRIPT'        => $script->Draw(false),
        ]);
    }


    //GENERA EL FORMAT DE LA DATA
    public function format_simple_data($data,$settings){
        $object     = [];

        $LabelName  = $settings->LabelName;
        $LabelValue = $settings->ValueName;
        $n = 0;
        foreach($data as $key => $item){

            if(is_null($item->{$LabelValue})):
                continue;
            endif;

            if(is_null($LabelName) OR empty($LabelName)):

                $object[$n] = array(
                    'Label'     => $key,
                    $LabelValue => (int) $item->{$LabelValue}
                );

            else:

                $object[$n] = array(
                    $LabelName  =>       $item->{$LabelName},
                    $LabelValue => (int) $item->{$LabelValue},
                );

            endif;
            if($settings->ColorName):
                $object[$n][$settings->ColorName] = $item->{$settings->ColorName};
            endif;
            $n++;

            
        }
        return $object;
    }

    public function ChartHeader($ChartType,$key){

        return "
            am4core.useTheme(am4themes_animated);
            var chart_$key = am4core.create('$key', $ChartType);
            chart_$key.hiddenState.properties.radius = am4core.percent(0);
            
            ";        
    }

    public function ColorInObject($settings,$key){
        if(!is_null($settings->ColorName) AND !empty($settings->ColorName)):    
            return "pieSeries_$key.slices.template.propertyFields.fill = '".$settings->ColorName."';";
        else:
            return "//Colores por defecto";
        endif;
    }

    public function SetLegend($settings,$key){

        if($settings->Legend == true):
            return  " 
            chart_$key.legend = new am4charts.Legend();
            
            chart_$key.legend.scrollable = false;"
            ;
        else:
            return "//Sin Legenda";
        endif;

    }

    public function ChartFooter(){
        return "$(`g[shape-rendering='auto']`).remove(); ";
    }

     
    
}

