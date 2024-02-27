<?php

?>

@@Layout(nuevousuario)

<div id="pageHeader" class="normalheader small-header">
    <div class="hpanel">
        <div class="panel-body">          
            <div>
                <h2 class="font-light m-b-xs">
                Resetear Contraseña
                </h2>
            </div>
            <small>Ingrese la nueva contraseña</small>
        </div>
    </div>
</div>

<div class="view-content">
    <div class="hpanel contact-panel">
        <div class="panel-body">
            <form method="post" enctype="multipart/form-data">

                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6 required">
                            <label class="control-label">@@{LblClaveActual, Contraseña actual}</label>
                            <input type="password" id="clave-actual" class="form-control" maxlength="32">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6 required">                        
                            <label class="control-label">@@{LblClaveNueva, Contraseña nueva}</label>
                            <input type="password" id="clave-nueva" class="form-control" maxlength="32">
                        </div>
                        <div class="col-sm-6 required">                        
                            <label class="control-label">@@{LblLoginRepitaClave, Repita su Contraseña}</label>
                            <input type="password" id="repita-clave-nueva" class="form-control" maxlength="32">
                        </div>
                    </div>
                </div>
            </form>
            
            <button id="btn_guardar" class="ladda-button btn btn-primary pull-right" data-style="zoom-in">@@{BtnGuardarCambios, Guardar Cambios}</button>

        </div>
    </div>
</div>

@@IncludeStyleBundle(cambiarclaveCSS)
@@IncludeScriptBundle(cambiarclaveJS)