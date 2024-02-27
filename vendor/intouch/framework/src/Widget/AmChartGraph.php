<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\AmChart\AmChartControls;
use Intouch\Framework\Widget\Definitions\AmChart\AmChartLegend;
use Intouch\Framework\Widget\Definitions\AmChart\AmChartSeries;
use Intouch\Framework\Widget\Definitions\AmChart\AmChartTypeEnum;
use Intouch\Framework\Widget\Definitions\Label\LabelStyleEnum;
use Intouch\Framework\Widget\GenericWidget;
use Intouch\Framework\Widget\Label;
use Intouch\Framework\Widget\ScriptDirect;
use Intouch\Framework\Widget\Text;

#[Widget(Template: 'ChartContainer')]
class AmChartGraph extends GenericWidget{
 
    private $_Key;
    private $_Type;
    private $_Series;
    private $_Data;
    private $_Label;
    private $_Header;
    private $_FOOTER;
    private $_LEGEND;
    private $_Title;

    private $_Controls;

    private $_SCRIPT_;

    public function __construct(
        public        $Data,
        public string $Type,
        public  array $Series,
        public string $Key = '',
        public string $Title = '',
        public        $Label = '',
        public array  $Classes = [],
        public array  $Styles = [],
        public array  $Attributes = [],
        public array  $Properties = [],
        public ?AmChartLegend $Legend = null,
        public bool $IsHiddeable = false,
        public $NoDataMessage = null,
        public ?AmChartControls $Controls = null,
         
    )
    {
        //CONFIGURACIÓN DE LAS VARIABLES PRIVADAS
        $this->_Key     = $Key;
        $this->_Type    = $Type;
        $this->_Series  = $Series;
        $this->_Data    = $Data;
        $this->_Label   = $Label;
        $this->_LEGEND  = $Legend;
        $this->_Title   = $Title;
        $this->_NoDataMessage = $NoDataMessage;
        $this->_IsHiddeable = $IsHiddeable;
        $this->_Controls = $Controls;
     
      
        $this->AddClasses($Classes);
        if($this->CheckDataIncome() OR $this->_IsHiddeable != true):
          $this->AddStyles($Styles);
        endif;
        $this->AddAttributes($Attributes);
        $this->AddAttribute('id', $Key);
        $this->AddProperties($Properties);

        //INIT DEL GRAPH
        $this->Init_graph();
    }

        //Función que inicializa el Graph
    private function Init_graph(){
        
      if($this->CheckDataIncome()  OR $this->_IsHiddeable != true):
     
        //SE CONFIGURAN LAS SERIES
        $this->_Series  =   $this->BuildSeries();

        //SE FORMATEA LA DATA
        $this->_Data    =   $this->BuildData(); 

        //SE CONSTRUYE EL HEADER
        $this->_Header  = $this->BuildHeader();

        //SE CONSTRUYE EL LABEL
        $this->_Label   = $this->BuildLabel(); 

        //SE CONSTRUYE EL FOOTER
        $this->_FOOTER = $this->BuildFooter();      

        //SE CONSTURYE EL LABEL
        $legend = '';
        if($this->_LEGEND instanceof AmChartLegend):
            $this->_LEGEND->Build_Legend($this->_Key);

            $legend = $this->_LEGEND->_SCRIPT;

        endif;
        
        $TimeOutStart = 
        "
        setTimeout(function(){ 
        ";
        $TimeOutEnd = 
        "
        }, 500);
        ";
        
        $this->_SCRIPT_ =  (new ScriptDirect(
            Scripts: [      
                $TimeOutStart ,  
         
                $this->_Header,
                $this->_Data ,
                //$this->_Label,
                $this->_Series,
                $legend,
                $this->_FOOTER,   
                $TimeOutEnd,
                
            
            ]
        ));         
      else:
      
        if(is_string($this->_NoDataMessage) AND !empty($this->_NoDataMessage)):
          $Message = $this->_NoDataMessage;
          $this->_SCRIPT_ = new Label(
            LabelStyle: LabelStyleEnum::LABEL_INFO,
            Content: $this->_NoDataMessage
          );
        else:
          $this->_SCRIPT_ = new Text('');
        endif;
        
      endif;
      parent::__construct(Replace: [
        'ATTRIBUTES'    => $this->DrawAttributes(),
        'PROPERTIES'    => $this->DrawProperties(),
        'CLASSES'       => $this->DrawClasses(),
        'STYLES'        => $this->DrawStyles(),
        'SCRIPT'        => $this->_SCRIPT_->Draw(false),
    ]);

        
        
      

    }

