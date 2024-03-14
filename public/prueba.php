<html>

    <div>
Esta es mi aplicacion, a las: <?php echo (new DateTime())->format('H:i') ?> hrs.
    </div>

    <div>
        <form action="recibeform.php" id="formulario">
            <input type="text" name="Nombre" />
        </form>
        <button name="boton" onclick="validar()">Enviar</button>
    </div>
</html>

<script language="javascript">

    function validar() {

        // $.ajax(
        //     method: 'post',
        //     url: '/core/login',
        //     data: {
        //         loginname: rrrrrr,
        //         password: rrrrr
        //     },
        //     function(response) {
        //         $('#contenido').html(response);
        //     }
        // );
        
        // validaciones OK
        document.getElementById('formulario').submit();
    }

</script>