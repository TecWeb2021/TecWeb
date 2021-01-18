var dettagli_form = {
    "titolo": ["Titolo", /^([\w\s]){2,20}$/, "Inserire il titolo del gioco"],
    "anno": ["Anno di Rilascio", /^(19|20)\d{2}$/, "Inserire l'anno di rilascio del gioco"],
    "descrizione": ["Descrizione del gioco", /.{25,}/, "Inserire la descrizione"],
    "recensione": ["Recensione del gioco", /.{25,}/, "Inserire la recensione"],
    "alternativo": ["testo alternativo", /^([\w\s]){0,50}$/, "Inserire il testo alternativo dell'immagine"],

    "titoloNews": ["Titolo", /^([\w\s\'\,\.\"]){10,40}$/, "Inserire il titolo della notizia"],
    "testo": ["Testo della notizia", /.{25,}/, "Inserire il testo della notizia"]
};

var buttons = [
    "annullaModifiche",
    "elimina"
];

function campoDefault(input) {
    input.className = "deftext";
    input.value = dettagli_form[input.id][0];
}

function campoPerInput(input) {

    if (input.value == dettagli_form[input.id][0]) {
        input.value = "";
        input.className = "";
    }
}

function backToDefault(input) {
    if (!input.value.search(/^$/)) {
        campoDefault(input);
    }
}

function caricamento() {

    for (var key in dettagli_form) {
        var input = document.getElementById(key);
        if (input) {
            campoDefault(input);
            input.onfocus = function() {campoPerInput(this);};
            input.onblur = function() {backToDefault(this);};
        }
    }
    
}

function mostraErrore(input) {
    var elemento = document.createElement("strong");
    elemento.className = "errori";
    elemento.appendChild(document.createTextNode(dettagli_form[input.id][2]));

    var p = input.parentNode;
    p.appendChild(elemento);
}

function validazioneCampo(input) {
    
    // controllo se e' gia' presente messaggio d'errore
    var parent = input.parentNode;
    if (parent.children.length == 2) {
        parent.removeChild(parent.children[1]);
    }

    var regex = dettagli_form[input.id][1];
    var text = input.value;
    if (text.search(regex) != 0) { 
        // -1 se non trova il match, 0 se lo trova, 6 (es) se trova il match dalla posizione 6
        mostraErrore(input);
        
        return false;
    }
    else {
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