    // ############################
    // FUNCIONES PROPIAS DEL OBJETO
    // ############################

    private function BuildSeries(){
        $TemporalSeries = "";
   

        $SeriesType = $this->_Type;         

        if(!empty($this->_Series)):
            switch($this->Type){
                case AmChartTypeEnum::DONUTH_CHART:
                case AmChartTypeEnum::PIE_CHART:
                    $TemporalSeries = $this->PIE_TEMPORAL_SERIES($SeriesType);
                    break;
                case AmChartTypeEnum::PIE_DRILLDOWN:
                    $TemporalSeries = $this->PIE_STACKED_TEMPORAL_SERIES($SeriesType);
                break;
                case AmChartTypeEnum::BAR_CHART:
                    $TemporalSeries = $this->BAR_TEMPORAL_SERIES($SeriesType);
                    break;
                case AmChartTypeEnum::BAR_CHART_HORIZONTAL:
                    $TemporalSeries = $this->BAR_TEMPORAL_SERIES($SeriesType,true);
                    break;  
                case AmChartTypeEnum::LINE_CHART:
                  $TemporalSeries  = $this->LINE_TEMPORAL_SERIES($SeriesType);
                  break;  
            }
            
        endif;
        return $TemporalSeries;

    }

    private function BuildData(){

        $TemporalData = '';
        if(empty(!$this->Data->_DATA)):
            
     
          
            if($this->_Type == AmChartTypeEnum::LINE_CHART){
              $TemporalData =  "chart_$this->_Key.data =[";
              foreach($this->Data->_DATA as $item){

                $TemporalData .= "{";
                
                $total = count($item);
                $counter = 1;
                foreach($item as $key => $value){

                
                  if($key != "date"){
                    if($counter >= $total){
                      if(is_int($value)){
                        $TemporalData  .=  ' "'.$key.'" : '.$value.' ';
                      } else{
                        $TemporalData  .=  ' "'.$key.'" : "'.$value.'" ';
                      } 
                     
                    }else{
                      $TemporalData  .=  ' "'.$key.'" : "'.$value.'", ';
                    } 
                  }else{
                    if($counter >= $total){
                      $TemporalData  .=  ' "'.$key.'" : '.$value.' ';
                    }else{
                      $TemporalData  .=  ' "'.$key.'" : '.$value.', ';
                    } 
                  }

                  $counter++;
                }
                $TemporalData .= "},"; 
                
              }
              $TemporalData .=  "]";

            }else{
              $TemporalData = 
              "
              chart_$this->_Key.data = ". json_encode($this->Data->_DATA);
            }





            /*
            $TemporalData =
            '
            chart_'.$this->_Key.'.data = [{
                "country": "USA",
                "visits": 2025
              }, {
                "country": "China",
                "visits": 1882
              }, {
                "country": "Japan",
                "visits": 1809
              }, {
                "country": "Germany",
                "visits": 1322
              }, {
                "country": "UK",
                "visits": 1122
              }, {
                "country": "France",
                "visits": 1114
              }, {
                "country": "India",
                "visits": 984
              }, {
                "country": "Spain",
                "visits": 711
              }, {
                "country": "Netherlands",
                "visits": 665
              }, {
                "country": "Russia",
                "visits": 580
              }, {
                "country": "South Korea",
                "visits": 443
              }, {
                "country": "Canada",
                "visits": 441
              }, {
                "country": "Brazil",
                "visits": 395
              }];
            ';
            */
           
        endif;

        return $TemporalData;

    }
  
