<?php
namespace Intouch\Framework\BLL\Filters;

use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;

class CustomExcelSettingsFilterDto {
    /**
     * 
     * @param $GroupTableKeyIndex SI SE INGRES AUN VALOR INT - EL SCRIPT CREARÁ DISTINTAS TABLAS DEPENDIENDO DE LA CANTIDAD DE DATOS DISTINTOS QUE SE MUESTREN EN DICHA TABLA
     */
    public function __construct(
        public bool                 $ShowButton     =       false,
        public string               $Controller     =       "",
        public string               $FileName       =       "",
        public ?string              $Estado         =       "",
        public string               $TableKey       =       ""

    )
    {
        
    }
}
