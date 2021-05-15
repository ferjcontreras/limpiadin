<?php
    include_once("../etc/opendb.php");
    //include_once("../etc/register_globals.php");
    $estado = $_POST['estado'];
    $email = $_POST['email'];

    function FormatoFecha($fecha) {
      return substr($fecha, 8,2)."-".substr($fecha, 5,2)."-".substr($fecha,0,4);
    }

    $sqlqry = "SELECT Compra.ID, Compra.Fecha, Cliente.Nombre , Estado.Nombre  , SUM(DetalleCompra.Cantidad ), Estado.ID FROM Compra left join Cliente on Compra.CodCliente = Cliente.ID left join DetalleCompra on DetalleCompra.IDCompra = Compra.ID left join Estado on Compra.Estado = Estado.ID ";
    if ($estado != 0 || $email != "") {
      $sqlqry .= " WHERE ";
      if ($estado != 0) $sqlqry .= " Estado.ID = '$estado' AND";
      if ($email != "") $sqlqry .= " Cliente.Email like '$email%' ";
      else $sqlqry = substr($sqlqry, 0, strlen($sqlqry) - 3);
    }

    $sqlqry .= " GROUP BY Compra.ID ORDER BY Compra.Fecha DESC;";
    //echo $sqlqry;
    $DBres = mysqli_query($db, $sqlqry);
    if (mysqli_errno($db)) {
      echo "error";
    }
    $MaxItems = 0;
    while($DBarr = mysqli_fetch_row($DBres)) {
      $IDCompra[$MaxItems] = $DBarr[0];
      $FechaCompra[$MaxItems] = $DBarr[1];
      $NombreCliente[$MaxItems] = $DBarr[2];
      $EstadoCompra[$MaxItems] = $DBarr[3];
      $CantidadArticulos[$MaxItems] = $DBarr[4];
      $IDEstado[$MaxItems] = $DBarr[5];
      $MaxItems++;
    }



    for ($i = 0; $i < $MaxItems; $i++) {
?>
      <div class="item_ingreso">
        <div class="row">
          <div class="col-sm-2">
            <center><p style="margin:1em;width:100%;"><?php echo FormatoFecha($FechaCompra[$i]) ?></p></center>
          </div>
          <div class="col-sm-4">
            <center><p style="margin:1em;width:100%;"><?php echo $NombreCliente[$i] ?></p></center>
          </div>
          <div class="col-sm-2">
            <center><p  style="margin:1em;width:100%;" class="
              <?php
                if ($IDEstado[$i] == 1) echo "pendiente";
                else if ($IDEstado[$i] == 2) echo "confirmado";
                else if ($IDEstado[$i] == 3) echo "rechazado";
                else echo "entregado"; ?>
                "><?php echo $EstadoCompra[$i] ?></p></center>
          </div>
          <div class="col-sm-2">
            <center><p  style="margin:1em;width:100%;"><?php echo $CantidadArticulos[$i] ?></p></center>
          </div>
          <div class="col-sm-2">
            <div class="botones_circulares">
              <img class="boton_redondo" src="images/detalle.png" alt="" title="Ver Detalle" onclick="javascript:VerDetalle(<?php echo $IDCompra[$i] ?>)">
              <?php if($IDEstado[$i] == 1 || $IDEstado[$i] == 3) { ?>
                <img class="boton_redondo" src="images/confirmar.png" alt="" title="Confirmar" onclick="javascript:CambiarEstado(<?php echo $IDCompra[$i] ?>, 2, <?php echo $IDEstado[$i] ?>)">
              <?php
                }
                if ($IDEstado[$i] == 1 || $IDEstado[$i] == 2) {
              ?>
                <img class="boton_redondo" src="images/rechazar.png" alt="" title="Rechazar" onclick="javascript:CambiarEstado(<?php echo $IDCompra[$i] ?>, 3, <?php echo $IDEstado[$i] ?>)">
              <?php
                }
                if ($IDEstado[$i] == 3 || $IDEstado[$i] == 2) {
              ?>
                <img class="boton_redondo" src="images/pendiente.png" alt="" title="Colocar como Pendiente" onclick="javascript:CambiarEstado(<?php echo $IDCompra[$i] ?>, 1, <?php echo $IDEstado[$i] ?>)">
              <?php } ?>
            </div>
            <!--<p style="margin:1em;width:100%;cursor:pointer;font-weight: bold;color: #4c9fbf;" onclick="javascript:VerDetalle(<?php //echo $IDCompra[$i] ?>);">Ver Detalle</p>-->
          </div>
        </div>
      </div>
<?php
    }
?>
