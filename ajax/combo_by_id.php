<?php
    include_once("../etc/opendb.php");
    include_once("../etc/register_globals.php");
    //$provincia = $_POST['provincia'];

    $sqlqry = "SELECT DetalleCombo.Cantidad, Producto.Nombre, Producto.Precio, DetalleCombo.IDProducto FROM Producto INNER JOIN DetalleCombo ON Producto.ID = DetalleCombo.IDProducto AND DetalleCombo.IDCombo = '$id_combo' ORDER BY Producto.Nombre";
    //echo $sqlqry;
    $DBres = mysqli_query($db, $sqlqry);
    if (mysqli_errno($db)) {
      echo "error";
    }
    $MaxItems = 0;
    while($DBarr = mysqli_fetch_row($DBres)) {
      $Cantidad[$MaxItems] = $DBarr[0];
      $NombreProducto[$MaxItems] = $DBarr[1];
      $PrecioProducto[$MaxItems] = $DBarr[2];
      $IDProducto[$MaxItems] = $DBarr[3];
      $MaxItems++;
    }



    for ($i = 0; $i < $MaxItems; $i++) {
?>
      <div class="item_combo">
        <div class="row">
          <div class="col-sm-2">
            <center><p style="margin:1em;width:100%;"><?php echo $Cantidad[$i] ?> un.</p></center>
          </div>
          <div class="col-sm-4">
            <center><p  style="margin:1em;width:100%;"><?php echo $NombreProducto[$i] ?></p></center>
          </div>
          <div class="col-sm-4">
            <center><p  style="margin:1em;width:100%;"><?php echo $PrecioProducto[$i] ?></p></center>
          </div>
          <div class="col-sm-2">
            <input type="button" class="boton_eliminar" name="" value="X" style="margin-top:0.5em;" onclick="javascript:EliminarProductoById(<?php echo "$id_combo, $IDProducto[$i]" ?>)">
          </div>
        </div>
      </div>
<?php
    }
?>
