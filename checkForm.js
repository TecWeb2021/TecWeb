var dettagli_form = {
    "nome": [/^([\w\s]){2,20}$/, "Inserire il nome del gioco"],
    "sviluppo": [/^([\w\s]){5,30}$/, "Inserire il nome della casa di sviluppo"],
    "pegi": [/^(3|7)$|^1(2|6|8)$/, "Possibili valori di PEGI: 3, 7, 12, 16, 18"],
    "prequel": [/^([\w\s]){2,20}$/, "Inserire il nome del prequel"],
    "sequel": [/^([\w\s]){2,20}$/, "Inserire il nome del sequel"],
    "dlc": [/^([\w\s]){2,20}$/, "Inserire il nome del dlc"],

    "descrizione": [/.{25,}/, "Inserire la descrizione"],
    "recensione": [/.{25,}/, "Inserire la recensione"],
    "alternativo": [/^([\w\s]){0,50}$/, "Alt lungo massimo 50 caratteri"],

    "titolo": [/^([\w\s\'\,\.\"]){10,40}$/, "Inserire il titolo della notizia"],
    "testo": [/.{25,}/, "Inserire il testo della notizia"],


    "immagine": [/./, "Nessun file selezionato"],


    "nomeUtente": [/^([\w\s]){5,15}$/, "Inserire il nome utente"],
    "password": [ /^(?=.*[0-9])(?=.*[a-z])[a-zA-Z0-9!.@#$%^&*]{8,16}$/, "Inserire la password"],
    "repeatpassword": [ /^(?=.*[0-9])(?=.*[a-z])[a-zA-Z0-9!.@#$%^&*]{8,16}$/, ""],
    "email": [/^\w+(\.?\w+)*@\w+(\.?\w+)*(\.\w{2,3})+$/, "Inserire la mail"]
};

function mostraMessaggio(input, stato) {
    var elemento = document.createElement("p");
    elemento.className = "messaggi";

    if(!stato) {
        elemento.appendChild(document.createTextNode("✘ " + dettagli_form[input.id][1]));
        input.className = "erroreBox";
    } else {
        elemento.appendChild(document.createTextNode("✔"));
        input.className = "correttoBox";
    }

    var p = input.parentNode;
    p.appendChild(elemento);
    return stato;
}

function validazioneCampo(input) {

    // controlla se e' gia' presente messaggio d'errore e lo rimuove
    var parent = input.parentNode;
    if (parent.children.length == 2) {
        parent.removeChild(parent.children[1]);
    }

    var regex = dettagli_form[input.id][0];
    var text = input.value;

    if(input.id == "immagine") {
        if (input.files.length == 0)
            return mostraMessaggio(input,false);
        else
            return mostraMessaggio(input,true);
    }

    // se il campo input e' opzionale e vuoto non effettua il controllo
    if (input.className === "opzionale" && !text) {
        return true;
    }

    // se il contenuto e' non vuoto e non rispetta l'espr. regolare mosto errore
    if (text.search(regex) != 0) {
        // -1 se non trova il match, 0 se lo trova, k se trova il match dalla posizione k
        return mostraMessaggio(input, false);
    } else {
        // se input è corretto corretto
        return mostraMessaggio(input, true);
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

var filtri = {
    "btn_console" : "console",
    "btn_generi" : "generi",
    "btn_da" : "annoin",
    "btn_a" : "annofin"
};

function preparaFiltri() {

    var handler = document.getElementsByClassName("container");
    if(handler) {
        handler[0].onclick = function(event) {
            var open = document.getElementsByClassName("dropdown-filter show");
            for (var i = 0; i < open.length; i++) {
              open[i].classList.remove('show');
            }
            document.getElementById(filtri[event.target.id]).classList.toggle("show");
        }
    }
}

function checkAnni() {

    var radios = document.getElementsByTagName('input');
    var anni = new Array();

    for (var i = 0; i < radios.length; i++)
        if (radios[i].type === 'radio' && radios[i].checked) {
            anni.push(radios[i].value);       
        }

    if(anni.length == 2)
        if(anni[0] > anni[1]) {
            var elemento = document.createElement("div");
            elemento.className = "erroriFiltri";
            elemento.appendChild(document.createTextNode("Intervallo temporale sbagliato"));
        
            var p = document.getElementById("categorie");
            p.appendChild(elemento);
            return false;
        }
    return true;
}