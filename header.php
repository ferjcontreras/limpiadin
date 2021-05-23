<!-- HEADER -->
<div class="header">
  <img src="images/banner.png" alt="" class="franja" onclick="Javascript:IrAHome();" style="cursor:pointer;">
  <div class="bidonescontainer">
    <div class="container" style="height:100%;">
      <div class="row" id="filaheader">
        <div class="col-sm-6 col-xs-12" id="busquedaheader">
          <center><div id="search-wrapper">
            <!--form name="BusquedaProducto" action="busqueda_productos.php"-->
              <input type="text" name="bproducto" id="search" placeholder="Buscar Producto..." onchange="Javascript:Buscar();"/>
              <i class="fa fa-search"></i>
            <!--/form-->
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
  function Buscar() {
    //alert("Buscamos...");
    //alert("Formulario apunta a :"+document.forms[0].action);
    document.forms[0].action = "busqueda_productos.php";
    document.forms[0].submit();
  }
</script>
<!-- END HEADER -->
