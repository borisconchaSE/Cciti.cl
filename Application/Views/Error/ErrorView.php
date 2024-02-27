<?php

?>
@@Layout(guest)

    <br>
    <div class="error-container">
        <i class="pe-7s-way text-success big-icon"></i>
        <h1><?=$data->getCode()?></h1>
        <strong>Ha ocurrido un error inesperado</strong>
        <p>
            <br>
            <input type="hidden" value="<?=$data->DebugMessage?>" />
            <input type="hidden" value="<?=$data->getMessage()?>" />
        </p>
        
    </div>
