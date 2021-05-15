<!-- HEADER -->
<div class="header" onclick="Javascript:IrAHome();" style="cursor:pointer;">
  <img src="images/banner.png" alt="" class="franja">
  <div class="bidonescontainer">
    <div class="container" style="height:100%;">
      <div class="row" id="filaheader">
        <div class="col-sm-6 col-xs-12" id="busquedaheader">
          <center><div id="search-wrapper">
            <input type="search" id="search" placeholder="Buscar Producto..."/>
            <i class="fa fa-search"></i>
          </div></center>
        </div>
        <div class="col-sm-6 col-xs-12" id="menuheader">
          <a href="index.php">Inicio</a>
          <a href="#">¿Quiénes Somos?</a>
          <a href="#">Contacto</a>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  function IrAHome() {
    document.forms[0].action = "index.php";
    document.forms[0].submit();
  }
</script>
<!-- END HEADER -->
