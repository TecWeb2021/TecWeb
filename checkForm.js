const dettagliForm = {
    "nome": [/^\w{1}.{0,38}\w{1}$/, "Inserire il nome del gioco"],
    "sviluppo": [/^\w{1}[\w\s]{0,28}\w{1}$/, "Inserire il nome della casa di sviluppo"],
    "pegi": [/^(3|7)$|^1(2|6|8)$/, "Inserire un valore valido"],
    "data": [/./, "Inserire una data valida"],

    "prequel": [/./, "Selezionare il prequel tra le opzioni suggerite", "listaPrequel"],
    "sequel": [/./, "Selezionare il sequel tra le opzioni suggerite", "listaSequel"],
    "barraDiRicerca": [/./, "Selezionare il gioco tra le opzioni suggerite", "listaTitoli"],

    "descrizione": [/^\w{1}.{24,}/, "Inserire la descrizione"],
    "recensione": [/^\w{1}.{24,}/, "Inserire la recensione"],
    "voto": [/^([0-5]{1}|[0-4]{1}\.[1-9]{1})$/, "Voto da 0 a 5"],

    "titolo": [/^\w{1}.{9,149}/, "Inserire il titolo della notizia"],
    "testo": [/^\w{1}.{24,}/, "Inserire il testo della notizia"],

    "immagine1": [/./, "Nessun file selezionato"],
    "immagine2": [/./, "Nessun file selezionato"],
    "alternativo1": [/^\w{1}[\w\s]{4,49}$/, "Inserire un alt valido"],
    "alternativo2": [/^\w{1}[\w\s]{4,49}$/, "Inserire un alt valido"],

    "nomeUtente": [/^[\w]{4,15}$/, "Inserire il nome utente"],
    "password": [ /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,16}$|^user$|^admin$/, "Inserire la password"],
    "repeatpassword": [ /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,16}$|^user$|^admin$/, "Le password non combaciano"],
    "email": [/^\w{2}\w*(\.?\w+)*@\w{2}\w*(\.?\w+)*(\.\w{2,3})+$/, "Inserire la mail"]
};
 
/******************* SCRIPT DELLE PAGINE CONTENENTI FORM *******************/

// Controllo dell'input delle form
function validateForm() {
    var corretto = true;
    for (var key in dettagliForm) {
        var input = document.getElementById(key);
        if (input) {
            var risultato = validateInput(input);
            corretto = corretto && risultato;
        }
    }
    var spuntate = checkChecked();
    corretto = corretto && spuntate;
    return corretto;
}

function validateInput(input) {
    // controlla se e' gia' presente il messaggio d'errore e lo rimuove
    var parent = input.parentNode;
    if (parent.children.length == 2) {
        parent.removeChild(parent.children[1]);
    }

    var regex = dettagliForm[input.id][0];
    var text = input.value;

    // se il campo input e' opzionale e vuoto non crea nessun messaggio
    if (input.className === "opzionale" && !text) {
        return true;
    }

    // controlli dei campi immagine, data e repeatpassword
    if(input.className == "immagine") {
        if (input.files.length == 0)
            return showMessage(input,false);
        else
            return showMessage(input,true);
    }
    if (input.id == "data") {
        var data = Date.parse(input.value);
        if (data > Date.UTC(1947) && data < Date.UTC(2022))
            return showMessage(input, true);
        else 
            return showMessage(input, false);
    }
    if(input.id == "repeatpassword")
        return checkPassword();

    if(input.id == "barraDiRicerca" || input.id == "prequel" || input.id == "sequel")
        return checkDataList(input, text);

    // se il contenuto non rispetta l'espr. regolare mostra l'errore
    if (text.search(regex) != 0)
        return showMessage(input, false);
    else
        return showMessage(input, true);
}

function showMessage(input, stato) {
    var elemento = document.createElement("p");
    elemento.className = "messaggi";

    if(!stato) {
        elemento.appendChild(document.createTextNode("✘ " + dettagliForm[input.id][1]));
        input.className = "erroreBox";
    } else {
        elemento.appendChild(document.createTextNode("✔"));
        input.className = "correttoBox";
    }

    var p = input.parentNode;
    p.appendChild(elemento);
    return stato;
}

function checkDataList(input, text) {
    var dataList = document.getElementById(dettagliForm[input.id][2]);

    for(var i = 0; i < dataList.options.length; i++)
        if (dataList.options[i].value == text)
            return showMessage(input,true);

    return showMessage(input ,false);
}

function checkPassword() {
    var pw = document.getElementById("password").value;
    if(pw) {
        var pw2 = document.getElementById("repeatpassword");
        if (pw == pw2.value)
            return showMessage(pw2,true);
        else
            return showMessage(pw2,false);
    }
}

