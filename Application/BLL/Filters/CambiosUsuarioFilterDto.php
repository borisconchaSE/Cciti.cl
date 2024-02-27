<?php

namespace Application\BLL\Filters;

class CambiosUsuarioFilterDto {
    public int          $IdUsuario                  = 0;
    public  String      $Nombre                     = "";
    public  ?String     $Email                      = null;
    public  String      $Cargo                      = "";
    public  ?int        $IdEmpresa                  = null;
 
}