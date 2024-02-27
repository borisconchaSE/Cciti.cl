<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\PopUpModal\ColorLineStyleEnum;

#[Widget(Template: 'Photo')]
class Photo extends GenericWidget {

    public function __construct(
        public string           $Key = '',
        public string           $ImageUrl,
        public \DateTime        $Date,
        public string           $DateTitle,
        public string           $AvatarUrl,
        public string           $Username = '',
        public string           $Origin = '',
        public array            $Classes = [],
        public array            $Styles = [],
        public array            $Attributes = [],
        public array            $Properties = [],
        public string           $Comentario = ''
    )
    {
        // Classes
        $this->AddClasses($Classes);
        $this->AddClass('photo-item');

        // Styles
        $this->AddStyles($Styles);

        // Attributes
        $this->AddAttributes($Attributes);

        if ($Key != '')
            $this->AddAttribute('id', $this->Key);

        // Properties
        $this->AddProperties($Properties);

        $fecha  = $Date->format('d-m-Y');
        $hora   = $Date->format('H:i');

        parent::__construct(Replace: [
            'DATE'          => $fecha,
            'TIME'          => $hora,
            'DATETITLE'     => $DateTitle,
            'AVATAR'        => $AvatarUrl,
            'IMAGEURL'      => $ImageUrl,
            'FOOTERLEFT'    => $Username,
            'FOOTERRIGHT'   => $Origin,
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties(),
            'OBSERVACION'   => $Comentario ?: ""
        ]);
    }
}