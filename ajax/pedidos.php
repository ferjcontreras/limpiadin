<?php
    include_once("../etc/opendb.php");
    //include_once("../etc/register_globals.php");
    $estado = $_POST['estado'];
    $email = $_POST['email'];
    $userpf = $_POST['userpf'];
    $userid = $_POST['userid'];
    $nombre = $_POST['nombre'];

    function FormatoFecha($fecha) {
      return substr($fecha, 8,2)."-".substr($fecha, 5,2)."-".substr($fecha,0,4);
    }

    $sqlqry = "SELECT Pedido.ID, Pedido.Fecha, Cliente.Nombre , Estado.Nombre  , SUM(DetallePedido.Cantidad ), Estado.ID, Usuario.Nombre FROM Pedido left join Cliente on Pedido.CodCliente = Cliente.ID left join DetallePedido on DetallePedido.IDPedido = Pedido.ID left join Estado on Pedido.Estado = Estado.ID left join Usuario on Pedido.Preventista = Usuario.ID ";
    if ($estado != 0 || $email != "" || $userpf == 5 || $nombre != "") {
      $sqlqry .= " WHERE ";
      if ($estado != 0) $sqlqry .= " Estado.ID = '$estado' AND";
      if ($userpf == 5) $sqlqry .= " Pedido.Preventista = '$userid' AND";
      if ($nombre != "") $sqlqry .= " Cliente.Nombre like '%$nombre%' AND";
      if ($email != "") $sqlqry .= " Cliente.Email like '$email%' ";
      else $sqlqry = substr($sqlqry, 0, strlen($sqlqry) - 3);
    }

    $sqlqry .= " GROUP BY Pedido.ID ORDER BY Pedido.Fecha DESC, Pedido.ID DESC;";
    //echo $sqlqry;
    $DBres = mysqli_query($db, $sqlqry);
    if (mysqli_errno($db)) {
      echo "error";
    }
    $MaxItems = 0;
    while($DBarr = mysqli_fetch_row($DBres)) {
      $IDPedido[$MaxItems] = $DBarr[0];
      $FechaPedido[$MaxItems] = $DBarr[1];
      $NombreCliente[$MaxItems] = $DBarr[2];
      $EstadoPedido[$MaxItems] = $DBarr[3];
      $CantidadArticulos[$MaxItems] = $DBarr[4];
      $IDEstado[$MaxItems] = $DBarr[5];
      $Preventista[$MaxItems] = $DBarr[6];
      $MaxItems++;
    }



    for ($i = 0; $i < $MaxItems; $i++) {
?>
      <div class="item_ingreso">
        <div class="row">
          <div class="col-sm-2">
            <center><p style="margin:1em;width:100%;"><?php echo FormatoFecha($FechaPedido[$i]) ?></p></center>
          </div>
          <div class="col-sm-3">
            <center><p style="margin:1em;width:100%;"><?php echo $NombreCliente[$i] ?></p></center>
          </div>
          <div class="col-sm-2">
            <center><p  style="margin:1em;width:100%;" class="
              <?php
                if ($IDEstado[$i] == 1) echo "pendiente";
                else if ($IDEstado[$i] == 2) echo "confirmado";
                else if ($IDEstado[$i] == 3) echo "rechazado";
                else echo "entregado"; ?>
                "><?php echo $EstadoPedido[$i] ?></p></center>
          </div>
          <div class="col-sm-1">
            <center><p  style="margin:1em;width:100%;"><?php echo $CantidadArticulos[$i] ?></p></center>
          </div>
          <div class="col-sm-2">
            <center><p  style="margin:1em;width:100%;"><?php echo $Preventista[$i] ?></p></center>
          </div>
          <div class="col-sm-2">
            <div class="botones_circulares">
              <img class="boton_redondo" src="images/detalle.png" alt="" title="Ver Detalle" onclick="javascript:VerDetalle(<?php echo $IDPedido[$i] ?>)">
              <?php if($IDEstado[$i] == 1 || $IDEstado[$i] == 3) { ?>
                <img class="boton_redondo" src="images/confirmar.png" alt="" title="Confirmar" onclick="javascript:CambiarEstado(<?php echo $IDPedido[$i] ?>, 2, <?php echo $IDEstado[$i] ?>)">
              <?php
                }
                if ($IDEstado[$i] == 1 || $IDEstado[$i] == 2) {
              ?>
                <img class="boton_redondo" src="images/rechazar.png" alt="" title="Rechazar" onclick="javascript:CambiarEstado(<?php echo $IDPedido[$i] ?>, 3, <?php echo $IDEstado[$i] ?>)">
              <?php
                }
                if ($IDEstado[$i] == 3 || $IDEstado[$i] == 2) {
              ?>
                <img class="boton_redondo" src="images/pendiente.png" alt="" title="Colocar como Pendiente" onclick="javascript:CambiarEstado(<?php echo $IDPedido[$i] ?>, 1, <?php echo $IDEstado[$i] ?>)">
              <?php } ?>
            </div>
            <!--<p style="margin:1em;width:100%;cursor:pointer;font-weight: bold;color: #4c9fbf;" onclick="javascript:VerDetalle(<?php //echo $IDPedido[$i] ?>);">Ver Detalle</p>-->
          </div>
        </div>
      </div>
<?php
    }
?>