    private function BuildHeader(){


        $_HEADER = 
        "
        // Themes begin
        am4core.useTheme(am4themes_animated);

        var chart_$this->_Key = am4core.create('$this->_Key',  $this->_Type);

        chart_$this->_Key.padding(20, 20, 20, 20);
        chart_$this->_Key.radius = am4core.percent(98);

        //chart_$this->_Key.cursor = new am4charts.XYCursor();

        ";


        if(!empty($this->_Title) AND is_string($this->_Title)):

          $_HEADER .= " var title_$this->_Key = chart_$this->_Key.titles.create();
          title_$this->_Key.text = '$this->_Title';
          title_$this->_Key.fontSize = 20; 
          title_$this->_Key.marginBottom = 30;
          title_$this->_Key.align = 'center';
          ";
        endif;

        return $_HEADER;
    }

    private function BuildFooter(){

      $script = "";

      //SE AGREGAN LOS CONTROLADORES ADICIONALES
      
      if($this->_Controls instanceof AmChartControls):
           $script .= $this->_Controls->BuildAmChartControls("chart_$this->_Key",$this->_Type);
      endif;

      $script .= "  $(`g[shape-rendering='auto']`).remove();";

      return $script;
         
    }

    private function BuildLabel(){

        return 
        "
            

        ";

    }


