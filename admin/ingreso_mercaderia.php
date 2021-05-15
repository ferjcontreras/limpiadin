<?php
  include_once("../etc/opendb.php");
  include_once("../etc/register_globals.php");

  session_start();

  if (!isset($_SESSION['UserIDAdmin'])) {
    include_once("login.php");
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




  if ($Flag == 1) {
    // Hacemos ingreso de mercadería
    for ($i = 0; $i<10; $i++) {
      if (${'producto'.$i} != 0) {
        //echo "Ingreso de producto ".${'producto'.$i};
        if (${'cantidad'.$i} != "" || ${'cantidad'.$i} != 0) AumentarStock(${'producto'.$i}, ${'cantidad'.$i}, "Ingreso de Mercadería");
      }
    }
  }

  $sqlqry = "SELECT Producto.ID, Producto.Nombre, Categoria.Nombre, Stock.Cantidad FROM Producto LEFT JOIN Categoria ON Producto.IDCategoria = Categoria.ID LEFT JOIN Stock ON Producto.ID = Stock.IDProducto  ORDER BY Producto.Nombre;";
  $DBres = mysqli_query($db, $sqlqry);
  if (mysqli_errno($db)) {
    echo "Error en consulta: $sqlqry";
  }
  $MaxProductos = 0;
  while($DBarr = mysqli_fetch_row($DBres)) {
    $IDProducto[$MaxProductos] = $DBarr[0];
    $NombreProducto[$MaxProductos] = $DBarr[1];
    $NombreCategoria[$MaxProductos] = $DBarr[2];
    $CantidadStock[$MaxProductos] = $DBarr[3];
    $MaxProductos++;
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


    <!-- bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script type="text/javascript">
      function Guardar(){
        document.forms['Ingreso'].Flag.value = 1;
        document.forms['Ingreso'].submit();
      }
      function Regresar() {
        document.forms['Ingreso'].action = 'index.php';
        document.forms['Ingreso'].Flag.value = 0;
        document.forms['Ingreso'].submit();
      }
      function CargarContenido(position) {
        cual = eval("parseInt(document.forms['Ingreso'].producto"+position+".options[document.forms['Ingreso'].producto"+position+".selectedIndex].value)");
        //alert(cual);
        switch (cual) {
            <?php
                for ($i=0; $i < $MaxProductos; $i++) {
                  echo "case ".$IDProducto[$i].":\n";
                  //echo "  alert('al menos uno che');\n";
                  echo "  eval(\"document.forms['Ingreso'].categoria\"+position+\".value = 'Categoría: ".$NombreCategoria[$i]."'\");\n";
                  if ($CantidadStock[$i] == "") $CantidadStock[$i] = 0;
                  echo "  eval(\"document.forms['Ingreso'].stock\"+position+\".value = 'Stock: ".$CantidadStock[$i]." un.'\");\n";
                  echo "  break;\n";
                }
            ?>
        }
      }
    </script>
  </head>
  <body>
    <form name="Ingreso" action="ingreso_mercaderia.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="Flag" value="">
      <?php include_once("header_admin.php"); ?>
      <div class="cuerpo">
        <div class="container">
          <?php if ($Flag == 1) { ?><p id="titulo" style="color:green;">¡Mercadería Ingresada!</p><?php } ?>
          <p id="titulo">Ingreso de Mercadería</p>
          <?php
            for ($i=0; $i<10; $i++) {
          ?>
                <div class="item_ingreso">
                  <div class="row">
                    <div class="col-sm-2">
                      <input type="number" name="cantidad<?php echo $i  ?>" value="" placeholder="Cant." style="width:100%;" class="entrada">
                    </div>
                    <div class="col-sm-4">
                      <select class="" name="producto<?php echo $i ?>" style="width:100%;height:30px;" class="entrada" onchange="javascript:CargarContenido(<?php echo $i ?>)">
                        <option value="0">-- Seleccionar Producto --</option>
                        <?php
                          for ($j = 0; $j < $MaxProductos; $j++) {
                            echo "<option value='".$IDProducto[$j]."'>".$NombreProducto[$j]."\n";
                          }
                        ?>
                      </select>
                    </div>
                    <div class="col-sm-4">
                      <input type="text" name="categoria<?php echo $i  ?>" value="" placeholder="Categoría" style="width:100%;" class="entrada" disabled>
                    </div>
                    <div class="col-sm-2">
                      <input type="text" name="stock<?php echo $i  ?>" value="" placeholder="Stock" style="width:100%;" class="entrada" disabled>
                    </div>
                  </div>
                </div>
          <?php
            }
          ?>
          <div style="margin-top:2em;">
            <input type="button" name="" value="Ingresar" class="boton_verde" onclick="javascript:Guardar();">
            <input type="button" name="" value="Regresar" class="boton_rojo" onclick="javascript:Regresar();">
          </div>
        </div>
        <?php include_once("../footer.php"); ?>
      </div>

    </form>
  </body>
</html>
