var dettagli_form = {
    "nome": [/^([\w\s]){2,20}$/, "Inserire il nome del gioco"],
    "sviluppo": [/^([\w\s]){5,30}$/, "Inserire il nome della casa di sviluppo"],
    "pegi": [/^(3|7)$|^1(2|6|8)$/, "Inserire il PEGI del gioco"],
    "dlc": [/^([\w\s]){2,30}$/, "Inserire il nome del DLC"],
    "descrizione": [/.{25,}/, "Inserire la descrizione"],
    "recensione": [/.{25,}/, "Inserire la recensione"],
    "alternativo": [/^([\w\s]){0,50}$/, "Inserire il testo alternativo dell'immagine"],

    "titolo": [/^([\w\s\'\,\.\"]){10,40}$/, "Inserire il titolo della notizia"],
    "testo": [/.{25,}/, "Inserire il testo della notizia"],

    "nomeUtente": [/^([\w\s]){5,20}$/, "Inserire il nickname"],
    "password": [/^([\w]){8,16}$/, "Inserire la password"],
    "repeatpassword": [/^([\w]){8,16}$/, "Le due password non coincidono"],
    "email": [/^\w+(\.?\w+)*@\w+(\.?\w+)*(\.\w{2,3})+$/, "Inserire la mail"]
    
};

/*-----------------------------------------------------------------------------------
function campoDefault(input) {
    input.className = "deftext";
    input.value = dettagli_form[input.id][ex0];
}

function campoPerInput(input) {

    if (input.value == dettagli_form[input.id][ex0]) {
        input.value = "";
        input.className = "";
    }
}

function backToDefault(input) {
    if (!input.value.search(/^\s*$/)) {
        campoDefault(input);
    }
}

function carica_placeholder() {

    for (var key in dettagli_form) {
        var input = document.getElementById(key);
        if (input) {
            campoDefault(input);
            input.onfocus = function() {campoPerInput(this);};
            input.onblur = function() {backToDefault(this);};
        }
    }
}

function caricamento() {
    handleClick();
    carica_placeholder();
}
--------------------------------------------------------------------------------------------*/

function mostraErrore(input) {
    var elemento = document.createElement("strong");
    elemento.className = "errori";
    elemento.appendChild(document.createTextNode(dettagli_form[input.id][1]));

    var p = input.parentNode;
    p.appendChild(elemento);

    input.className = "erroriBox";
}

function validazioneCampo(input) {

    // controllo se e' gia' presente messaggio d'errore
    var parent = input.parentNode;
    if (parent.children.length == 2) {
        parent.removeChild(parent.children[1]);
    }

    var regex = dettagli_form[input.id][0];
    var text = input.value;
    if (text.search(regex) != 0) {
        // -1 se non trova il match, 0 se lo trova, 6 (es) se trova il match dalla posizione 6
        mostraErrore(input);
        return false;
    }
    else {
        input.className = "correttiBox";
        return true;
    }
}

function validateForm() {

    var corretto = true;
    for (var key in dettagli_form) {
        var input = document.getElementById(key);
        if (input) {
            var risultato = validazioneCampo(input);
            corretto = corretto && risultato;
        }
    }
    return corretto;
}

function handleClick() {

    var radio = document.getElementById("handler");
    var barraDiRicerca = document.getElementById("cercaTitolo");
    if (radio) {
        radio.onchange = function(event) {
            if(event.target.value == "Giochi")
                barraDiRicerca.className = "visibile";
            else
                barraDiRicerca.className = "invisibile";
        }
    }
}

function caricamento() {
    handleClick();
    /*carica_placeholder();*/
}

/* Controlla che l'input della searchbar non sia vuoto o composto da spazi */
function checkNotEmpty() {
    var input = document.getElementById("searchbar");
    if (!input.value.search(/^\s*$/))
        return false;
    else
        return true;
}

/* Toggle between adding and removing the "responsive" class to topnav when the user clicks on the icon */
function responsiveMenu() {
    var x = document.getElementById("menu");
    if (x.className === "topnav") {
      x.className += " responsive";
    } else {
      x.className = "topnav";
    }
}

//script dalla pagina giochi//

var filtri = ["console", "generi", "annofin", "annoin", "ordine"];

function OpenFiltro() {
    for (var key in filtri) {
        var filtro = document.getElementById(key);
        if (filtro)
            filtro.classList.toggle("show");
    }
}
/*
function OpenConsole() {
  document.getElementById("console").classList.toggle("show");
}

function OpenGeneri() {
  document.getElementById("generi").classList.toggle("show");
}

function OpenAnnoFIN() {
  document.getElementById("annofin").classList.toggle("show");
}

function OpenAnnoIN() {
  document.getElementById("annoin").classList.toggle("show");
}

function OpenOrdine() {
  document.getElementById("ordine").classList.toggle("show");
}
*/
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