    private function PIE_STACKED_TEMPORAL_SERIES($SeriesType){
        $enum = 1;
        $SeriesType = $this->_Type;
        $TemporalSeries = "";
        foreach($this->_Series as $item){

          $varname = $enum.$this->_Key;
            if($item instanceof AmChartSeries):
            
                $TemporalSeries .= "";
                if(
                    empty($this->_Data->_DATAGROUP) AND 
                    empty($this->_Data->_GROUPVALUE)
                    ):              
                    $TemporalSeries .= 
                    "
                    //GENERACION SERIES N° $enum

                    let ChartSeries_$varname = chart_$this->_Key.series.push(new $SeriesType);
                    ChartSeries_$varname.dataFields.value = '$item->DataValue';
                    ChartSeries_$varname.dataFields.category  = '$item->DataLabel';

                    ChartSeries_$varname.slices.template.stroke = am4core.color('$item->Color');
                    ChartSeries_$varname.slices.template.strokeOpacity = $item->Opacity;

                    ChartSeries_$varname.slices.template.propertyFields.fill = 'Color';

                    ";

                    if(!empty($item->HiddenState)):

                        $HiddenObject = (object) $item->HiddenState;

                        $TemporalSeries .= 
                        "
                        //SETTINGS DE ANIMACIÓN DE LA SERIE
                        ChartSeries_$varname.hiddenState.properties.opacity = 1;
                        ChartSeries_$varname.hiddenState.properties.endAngle = -90;
                        ChartSeries_$varname.hiddenState.properties.startAngle = -90;

                        ";

                    endif;
                else:
                    $TemporalSeries = 
                    "
                    chart_$this->_Key.colors.step = 2;
                    chart_$this->_Key.fontSize = 11;
                    chart_$this->_Key.innerRadius = am4core.percent(20);

                    chart_$this->_Key.dataFields.value      = '$item->DataValue';
                    chart_$this->_Key.dataFields.name       = '$item->DataLabel';
                    chart_$this->_Key.dataFields.children   = 'children';

                    chart_$this->_Key.dataFields.color = 'Color';


                    //DEFINICION DEL ZOOM BUTTON
                    var zoomOutButton = chart_$this->_Key.seriesContainer.createChild(am4core.ZoomOutButton);

                    zoomOutButton.visible           = false;
                    zoomOutButton.horizontalCenter  = 'middle';
                    zoomOutButton.verticalCenter    = 'middle';
                    zoomOutButton.events.on('hit', function() {
                        drillUp(currentlySelected)
                    })

                    function drillUp(slice) {
                        collapse(slice);
                        var series = slice.dataItem.component;
                        series.tooltip.disabled = false;
                        series.dummyData = false;
                        series.setState('default');
                      
                        series.slices.each(function(slice) {
                          if (slice != event.target) {
                            slice.dataItem.show();
                          }
                        })
                      
                        if (series.parentDataItem.seriesDataItem) {
                          currentlySelected = series.parentDataItem.seriesDataItem.slice;
                        }
                        else {
                          zoomOutButton.hide();
                        }
                    }


                    function collapse(slice) {

                        slice.dataItem.label.bent = false;
                        slice.dataItem.label.radius = 10;
                      
                      
                        if (slice.dataItem.sunburstDataItem.children) {
                          slice.dataItem.sunburstDataItem.children.each(function(child) {
                            child.seriesDataItem.component.setState('hidden');
                            collapse(child.seriesDataItem.slice);
                          })
                        }
                    }   


                    var ChartSeries_$varname = new am4plugins_sunburst.SunburstSeries();

                    chart_$this->_Key.seriesTemplates.setKey('0', ChartSeries_$varname);

                    ChartSeries_$varname.labels.template.truncate      = true;
                    ChartSeries_$varname.labels.template.hideOversized = true;
                    ChartSeries_$varname.showOnInit                    = false;
                    ChartSeries_$varname.usePercentHack                = false;
                    ChartSeries_$varname.radius                        = am4core.percent(100);
                    ChartSeries_$varname.innerRadius                   = am4core.percent(0);

                    ChartSeries_$varname.slices.template.propertyFields.fill = 'Color';

                    let selectedState = ChartSeries_$varname.states.create('selected');
                    selectedState.properties.opacity = 0.7;

                    ChartSeries_$varname.defaultState.properties.radius = am4core.percent(100);

                    var currentlySelected;

                    ChartSeries_$varname.slices.template.events.on('over', function(event) {
                        if(event.target.dataItem.sunburstDataItem.children){
                         event.target.cursorOverStyle = am4core.MouseCursorStyle.pointer;
                        }
                    });

                    ChartSeries_$varname.slices.template.events.on('hit', function(event) {

                        zoomOutButton.show();
                        var hitSlice = event.target;
                      
                        if (hitSlice.dataItem.sunburstDataItem.children) {
                      
                          var series = event.target.dataItem.component;
                          
                          if (!series.dummyData) {
                            series.tooltip.disabled = true;
                            hitSlice.dataItem.label.radius = (hitSlice.radius - hitSlice.pixelInnerRadius) - 7;
                            hitSlice.dataItem.label.bent = true;
                            hitSlice.dataItem.label.rotation = -180;
                      
                            currentlySelected = hitSlice;
                            series.dummyData = true;
                            series.setState('selected');
                            hitSlice.dataItem.sunburstDataItem.series.show();
                            series.slices.each(function(slice) {
                              if (slice != event.target) {
                                slice.dataItem.hide();
                              }
                            })
                          }
                          else {
                            drillUp(hitSlice);
                          }
                        }
                    })
                      
                    ChartSeries_$varname.labels.template.adapter.add('rotation', function(rotation, target) {
                        target.maxWidth = target.dataItem.slice.radius - target.dataItem.slice.innerRadius - 10;
                        target.maxHeight = Math.abs(target.dataItem.slice.arc * (target.dataItem.slice.innerRadius + target.dataItem.slice.radius) / 2 * am4core.math.RADIANS);
                        return rotation;
                    })

                    var level1SeriesTemplate_$varname = ChartSeries_$varname.clone();
                    level1SeriesTemplate_$varname.hidden = true;
                    level1SeriesTemplate_$varname.innerRadius = am4core.percent(10);
                    level1SeriesTemplate_$varname.hiddenInLegend = true;

                    chart_$this->_Key.seriesTemplates.setKey('1', level1SeriesTemplate_$varname)
                    
                    level1SeriesTemplate_$varname.fillOpacity = 0.75;

                    var level2SeriesTemplate_$varname = level0SeriesTemplate.clone();                        
                    level2SeriesTemplate_$varname.hidden = true;
                    level2SeriesTemplate_$varname.innerRadius = am4core.percent(20);
                    level2SeriesTemplate_$varname.hiddenInLegend = true;

                    chart_$this->_Key.seriesTemplates.setKey('2', level2SeriesTemplate_$varname);

                    
                                        
                    
                    ";
                endif;

            endif;

            $enum++;
        }
     

        return $TemporalSeries;
    }

