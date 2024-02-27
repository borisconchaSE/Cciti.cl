<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

/**
 * @param $Script -> Recibe dos inputs por defecto ($element, y $roles)
 */
 
#[Widget(Template: 'ScriptDirect')]
class JSTableScriptFilter extends GenericWidget {

    public function __construct(
        public string $FunctionName,
        public array $Script
    )
    { 
        $id = (new \DateTime())->format('YmdHis') . random_int(1,5000); 

        parent::__construct(Replace: [
            'EVENTS'   => $Script,
            'IDSCRIPT' => $id
        ]);
    }
}