<?php
  include_once("../etc/opendb.php");

  session_start();

    if (!isset($_SESSION['UserIDAdmin'])) {
      include_once("login.php");
    }


?>

<!DOCTYPE html>
<html lang="es" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Limpiadín - Admin</title>
    <meta name="author" content="Fernando Contreras">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="../css/style.css">

    <!-- bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>

    <!-- bootstrap -->
    <!--script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
    <script type="text/javascript">
      function HacerSubmit(option){
        switch (option) {
          case 1:
            document.forms['Admin'].action = "lista_pedidos.php";
            document.forms['Admin'].submit();
            break;
          case 2:
            document.forms['Admin'].action = "lista_ventas.php";
            document.forms['Admin'].submit();
            break;
          case 3:
            document.forms['Admin'].action = "imprimir_pedidos.php";
            document.forms['Admin'].submit();
            break;
          default:
        }
      }
      function Regresar() {
        document.forms['Admin'].action = 'index.php';
        document.forms['Admin'].submit();
      }
    </script>
    <style>
      .material-icons {vertical-align:-14%}
    </style>
  </head>
  <body>
    <form name="Admin" action="index.php" method="post">
      <?php include_once("header_admin.php"); ?>
      <div class="cuerpo">
        <div class="container">
          <div class="col-sm-6 offset-sm-3">
            <?php if ($_SESSION['UserPfAdmin'] == 1){ ?>
              <button type="button" name="button" class="boton_menu" onclick="HacerSubmit(1);">Listado de Pedidos<img src="images/articulos.svg" ></button>
              <button type="button" name="button" class="boton_menu" onclick="HacerSubmit(2);">Listado de Ventas (MP)<img src="images/goods.svg"></button>
            <?php } ?>
            <button type="button" name="button" class="boton_menu" onclick="HacerSubmit(3);">Entrega de Pedidos<img src="images/truck.svg"></button>
          </div>
        </div>
      <?php include_once("../footer.php"); ?>
    </div>
    </form>
  </body>
</html>
