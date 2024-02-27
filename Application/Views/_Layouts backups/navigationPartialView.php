<?php

use Application\BLL\BusinessObjects\Producto\EmpaqueBO;
use Application\BLL\DataTransferObjects\Core\UsuarioDto;
use Application\BLL\DataTransferObjects\Core\PerfilDto;
use Application\BLL\DataTransferObjects\Core\ClienteDto;
use Application\BLL\Services\Core\PerfilSvc;
use Application\BLL\DataTransferObjects\Core\FuncionalidadDto;
use Application\Views\Widgets\TitleAndContent;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\FormRowGroup;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\Definitions\Container\Edge;
use Intouch\Framework\Widget\Definitions\Label\LabelSizeEnum;
use Intouch\Framework\Widget\Definitions\Label\LabelStyleEnum;
use Intouch\Framework\Widget\Html;
use Intouch\Framework\Widget\Image;
use Intouch\Framework\Widget\Label;
use Intouch\Framework\Widget\Panel;
use Intouch\Framework\Widget\Progress;
use Intouch\Framework\Widget\Text;
use Intouch\Framework\Widget\TitleDescription;


$display = new Display();

// Analizar si vienen datos del usuario en la sesion
$objUsuario = null;
$objPerfil = null;

$usuario = "";
$perfil = "";

if (isset(Session::Instance()->usuario)) {
    $objUsuario = Session::Instance()->usuario;
    if (isset($objUsuario)) {
        $usuario = $objUsuario->Nombre;
        if (isset($objUsuario->Cliente)) {
            $objCliente = $objUsuario->Cliente;
            $cliente = $objCliente->RazonSocial;
        }
        if (isset($objUsuario->Perfil)) {
            $objPerfil = $objUsuario->Perfil;
            $perfil = $objPerfil->Descripcion;
        }
        if (isset($objUsuario->Funcionalidades)) {
            $funcs = $objUsuario->Funcionalidades;
        }
    }
}

// LOGO EMPRESA
$logo = new Container(
    Classes: ['logo-empresa'],
    Children: [
        new Image(
            Source: $objUsuario->Logo,
            Classes: ['logo-empresa-imagen']
        )
    ]
);

?>
<div id="navigation">

    <!-- LOGO EMPRESA -->
    <?php
    $logo->Draw(true);
    ?>

</div>
