<?php
  include_once("../etc/opendb.php");
  include_once("../etc/register_globals.php");

  session_start();

    if (!isset($_SESSION['UserIDAdmin'])) {
      include_once("login.php");
    }


      function DescontarStock($CodProducto, $Cantidad, $Comments) {
        global $db;
        $UserID = $_SESSION['UserID'];

        // Primero debemos verificar que exista el registro de Stock para ese producto
        $sqlqry = "SELECT ID FROM Stock WHERE IDProducto = '$CodProducto';";
        $DBres = mysqli_query($db, $sqlqry);
        if (mysqli_errno($db)) {
          echo "Error en consulta: $sqlqry";
        }
        if (mysqli_num_rows($DBres) != 0) {
          $DBarr = mysqli_fetch_row($DBres);
          $IDStock = $DBarr[0];
        }
        else {
          // Tenemos que agregar el registro de Stock
          $sqlqry = "INSERT INTO Stock (IDProducto, Cantidad, UserID) VALUES('$CodProducto', 0, '$UserID');";
          mysqli_query($db, $sqlqry);
          if (mysqli_errno($db)) {
            echo "Error en consulta: $sqlqry";
          }
          $IDStock = mysqli_insert_id($db);
        }

        $sqlqry = "UPDATE Stock SET Cantidad = Cantidad - $Cantidad, Detalle = '$Comments' WHERE ID = '$IDStock'";
        mysqli_query($db, $sqlqry);
        if (mysqli_errno($db)) {
          echo "Error en consulta: $sqlqry";
        }

        // Insertamos el registro de zHis_Stock
        $sqlqry = "INSERT INTO zHis_Stock SELECT * FROM Stock WHERE ID = '$IDStock'";
        mysqli_query($db, $sqlqry);
        if (mysqli_errno($db)) {
          echo "Error en consulta: $sqlqry";
        }
      }


      function AumentarStock($CodProducto, $Cantidad, $Comments) {
        global $db;
        $UserID = $_SESSION['UserIDAdmin'];

        // Primero debemos verificar que exista el registro de Stock para ese producto
        $sqlqry = "SELECT ID FROM Stock WHERE IDProducto = '$CodProducto';";
        $DBres = mysqli_query($db, $sqlqry);
        if (mysqli_errno($db)) {
          echo "Error en consulta: $sqlqry";
        }
        if (mysqli_num_rows($DBres) != 0) {
          $DBarr = mysqli_fetch_row($DBres);
          $IDStock = $DBarr[0];
        }
        else {
          // Tenemos que agregar el registro de Stock
          $sqlqry = "INSERT INTO Stock (IDProducto, Cantidad, UserID) VALUES('$CodProducto', 0, '$UserID');";
          mysqli_query($db, $sqlqry);
          if (mysqli_errno($db)) {
            echo "Error en consulta: $sqlqry";
          }
          $IDStock = mysqli_insert_id($db);
        }

        $sqlqry = "UPDATE Stock SET Cantidad = Cantidad + $Cantidad, Detalle = '$Comments' WHERE ID = '$IDStock'";
        mysqli_query($db, $sqlqry);
        if (mysqli_errno($db)) {
          echo "Error en consulta: $sqlqry";
        }

        // Insertamos el registro de zHis_Stock
        $sqlqry = "INSERT INTO zHis_Stock SELECT * FROM Stock WHERE ID = '$IDStock'";
        mysqli_query($db, $sqlqry);
        if (mysqli_errno($db)) {
          echo "Error en consulta: $sqlqry";
        }
      }




    if ($estadonuevo != "") {
      //echo "Hacemos cambios: $est";
      $sqlqry = "UPDATE Compra SET Estado = '$estadonuevo' WHERE ID = '$id_compra';";
      mysqli_query($db, $sqlqry);
      if (mysqli_errno($db)) {
        echo "error: $sqlqry";
      }
      if ($estadonuevo == 2) { // Se confirmó el pedido
        // Debemos hacer descuento de stock en este punto dado que no se hizo al momento de realizar el pedido
        $sqlqry = "SELECT Cantidad, CodProducto FROM DetalleCompra WHERE IDCompra = '$id_compra'";
        $DBres = mysqli_query($db, $sqlqry);
        if (mysqli_errno($db)) {
          echo "error: $sqlqry";
        }
        while($DBarr = mysqli_fetch_row($DBres)) {
          DescontarStock($DBarr[1], $DBarr[0], "Confirmación de Compra N° $id_compra");
        }
      }
      else if (($estadonuevo == 1 && $estadoactual == 2) || ($estadonuevo == 3 && $estadoactual == 2)) { // se colocó denuevo para pendiente o rechazado
        // Debemos aumentar el stock de productos
        $sqlqry = "SELECT Cantidad, CodProducto FROM DetalleCompra WHERE IDCompra = '$id_compra'";
        $DBres = mysqli_query($db, $sqlqry);
        if (mysqli_errno($db)) {
          echo "error: $sqlqry";
        }
        while($DBarr = mysqli_fetch_row($DBres)) {
          AumentarStock($DBarr[1], $DBarr[0], "Pendiente de Compra N° $id_compra");
        }
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
      function Filtrar() {
        var estado = document.forms['Compras'].estado.options[document.forms['Compras'].estado.selectedIndex].value;
        //document.forms['Minimos'].submit();
        CargarContenido(estado);
      }
      function CargarContenido(estado){
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
        var email = document.forms['Compras'].email.value;
        var parameters = "estado="+estado+"&email="+email;
        xhttp.open("POST", "../ajax/compras.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
        xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
        xhttp.send(parameters);
      }
      function VerDetalle(cual) {
        document.forms['Compras'].id_compra.value = cual;
        document.forms['Compras'].action = "detalle_compra.php";
        document.forms['Compras'].submit();
      }
      function CambiarEstado(id, nuevo, actual) {
        //alert("id "+id+" - estado:"+est)
        document.forms['Compras'].id_compra.value = id;
        document.forms['Compras'].estadonuevo.value = nuevo;
        document.forms['Compras'].estadoactual.value = actual;
        document.forms['Compras'].submit();
      }
      function Regresar() {
        document.forms['Compras'].action = 'index.php';
        document.forms['Compras'].submit();
      }
    </script>
  </head>
  <body onload="CargarContenido(0);">
    <form name="Compras" action="lista_ventas.php" method="post">
      <input type="hidden" name="id_compra" value="">
      <input type="hidden" name="estadonuevo" value="">
      <input type="hidden" name="estadoactual" value="">
      <?php include_once("header_admin.php"); ?>
      <div class="cuerpo">

        <div class="container">
          <p id="titulo">Listado de Compras de Clientes</p>
          <div class="filtro">
            <div class="row">
              <div class="col-sm-4 offset-sm-1">
                <p>Mostrar solamente compras:</p>
              </div>
              <div class="col-sm-4">
                <select class="" name="estado" onchange="javascript:Filtrar();" style="width:100%;">
                  <option value="0">-- Seleccionar Estado --</option>
                  <?php
                    $sqlqry = "SELECT ID, Nombre FROM Estado ORDER BY Nombre ";
                    $DBres = mysqli_query($db, $sqlqry);
                    if (mysqli_errno($db)) {
                      echo "Error: $sqlqry";
                    }
                    while($DBarr = mysqli_fetch_row($DBres)) {
                      echo "<option value='".$DBarr[0]."'>".$DBarr[1]."\n";
                    }
                  ?>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-4 offset-sm-1">
                <p>Buscar por Email:</p>
              </div>
              <div class="col-sm-4">
                <input type="text" name="email" value="" onkeydown="javascript:Filtrar()" style="width:100%;">
              </div>
            </div>
          </div>
          <div class="head_tabla">
            <div class="row">
              <div class="col-sm-2">
                <p>Fecha</p>
              </div>
              <div class="col-sm-4">
                <p>Cliente</p>
              </div>
              <div class="col-sm-2">
                <p>Estado</p>
              </div>
              <div class="col-sm-2">
                <p>Artículos</p>
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
