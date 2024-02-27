<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'DateAndTime')]
class DateAndTime extends GenericWidget {

    public function __construct(
        public \DateTime $DateTime,
        public string  $Key = '',
        public string  $CustomDateFormat = 'd-m-Y',
        public string  $CustomTimeFormat = 'H:i',
        public array   $Classes = [],
        public array   $Styles = [],
        public array   $Attributes = [],
        public array   $Properties = [],
        public array   $DateClasses = [],
        public array   $DateStyles = [],
        public array   $DateAttributes = [],
        public array   $DateProperties = [],
        public array   $TimeClasses = [],
        public array   $TimeStyles = [],
        public array   $TimeAttributes = [],
        public array   $TimeProperties = [],
    )
    {        
        // Classes
        $this->AddClasses($Classes);
        $this->AddClasses($DateClasses, 'date');
        $this->AddClasses($TimeClasses, 'time');

        // Styles
        $this->AddStyles($Styles);
        $this->AddStyles($DateStyles, 'date');
        $this->AddStyles($TimeStyles, 'time');

        // Attributes
        $this->AddAttributes($Attributes);
        $this->AddAttributes($DateAttributes, 'date');
        $this->AddAttributes($TimeAttributes, 'time');

        // Properties
        $this->AddProperties($Properties);
        $this->AddProperties($DateProperties, 'date');
        $this->AddProperties($TimeProperties, 'time');

        if ($Key != '') {
            $this->AddAttribute('id', $Key);
        }

        $date = $DateTime->format($CustomDateFormat);
        $time = $DateTime->format($CustomTimeFormat);

        parent::__construct(Replace: [
            'DATE'              => $date,
            'TIME'              => $time,
            'CLASSES'           => $this->DrawClasses(),
            'STYLES'            => $this->DrawStyles(),
            'ATTRIBUTES'        => $this->DrawAttributes(),
            'PROPERTIES'        => $this->DrawProperties(),
            'DATECLASSES'       => $this->DrawClasses('date'),
            'DATESTYLES'        => $this->DrawStyles('date'),
            'DATEATTRIBUTES'    => $this->DrawAttributes('date'),
            'DATEPROPERTIES'    => $this->DrawProperties('date'),
            'TIMECLASSES'       => $this->DrawClasses('time'),
            'TIMESTYLES'        => $this->DrawStyles('time'),
            'TIMEATTRIBUTES'    => $this->DrawAttributes('time'),
            'TIMEPROPERTIES'    => $this->DrawProperties('time'),
        ]);
    }
}