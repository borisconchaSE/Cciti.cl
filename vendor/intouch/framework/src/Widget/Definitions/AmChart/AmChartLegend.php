<?php
namespace Intouch\Framework\Widget\Definitions\AmChart;

class AmChartLegend {

    private $_MaxWidth;
    public  $_SCRIPT = '';
    
    /**
     * 
     * @param int $MaxHeight Height Maxima del contenedor de la legenda
     * @param int $MaxWidth  Width  Maxima del contenedor de la legenda
     * @param bool $Scroll  Define si el contenedor tiene overflow (Scroll)
     * @param bool $Icon  Mostrar o ocultar el icono (Solo deja el texto)
     * @param string $Width  Define el Width del icono
     * @param string $height  Define el alto del icono
     * @param string $Position  Define la posición de la legenda (Left,Right,Top,bottom)
     * 
     */

    public function __construct(
        public int      $MaxHeight  = 0,
        public int      $MaxWidth   = 0,
        public bool     $Scroll     = false,
        public bool     $Icon       = true,
        public string   $Width      = '',
        public string   $Height     = '',
        public string   $Align      = 'center',
        public string   $Position   = 'Bottom'
        
    )
    {
        //AUTODEFINICIÓN VARIABLES
        $this->_MaxHeight       = $MaxHeight;
        $this->_MaxWidth        = $MaxWidth;
        $this->_Scroll          = $Scroll;
        $this->_Icon            = $Icon;
        $this->_Height          = $Height;
        $this->_Width           = $Width;
        $this->_Position        = $Position;
        $this->_Align           = $Align;

        //CONSTRUCCIÓN DEL OBJETO
      
        
         
        
    }


    public function Build_Legend($ChartKey){
        //SE GENERA UNA LEGENDA POR DEFECTO
        $TemporalLabel = " chart_$ChartKey.legend = new am4charts.Legend(); ";

        //DEFINE LA POSICIÓN DE LA LEGENDA
        $TemporalLabel .= 
            "             
               
                chart_$ChartKey.legend.position         =   '". strtolower($this->_Position) ."';
                chart_$ChartKey.legend.contentAlign     =   '". strtolower($this->_Align). "';
                chart_$ChartKey.legend.valueLabels.template.align       = 'left';
                chart_$ChartKey.legend.valueLabels.template.textAlign   = 'end'; 
            
            ";

        //SE DEFINE LA ALTURA DEL CONTENEDOR DE LA LEGENDA
        if($this->MaxHeight != 0):
            $TemporalLabel .= 
            " chart_$ChartKey.legend.maxHeight = $this->MaxHeight ; ";
        endif;
        
        //SE DEFINE EL ANCHO DEL CONTENEDOR DE LA LEGENDA
        if($this->_MaxWidth != 0):
            $TemporalLabel .= 
            " chart_$ChartKey.legend.maxWidth  = $this->_MaxWidth ; ";
        endif;

        //APLICA UN PARAMETRO DE OVERFLOW AL CONTENEDOR DE LA LEGENDA
        if($this->_Scroll == true):
            $TemporalLabel .= 
            " chart_$ChartKey.legend.scrollable = true; ";
        endif;

        //SI ES FALSE, SE OCULTA EL ICONO DE LA LEGENDA
        if($this->Icon != true):
            $TemporalLabel .= 
            " chart_$ChartKey.legend.markers.template.disabled  = true ; ";
        endif;

        //DEFINE EL ALTO DEL ICONO DE LA LEGENDA
        if(!empty($this->_Height)):
            $TemporalLabel .= 
            " chart_$ChartKey.legend.markers.template.height   = '$this->_Height' ; ";
        endif;
        
        // DEFINE EL ANCHO DEL ICONO DE LA LEGENDA
        if(!empty($this->_Width)):
            $TemporalLabel .= 
            " chart_$ChartKey.legend.markers.template.width    = '$this->_Width' ; ";
        endif;

        $this->_SCRIPT = $TemporalLabel;

    }
}