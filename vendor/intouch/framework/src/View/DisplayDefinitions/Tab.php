<?php

namespace Intouch\Framework\View\DisplayDefinitions;

use Intouch\Framework\Widget\GenericWidget;

class Tab {

    public function __construct(
        public string           $TabKey,
        public string           $Title,
        public string           $ContentSourceUriFunction = '',
        public bool             $TriggerLoadContent = false,
        public array            $TabData = [],
        public string           $Icon = '',
        public array            $PageButtons = [],
        public array            $Classes = [],
        public array            $Styles = [],
        public array            $Attributes = [],
        public array            $Properties = [],
        public array            $IconClasses = [],
        public array            $IconStyles = [],
        public array            $IconAttributes = [],
        public array            $IconProperties = [],
    )
    {        
    }

}