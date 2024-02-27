<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\Form\Edge;
use Intouch\Framework\Widget\Definitions\Form\Position;

#[Widget(Template: 'Form')]
class Form extends GenericWidget {

    public function __construct(        
        public array     $FormGroups,
        public string    $Key = '',
        public string    $Method = 'post',
        public string    $EncriptionType = 'multipart/form-data',
        public array     $Classes = [],
        public array     $Styles = [],
        public array     $Attributes = [],
        public array     $Properties = []
    )
    {
        // Classes
        $this->AddClasses($Classes);

        // Styles
        $this->AddStyles($Styles);

        // Attributes
        $this->AddAttributes($Attributes);
        $this->AddAttributes(
            [
                ['id', $this->Key],
                ['method', $this->Method],
                ['enctype', $this->EncriptionType]
            ]);

        // Properties
        $this->AddProperties($Properties);

        $builder = "";
        foreach ($FormGroups as $child) {
            if ($child instanceof FormGroup) {
                if ($builder != "") {
                    $builder = $builder . "\n";
                }
                $builder .= $child->Draw(false);
            }
        }

        parent::__construct(Replace: [
            'GROUPS'        => $builder,
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}