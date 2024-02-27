<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\AmChart5\RootElement;

#[Widget(Template: 'Am5Chart')]
class Am5Chart extends GenericWidget {

    public function __construct(
        public RootElement      $RootElement,
        public array            $Classes = [],
        public array            $Styles = [],
        public array            $Attributes = [],
        public array            $Properties = [],
    )
    {
        $this->AddClasses($Classes);
        $this->AddStyles($Styles);
        $this->AddAttributes($Attributes);
        $this->AddAttribute('id', $RootElement->DivContainer);
        $this->AddProperties($Properties);

        $script = new ScriptDirectSimple(
            Key: 'chart_' . str_replace('-', '_', $RootElement->DivContainer),
            Scripts: [      
                $RootElement->Draw(false),
            ]
        );

        $scriptContent = $script->Draw(false);
        $scriptContent = str_replace('[[DIVCONTAINER]]', $RootElement->DivContainer, $scriptContent);

        parent::__construct(Replace: [
                'ATTRIBUTES'    => $this->DrawAttributes(),
                'PROPERTIES'    => $this->DrawProperties(),
                'CLASSES'       => $this->DrawClasses(),
                'STYLES'        => $this->DrawStyles(),
                'SCRIPT'        => $scriptContent,
            ]
        );
    }
}