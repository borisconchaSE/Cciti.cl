<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'Panel')]
class Panel extends GenericWidget {

    public function __construct(
        public GenericWidget    $Body,
        public ?GenericWidget   $Header = null,
        public ?GenericWidget   $Footer = null,
        public bool             $Narrow = false,
        public ?string          $ColorLine = null,
        public string           $Key = '',
        public array            $Classes = [],
        public array            $Styles = [],
        public array            $Attributes = [],
        public array            $Properties = [],
        public array            $BodyClasses = [],
        public array            $BodyStyles = [],
        public array            $BodyAttributes = [],
        public array            $BodyProperties = []
    )
    {
        // Classes
        $this->AddClasses($Classes);
        $this->AddClasses(['hpanel', 'contact-panel']);

        if ($Narrow)        
            $this->AddClass('narrow');
        
        // Styles
        $this->AddStyles($Styles);

        // Attributes
        $this->AddAttributes($Attributes);

        // Properties
        $this->AddProperties($Properties);

        // Header
        $header = '';
        if (isset($this->Header)) {
            $header = (new Container(
                Classes: [ ($Narrow) ? 'panel-heading-narrow' : 'panel-heading'  , 'hbuilt'],
                Children: [$this->Header]
            ))->Draw(false);
        }

        // Body
        $bodyClasses = [ ($Narrow) ? 'panel-body-narrow' :  'panel-body'];
        foreach($BodyClasses as $class) {
            $bodyClasses[] = $class;
        }
        $body = (
            new Container(
                Classes: $bodyClasses,
                Styles : $BodyStyles,
                Attributes : $BodyAttributes,
                Properties : $BodyProperties,
                Children: [$this->Body]
            )
        )->Draw(false);

        // Footer
        $footer = '';
        if (isset($this->Footer)) {
            $footer = (new Container(
                Classes: ['panel-footer'],
                Children: [$this->Footer]
            ))->Draw(false);
        }   
        
        $colorLine = '';
        if (isset($ColorLine)) {
            $colorLine = (new Container(
                Classes: ['color-mantencion color-line ' . $ColorLine],
                Children: [new Html('&nbsp;')]
            ))->Draw(false);
        }

        parent::__construct(Replace: [
            'COLORLINE'         => $colorLine,
            'BODY'              => $body,
            'HEADER'            => $header,
            'FOOTER'            => $footer,
            'CLASSES'           => $this->DrawClasses(),
            'STYLES'            => $this->DrawStyles(),
            'ATTRIBUTES'        => $this->DrawAttributes(),
            'PROPERTIES'        => $this->DrawProperties()
        ]);
    }
}