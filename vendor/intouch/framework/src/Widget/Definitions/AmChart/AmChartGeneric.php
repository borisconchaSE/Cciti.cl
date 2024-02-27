<?php
namespace Intouch\Framework\Widget\Definitions\AmChart;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'ChartContainer')]
class AmChartGeneric{

    protected string $_script   = "";
    protected        $Settings  = "";
    protected        $Series    = null;

    public function __construct( 
    )
    {
        /*
        $this->AddClasses($Classes);
        $this->AddStyles($Styles);
        $this->AddAttributes($Attributes);
        $this->AddAttribute('id', $Key);
        $this->AddProperties($Properties);

        parent::__construct(Replace: [
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties(),
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'SCRIPT'        => $script->Draw(false),
        ]);
        */
    }


    


  
    
}

