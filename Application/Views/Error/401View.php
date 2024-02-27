<?php

?>
@@Layout(guest)

    <br>
    <div class="error-container">
        <i class="pe-7s-way text-success big-icon"></i>
        <h1>401</h1>
        <strong>@@{Error401}</strong>
        <p>
            @@{Error401Descripcion}<br>
            <br>
            <input type="hidden" value="<?=$data->DebugMessage?>" />
            <input type="hidden" value="<?=$data->getMessage()?>" />
            <a href='/core/login' class='btn btn-primary'>@@{Error401IrLogin}</a>
        </p>
        
    </div>
