<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\Container\Edge;
use Intouch\Framework\Widget\Definitions\Container\Position;

#[Widget(Template: 'FlexGroup')]
class FlexGroup extends GenericWidget {

    public function __construct(        
        public array     $Children,
        public string    $Key = '',
        public bool      $Wrap = true,
        public array     $Classes = [],
        public array     $Styles = [],
        public array     $Attributes = [],
        public array     $Properties = [],
    )
    {
        // Classes
        $this->AddClasses($Classes);

        // Styles
        $this->AddStyles($Styles);
        $this->AddStyle('display', 'flex');

        if ($Wrap) {
            $this->AddStyles([
                ['-webkit-flex-wrap','wrap'], 
                ['flex-wrap','wrap']
            ]);
        }

        // Attributes
        $this->AddAttributes($Attributes);

        if ($Key != '') {
            $this->AddAttribute('id', $this->Key);
        }

        // Properties
        $this->AddProperties($Properties);

        $items = '';
        foreach($Children as $child) {
            if ($child instanceof FlexItem) {
                if ($items != '') {
                    $items .= "\n";
                }
                $items .= $child->Draw(false);
            }
        }

        parent::__construct(Replace: [
            'ITEMS'         => $items,
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}