<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\Action\ActionTargetEnum;
use Intouch\Framework\Widget\Definitions\Container\Edge;
use Intouch\Framework\Widget\Definitions\Container\Position;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Content;

#[Widget(Template: 'CardAction')]
class CardAction extends GenericWidget {

    public function __construct(
        public string           $Key,
        public string           $Action,
        public GenericWidget    $Content,
        public ?string          $ActionTarget = ActionTargetEnum::TARGET_BLANK,
        public array            $Classes = [],
        public array            $Styles = [],
        public array            $Attributes = [],
        public array            $Properties = [],
        public array            $ActionClasses = [],
        public array            $ActionStyles = [],
        public array            $ActionAttributes = [],
        public array            $ActionProperties = [],     
    )
    {        
        // Classes
        $this->AddClasses($Classes);
        $this->AddClass('card text-center');
        $this->AddClasses($ActionClasses, 'action');

        // Styles
        $this->AddStyles($Styles);
        $this->AddStyles($ActionStyles, 'action');

        // Attributes
        $this->AddAttributes($Attributes);
        $this->AddAttributes($ActionAttributes, 'action');
        $this->AddAttribute('href', $Action, 'action');

        if ($Key != '') {
            $this->AddAttribute('id', $this->Key);
        }

        // Properties
        $this->AddProperties($Properties);
        $this->AddProperties($ActionProperties, 'action');

        parent::__construct(Replace: [
            'CONTENT'             => $Content->Draw(false),
            'CLASSES'             => $this->DrawClasses(),
            'STYLES'              => $this->DrawStyles(),
            'ATTRIBUTES'          => $this->DrawAttributes(),
            'PROPERTIES'          => $this->DrawProperties(),
            'ACTIONCLASSES'       => $this->DrawClasses('action'),
            'ACTIONSTYLES'        => $this->DrawStyles('action'),
            'ACTIONATTRIBUTES'    => $this->DrawAttributes('action'),
            'ACTIONPROPERTIES'    => $this->DrawProperties('action')
        ]);
    }
}