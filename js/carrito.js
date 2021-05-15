function AgregarProducto(id){
  //alert("vamos a agregar el producto "+id);
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      respuesta = this.responseText;
      //alert(respuesta);
      //alert(respuesta);

      if (respuesta == "error") {
        alert("Ocurrió un problema al agregar el producto al carrito");
      }
      else {
        //alert("Agregamos "+respuesta+"... cant-"+id+"-id");
        html_cant = document.getElementById("cant"+id);
        html_cant.innerHTML = respuesta;
        document.getElementById("numero_carrito").style.display = "block";
        SumarTotalCarrito();
      }
    } else if (this.readyState == 404) {
      alert("No se encuentra el archivo php");
    }
  };
  var parameters = "producto="+id;
  xhttp.open("POST", "ajax/agregar_al_carrito.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
  xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
  xhttp.send(parameters);
}

function RestarProducto(id) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      respuesta = this.responseText;

      if (respuesta == "error") {
        alert("Ocurrió un problema al restar el producto del carrito");
      }
      else {
        html_cant = document.getElementById("cant"+id);
        html_cant.innerHTML = respuesta;
        document.getElementById("numero_carrito").style.display = "block";
        RestarTotalCarrito();
      }
    } else if (this.readyState == 404) {
      alert("No se encuentra el archivo php");
    }
  };
  var parameters = "producto="+id;
  xhttp.open("POST", "ajax/restar_al_carrito.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
  xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
  xhttp.send(parameters);
}
function EliminarItem(id){
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      respuesta = this.responseText;

      if (respuesta == "error") {
        alert("Ocurrió un problema al eliminar el producto del carrito");
      }
      else {
        //html_cant = document.getElementById("cant"+id);
        //html_cant.innerHTML = respuesta;
      }
    } else if (this.readyState == 404) {
      alert("No se encuentra el archivo php");
    }
  };
  var parameters = "producto="+id;
  xhttp.open("POST", "ajax/colocar_en_cero.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
  xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
  xhttp.send(parameters);
}
