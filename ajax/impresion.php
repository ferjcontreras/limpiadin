<?php
    include_once("../etc/opendb.php");
    //include_once("../etc/register_globals.php");
    $estado = $_POST['estado'];
    $email = $_POST['email'];
    $nombre = $_POST['nombre'];
    $userpf = $_POST['userpf'];
    $userid = $_POST['userid'];

    function FormatoFecha($fecha) {
      return substr($fecha, 8,2)."-".substr($fecha, 5,2)."-".substr($fecha,0,4);
    }



    $sqlqry = "SELECT Pedido.ID, Pedido.Fecha, Cliente.Nombre , Estado.Nombre  , SUM(DetallePedido.Cantidad ), Estado.ID, Usuario.Nombre FROM Pedido left join Cliente on Pedido.CodCliente = Cliente.ID left join DetallePedido on DetallePedido.IDPedido = Pedido.ID left join Estado on Pedido.Estado = Estado.ID left join Usuario on Pedido.Preventista = Usuario.ID WHERE (Estado.ID = 4 OR Estado.ID = 2) ";
    if ($email != "") $sqlqry .= " AND Cliente.Email like '$email%' ";
    if ($userpf == 5) $sqlqry .= " AND Pedido.Preventista = '$userid' ";
    if ($nombre != "") $sqlqry .= " AND Cliente.Nombre like '%$nombre%' ";
    $sqlqry .= " GROUP BY Pedido.ID ORDER BY Pedido.Estado, Pedido.Fecha DESC, Pedido.ID DESC LIMIT 10;";
    //echo $sqlqry;
    $DBres = mysqli_query($db, $sqlqry);
    if (mysqli_errno($db)) {
      echo "error";
    }
    $MaxPedidos = 0;
    while($DBarr = mysqli_fetch_row($DBres)) {
      $IDPedido[$MaxPedidos] = $DBarr[0];
      $FechaPedido[$MaxPedidos] = $DBarr[1];
      $NombreCliente[$MaxPedidos] = $DBarr[2];
      $EstadoPedido[$MaxPedidos] = $DBarr[3];
      $CantidadArticulos[$MaxPedidos] = $DBarr[4];
      $IDEstado[$MaxPedidos] = $DBarr[5];
      $Preventista[$MaxPedidos] = $DBarr[6];
      $MaxPedidos++;
    }


    $sqlqry = "SELECT Compra.ID, Compra.Fecha, Cliente.Nombre , Estado.Nombre  , SUM(DetalleCompra.Cantidad ), Estado.ID FROM Compra left join Cliente on Compra.CodCliente = Cliente.ID left join DetalleCompra on DetalleCompra.IDCompra = Compra.ID left join Estado on Compra.Estado = Estado.ID WHERE (Estado.ID = 4 OR Estado.ID = 2) ";
    if ($email != "") $sqlqry .= " AND Cliente.Email like '$email%' ";
    $sqlqry .= " GROUP BY Compra.ID ORDER BY Compra.Estado, Compra.Fecha DESC LIMIT 10;";
    //echo $sqlqry;
    $DBres = mysqli_query($db, $sqlqry);
    if (mysqli_errno($db)) {
      echo "error";
    }
    $MaxCompras = 0;
    while($DBarr = mysqli_fetch_row($DBres)) {
      $IDCompra[$MaxCompras] = $DBarr[0];
      $FechaCompra[$MaxCompras] = $DBarr[1];
      $NombreClienteC[$MaxCompras] = $DBarr[2];
      $EstadoCompra[$MaxCompras] = $DBarr[3];
      $CantidadArticulosC[$MaxCompras] = $DBarr[4];
      $IDEstadoC[$MaxCompras] = $DBarr[5];
      $MaxCompras++;
    }

?>
<div class="banner_separador">
  <p>Pedidos</p>
