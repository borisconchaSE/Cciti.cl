<?php

?>
@@Layout(guest)

    <br>
    <div class="error-container">
        <i class="pe-7s-way text-success big-icon"></i>
        <h1>Sesión No Iniciada</h1>
        <strong>Debe iniciar sesión para acceder a esta funcionalidad</strong>
        <p>
            <br>
            <input type="hidden" value="<?=$data->DebugMessage?>" />
            <input type="hidden" value="<?=$data->getMessage()?>" />
        </p>
        
    </div>
