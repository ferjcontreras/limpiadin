<?php
  include_once("../etc/opendb.php");
  include_once("../etc/register_globals.php");

  session_start();

    if (!isset($_SESSION['UserIDAdmin'])) {
      include_once("login.php");
    }

    if ($estadonuevo != "" && $tipo == "p") {
      //echo "Hacemos cambios";
      $sqlqry = "UPDATE Pedido SET Estado = '$estadonuevo' WHERE ID = '$id_pedido';";
      mysqli_query($db, $sqlqry);
      if (mysqli_errno($db)) {
        echo "error: $sqlqry";
      }
    }

    if ($estadonuevo != "" && $tipo == "c") {
      //echo "Hacemos cambios: $est";
      $sqlqry = "UPDATE Compra SET Estado = '$estadonuevo' WHERE ID = '$id_compra';";
      mysqli_query($db, $sqlqry);
      if (mysqli_errno($db)) {
        echo "error: $sqlqry";
      }
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

      function CargarContenido(){
        //alert("vamos a agregar el producto "+id);
        document.getElementById("contenido").innerHTML = "Cargando...";
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            respuesta = this.responseText;
            //alert(respuesta);
            //alert(respuesta);

            if (respuesta == "error") {
              alert("Ocurrió un problema al cargar el contenido");
            }
            else {
              //alert("Agregamos "+respuesta+"... cant-"+id+"-id");
              html_cant = document.getElementById("contenido");
              html_cant.innerHTML = respuesta;
            }
          } else if (this.readyState == 404) {
            alert("No se encuentra el archivo php");
          }
        };
        var email = document.forms['Pedidos'].email.value;
        var nombre = document.forms['Pedidos'].nombre.value;
        var parameters = "email="+email+"&nombre="+nombre+"&userpf=<?php echo $_SESSION["UserPfAdmin"] ?>&userid=<?php echo $_SESSION["UserIDAdmin"]?>";
        xhttp.open("POST", "../ajax/impresion.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
        xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
        xhttp.send(parameters);
      }
      function VerDetalle(cual, tipo) {
        document.forms['Pedidos'].target = "";
        document.forms['Pedidos'].tipo.value = tipo;
        document.forms['Pedidos'].id_pedido.value = cual;
        document.forms['Pedidos'].id_compra.value = cual;
        document.forms['Pedidos'].action = "detalle_impresion.php";
        document.forms['Pedidos'].submit();
      }
      function CambiarEstado(id, nuevo, tipo) {
        //alert("id "+id+" - estado:"+est)
        document.forms['Pedidos'].target = "";
        document.forms['Pedidos'].tipo.value = tipo;
        document.forms['Pedidos'].id_pedido.value = id;
        document.forms['Pedidos'].id_compra.value = id;
        document.forms['Pedidos'].estadonuevo.value = nuevo;
        document.forms['Pedidos'].action = "imprimir_pedidos.php";
        document.forms['Pedidos'].submit();
      }
      function Imprimir(id, tipo) {
        //alert("hola");
        document.forms['Pedidos'].target = "_blank";
        document.forms['Pedidos'].tipo.value = tipo;
        document.forms['Pedidos'].id_pedido.value = id;
        document.forms['Pedidos'].id_compra.value = id;
        document.forms['Pedidos'].action = "generar_pdf.php";
        document.forms['Pedidos'].submit();
      }
      function Regresar() {
        document.forms['Pedidos'].action = 'index.php';
        document.forms['Pedidos'].submit();
      }

    </script>
  </head>
  <body onload="CargarContenido();">
    <form name="Pedidos" action="imprimir_pedidos.php" method="post">
      <input type="hidden" name="id_pedido" value="">
      <input type="hidden" name="id_compra" value="">
      <input type="hidden" name="estadonuevo" value="">
      <input type="hidden" name="tipo" value="">
      <?php include_once("header_admin.php"); ?>
      <div class="cuerpo">

        <div class="container">
          <p id="titulo">Impresión de Pedidos y Compras</p>
          <div class="filtro">
            <div class="row">
              <div class="col-sm-4 offset-sm-1">
                <p>Buscar por Email:</p>
              </div>
              <div class="col-sm-4">
                <input type="text" name="email" value="" onkeydown="javascript:CargarContenido()" style="width:100%;">
              </div>
            </div>
            <div class="row">
              <div class="col-sm-4 offset-sm-1">
                <p>Buscar por Nombre:</p>
              </div>
              <div class="col-sm-4">
                <input type="text" name="nombre" value="" onkeydown="javascript:CargarContenido()" style="width:100%;">
              </div>
            </div>
          </div>
          <div class="head_tabla">
            <div class="row">
              <div class="col-sm-1">
                <p>N°</p>
              </div>
              <div class="col-sm-2">
                <p>Fecha</p>
              </div>
              <div class="col-sm-2">
                <p>Cliente</p>
              </div>
              <div class="col-sm-2">
                <p>Estado</p>
              </div>
              <div class="col-sm-1">
                <p>Artículos</p>
              </div>
              <div class="col-sm-2">
                <p>Preventista</p>
              </div>
            </div>
          </div>
          <div id="contenido">

          </div>
        </div>
        <?php include_once("../footer.php"); ?>
      </div>
    </form>
  </body>
</html>
