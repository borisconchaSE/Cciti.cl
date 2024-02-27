<?php
namespace Intouch\Framework\Widget\Definitions\AmChart;

use Intouch\Framework\Dates\Date;


class AmChartGenericToAmData{

    public $_DATALABEL          = '';
    public $_DATAVALUE          = '';
    public $_DATACOLOR          = null;
    public $_DATAGROUP          = '';
    public $_GROUPVALUE         = '';
    public $_DATA               = [];
    private $_ESTADOCOMRPOMISO  = null;
    private $_TYPE              = null; 
 
    
    /**
     * 
     * @param string $EstadoCompromiso DEFINE SI DEBE AGRUPAR POR ESTADOS DE COMPROMISO
     * @param string $GroupValue Para Drill Down | Nombre del key del arreglo que contiene el valor a usar para agrupar
     * @param string $GroupBy    Para Drill Down | Etiqueta para el widger del grupo (no afecta al arreglo)
     * @param string $SimpleStackedData Se usa para agrupar todos los datos en un solo arreglo (IDEAL PARA GRAFICOS DE BARRAS STACKED)
     */
    public function __construct(
        public $Type    = null,
        public string   $DataLabel,
        public string   $DataValue,
        public string   $GroupBy    ='',
        public string   $GroupValue = '',
        public ?String  $DateFormat = null,
        public bool     $GroupCount = false,
        public string   $DataColor  = '',
        public bool     $EstadoCompromiso = false,
        public bool     $SimpleStackedData = false,
        public array    $AddData = [  ],     
        public $Datos
    )
    {
        if(!empty($DataColor)):
            $this->_DATACOLOR = $DataColor;
        endif;         

        $this->_DATALABEL   = $DataLabel;
        $this->_DATAVALUE   = $DataValue;
        $this->_DATAGROUP   = $GroupBy;
        $this->_GROUPVALUE  = $GroupValue;
        $this->_ESTADOCOMRPOMISO = $EstadoCompromiso;       
     
        if($Type != null) {    
            
            //SE USAN LOS OBJETOS NUEVOS PARA GENERAR LA DATA
            switch($Type){
                case AmChartTypeEnum::LINE_CHART:
                case AmChartTypeEnum::BAR_CHART:
                case AmChartTypeEnum::BAR_CHART_HORIZONTAL:
                    $this->_DATA = (new AmChartBarData(
                        Data    : $Datos,
                        AddData : $AddData,
                        PrimaryDataName: $DataLabel,
                        PrimaryDataValue: $DataValue                        
                    ))->_DATA;
                break;
                case AmChartTypeEnum::PIE_CHART:
                case AmChartTypeEnum::DONUTH_CHART:
                    $this->_DATA = (new AmChartPieData(
                        Data    : $Datos,
                        AddData : $AddData,
                        PrimaryDataName: $DataLabel,
                        PrimaryDataValue: $DataValue                        
                    ))->_DATA;
                break;
            }
        };         

        return $DataLabel;
    }


    private function FormatData($Datos){

        $TemporalObject = [];      

        $Label = $this->_DATALABEL;
        $Value = $this->_DATAVALUE;
        $Color = $this->_DATACOLOR;

        foreach($Datos as $item){              

            $TemporalItem = [
                $Label  => $item->{$Label},
                $Value  => $item->{$Value},
                "Color" => $item->{$Color}
            ];
            $TemporalObject[] = $TemporalItem;
        }

        $this->_DATA = $TemporalObject;
    }

    private function ReturnGroupedArray($Datos){

        $TemporalFilter = [];

        $Label          = $this->_DATALABEL;
        $Value          = $this->_DATAVALUE;
        $Color          = $this->_DATACOLOR;

        $GroupBy = $this->_DATAGROUP;    

        foreach($Datos as $item){

            $Filter = $item->{$GroupBy};

            if(key_exists($Filter,$TemporalFilter)):
                $TemporalFilter[$Filter][] = $item;
            else:
                $TemporalFilter[$Filter][] = $item;
            endif;
        }


        return $TemporalFilter;

    }


     
}