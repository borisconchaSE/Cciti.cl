<div class="container ">
    <table id="compra">
        <thead>
            <tr>
                <th>Id</th>
                <th>Fecha Asignacion</th>
                <th>Descripcion</th>
                <th>Usuario Asignado</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Precio Total</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>tipo</th>
                <th>OC</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($data as $info):?>
                <tr>
                    <td><?php echo $info->Values->id_stock?></td>
                    <td><?php echo $info->Values->Fecha_asignacion?></td>
                    <td><?php echo $info->Values->Descripcion?></td>
                    <td><?php echo $info->Values->Usuario_asginado?></td>
                    <td><?php echo $info->Values->Cantidad?></td>
                    <td><?php echo $info->Values->Precio_unitario?></td>
                    <td><?php echo $info->Values->Precio_total?></td>
                    <td><?php echo $info->Values->id_marca?></td>
                    <td><?php echo $info->Values->id_modelo?></td>
                    <td><?php echo $info->Values->tipo?></td>
                    <td><?php echo $info->Values->id_oc?></td>
                </tr>
            <?php endforeach?>
        </tbody>
    </table>
</div>