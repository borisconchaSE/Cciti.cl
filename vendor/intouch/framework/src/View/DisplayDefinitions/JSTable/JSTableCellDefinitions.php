<?php

namespace Intouch\Framework\View\DisplayDefinitions\JSTable;

use Closure;
 /** @param $JSDataFilter Arreglo que contiene de script de javascript a utilizar para filtrar la información antes de insertarla en la tabla dinamica, La function recibe dos parametros ($element y $roles) */
class JSTableCellDefinitions {

    public function __construct(
        public string       $HTMLTemplate,
        public string       $CustomRowFunctionCallback  = "",
        public string       $CustomHeaderFuncitonRender = ""

       
    )
    {        
    }

}