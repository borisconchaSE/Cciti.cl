<html>

    <div>
<?php
    require "database.php";

    $dato = $_GET['Nombre'];

    $qry = "INSERT INTO PLOP (DESC) VALUES ('" . $dato . "')";

    $db = open_db();

    $db->Execute($qry);
    echo "Llegó el formulario";










    //$IdCiudad == -1;

    $texto = '';
    if ($IdCiudad == -1)
        $texto = 'Seleccione CIUDAD primero';
    else
        $texto = 'Sin Selección';


    $texto = ($IdCiudad == -1) ? 'Seleccione CIUDAD primero' : 'Sin Selección';


    $ubicaciones->Add(
        new VWUbicacionDto(
            IdUbicacion: -1,
            Ubicacion: $texto
        )
    );


    
?>

    </div>

    <table>

    </table>

</html>