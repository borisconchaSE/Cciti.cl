<?php

?>
@@Layout(guest)

    <br>
    <div class="error-container">
        <i class="pe-7s-way text-success big-icon"></i>
        <h1>500</h1>
        <strong>@@{Error500}</strong>
        <p>
            @@{Error500Descripcion}<br>
            <br>
            <input type="hidden" value="<?=$data->DebugMessage?>" />
            <input type="hidden" value="<?=$data->getMessage()?>" />       
        </p>
        
    </div>
