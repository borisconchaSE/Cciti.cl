<?php
namespace Intouch\Framework\Widget\Definitions\AmChart;

class AmChartPieData{

    public  $_DATA; //ESTE ARREGLO ENTREGA AL OBJETO  LA DATA YA PROCESADA 
    private $_NodeChild;
    private $_GroupBy;
    private $_GroupValue ;
    private $_PRIMARYLABEL;
    private $_PRIMARYVALUE;


    public function __construct(
        public $Data,
        public string   $PrimaryDataName,
        public string   $PrimaryDataValue,
        public bool $ChildNode  = false,
        public array $AddData   = [],
        public string $GroupBy = '',
        public string   $GroupValue = '',
    )
    {

        $this->_DATA        = $Data;
        $this->_NodeChild   = $ChildNode;
        $this->_AddData     = $AddData;
        $this->GroupBy      = $GroupBy;
        $this->GroupValue   = $GroupValue;

        $this->_PRIMARYLABEL = $PrimaryDataName;
        $this->_PRIMARYVALUE = $PrimaryDataValue;

        if(!empty($GroupBy) AND !empty($GroupValue)):
            $this->Stacked_Data();
        else:
            $this->Normal_Data();
        endif;

        
    }



    private function Stacked_Data(){
        
    }

    private function Normal_Data(){
        $temporal_data = [];

        if(!empty($this->_DATA)):

            $PrimaryLabel = $this->_PRIMARYLABEL;
            $PrimaryValue = $this->_PRIMARYVALUE;        
           
        
            foreach($this->_DATA as $item){

                $temporal_array = [
                    $PrimaryLabel => $item->{$PrimaryLabel},
                    $PrimaryValue => $item->{$PrimaryValue},
                     
                ];
                $subtemporal = [];
                //SI SE NECESITAN DATOS ADICIONALES SE AGREGAN
                if(!empty($this->_AddData)):
                    foreach((object) $this->_AddData as $item_2){
                        $name   = $item_2->LabelName ;
                        $value  = $item_2->ValueName ;
                        $subtemporal = [
                            $name=> $item->{$value}
                        ];
                     
                        $temporal_array = array_merge($temporal_array,$subtemporal);
                    
                    }
                endif;

                $temporal_data[] = $temporal_array;
            }

        endif;

        $this->_DATA = $temporal_data;
        return $temporal_data;

    }

}