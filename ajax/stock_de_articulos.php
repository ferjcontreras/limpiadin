<?php
    include_once("../etc/opendb.php");
    //include_once("../etc/register_globals.php");


    $sqlqry = "SELECT Stock.ID, Producto.ID , Producto.Nombre, Stock.Cantidad, Stock.Detalle FROM Producto LEFT JOIN Stock ON Producto.ID = Stock.IDProducto ;";
    $DBres = mysqli_query($db, $sqlqry);
    if (mysqli_errno($db)) {
      echo "error";
    }
    $MaxItems = 0;
    while($DBarr = mysqli_fetch_row($DBres)) {
      $IDStock[$MaxItems] = $DBarr[0];
      $IDProducto[$MaxItems] = $DBarr[1];
      $NombreProducto[$MaxItems] = $DBarr[2];
      $CantidadStock[$MaxItems] = $DBarr[3];
      $StockDetalle[$MaxItems] = $DBarr[4];
      $MaxItems++;
    }



    for ($i = 0; $i < $MaxItems; $i++) {
?>
      <div class="item_ingreso" >
        <div class="row">
          <div class="col-sm-4">
            <center><p style="margin:1em;width:100%;"><?php echo $NombreProducto[$i] ?></p></center>
          </div>
          <div class="col-sm-2">
            <center><p  style="margin:1em;width:100%;"><?php if ($CantidadStock[$i] != "") echo $CantidadStock[$i]; else echo "0" ?></p></center>
          </div>
          <div class="col-sm-4">
            <center><p  style="margin:1em;width:100%;"><?php echo $StockDetalle[$i] ?></p></center>
          </div>
          <div class="col-sm-2">
            <p style="margin:1em;width:100%;cursor:pointer;font-weight: bold;color: #4c9fbf;" onclick="javascript:VerHistorial(<?php echo $IDProducto[$i] ?>);">Ver Historial</p>
          </div>
        </div>
      </div>
<?php
    }
?>
