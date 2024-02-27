<?php

namespace Application\BLL\Filters;

class CambiarDatosUsuarioFilterDto {
    public int      $IdUsuario      =   0;
    public string   $Nombre         =   "";
    public string   $Sigla          =   "";
    public string   $Cargo          =   "";
    public int      $IdTipoUsuario  =   0;
    public int      $IdJefeDirecto  =   0;
}