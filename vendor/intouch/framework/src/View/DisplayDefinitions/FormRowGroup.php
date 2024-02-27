<?php

namespace Intouch\Framework\View\DisplayDefinitions;

use Application\BLL\BusinessEnumerations\TipoLayoutFilaEnum;
use Intouch\Framework\Widget\FaIcon;

class FormRowGroup {

    public function __construct(
        public string   $Key,
        public string   $Title          =   '',
        public array    $Rows           =   [],
        public bool     $Visible        =   true,
        public ?string   $Icon          =   null,
        public ?string   $Tooltip       =   null,
        public int      $Layout         = TipoLayoutFilaEnum::BOOTSTRAP

    )
    {
    }
    
}