    private function BAR_TEMPORAL_SERIES($SeriesType, $inverted = false){
        $enum = 1;
        $SeriesType = $this->_Type;
        $TemporalSeries = "";
        $KeyCount = 0;

        foreach($this->_Series as $item){
            if($item instanceof AmChartSeries):
                $varname = $enum.$this->_Key;
                $enum++;
                
                $LabelName = $item->DataLabel;
                $ValueName = $item->DataValue;
                $GrouName  = $item->DataLabeGroupName ?: $LabelName;
                $GrouKey   = $item->DataLabeKeyName ?: $LabelName;

                $_value     = "Y";
                $_Category  = "X";
                $_valueAxes = "y";
                $_CategoryAxes = "x";
                if($inverted == true):
                    $_value     = "X";
                    $_Category  = "Y";
                    $_valueAxes = "x";
                $_CategoryAxes = "y";
                endif;

                //DEFINICION DE LOS COLORES
                $color_definition = "'Color'";
                if(!empty($item->DataColor) AND $item->DataColor != 'Color'){
                    $color_definition = "am4core.color('$item->DataColor')";
                }elseif(!empty($item->DataColor)){
                  $color_definition = "'Color'";
                }else{
                  $color_definition = "am4core.color('blue')";
                }

                ///////////////////////////////////////////////////
                // DEFINICION DEL AXIS (SOLO DEBE DEFINIRCE UNA VEZ EN TODO LE CHART CONFIG)

                //SE DEFINIE EL GRUPO
                $GroupedAxis = "categoryAxis_$varname.dataFields.category = '$LabelName';";
                
                $stacked = "";
                if($this->_Data->SimpleStackedData == true):
                    $GroupedAxis = "categoryAxis_$varname.dataFields.category = '$GrouKey';";
                    if($KeyCount > 0):
                        $stacked = "series_$varname.stacked = true;";   
                    endif;
                                     
                endif;

                if($KeyCount < 1):
                    $TemporalSeries .= 
                    "        
                    var categoryAxis_$varname = chart_$this->_Key.".$_CategoryAxes."Axes.push(new am4charts.CategoryAxis());
                    $GroupedAxis
                    categoryAxis_$varname.renderer.grid.template.location = 0;
                    categoryAxis_$varname.renderer.minGridDistance = 30; 

                    categoryAxis_$varname.renderer.startLocation = 0;
                    categoryAxis_$varname.renderer.cellStartLocation = 0.2;
                    categoryAxis_$varname.renderer.cellEndLocation = 0.8;

          
                
                   
             
                    "; 

                    $TemporalSeries .= 
                    "
                    
                    let label_$varname = categoryAxis_$varname.renderer.labels.template;
                    label_$varname.wrap = true;
                    label_$varname.maxWidth = 150;
                    label_$varname.tooltipText = '{category}';";


                    if($this->_Type == AmChartTypeEnum::BAR_CHART):
                      $TemporalSeries .= 
                      "
                      categoryAxis_$varname.events.on('sizechanged', function(ev) {
                        let axis = ev.target;
                          var cellWidth = axis.pixelWidth / (axis.endIndex - axis.startIndex);
                          if (cellWidth < axis.renderer.labels.template.maxWidth) {
                            axis.renderer.labels.template.rotation = -45;
                            axis.renderer.labels.template.horizontalCenter = 'right';
                            axis.renderer.labels.template.verticalCenter = 'middle';
                          }
                          else {
                            axis.renderer.labels.template.rotation = 0;
                            axis.renderer.labels.template.horizontalCenter = 'middle';
                            axis.renderer.labels.template.verticalCenter = 'top';
                          }
                        });
                      ";  
                    endif;
                endif;
                $KeyCount++;
                
                 
                $TemporalSeries .= "

                var valueAxis = chart_$this->_Key.".$_valueAxes."Axes.push(new am4charts.ValueAxis());
              
                // SE CREEA LA SERIE
                var series_$varname = chart_$this->_Key.series.push(new am4charts.ColumnSeries());
                series_$varname.dataFields.value$_value = '$ValueName';
                series_$varname.dataFields.category$_Category = '$LabelName';                
                series_$varname.name = '$GrouName';
                series_$varname.columns.template.tooltipText = '{name}: [bold]{value$_value}[/]';
                series_$varname.columns.template.fillOpacity = .8;
                series_$varname.columns.template.propertyFields.fill = $color_definition;

                series_$varname.columns.template.fill =  $color_definition;

                let valueLabel_$varname = series_$varname.bullets.push(new am4charts.LabelBullet());
                valueLabel_$varname.label.text = '{value$_value}';
                valueLabel_$varname.label.fontSize = 12;               
                valueLabel_$varname.label.truncate = true;
                valueLabel_$varname.label.hideOversized = true;

                $stacked

          
               

                 
           
                ";
 
                


               

            endif;
        }
        return $TemporalSeries;
    }

