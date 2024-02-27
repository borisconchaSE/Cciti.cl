<?php

namespace Application\BLL\Filters;

class NuevoUsuarioFilterDto {

    public string   $Cargo              =   "";
    public int      $IdTipoUsuario      =   0;
    public ?int     $IdUsuario          =   null;
    public string   $LoginName          =   '';
    public string   $Nombre             =   '';
    public string   $Sigla              =   '';
    public string   $Password           =   '';

}