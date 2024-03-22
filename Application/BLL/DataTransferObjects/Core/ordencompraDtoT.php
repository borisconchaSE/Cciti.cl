<?php
namespace Application\BLL\DataTransferObjects\Core;

trait ordencompraDtoT
{
    public $proveedor       = null;
    public $estadoOC        = null;
    public $estadoFC        = null;
    public $empresa         = null;
    public $tipoproducto    = null;
    
    function __get($name) {

        switch($name) {

            default: return null;
        }
    }
    
    function __set($name, $value) {

        switch($name) {

            default: return null;
        }
    }
}