    private function PIE_TEMPORAL_SERIES($SeriesType){
        $enum = 1;
        $SeriesType = $this->_Type;
        $TemporalSeries = "";      
        foreach($this->_Series as $item){

          if($enum > 1):
              break;
          endif;
          $enum++;

          $total_data = 0;
            if($item instanceof AmChartSeries):
                $varname = $enum.$this->_Key;
                
                $LabelName      = $item->DataLabel;
                $ValueName      = $item->DataValue;
 
              foreach($this->_Data->Datos as $item){
                $subtotal = $item->{$ValueName};

                if(is_numeric($subtotal)):
                  $total_data += $subtotal;
                endif;
              }
             
                if($this->Type == AmChartTypeEnum::DONUTH_CHART):

                  $TemporalSeries .= 
                  "
                  chart_$this->_Key.innerRadius = am4core.percent(60);
                  ";

                endif;

                $TemporalSeries .= 
                "        
                // CONFIGURACION DE LA SERIE
                var ChartSeries_$varname = chart_$this->_Key.series.push(new am4charts.PieSeries());

                

                ChartSeries_$varname.dataFields.value = '$ValueName';
                ChartSeries_$varname.dataFields.category = '$LabelName';
                ChartSeries_$varname.slices.template.stroke = am4core.color('#fff');
                ChartSeries_$varname.slices.template.strokeOpacity = 1;

                ChartSeries_$varname.slices.template.propertyFields.fill = 'Color';

                ChartSeries_$varname.ticks.template.disabled = true;
                ChartSeries_$varname.alignLabels = false;
                ChartSeries_$varname.labels.template.text = '{value}';
                ChartSeries_$varname.labels.template.radius = am4core.percent(-20);
                ChartSeries_$varname.labels.template.fill = am4core.color('white');

                ChartSeries_$varname.labels.template.adapter.add('radius', function(radius, target) {
                  if (target.dataItem && (target.dataItem.values.value.percent < 10)) {
                    return 0;
                  }
                  return radius;
                });
                
                ChartSeries_$varname.labels.template.adapter.add('fill', function(color, target) {
                  if (target.dataItem && (target.dataItem.values.value.percent < 10)) {
                    return am4core.color('#000');
                  }
                  return color;
                });

                
             
                ";
                if($this->Type == AmChartTypeEnum::DONUTH_CHART):
                  $TemporalSeries .= 
                  "
                  let label_$varname                = ChartSeries_$varname.createChild(am4core.Label);
                  label_$varname .text              = '". number_format($total_data,0,',','.') ."';
                  label_$varname .horizontalCenter  = 'middle';
                  label_$varname .verticalCenter    = 'middle';
                  label_$varname .fontSize          = 40;
                  ";
                endif;

                if(!empty($item->HiddenState)):

                    $HiddenObject = (object) $item->HiddenState;

                    $TemporalSeries .= 
                    "
                    //SETTINGS DE ANIMACIÓN DE LA SERIE
                    ChartSeries_$varname.hiddenState.properties.opacity = 1;
                    ChartSeries_$varname.hiddenState.properties.endAngle = -90;
                    ChartSeries_$varname.hiddenState.properties.startAngle = -90;

                    ";

                endif;

            endif;
        }
        return $TemporalSeries;
    }


