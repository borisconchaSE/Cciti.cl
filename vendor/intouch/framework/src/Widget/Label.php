<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\Label\LabelSizeEnum;
use Intouch\Framework\Widget\Definitions\Label\LabelStyleEnum;

#[Widget(Template: 'Label')]
class Label extends GenericWidget {

    public function __construct(
        public string $Content,
        public string $Key = '',
        public string $LabelStyle = LabelStyleEnum::LABEL_DEFAULT,
        public string $LabelSize = LabelSizeEnum::LABEL_MEDIUM,
        public array  $Classes = [],
        public array  $Styles = [],
        public array  $Attributes = [],
        public array  $Properties = []
    )
    {
        // Classes
        $this->AddClass('badge');
        $this->AddClass($LabelStyle);
        $this->AddClass($LabelSize);
        $this->AddClasses($Classes);

        // Styles
        $this->AddStyles($Styles);
        
        // Attributes
        $this->AddAttributes($Attributes);
        $this->AddAttribute('id', $this->Key);

        // Properties
        $this->AddProperties($Properties);

        parent::__construct(Replace: [
            'CONTENT'   => $Content,
            'CLASSES'   => $this->DrawClasses(),
            'STYLES'    => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}