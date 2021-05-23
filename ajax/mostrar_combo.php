<?php
  include_once("../etc/opendb.php");
  include_once("../etc/register_globals.php");
?>
    <div class="detalle_combo_mostrar">
      <?php
        $sqlqry = "SELECT DetalleCombo.Cantidad, Producto.Nombre, Producto.Precio, Producto.Foto FROM Producto INNER JOIN DetalleCombo ON Producto.ID = DetalleCombo.IDProducto AND DetalleCombo.IDCombo ='$id_combo';";
        $DBres = mysqli_query($db, $sqlqry);
        if (mysqli_errno($db)) {
          echo "Error en consulta: $sqlqry";
        }
        while($DBarr = mysqli_fetch_row($DBres)) {
      ?>
        <div class="item_combo_mostrar">
          <div class="cantidad_combo">
            <p><?php echo $DBarr[0] ?> un.</p>
          </div>
          <div class="imagen_combo">
            <img class="imagen_combo" src="<?php if ($DBarr[3] != "") echo "pictures/productos/".$DBarr[3]; else echo "images/no_disponible.png" ?>" alt="">
          </div>
          <div class="nombre_producto_combo">
            <p><?php echo $DBarr[1] ?></p>
          </div>
          <div class="precio_producto_combo">
            <p>$ <?php echo $DBarr[2] ?></p>
          </div>
        </div>
      <?php
        }
      ?>
    </div>
    <div class="cerrar_combo">
      <a href="Javascript:CerrarCombo();">Cerrar</a>
    </div>
