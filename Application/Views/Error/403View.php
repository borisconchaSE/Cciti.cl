@@Layout(guest)

    <br>
    <div class="error-container">
        <i class="pe-7s-way text-success big-icon"></i>
        <h1>403</h1>
        <strong>@@{Error403}</strong>
        <p>
            @@{Error403Descripcion}<br>            <br>
            <input type="hidden" value="<?=$data->DebugMessage?>" />
            <input type="hidden" value="<?=$data->getMessage()?>" />
        </p>
        
    </div>
