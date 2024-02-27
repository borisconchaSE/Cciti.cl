<?php
namespace Intouch\Framework\Widget\Definitions\AmChart5;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\AmChart5\Chart\Chart;
use Intouch\Framework\Widget\GenericWidget;

#[Widget(Template: 'RootElement', Path: 'Templates', Extension: '.js')]
class RootElement extends Basic {

    public function OnBeforeDraw()
    {
        // Asignar las llaves a los charts
        foreach($this->Charts as $chart) {
            // Asignar Rootkey
            $chart->RootKey = $this->Key;
        }
    }

    public function __construct(
                string  $Key,
        public  string  $DivContainer,
        public  array   $Charts, // array of charts to include
        public  ?string $Timezone = null,
        public  ?int    $FPS = null,
        public  ?bool   $UseSafeResolution = null,

    ) {

        // Validar Charts y asignar RootKey
        //
        foreach($Charts as $chart) {
            if (!($chart instanceof Chart)) {
                throw new \Exception("Uno de los elementos de la colección no es una instancia de -Chart-");
            }
        }

        $this->RootKey = $Key;        

        // Obtener los Charts
        //
        /*
        $builder = "";
        foreach ($Charts as $child) {            

            if ($child instanceof Chart) {
                
                if ($builder != "") {
                    $builder = $builder . "\n";
                }
                $builder .= $child->Draw(false);
            }
        }
        */

        // configuraciones
        $rootConfiguration = [];

        // agregar configuraciones
        // TO-DO
        $rootConfiguration = $this->AddOption($rootConfiguration, 'useSafeResolution', $UseSafeResolution);        
        // if (isset($UseSafeResolution)) {
        //     $rootConfiguration[] = 'useSafeResolution: ' . ( ($UseSafeResolution) ? 'true' : 'false' );
        // }

        $config = $this->BuildOptionList(Options: $rootConfiguration, Tabs: 6);
        // $config = '';
        // foreach($rootConfiguration as $conf) {
        //     if ($config != '') {
        //         $config .= ",\n";
        //     }
        //     $config .= "\t\t\t\t\t\t" . $conf;
        // }

        $rootContent = "\t\t\t\t\t\"$DivContainer\"";

        if ($config != "") {
            $rootContent .= ", " . $config;
            //$rootContent .= ", {\n" . $config . "\n\t\t\t\t\t}";
        }

        // Reemplazar ROOTKEY
        //$builder = str_replace('[[ROOTKEY]]', $this->RootKey, $builder);

        parent::__construct(
            Key     : $Key,
            Replace : [
                'ROOTKEY'               => $this->RootKey,
                'ROOTCONFIGURATION'     => $rootContent,
                'SETTINGS'              => '',
                'CHARTS'                => $Charts, // $builder,
            ]
        );
    }
}