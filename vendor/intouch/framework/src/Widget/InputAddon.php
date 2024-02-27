<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\InputAddon\AnimationTypeEnum;

#[Widget(Template: 'InputAddon')]
class InputAddon extends GenericWidget {

    public function __construct(
        public array $Classes = [],
        public array $Styles = [],
        public ?FaIcon $Icon = null,
        public array $Attributes = [],
        public array $Properties = []
    )
    {
        // Classes
        $this->AddClasses($Classes);
        $this->AddClass('input-group-addon');

        // Styles
        $this->AddStyles($Styles);

        // Attributes
        $this->AddAttributes($Attributes);

        // Properties
        $this->AddProperties($Properties);

        $faicon = '';
        if (isset($Icon)) {
            $faicon = $Icon->Draw(false);
        }

        parent::__construct(Replace: [
            'ICON'          => $faicon,
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}