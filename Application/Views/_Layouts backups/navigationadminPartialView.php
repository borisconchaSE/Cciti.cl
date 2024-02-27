<?php
use Application\BLL\DataTransferObjects\Core\UsuarioDto;
use Application\BLL\DataTransferObjects\Core\PerfilDto;
use Application\BLL\DataTransferObjects\Core\ClienteDto;
use Application\BLL\Services\Core\PerfilSvc;
use Application\BLL\DataTransferObjects\Core\FuncionalidadDto;
use Intouch\Framework\Environment\Session;

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

        $logo = $objUsuario->Logo;
    }
}

?>
<div id="navigation">

    <?php
    if (isset($objUsuario)) {
    ?>
    <br>
    <br>
    <ul class="nav metismenu" id="side-menu">
        <?php        
        foreach($funcs as $idFunc=>$func) {

            // Solo procesar los menus de tipo vertical (menu = 1)
            if ($func->IsMenu == 1) {

                // Ver si es un menu suelto o un menu con items
                if (count($func->Items) == 0) {
                    
                    $activa = ($_SERVER['REQUEST_URI'] == $func->Uri) ? " active " : "";

        ?>
            <li class="<?=$activa; ?>">
                <a href="<?=$func->Uri; ?>"><?=$func->Descripcion ?></a>
            </li>        
        <?php
                }
                else {
                    $activa = "";
                    foreach ($func->Items as $idItem=>$item)
                    {
                        if ($_SERVER['REQUEST_URI'] == $item->Uri) {
                            $activa = " active ";
                            break;
                        }
                    }                
                ?>
                <li class="<?=$activa; ?>">
                    <a href="<?=$func->Uri; ?>"><span class="nav-label"><?=$func->Descripcion ?></span> <span class="fa arrow"></span> </a>
                    <ul class="nav nav-second-level">
                    <?php
                    foreach ($func->Items as $idItem=>$item)
                    {
                        $activa = ($_SERVER['REQUEST_URI'] == $item->Uri) ? " active " : "";
                    ?>
                        <li class="<?=$activa; ?>"><a href="<?=$item->Uri; ?>"><?=$item->Descripcion ?></a></li>
                    <?php
                    }
                    ?>
                </ul>
                </li>        
            <?php
                }
            }
        }
        ?>
    </ul>
    <?php
    }
    ?>
</div>
