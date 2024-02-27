<?php

namespace Application\BLL\Filters;

class CustomPdfDatatableFilterDto {

    /**
     * 
     * @param $GroupTableKeyIndex SI SE INGRES AUN VALOR INT - EL SCRIPT CREARÁ DISTINTAS TABLAS DEPENDIENDO DE LA CANTIDAD DE DATOS DISTINTOS QUE SE MUESTREN EN DICHA TABLA
     */
    public function __construct(
        public String   $TituloPdf               = "",
        public String   $Modulo                  = "",
        public bool     $OcultarSubtituloModulo  = false,
        public ?int     $GroupTableKeyIndex     = null
    )
    {
        
    }
}