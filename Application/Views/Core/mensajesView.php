<?php

if (isset($data)) {

    $idiomas = $data->Idiomas;

    foreach($data->Mensajes as $idioma => $mensaje) {

        $nombreIdioma = $idiomas[$idioma];

        if ($idioma == "es" && (!isset($mensaje) || trim($mensaje) == "")) {
            // buscar alternativo para el español
            if (isset($data->Alternativo) && $data->Alternativo != "") {
                $mensaje = $data->Alternativo;
            }
        }
?>
                        <div>
                            <label class="control-label"><?=$nombreIdioma?> [<?=$idioma?>]</label>
                            <textarea
                                data-locale="<?=$idioma?>"
                                class="form-control language-entry" maxlength="512" style="margin-bottom:10px"><?=$mensaje?></textarea>
                        </div>
<?php
    }
}