<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\Label\LabelSizeEnum;
use Intouch\Framework\Widget\Definitions\Label\LabelStyleEnum;
use Intouch\Framework\Widget\GenericWidget;

#[Widget(Template: 'Progress')]
class Progress extends GenericWidget {

    public function __construct(
        public string $Content,
        public string $Key = '',
        public string $LabelStyle = LabelStyleEnum::LABEL_DEFAULT,
        public string $LabelSize = LabelSizeEnum::LABEL_MEDIUM,
        public string $ProgressStyle = LabelStyleEnum::LABEL_DEFAULT,
        public array  $Classes = [],
        public array  $Styles = [],
        public array  $Attributes = [],
        public array  $Properties = [],
        public int    $Percentage = 0,
    )
    {
        // Classes
        $this->AddClasses($Classes);

        // Styles
        $this->AddStyles($Styles);
        
        // Attributes
        $this->AddAttributes($Attributes);
        $this->AddAttribute('id', $this->Key);

        // Properties
        $this->AddProperties($Properties);

        parent::__construct(Replace: [
            'CONTENT'          => $Content,
            'CLASSES'          => $this->DrawClasses(),
            'STYLES'           => $this->DrawStyles(),
            'ATTRIBUTES'       => $this->DrawAttributes(),
            'PROPERTIES'       => $this->DrawProperties(),
            'PERCENTAGE'       => $this->Percentage,
            'LABELSTYLE'       => $this->LabelStyle . ' ' . $this->LabelSize,
            'PROGRESSSTYLE'    => $this->ProgressStyle,
        ]);
    }
}