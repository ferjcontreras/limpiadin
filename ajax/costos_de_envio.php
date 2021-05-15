<?php
    include_once("../etc/opendb.php");
    //include_once("../etc/register_globals.php");
    //$provincia = $_POST['provincia'];

    $sqlqry = "SELECT CostoEnvio.ID, CostoEnvio.Distancia, CostoEnvio.Costo FROM CostoEnvio ORDER BY CostoEnvio.Distancia";
    $DBres = mysqli_query($db, $sqlqry);
    if (mysqli_errno($db)) {
      echo "error";
    }
    $MaxItems = 0;
    while($DBarr = mysqli_fetch_row($DBres)) {
      $IDCosto[$MaxItems] = $DBarr[0];
      $Distancia[$MaxItems] = $DBarr[1];
      $Costo[$MaxItems] = $DBarr[2];
      $MaxItems++;
    }



    for ($i = 0; $i < $MaxItems; $i++) {
?>
      <div class="item_ingreso" >
        <div class="row">
          <div class="col-sm-4">
            <center><p style="margin:1em;width:100%;">Más de <?php echo $Distancia[$i] ?> km</p></center>
          </div>
          <div class="col-sm-4">
            <center><p  style="margin:1em;width:100%;"><?php echo $Costo[$i] ?></p></center>
          </div>
          <div class="col-sm-4">
            <input type="button" class="boton_eliminar" name="" value="X" style="margin-top:0.5em;" onclick="javascript:EliminarEntrada(<?php echo $IDCosto[$i] ?>)">
          </div>
        </div>
      </div>
<?php
    }
?>
