<?php
  include_once("etc/opendb.php");

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" >
    <!-- bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">


    <!-- font-awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet"/>

    <!-- swiper-->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">

    <!-- swiper-->
    <script src="https://unpkg.com/swiper/swiper-bundle.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

    <!-- bootstrap -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script type="text/javascript">
      function AbrirCategoria(valor){
        document.forms['Home'].categoria.value=valor;
        document.forms['Home'].action = 'categoria.php';
        document.forms['Home'].submit();
      }
      function VerCarrito(){
        UserID = document.forms['Session'].UserID.value;
        if (UserID != ''){
          document.forms['Home'].action = 'ver_carrito.php';
          document.forms['Home'].submit();
        }
        else {
          alert("Debe Iniciar sesión o Registrarse para poder comprar");
          AbrirLogin();
        }
      }
      function SumarTotalCarrito() {
        document.forms['Home'].total_carrito.value = parseInt(document.forms['Home'].total_carrito.value) + 1;
        document.getElementById("numero_carrito").innerHTML = document.forms['Home'].total_carrito.value;
      }
      function RestarTotalCarrito() {
        document.forms['Home'].total_carrito.value = parseInt(document.forms['Home'].total_carrito.value) - 1;
        document.getElementById("numero_carrito").innerHTML = document.forms['Home'].total_carrito.value;
      }
    </script>
  </head>
  <body <?php if (!isset($_SESSION['UserID'])) echo "onload='Javascript:AbrirLogin();'" ?>>
    <?php include_once("carrito.php"); ?>
    <form name="Home" action="index.php" method="post">
      <input type="hidden" name="categoria" value="">
      <input type="hidden" name="total_carrito" value="<?php echo $cantidad_carrito ?>">
      <?php include_once("header.php"); ?>

      <div class="cuerpo">
        <div class="combos container">
          <p>Combos - Ofertas</p>
          <div class="slidermaster">
            <div class="swiper-container1 swiper-container">
              <div class="swiper-wrapper">
                <?php
                  $sqlqry = "SELECT Producto.ID, Producto.Nombre, Producto.Detalle, Producto.Precio, Producto.Foto FROM Producto, Categoria WHERE Producto.IDCategoria = Categoria.ID AND Categoria.Nombre = 'Combos' AND Producto.Disponible = 1 ORDER BY Producto.Nombre;";
                  $DBres = mysqli_query($db, $sqlqry);
                  if (mysqli_errno($db)) {
                    echo "Error en consulta: $sqlqry";
                  }
                  while($DBarr = mysqli_fetch_row($DBres)) {
                ?>

                <?php if ($DBarr[4] != ""){ ?>
                  <div class="swiper-slide swiper-slide1" style="background-image: url('pictures/productos/<?php echo $DBarr[4] ?>'); background-size: cover; background-repeat: no-repeat; background-position: center center;">
                <?php } else { ?>
                  <div class="swiper-slide swiper-slide1" style="background-image: url('images/no_disponible.png'); background-size: cover; background-repeat: no-repeat; background-position: center center;">
                <?php } ?>
                    <div class="producto">
                      <p class="precio">$<?php echo $DBarr[3] ?></p>
                      <div class="nombre_producto">
                        <p ><b><?php echo $DBarr[1] ?></b><br><?php echo $DBarr[2] ?></p>
                      </div>
                    </div>
                    <div class="botones_combo">
                      <input class="boton_cantidad" type="button" name="" value="+" onclick="javascript:AgregarProducto(<?php echo $DBarr[0] ?>)">
                      <div id="cant<?php echo $DBarr[0] ?>" class="valor_cantidad"><?php echo ObtenerCantidad($DBarr[0]) ?></div>
                      <input class="boton_cantidad" type="button" name="" value="-" onclick="javascript:RestarProducto(<?php echo $DBarr[0] ?>)">
                      <input type='hidden' name='cantInt<?php echo $DBarr[0] ?>' value=0 >
                    </div>
                  </div>
                <?php
                  }
                ?>

          </div> <!-- swiper container -->
          <div class="swiper-button-next swiper-button-next1"></div>
          <div class="swiper-button-prev swiper-button-prev1"></div>
        </div> <!-- slidemaster -->
      </div> <!-- combos container -->
    </div>


        <div class="categorias container">
          <p>Categorías</p>
          <div class="slidermaster2">
            <div class="swiper-container2 swiper-container">
              <div class="swiper-wrapper">
                <?php
                  $sqlqry = "SELECT ID, Nombre, Foto FROM Categoria WHERE ID > 1 ORDER BY ID;";
                  $DBres = mysqli_query($db, $sqlqry);
                  if (mysqli_errno($db)) {
                    echo "Error en consulta: $sqlqry";
                  }
                  while($DBarr = mysqli_fetch_row($DBres)) {
                ?>
                <div class="swiper-slide swiper-slide2" onclick='AbrirCategoria(<?php echo $DBarr[0] ?>);' style="background-image: url('pictures/categorias/<?php echo $DBarr[2] ?>'); background-size: cover; background-repeat: no-repeat; background-position: center center;">
                  <p class="lista_categoria"><?php echo $DBarr[1] ?></p>
                </div>
                <?php
                  }
                ?>

              </div>
              <!-- Add Pagination -->

              <!-- Add Arrows -->
              <div class="swiper-button-next swiper-button-next2"></div>
              <div class="swiper-button-prev swiper-button-prev2"></div>
            </div>
          </div>
        </div>
        <div class="fondo_categoria">
          &nbsp;
        </div>

        <?php include_once("footer.php"); ?>
      </div>


      <script>
          var swiper = new Swiper('.swiper-container1', {
            /*slidesPerView: 3,
            spaceBetween: 30,
            slidesPerGroup: 3,*/
            /*loop: true,*/
            loopFillGroupWithBlank: true,
            pagination: {
              el: '.swiper-pagination',
              clickable: true,
            },
            navigation: {
              nextEl: '.swiper-button-next1',
              prevEl: '.swiper-button-prev1',
            },
            breakpoints: {
              640: {
                slidesPerView: 2,
                spaceBetween: 20,
              },
              768: {
                slidesPerView: 2,
                spaceBetween: 40,
              },
              1024: {
                slidesPerView: 3,
                spaceBetween: 20,
              },
            }
          });

          var swiper = new Swiper('.swiper-container2', {
            /*slidesPerView: 3,
            spaceBetween: 30,
            slidesPerGroup: 3,*/
            loop: true,
            loopFillGroupWithBlank: true,
            navigation: {
              nextEl: '.swiper-button-next2',
              prevEl: '.swiper-button-prev2',
            },
            breakpoints: {
              359: {
                slidesPerView: 2,
                spaceBetween: 10,
              },
              499: {
                slidesPerView: 3,
                spaceBetween: 10,
              },
              640: {
                slidesPerView: 4,
                spaceBetween: 10,
              },
              768: {
                slidesPerView: 5,
                spaceBetween: 10,
              },
              1024: {
                slidesPerView: 6,
                spaceBetween: 10,
              },
            }
          });
        </script>
      </form>
      <?php include_once("login_modal.php"); ?>
      <script type="text/javascript" src="js/login.js"></script>
      <script type="text/javascript" src="js/carrito.js"></script>
  </body>
</html>
