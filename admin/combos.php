<?php
  include_once("../etc/opendb.php");
  include_once("../etc/register_globals.php");

  session_start();

    if (!isset($_SESSION['UserIDAdmin'])) {
      include_once("login.php");
    }



    // Voy a hacer un select de la tabla Producto para poder referenciarlo en los selects de combos mas abajo
    $sqlqry = "SELECT Producto.ID, Producto.Nombre, Producto.Precio FROM Producto WHERE IDCategoria != 1 AND disponible = 1";
    $DBres = mysqli_query($db, $sqlqry);
    if (mysqli_errno($db)) {
      echo "Error en la consulta $sqlqry";
    }
    $MaxProductos = 0;
    while ($DBarr = mysqli_fetch_row($DBres)) {
      $IDProductoCombo[$MaxProductos] = $DBarr[0];
      $NonbreProductoCombo[$MaxProductos] = $DBarr[1];
      $PrecioProductoCombo[$MaxProductos] = $DBarr[2];
      $MaxProductos++;
    }


    //echo "Porcentaje: $porcentaje_aumento";

    if ($Flag == 1) {
      $sqlqry = "SELECT Producto.ID, Producto.Foto, Producto.Nombre FROM Producto WHERE Producto.ID != 0 AND Producto.IDCategoria = 1 "; // seleccionamos la categoria 1 correspondiente solo a los combos
      if ($fnombre != "") $sqlqry .= " AND Producto.Nombre LIKE \"%$fnombre%\" ";
      $sqlqry .= " ORDER BY Producto.Nombre";
      //echo $sqlqry."<br>";
      $DBres = mysqli_query($db, $sqlqry);
      if (mysqli_errno($db)) {
        echo "Error: $sqlqry";
      }
      //$nn = 1;
      while ($DBarr = mysqli_fetch_row($DBres)) {
        $IDProducto = $DBarr[0];
        $FotoProducto = $DBarr[1];
        $NombreProducto = $DBarr[2];


        //echo "$nn - IDPRO:  $IDProducto - $NombreProducto<br>";
        //$nn++;
        if (${'nombre'.$IDProducto} != "") { // Me aseguro de que al menos el nombre no se encuentre vacío para no generar registros vacíos.
          ${'nombre'.$IDProducto}  = str_replace('"', "'", ${'nombre'.$IDProducto} );
          $sqlqry = "UPDATE Producto SET Nombre = \"".${'nombre'.$IDProducto}."\", Detalle = '".${'detalle'.$IDProducto}."', Precio = '".${'precio'.$IDProducto}."'";
          $archivo = $_FILES['foto'.$IDProducto]['name'];
          //echo "Archivo: $archivo";
          if ($archivo != "") {
              $extension = substr(basename($archivo), strpos(basename($archivo), ".") , strlen(basename($archivo)));
              $subido = "../pictures/productos/foto_".$IDProducto."_".date('Y_m_d_H_m_s')."$extension";

              if (move_uploaded_file($_FILES['foto'.$IDProducto]['tmp_name'], $subido)) {
                $sqlqry .= " , Foto = '".basename($subido)."' ";
                // Si todo está OK, voy a borrar el archivo anterior para no generar tanto espacio
                $ArchivoViejo = "../pictures/productos/$FotoProducto";
                unlink($ArchivoViejo);
              } else {
                echo "Error al subir archivo\n";
              }
          }
          if (${'disponible'.$IDProducto} == 'on') $sqlqry .= " , Disponible = 1 ";
          else $sqlqry .= " , Disponible = 0 ";
          $sqlqry .= " WHERE ID = '$IDProducto' ";
          //echo $sqlqry." - $porcentaje_aumento<br>";
          mysqli_query($db, $sqlqry);
          if (mysqli_errno($db)) {
            echo "Error: $sqlqry<br>";
          }
          // Aca tendría que modificar los combos ??
        }
      }
    }
    else if ($Flag == 3) {
      $error = 0;
      $archivo = $_FILES['new_foto']['name'];
      echo "Archivo: $archivo";
      if ($archivo != "") {
          $extension = substr(basename($archivo), strpos(basename($archivo), ".") , strlen(basename($archivo)));
          $subido = "../pictures/productos/foto_new_".date('Y_m_d_H_m_s')."$extension";

          if (move_uploaded_file($_FILES['new_foto'.$IDProducto]['tmp_name'], $subido)) {
            $sqlqry = "INSERT INTO Producto(Nombre, Detalle, IDCategoria, Precio, Foto, Disponible) VALUES ('$new_nombre', '$new_detalle', 1, '$new_precio', '".basename($subido)."', 1);";
          } else {
            echo "Error al subir archivo\n";
            $sqlqry = "INSERT INTO Producto(Nombre, Detalle, IDCategoria, Precio, Disponible) VALUES ('$new_nombre', '$new_detalle', 1, '$new_precio', 1);";
          }
      } else $sqlqry = "INSERT INTO Producto(Nombre, Detalle, IDCategoria, Precio, Disponible) VALUES ('$new_nombre', '$new_detalle', 1, '$new_precio', 1);";
      // Ejecutamos la consulta
      //echo $sqlqry;
      mysqli_query($db, $sqlqry);
      if (mysqli_errno($db)) {
        echo mysqli_error($db);
        $error = 1;
      } else {
        $IDProductoNuevo = mysqli_insert_id($db);
        // Ahora tenemos que cargar los productos de combo desde la tabla temporal y borrar el contenido de la tabla combo_temporal
        $sqlqry = "SELECT IDProducto, Cantidad FROM DetalleComboTemp";
        $DBres2 = mysqli_query($db, $sqlqry);
        if (mysqli_errno($db)) {
          echo "Error en consulta: $sqlqry";
        }
        while ($DBarr2 = mysqli_fetch_row($DBres2)) {
          $sqlqry = "INSERT INTO  DetalleCombo (IDProducto, Cantidad, IDCombo) VALUES('".$DBarr2[0]."', '".$DBarr2[1]."', '$IDProductoNuevo');";
          mysqli_query($db, $sqlqry);
          if (mysqli_errno($db)) {
            echo "Error en consulta: $sqlqry";
          }
        }
        // Ahora procedemos a borrar el contenido de la tabla temporal de combos...
      }
      $sqlqry = "DELETE FROM DetalleComboTemp";
      mysqli_query($db, $sqlqry);
      if (mysqli_errno($db)) {
        echo "Error en consulta: $sqlqry";
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


    <!-- bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script type="text/javascript">
        function Guardar() {
          sessionStorage.setItem("coords", JSON.stringify({
            y: window.scrollY,
            x: window.scrollX
          }));
          document.forms['Combo'].Flag.value = 1;
          document.forms['Combo'].submit();
        }
        function Regresar() {
          document.forms['Combo'].action = 'index.php';
          document.forms['Combo'].Flag.value = 0;
          document.forms['Combo'].submit();
        }
        function Agregar() {
          document.forms['Combo'].Flag.value = 2;
          document.forms['Combo'].submit();
        }
        function SetFlagZero() {
          document.forms['Combo'].Flag.value = 0;
          document.forms['Combo'].submit();
        }
        function Nuevo() {
          document.forms['Combo'].Flag.value = 3;
          document.getElementById("botonGuardar").enabled = false;
          document.forms['Combo'].submit();
        }
        function SetPosition() {
          sessionStorage.setItem("coords", JSON.stringify({
            y: window.scrollY,
            x: window.scrollX
          }));
          CargarCombos();
        }
        function CargarCombos() {
          // por cada ID de Producto de Categoria Combo, tenemos que cargar de DetalleCombo
          <?php
            $sqlqry = "SELECT Producto.ID FROM Producto WHERE Producto.ID != 0 AND Producto.IDCategoria = 1 "; // seleccionamos la categoria 1 correspondiente solo a los combos
            if ($fnombre != "") $sqlqry .= " AND Producto.Nombre LIKE \"%$fnombre%\" ";
            $sqlqry .= " ORDER BY Producto.Nombre";
            $DBres = mysqli_query($db, $sqlqry);
            if (mysqli_errno($db)) {
              echo "Error en consulta: $sqlqry";
            }
            while ($DBarr = mysqli_fetch_row($DBres)) {
                echo "CargarComboById(".$DBarr[0].");\n";
            }
            ?>
        }
        function CargarComboById(id) {
              //alert("Hola");
          var element = document.getElementById("prodscombo"+id);
          var xhttp = new XMLHttpRequest();
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              respuesta = this.responseText;
              if (respuesta == "error") {
                alert("Ocurrió un problema al cargar el contenido");
              }
              else {
                //alert("Agregamos "+respuesta+"... cant-"+id+"-id");
                element.innerHTML = respuesta;
                //alert(respuesta);
              }
            } else if (this.readyState == 404) {
              alert("No se encuentra el archivo php");
            }
          };
          var parameters = "id_combo="+id;
          xhttp.open("POST", "../ajax/combo_by_id.php", true);
          xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
          xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
          xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
          xhttp.send(parameters);
          //alert("Se envio");
        }
        function Filtrar() {
          //document.forms['Combo'].fcategoria.value = document.forms['Combo'].bcategoria.options[document.forms['Combo'].bcategoria.selectedIndex].value;
          document.forms['Combo'].fnombre.value = document.forms['Combo'].bnombre.value;
          document.forms['Combo'].submit();
        }
        function CargarContenidoTemporal() {
          //alert("vamos a agregar el producto "+id);
          document.getElementById("prodscombo").innerHTML = "Cargando...";
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
                document.getElementById("prodscombo").innerHTML = respuesta;
              }
            } else if (this.readyState == 404) {
              alert("No se encuentra el archivo php");
            }
          };
          xhttp.open("POST", "../ajax/combo_temporal.php", true);
          xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
          xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
          xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
          xhttp.send();
        }
        function AgregarProductoTemporal() {
          var cantidad = document.forms['Combo'].cantidad.value;
          var id_producto = document.forms['Combo'].producto_agregar.options[document.forms['Combo'].producto_agregar.selectedIndex].value;
          if (id_producto == 0 || cantidad == "") {
            alert("Debe seleccionar un producto y una cantidad para agregar al combo");
            return;
          }
          else {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
                respuesta = this.responseText;

                if (respuesta == "error") {
                  alert("Ocurrió un problema al cargar el contenido");
                }
                else {
                  //alert("Agregamos "+respuesta+"... cant-"+id+"-id");
                  CargarContenidoTemporal();
                }
              } else if (this.readyState == 404) {
                alert("No se encuentra el archivo php");
              }
            };
            var parameters = "id_producto="+id_producto+"&cantidad="+cantidad;
            xhttp.open("POST", "../ajax/agregar_producto_temporal_combo.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
            xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
            xhttp.send(parameters);

          }
        }
        function EliminarProductoTemporal(id_producto) {
          var xhttp = new XMLHttpRequest();
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              respuesta = this.responseText;

              if (respuesta == "error") {
                alert("Ocurrió un problema al eliminar el producto del combo");
              }
              else {
                //alert("Agregamos "+respuesta+"... cant-"+id+"-id");
                CargarContenidoTemporal();
              }
            } else if (this.readyState == 404) {
              alert("No se encuentra el archivo php");
            }
          };
          var parameters = "id_producto="+id_producto;
          xhttp.open("POST", "../ajax/eliminar_producto_temporal_combo.php", true);
          xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
          xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
          xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
          xhttp.send(parameters);
        }
        function AgregarById(id_producto) {
          // Agregar Productos al Combo
          producto = eval("document.forms['Combo'].producto_agregar"+id_producto+".options[document.forms['Combo'].producto_agregar"+id_producto+".selectedIndex].value;");
          cantidad = eval("document.forms['Combo'].cantidad"+id_producto+".value");
          if (producto == 0 || cantidad == "") {
            alert("Debe colocar al menos un producto y una cantidad para agregar al combo");
          }
          else {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
                respuesta = this.responseText;

                if (respuesta == "error") {
                  alert("Ocurrió un problema al cargar el contenido");
                }
                else {
                  //alert("Agregamos "+respuesta+"... cant-"+id+"-id");
                  CargarComboById(id_producto);
                }
              } else if (this.readyState == 404) {
                alert("No se encuentra el archivo php");
              }
            };
            var parameters = "id_combo="+id_producto+"&cantidad="+cantidad+"&id_producto="+producto;
            xhttp.open("POST", "../ajax/agregar_producto_byid_combo.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
            xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
            xhttp.send(parameters);
          }
        }
        function EliminarProductoById(id_combo, id_producto) {
          //alert("Vamos a eliminar el producto "+id_producto+" del combo "+id_combo);
          var xhttp = new XMLHttpRequest();
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              respuesta = this.responseText;

              if (respuesta == "error") {
                alert("Ocurrió un problema al cargar el contenido");
              }
              else {
                //alert("Agregamos "+respuesta+"... cant-"+id+"-id");
                CargarComboById(id_combo);
              }
            } else if (this.readyState == 404) {
              alert("No se encuentra el archivo php");
            }
          };
          var parameters = "id_combo="+id_combo+"&id_producto="+id_producto;
          xhttp.open("POST", "../ajax/eliminar_producto_byid_combo.php", true);
          xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
          xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
          xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
          xhttp.send(parameters);
        }
    </script>
  </head>
  <body <?php if ($Flag== 0 || empty($Flag)) echo " onload='javascript:CargarCombos();' "?><?php if ($Flag == 1) echo "onload='javascript:SetPosition();'" ?> <?php if ($Flag == 2) echo "onload='javascript:CargarContenidoTemporal()'" ?>>
    <form name="Combo" action="combos.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="Flag" value="">
      <input type="hidden" name="fcategoria" value="<?php echo $fcategoria ?>">
      <input type="hidden" name="fnombre" value="<?php echo $fnombre ?>">
      <?php include_once("header_admin.php"); ?>
      <div class="cuerpo">
        <div class="container">
          <?php if ($Flag == 1 || $Flag == 0 || empty($Flag)) { ?>
                <p id="titulo">Listado de Combos</p>
                <div class="filtro" style="margin-bottom:1em;">
                  <div class="row">
                    <div class="col-sm-4 offset-sm-1">
                      <p>Filtrar por Nombre:</p>
                    </div>
                    <div class="col-sm-4">
                      <input type="text" name="bnombre" value="<?php echo $fnombre ?>" onchange="javascript:Filtrar()" style="width:100%;">
                    </div>
                  </div>
          <?php } ?>
          </div>
          <?php
            if ($Flag == 1 || $Flag == 0 || empty($Flag)){
                  $sqlqry = "SELECT Producto.ID, Producto.Nombre, Producto.Detalle, Producto.Precio, Producto.Foto, Producto.Disponible FROM Producto WHERE Producto.ID != 0 AND Producto.IDCategoria = 1 ";
                  if ($fnombre != "") $sqlqry .= " AND Producto.Nombre LIKE \"%$fnombre%\" ";
                  //if ($fcategoria != 0) $sqlqry .= " AND Producto.IDCategoria = '$fcategoria' ";
                  $sqlqry .= " ORDER BY Producto.Nombre";
                  $DBres = mysqli_query($db, $sqlqry);
                  if (mysqli_errno($db)) {
                    echo "Error: $sqlqry";
                  }
                  while ($DBarr = mysqli_fetch_row($DBres)) {
                    $IDProducto = $DBarr[0];
                    $NombreProducto = $DBarr[1];
                    $DetalleProducto = $DBarr[2];
                    $PrecioProducto = $DBarr[3];
                    $FotoProducto = $DBarr[4];
                    $Disponible = $DBarr[5];
                ?>
                    <div class="item_articulo">
                      <div class="row">
                        <div class="col-sm-4">

                          <div class="imagen_articulo">
                            <?php if ($FotoProducto != ""){ ?>
                              <center><img src="pictures/productos/<?php echo $FotoProducto ?>" alt=""></center>
                            <?php } else { ?>
                              <center><img src="images/no_disponible.png" alt=""></center>
                            <?php } ?>

                          </div>
                        </div>
                        <div class="col-sm-8">
                          <div class="detalle_articulo">
                            <div class="renglon" style="justify-content: left;">
                                <input type="checkbox" name="disponible<?php echo $IDProducto; ?>" value="on" <?php if ($Disponible == 1) echo "checked" ?>>&nbsp; Mostrar en Catálogo
                            </div>
                            <input type="text" name="nombre<?php echo $IDProducto; ?>" value="<?php echo $NombreProducto ?>" style="width:100%;">
                            <textarea name="detalle<?php echo $IDProducto; ?>" rows="4" cols="80"><?php echo $DetalleProducto ?></textarea>
                            <div class="renglon">

                              Efectivo $ <input style="width:20%" type="number" name="precio<?php echo $IDProducto; ?>" value="<?php echo $PrecioProducto ?>" class="entrada">
                            </div>
                            <input type="file" name="foto<?php echo $IDProducto; ?>" value="" style="margin-top:0.5em">
                            <div class="renglon" style="justify-content: left;width:100%;">
                              <div class="productos_combo" id="prodscombo<?php echo $IDProducto ?>" style="width:100%;">

                              </div>
                            </div>
                            <div class="renglon" style="padding-right:2em;">
                              <input type="number" name="cantidad<?php echo $IDProducto  ?>" value="" placeholder="Cant." style="width:10%;margin-right:1em;">
                              <select class="" name="producto_agregar<?php echo $IDProducto ?>" style="width:60%">
                                <option value="0"> -- Seleccionar Producto --</option>
                                <?php
                                  for ($i = 0; $i<$MaxProductos; $i++) {
                                ?>
                                    <option value="<?php echo $IDProductoCombo[$i] ?>"><?php echo $NonbreProductoCombo[$i] ?> -- $ <?php echo $PrecioProductoCombo[$i] ?></option>
                                <?php
                                  }
                                ?>
                              </select>
                              <input type="button" class="boton_azul" name="" value="Agregar" style="margin-left:1em;" onclick="AgregarById(<?php echo $IDProducto ?>)">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
          <?php
                  }
          } else if($Flag == 2) {
          ?>
              <div class="item_nuevo row">
                  <div class="col-sm-12">
                    <p id="titulo">Ingresar Combo</p>
                  </div>
                  <div class="col-sm-6 caja_entrada">
                    <p>Nombre:</p><input type="text" name="new_nombre" value="" placeholder="Nombre">
                  </div>
                  <div class="col-sm-6 caja_entrada">
                    <p>Efectivo:</p>
                    <input type="number" name="new_precio" value="0" placeholder="Precio">
                  </div>
                  <div class="col-sm-12 caja_entrada">
                    <p>Detalle:</p>
                    <textarea style="width:100%;" name="new_detalle" rows="3" cols="80" placeholder="Detalle del Combo"></textarea>
                  </div>

                  <div class="col-sm-12 caja_entrada">
                    <p>Foto:</p>
                    <input type="file" name="new_foto" value="" placeholder="Cargar Foto">
                  </div>
                  <div class="col-sm-12 caja_entrada">
                    <p>Productos:</p>

                  </div>
                  <div class="caja_entrada" style="width:100%;">
                    <div class="productos_combo" id="prodscombo" style="width:100%;">

                    </div>
                  </div>

                  <div class="col-sm-12 caja_entrada">
                    <p>Agregar Producto al Combo:</p>
                    <input type="number" name="cantidad" value="" placeholder="Cantidad">
                    <select name="producto_agregar">
                      <option value="0">-- Seleccionar Producto --</option>
                      <?php
                          $sqlqry = "SELECT Producto.ID, Producto.Nombre, Producto.Precio FROM Producto WHERE IDCategoria != 1 and Disponible = 1";
                          $DBres = mysqli_query($db, $sqlqry);
                          if (mysqli_errno($db)) {
                            echo "Error: $sqlqry";
                          }
                          while ($DBarr = mysqli_fetch_row($DBres)) {
                          ?>
                              <option value="<?php echo $DBarr[0] ?>"><?php echo $DBarr[1] ?> -- $ <?php echo $DBarr[2] ?>
                          <?php
                          }
                      ?>
                    </select>
                    <input type="button" class="boton_azul" name="" value="Agregar Producto" onclick="javascript:AgregarProductoTemporal();">
                  </div>
                  <div class="col-sm-12 caja_botones">
                    <input type="button" name="" value="Guardar" class="boton_verde" onclick="javascript:Nuevo();" id="botonGuardar">
                    <input type="button" name="" value="Regresar" class="boton_rojo" onclick="javascript:SetFlagZero();">
                  </div>
              </div>

          <?php
          }
          else if ($Flag == 3){
            if ($error == 1) echo "<p style='color:red;font-size:2em;'>Se produjo un error al registrar el nuevo combo</p>";
            else echo "<p style='color:green;font-size:1.5em;'>¡Combo almacenado!</p>";
          ?>

          <input style="margin-top:1em;" type="button" name="" value="Regresar" class="boton_azul" onclick="javascript:SetFlagZero();" id="botonGuardar">

          <?php
          }
          ?>
        </div>

        <?php if ($Flag == 0 || empty($Flag) || $Flag == 1) { ?>

        <div class="botones_flotantes" style="background: white;">
          <input type="button" name="" value="Guardar" class="boton_verde" onclick="javascript:Guardar()">
          <input type="button" name="" value="Agregar" class="boton_azul" onclick="javascript:Agregar()">
          <input type="button" name="" value="Regresar" class="boton_rojo" onclick="javascript:Regresar()">
        </div>
        <?php } ?>
        <?php include_once("../footer.php"); ?>
      </div>
    </form>
  </body>
</html>
