<?php
namespace Application\BLL\DataTransferObjects\Core;

trait stockDtoT
{

    public $empresa = null;
    public $marca = null;

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