const spuntabili = { 
    "genere[]" : ["labelGenere", "Selezionare almeno un genere"],
    "console[]" : ["labelConsole", "Selezionare almeno una console"],
    "tipologia" : ["labelTipologia", "Selezionare una tipologia"]
};

// Controllo dei campi checkbox e radio
function checkChecked() {
    var listaInput = document.getElementsByTagName('input');
    var sezioni = new Array();
    for (var key in spuntabili) {
        var spuntato = false;
        var i = 0;
        while (!spuntato && i < listaInput.length) {
            if (listaInput[i].name === key && listaInput[i].checked)
                spuntato = true;
            i++;
        }
        sezioni[key] = spuntato;
    }
    return showMessageCheckbox(sezioni);
}

function showMessageCheckbox(sezioni) {
    var corretto = true;
    for(var key in sezioni) {
        sezione = document.getElementById(spuntabili[key][0]);
        if(sezione) {
            // controlla se e' gia' presente il messaggio d'errore e lo rimuove
            var parent = sezione.parentNode;
            if (parent.children.length == 2) {
                parent.removeChild(parent.children[1]);
            }

            corretto = corretto && sezioni[key];
            var elemento = document.createElement("p");
            elemento.className = "messaggi";
            if(!sezioni[key])
                elemento.appendChild(document.createTextNode("✘ " + spuntabili[key][1]));
            else
                elemento.appendChild(document.createTextNode("✔"));
            var p = sezione.parentNode;
            p.appendChild(elemento);
        }
    }
    return corretto;
}

// Fa comparire il campo "Gioco trattato" se viene selezionata la categoria 'Giochi' in Tipologia notizia
function handleClick() {

    removeNoJs();

    var radio = document.getElementById("handler");
    var barraDiRicerca = document.getElementById("cercaTitolo");
    if (radio)
        radio.onchange = function(event) {
            if(event.target.value == "Giochi")
                barraDiRicerca.className = "";
            else
                barraDiRicerca.className = "invisibile";
        }

    // Gestisce il caricamento della pagina
    // se l'input 'Giochi' non possiede l'attributo checked => rende invisibile "Gioco trattato"
    var preSelezionato = document.getElementById("Giochi");
    if(!preSelezionato.checked)
        barraDiRicerca.className = "invisibile";
}

/******************* Script presenti in tutte le pagine *******************/

function removeNoJs() {
    var rootNodeEl = document.documentElement;
    if (rootNodeEl)
        rootNodeEl.className = rootNodeEl.className.replace(/(^|\s)no-js(\s|$)/, '$1');
}

/* Controlla che il campo input con id='id' non sia vuoto o composto da spazi */
function checkNotEmpty(id) {
    var input = document.getElementById(id);
    if (!input.value.search(/^\s*$/))
        return false;
    else
        return true;
}

/* Bottone menu a tendina */
function responsiveMenu() {
    var x = document.getElementById("menu");
    if (x.className === "topnav") {
      x.className += " responsive";
    } else {
      x.className = "topnav";
    }
}

/******************* Script dalla pagina giochi *******************/

var filtri = {
    "btn_console" : "console",
    "btn_generi" : "generi",
    "btn_da" : "annoin",
    "btn_a" : "annofin"
};

// Gestisce l'apertura delle tendine dei filtri all'evento onclick
function preparaFiltri() {

    removeNoJs();

    var handler = document.getElementsByClassName("container");
    if(handler)
        handler[0].onclick = function(event) {
            var open = document.getElementsByClassName("dropdown-filter show");
            for (var i = 0; i < open.length; i++) {
              open[i].classList.remove('show');
            }
            if(event.target.classList == "dropbtn")
                document.getElementById(filtri[event.target.id]).classList.toggle("show");
        }
}

// Controllo che i filtri degli anni includano un intervallo temporale valido
function checkAnni() {
    var radios = document.getElementsByTagName('input');
    var anni = new Array();
    for (var i = 0; i < radios.length; i++)
        if (radios[i].type === 'radio' && radios[i].checked) {
            anni.push(radios[i].value);       
        }

    var p = document.getElementById("categorie");
    if (p.children.length == 3) {
        p.removeChild(p.children[2]);
    }

    if(anni.length == 2)
        if(anni[0] > anni[1]) {
            var elemento = document.createElement("div");
            elemento.className = "erroriFiltri";
            elemento.appendChild(document.createTextNode("Intervallo temporale sbagliato"));
            p.appendChild(elemento);
            return false;
        }
    return true;
}