    private function LINE_TEMPORAL_SERIES($SeriesType,$inverted = false){
      $enum = 1;
      $SeriesType = $this->_Type;
      $TemporalSeries = "";
      $KeyCount = 0; 
      foreach($this->_Series as $item){
          if($item instanceof AmChartSeries):
              $varname = $enum.trim($this->_Key);
              $enum++;
              
              $LabelName = $item->DataLabel;
              $ValueName = $item->DataValue;
              $GrouName  = $item->DataLabeGroupName ?: $LabelName;
              $GrouKey  = $item->DataLabeKeyName ?: $LabelName; 

              // -------------------------------------------------------------------------------------------------------
              // SI EL KEY COUNT ES MENOR A 1 SE INGRESAN TODAS LAS FUNCIONES DEL GRAFICO (QUE TENGA QUE VER CON SERIES)
              // -------------------------------------------------------------------------------------------------------
              if($KeyCount < 1){
                
                $TemporalSeries .= 
                ' 

                var dateAxis = chart_'.$this->_Key.'.xAxes.push(new am4charts.DateAxis());             
                dateAxis.baseInterval = { timeUnit: "hour", count: 1 };
                dateAxis.dateFormats.setKey("hour", "dd/MM/yyyy HH:mm:ss");
              ; 

                var valueAxis = chart_'.$this->_Key.'.yAxes.push(new am4charts.ValueAxis());




                function createSeries(field, name) {
                  var series = chart_'.$this->_Key.'.series.push(new am4charts.LineSeries());
                  series.dataFields.valueY = field;
                  series.dataFields.dateX = "date";
                  series.name = name;
                  series.tooltipText = "Accesos {valueY}: [b]{name}[/]";
                  series.strokeWidth = 1;
                  series.tensionX = 0.99; 
                  series.tooltip.label.adapter.add("text", function(text, target) {
                    if (target.dataItem && target.dataItem.valueY == 0) {
                      return "";
                    }
                    else {
                      return text;
                    }
                  });
                  
                  
                  var bullet = series.bullets.push(new am4charts.CircleBullet());
                
                  bullet.circle.strokeWidth = 1;
                  
                  return series;
                } 
                ';

              } 


              // -------------------------------------------------
              // SE GENERA LA SERIA DEL GRAFICO DE LINEA
              // -------------------------------------------------
              $TemporalSeries .= " var series_$varname = createSeries('$ValueName', '$GrouName'); ";


              // --------------------------------------------- 
 

             
              $KeyCount++; 
             

          endif;
      }
      return $TemporalSeries;
  }




    private function CheckDataIncome(){
      $status = false;

      $ValueName      = $this->_Series[0]->DataValue;

      if (!isset($this->_Data) || !isset($this->_Data->Datos))
        return false;

      foreach($this->_Data->Datos as $item){

        $Value = $item->{$ValueName};

        if(is_numeric($Value)):

          if($Value > 0):

            $status = true;

          endif;  

        endif;

        if(is_string($Value)):

          if(!empty($Value)):

            $status = true;

          endif;

        endif;
      }

      return $status;
    }

}