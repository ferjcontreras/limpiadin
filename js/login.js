function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

function EliminarSesion() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      respuesta = this.responseText;
    } else if (this.readyState == 404) {
      alert("No se encuentra el archivo php");
    }
  };
  xhttp.open("POST", "ajax/cerrar_session.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
  xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
  xhttp.send();
}



function CerrarSession() {
  EliminarSesion();
  LoadLogin();
}


function LoadLogin(){
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      respuesta = this.responseText;
      div_login = document.getElementById("div_login");
      div_login.innerHTML = respuesta;
    } else if (this.readyState == 404) {
      alert("No se encuentra el archivo php");
    }
  };
  xhttp.open("POST", "ajax/load_login.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
  xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
  xhttp.send();
}



function ValidarUsuario(){

  usuario = document.forms['Login'].usuario.value;
  clave = document.forms['Login'].clave.value;

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      respuesta = this.responseText;
      if (respuesta == "error") {
        alert("Ocurrió un problema al validar el usuario");
      }
      else {
        div_login = document.getElementById("div_login");
        div_login.innerHTML = respuesta;
      }
    } else if (this.readyState == 404) {
      alert("No se encuentra el archivo php");
    }
  };
  var parameters = "usuario="+usuario+"&clave="+clave;
  xhttp.open("POST", "ajax/load_login.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
  xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
  xhttp.send(parameters);
}


function Acceder(){
  if (document.forms['Login'].usuario.value != "" && document.forms['Login'].clave.value != "") {
    // Si tiene valores, entonces validamos
    ValidarUsuario();
  }
  else {
    alert('Verifique que los datos estén completos');
  }
}

async function AbrirLogin() {
  document.getElementsByClassName("fondo_login")[0].style.display="block";
  await sleep(500);
  document.getElementsByClassName("login_modal")[0].style.top="50vh";
}
async function CerrarLogin() {
  document.getElementsByClassName("login_modal")[0].style.top="-100vh";
  await sleep(500);
  document.getElementsByClassName("fondo_login")[0].style.display="none";
}
document.getElementById("btnabrir").addEventListener("click",async function(){
    AbrirLogin();
    //   return false
})
/*document.getElementsByClassName("modal_cerrar")[0].addEventListener("click",async function(){
  CerrarLogin();

})*/

LoadLogin();
