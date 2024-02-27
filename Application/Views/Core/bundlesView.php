
@@Layout(login)

<div style="padding: 30px;">
<?php
use Bundles\Bundle;

if (isset($data)) {
    foreach($data as $bundle) {
        if ($bundle->Versionado) {

            foreach($bundle->Sources as $source) {
                $base = $bundle->BaseDir;
                $base[0] = " ";
                $base = trim($base);
            ?>
            <div><?php echo "public/" . $base . "/" . $source . "=1"; ?></div>
            <?php                
            }
        }
    }
}
else {
    echo "@@{ErrorNoBundles, No se han encontrado bundles versionados}";
}
?>
</div>