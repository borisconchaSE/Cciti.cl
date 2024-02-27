<?php

use Intouch\Framework\Configuration\SystemConfig;
use Intouch\Framework\Environment\Session;

unset(Session::Instance()->usuario);

?>
@@Layout(login)


<div class="login-container">
    <div class="hpanel" style='padding: 0px; border: 0px;'>
        <div class="panel-body" style='padding: 0px'>
            <div class='row' style='margin: 0px;'>
                <div class='hidden-xs hidden-sm col-md-7 login-left' style="text-align: justify !important;">

                    <div class='login-title'>
                        @@{LblLoginBienvenido}
                    </div>
                    <div class='login-text'>@@{LblLoginShutdownDescripcion}
                    </div>
                    <div id="carousel-example-generic" class="carousel slide" data-ride="carousel" style="margin-top: 20px; height: 370px;">
                        <!-- Indicators -->
                        <ol class="carousel-indicators">
                            <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                            <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                            <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                        </ol>

                        <!-- Wrapper for slides -->
                        <div class="carousel-inner" role="listbox">
                            <div class="item active" style="height: 320px;">
                                <img src="/images/mantenciones.png" alt="First slide [900x500]">
                                <div class="carousel-caption" style="padding-bottom: 0px !important;">
                                <strong>@@{LblLoginCarruselOneTitulo}</strong><br>@@{LblLoginCarruselOneDescripcion}
                                </div>
                            </div>
                            <div class="item" style="height: 320px;">
                                <img src="/images/curvas.png" alt="Second slide [900x500]">
                                <div class="carousel-caption" style="padding-bottom: 0px !important;">
                                <strong>@@{LblLoginCarruselTwoTitulo}</strong><br>@@{LblLoginCarruselTwoDescripcion}
                                </div>
                            </div>
                            <div class="item" style="height: 320px;">
                                <img src="/images/moviles.png" alt="Third slide [900x500]">
                                <div class="carousel-caption" style="padding-bottom: 0px !important;">
                                <strong>@@{LblLoginCarruselThreeTitulo}</strong><br>@@{LblLoginCarruselThreeDescripcion}
                                </div>
                            </div>  
                        </div>
                    </div>
                </div>
                <div class='col-sm-12 col-md-5 login-right'>
                    <div class="text-center m-b-md">
                        <h3><?=SystemConfig::Instance()->ApplicationName?></h3>
                        <small>Ingrese la nueva contraseña</small>
                    </div>
                    <br>
                    <form action="#" id="nuevaclave">
                        <input type="hidden" id="id-usuario" value="<?=$data->IdUsuario?>">

                        <div class="form-group required">                        
                            <label class="control-label">@@{LblClaveNueva, Contraseña nueva}</label>
                            <input type="password" id="clave-nueva" class="form-control" maxlength="32">
                        </div>
                        <div class="form-group required">
                            <label class="control-label">@@{LblLoginRepitaClave, Repita su Contraseña}</label>
                            <input type="password" id="repita-clave-nueva" class="form-control" maxlength="32">
                        </div>
                        <br>
                        <button type='button' class="btn btn-info btn-block" id="btn_guardar">@@{BtnGuardarCambios, Guardar Cambios}</button>
                    </form>
                    <div style='width: 100%; padding: 0px 50px 0px 0px; text-align: right; position: absolute; bottom: 10px; font-family: "Open Sans"; font-size: 10px; font-weight: 700; color: #bfbfbf;'>
                    <?=str_replace('_', '.', ($GLOBALS['app_version']=="" ? "v1.10" : $GLOBALS['app_version']))?>
                    </div>
                </div>
            </div>                        
        </div>
    </div>
</div>

@@IncludeScriptBundle(nuevaclaveJS)
@@IncludeStyleBundle(nuevaclaveCSS)

