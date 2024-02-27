<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\Container\Edge;
use Intouch\Framework\Widget\Definitions\Container\Position;

#[Widget(Template: 'Card')]
class Card extends GenericWidget {

    public function __construct(        
        public array    $Children,
        public string   $Key = '',
        public int      $Elevation = 3,
        public int      $Transparency = 18,
        public int      $BorderRadius = 4,
        public array    $Classes = [],
        public array    $Styles = [],
        public array    $Attributes = [],
        public array    $Properties = [],
        public ?Edge    $Padding = null,
        public ?Edge    $Margin = null,
    )
    {

        // Classes
        $this->AddClasses($Classes);     
        $this->AddClass('card');

        // Styles
        $this->AddStyles($Styles);
        $this->AddStyle('background-color', '#ffffff');
        
        // Shadow styles
    

        // PADDING
        if (!isset($Padding)) {
            $Padding = new Edge(Top: 10, Right: 10, Bottom: 10, Left: 10);
        }

        if (isset($Padding->Left)) {
            $this->AddStyle('padding-left', $Padding->Left . $Padding->Unit);
        }
        if (isset($Padding->Right)) {
            $this->AddStyle('padding-right', $Padding->Right . $Padding->Unit);
        }
        if (isset($Padding->Top)) {
            $this->AddStyle('padding-top', $Padding->Top . $Padding->Unit);
        }
        if (isset($Padding->Bottom)) {
            $this->AddStyle('padding-bottom', $Padding->Bottom . $Padding->Unit);
        }

        // MARGIN
        if (!isset($Margin)) {
            $Margin = new Edge(Top: 10, Right: 10, Bottom: 10, Left: 10);
        }
        
        if (isset($Margin->Left)) {
            $this->AddStyle('margin-left', $Margin->Left . $Margin->Unit);
        }
        if (isset($Margin->Right)) {
            $this->AddStyle('margin-right', $Margin->Right . $Margin->Unit);
        }
        if (isset($Margin->Top)) {
            $this->AddStyle('margin-top', $Margin->Top . $Margin->Unit);
        }
        if (isset($Margin->Bottom)) {
            $this->AddStyle('margin-bottom', $Margin->Bottom . $Margin->Unit);
        }

        // Attributes
        $this->AddAttributes($Attributes);

        if ($Key != '') {
            $this->AddAttribute('id', $this->Key);
        }

        // Properties
        $this->AddProperties($Properties);

        $builder = "";
        foreach ($Children as $child) {
            if ($child instanceof GenericWidget) {
                if ($builder != "") {
                    $builder = $builder . "\n";
                }
                $builder .= $child->Draw(false);
            }
        }

        parent::__construct(Replace: [
            'CONTENT'       => $builder,
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}