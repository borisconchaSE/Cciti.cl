<?php
namespace Application\BLL\DataTransferObjects\Core;

trait UsuarioDtoT
{
    public $Perfil = null;
    public $Cliente = null;
    public $Contacto = null;
    public $Empresa = null;

    public $Logo = null;
    public $LogoHttp = null;
    public $Funcionalidades = null;
    public $Menu = null;

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