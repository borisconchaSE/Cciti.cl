<?php

namespace Intouch\Framework\Widget;

use DateTime;
use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\Container\Edge;
use Intouch\Framework\Widget\Definitions\Container\Position;
use Intouch\Framework\Widget\Definitions\TimeLine\TimeLineStyleEnum;

#[Widget(Template: 'TimeLineItem')]
class TimeLineItem extends GenericWidget {

    public function __construct(      
        // DEFINICION ESTANDAR DEL WIDGETS  
        public string           $Key                = '',
        public array            $Classes            = [],
        public array            $Styles             = [],
        public array            $Attributes         = [],
        public array            $Properties         = [],  
        public ?Edge            $Padding            = null,
        public ?Edge            $Margin             = null,
        public ?Position        $Position           = null,
        // DEFICIÓN ESPECIAL DEL WIDGET
        public ?string          $AvatarUrl          = null,
        public string           $Username           = "",
        public string           $Origen             = "Web",
        public DateTime         $FechaCreacion,
        public string           $Content            = "",
        public ?DateTime        $FechaSincronizado  = null,
        public ?GenericWidget $Label                = null,
        public ?String $TimeLineStyle               = TimeLineStyleEnum::TIMELINE_PRIMARY
    )
    { 
        // ----------------------------------------------------------------------------------
        // DEFINICIÓN ESTANDAR DE LOS WIDGETS
        // ----------------------------------------------------------------------------------

        // Classes
        $this->AddClasses(["vertical-timeline-block"]);
        $this->AddClasses($Classes);

        // Styles
        $this->AddStyles($Styles);

        if (isset($Padding)) {
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
        }

        if (isset($Margin)) {
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
        }

        if (isset($Position)) {

            $this->AddStyle('position', $Position->Type);

            if (isset($Position->Left)) {
                $this->AddStyle('left', $Position->Left . $Position->Unit);
            }
            if (isset($Position->Right)) {
                $this->AddStyle('right', $Position->Right . $Position->Unit);
            }
            if (isset($Position->Top)) {
                $this->AddStyle('top', $Position->Top . $Position->Unit);
            }
            if (isset($Position->Bottom)) {
                $this->AddStyle('bottom', $Position->Bottom . $Position->Unit);
            }
        }

        // Attributes
        $this->AddAttributes($Attributes);

        if ($Key != '') {
            $this->AddAttribute('id', $this->Key);
        }

        // Properties
        $this->AddProperties([
            ["child","vertical-timeline-block"]
        ]);
        $this->AddProperties($Properties);

        // ----------------------------------------------------------------------------------
        // DEFINICIÓN ESPECIAL DEL WIDGET
        // ----------------------------------------------------------------------------------

        //-- definicion del avatar por defecto en caso de que se encuentre vacio
        if(empty($AvatarUrl)){
            $AvatarUrl = "/assets/avatar/default/USER.png"; 
        }
        //-- definicion de la fecha de creación
        setlocale(LC_ALL,"es_ES");  
        $daynumber          = $FechaCreacion->format('w');
        $dias               = ["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado"];
        $WG_DAYNAME         = $dias[$daynumber];
        $WG_FechaCreacion   = $FechaCreacion->format('d-m-Y');
        $WG_HoraCreacion    = $FechaCreacion->format('H:i');

        //--- verifico si viene la fecha en la que se sincronizo la bitacora
        $Sincronizado = "";
        if(!empty($FechaSincronizado) AND $FechaSincronizado instanceof DateTime){

            $Sincronizado = " Sincronizado: ". $FechaSincronizado->format('d-m-Y H:i') . " Hrs";

        }

        //--- verifico que venga un label de decoración
        $LabelHtml = "";
        if($Label instanceof GenericWidget){
            $LabelHtml = $Label->Draw(false);
        }
        

        parent::__construct(Replace: [
            // DEFINICION ESTANDAR DEL WIDGET 
            'CLASSES'           => $this->DrawClasses(),
            'STYLES'            => $this->DrawStyles(),
            'ATTRIBUTES'        => $this->DrawAttributes(),
            'PROPERTIES'        => $this->DrawProperties(),
            // DEFINICION ESPECIAL DEL WIDGET
            'AVATAR_URL'        => $AvatarUrl,
            'ORIGEN_SRC'        => $Origen,
            'FECHA_CREACION'    => $WG_FechaCreacion,
            'HORA_CREACION'     => $WG_HoraCreacion,
            'DAY_NAME'          => $WG_DAYNAME,
            'CONTENT'           => $Content,
            'USERNAME'          => $Username,
            'SINCRONIZADO'      => $Sincronizado,
            'LABEL'             => $LabelHtml,
            'TIMELINESTYLE'     => $TimeLineStyle
            
        ]);
    }
}