<?php

namespace Intouch\Framework\View\DisplayDefinitions\JSTable;

use Closure;
 /** @param $JSDataFilter Arreglo que contiene de script de javascript a utilizar para filtrar la información antes de insertarla en la tabla dinamica, La function recibe dos parametros ($element y $roles) */
class JSTableCell {

    public function __construct(
        public string       $PropertyName,
        public string       $Label                      = '',
        public int          $Colspan                    = 1,
        public ?Closure     $WidgetFunction              = null,       
        public ?array       $JSDataFilter                = null,
        public array        $HeaderClasses              = [],
        public array        $HeaderStyles               = [],
        public array        $HeaderAttributes           = [],
        public array        $HeaderProperties           = [],
        public array        $BodyClasses                = [],
        public array        $BodyStyles                 = [],
        public array        $BodyAttributes             = [],
        public array        $BodyProperties             = [],
        public array        $PropertyList               = []
    )
    {        
    }

}