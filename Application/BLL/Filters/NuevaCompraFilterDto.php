<?php

namespace Application\BLL\Filters;

class NuevaCompraFilterDto {

    public ?int     $IdO_C                      =   null;
    public string   $Fecha_Compra               =   "";
    public string   $Descripcion                =   "";
    public string   $marca                      =   "";
    public string   $modelo                     =   '';
    public string   $Orden_compra               =   '';
    public string   $Factura_compra             =   '';
    public int      $Precio_U                   =   0;
    public int      $Cantidad                   =   0;
    public int      $Precio_total               =   0;
    public string    $tipo                      =   '';
    public ?int      $id_estadoOC               =   null;
    public ?int      $id_estadoFC               =   null;
    public ?int      $id_empresa                =   null;

}