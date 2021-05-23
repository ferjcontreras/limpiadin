<?php
  include_once("etc/opendb.php");
  include_once("etc/register_globals.php");


  session_start();

  function ObtenerCantidad($producto) {
    if (isset($_SESSION['carrito'])) {
      $arreglocarrito = $_SESSION['carrito'];

      $econtro = false;
      for ($i=0; $i<count($arreglocarrito); $i++) {
        if ($producto == $arreglocarrito[$i]['Id']) {
          $encontro = true; //$arreglocarrito['Cantidad'] = $arreglocarrito['Cantidad'] + 1;
          $position = $i;
          break;
        }
      }
      if ($encontro == true) return $arreglocarrito[$position]['Cantidad'];
      else return "0";
    }
    else return "0";
  }





?>
<!DOCTYPE html>
<html lang="es" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Limpiadín - Home</title>
    <meta name="author" content="Fernando Contreras">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="css/style.css">

    <!-- bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <!-- font-awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet"/>

    <!-- bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script type="text/javascript">
      function VerCarrito(){
        UserID = document.forms['Session'].UserID.value;
        if (UserID != ''){
          document.forms['BuscarProducto'].action = 'ver_carrito.php';
          document.forms['BuscarProducto'].submit();
        }
        else {
          alert("Debe Iniciar sesión o Registrarse para poder comprar");
          AbrirLogin();
        }
      }
      function SumarTotalCarrito() {
        document.forms['BuscarProducto'].total_carrito.value = parseInt(document.forms['BuscarProducto'].total_carrito.value) + 1;
        //alert(document.getElementById("numero_carrito").innerHTML);
        document.getElementById("numero_carrito").innerHTML = document.forms['BuscarProducto'].total_carrito.value;
      }
      function RestarTotalCarrito() {
        document.forms['BuscarProducto'].total_carrito.value = parseInt(document.forms['BuscarProducto'].total_carrito.value) - 1;
        document.getElementById("numero_carrito").innerHTML = document.forms['BuscarProducto'].total_carrito.value;
      }
    </script>
  </head>
  <body>
    <?php include_once("carrito.php"); ?>
    <form name="BuscarProducto" action="index.php" method="post">
      <input type="hidden" name="total_carrito" value="<?php echo $cantidad_carrito ?>">
      <?php include_once("header.php"); ?>
      <div class="cuerpo">
        <div class="container">
          <div class='row' style='display: flex; justify-content: space-between'>
            <?php
              $sqlqry = "SELECT ID, Nombre, Detalle, Precio, Foto FROM Producto WHERE Nombre like '%$bproducto%' AND Disponible = 1 ORDER BY Nombre;";
              $DBres = mysqli_query($db, $sqlqry);
              if (mysqli_errno($db)) {
                echo "Error en consulta: $sqlqry";
              }
              while($DBarr = mysqli_fetch_row($DBres)) {
            ?>
                <div class='item_carta col-sm-5'>
                  <?php if ($DBarr[4] != ""){ ?>
                    <img src='pictures/productos/<?php echo $DBarr[4] ?>' class='foto_carta'>
                  <?php } else { ?>
                    <img src='images/no_disponible.png' class='foto_carta'>
                  <?php } ?>

                  <div class='detalle_producto'>
                    <p class="nombre_producto"><?php echo $DBarr[1] ?></p>
                    <p class="descripcion_producto"><?php echo $DBarr[2] ?></p>
                    <p class="precio_producto">$ <?php echo $DBarr[3] ?></p>
                  </div>
                  <div class="botones_cantidad">
                    <input class="boton_cantidad" type="button" name="" value="+" onclick="AgregarProducto(<?php echo $DBarr[0] ?>)">
                    <p id="cant<?php echo $DBarr[0] ?>" class="valor_cantidad"><?php echo ObtenerCantidad($DBarr[0]) ?></p>
                    <input class="boton_cantidad" type="button" name="" value="-" onclick="RestarProducto(<?php echo $DBarr[0] ?>)">
                    <input type='hidden' name='cantInt<?php echo $DBarr[0] ?>' value=0 >
                  </div>
                </div>
            <?php
              }
            ?>
          </div>
        </div>
        <?php include_once("footer.php"); ?>
      </div>

    </form>
    <?php include_once("login_modal.php"); ?>
    <script type="text/javascript" src="js/login.js"></script>
    <script type="text/javascript" src="js/carrito.js"></script>
  </body>
</form>