</div>
<?php
    for ($i = 0; $i < $MaxPedidos; $i++) {
?>
      <div class="item_ingreso">
        <div class="row">
          <div class="col-sm-1">
            <center><p style="margin:1em;width:100%;"><?php echo $IDPedido[$i] ?></p></center>
          </div>
          <div class="col-sm-2">
            <center><p style="margin:1em;width:100%;"><?php echo FormatoFecha($FechaPedido[$i]) ?></p></center>
          </div>
          <div class="col-sm-2">
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
              <img class="boton_redondo" src="images/detalle.png" alt="" title="Ver Detalle" onclick="javascript:VerDetalle(<?php echo $IDPedido[$i] ?>, 'p')">
              <img class="boton_redondo" src="images/imprimir.png" alt="" title="Imprimir" onclick="javascript:Imprimir(<?php echo $IDPedido[$i]?>, 'p')">
              <?php if($IDEstado[$i] == 2) { ?>
                <img class="boton_redondo" src="images/entregado.png" alt="" title="Marcar como Entregado" onclick="javascript:CambiarEstado(<?php echo $IDPedido[$i] ?>, 4, 'p')">
              <?php
                }
                if ($IDEstado[$i] == 4) {
              ?>
                <img class="boton_redondo" src="images/undo.png" alt="" title="Regresar a Confirmado" onclick="javascript:CambiarEstado(<?php echo $IDPedido[$i] ?>, 2, 'p')">
              <?php
                }
              ?>
            </div>
            <!--<p style="margin:1em;width:100%;cursor:pointer;font-weight: bold;color: #4c9fbf;" onclick="javascript:VerDetalle(<?php //echo $IDPedido[$i] ?>);">Ver Detalle</p>-->
          </div>
        </div>
      </div>
<?php
    }
?>

<div class="banner_separador">
  <img src="images/mercadopago2.png" alt="">
</div>
<?php
    for ($i = 0; $i < $MaxCompras; $i++) {
?>
      <div class="item_ingreso">
        <div class="row">
          <div class="col-sm-1">
            <center><p style="margin:1em;width:100%;"><?php echo $IDCompra[$i] ?></p></center>
          </div>
          <div class="col-sm-2">
            <center><p style="margin:1em;width:100%;"><?php echo FormatoFecha($FechaCompra[$i]) ?></p></center>
          </div>
          <div class="col-sm-2">
            <center><p style="margin:1em;width:100%;"><?php echo $NombreClienteC[$i] ?></p></center>
          </div>
          <div class="col-sm-2">
            <center><p  style="margin:1em;width:100%;" class="
              <?php
                if ($IDEstadoC[$i] == 1) echo "pendiente";
                else if ($IDEstadoC[$i] == 2) echo "confirmado";
                else if ($IDEstadoC[$i] == 3) echo "rechazado";
                else echo "entregado"; ?>
                "><?php echo $EstadoCompra[$i] ?></p></center>
          </div>
          <div class="col-sm-1">
            <center><p  style="margin:1em;width:100%;"><?php echo $CantidadArticulosC[$i] ?></p></center>
          </div>
          <div class="col-sm-2">
            <center><p  style="margin:1em;width:100%;">&nbsp;</p></center>
          </div>
          <div class="col-sm-2">
            <div class="botones_circulares">
              <img class="boton_redondo" src="images/detalle.png" alt="" title="Ver Detalle" onclick="javascript:VerDetalle(<?php echo $IDCompra[$i] ?>, 'c')">
              <img class="boton_redondo" src="images/imprimir.png" alt="" title="Imprimir" onclick="javascript:Imprimir(<?php echo $IDCompra[$i]?>, 'c')">
              <?php if($IDEstadoC[$i] == 2) { ?>
                <img class="boton_redondo" src="images/entregado.png" alt="" title="Marcar como Entregado" onclick="javascript:CambiarEstado(<?php echo $IDCompra[$i] ?>, 4, 'c')">
              <?php
                }
                if ($IDEstadoC[$i] == 4) {
              ?>
                <img class="boton_redondo" src="images/undo.png" alt="" title="Regresar a Confirmado" onclick="javascript:CambiarEstado(<?php echo $IDCompra[$i] ?>, 2, 'c')">
              <?php
                }
              ?>
            </div>
            <!--<p style="margin:1em;width:100%;cursor:pointer;font-weight: bold;color: #4c9fbf;" onclick="javascript:VerDetalle(<?php //echo $IDPedido[$i] ?>);">Ver Detalle</p>-->
          </div>
        </div>
      </div>
<?php
    }
?>
