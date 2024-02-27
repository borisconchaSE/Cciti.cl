<?php
namespace Intouch\Framework\BLL\Filters;

class DataTableSettingsFilterDto {
    /**
     * 
     * @param $GroupTableKeyIndex SI SE INGRES AUN VALOR INT - EL SCRIPT CREARÁ DISTINTAS TABLAS DEPENDIENDO DE LA CANTIDAD DE DATOS DISTINTOS QUE SE MUESTREN EN DICHA TABLA
     */
    public function __construct(
        public bool                             $HideAllButtons             = false,
        public bool                             $HideDefaultButtons         = false,
        public bool                             $CustomPdf                  = false,
        public String                           $TituloPdf                  = "",
        public String                           $Modulo                     = "",
        public bool                             $OcultarSubtituloModulo     = false,
        public bool                             $TableHasButtons            = true,
        public bool                             $GroupedButtons             = true,
        public ?int                             $GroupTableKeyIndex         = null,
        public ?array                           $DrawTableCallback          = null,
        public array                            $JSCustomButton             = []
    )
    {
        
    }
}
