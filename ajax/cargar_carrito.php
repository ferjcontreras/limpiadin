<?php
  session_start();
  $arreglocarrito = $_SESSION['carrito'];
  //echo "Carrito este:";
  //print_r($arreglocarrito);
  for ($i = 0; $i<count($arreglocarrito); $i++) {
    if ($arreglocarrito[$i]['Cantidad'] > 0 ){
?>
<div class="item_carrito">
    <div class="botones_cantidad">
      <input class="boton_cantidad" type="button" name="" value="+" onclick="AgregaryCargar(<?php echo $arreglocarrito[$i]['Id'] ?>)">
      <p style="margin:auto;" id="cant<?php echo $arreglocarrito[$i]['Id'] ?>"><?php echo $arreglocarrito[$i]['Cantidad'] ?></p>
      <input class="boton_cantidad" type="button" name="" value="-" onclick="RestaryCargar(<?php echo $arreglocarrito[$i]['Id'] ?>)">
    </div>
    <img src="pictures/productos/<?php echo $arreglocarrito[$i]['Foto'] ?>" alt="">
    <p style="margin:auto;"><?php echo $arreglocarrito[$i]['Nombre'] ?></p>

    <p style="margin:auto;">$ <?php echo $arreglocarrito[$i]['Precio'] ?></p>
    <input type="button" class="eliminar_item_carrito" name="" value="X" onclick="EliminaryCargar(<?php echo $arreglocarrito[$i]['Id'] ?>)">
</div>
<?php
    }
  }
?>
