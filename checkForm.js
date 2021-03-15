var dettagli_form = {
    "nome": [/^([\w\s]){2,20}$/, "Inserire il nome del gioco"],
    "sviluppo": [/^([\w\s]){5,30}$/, "Inserire il nome della casa di sviluppo"],
    "pegi": [/^(3|7)$|^1(2|6|8)$/, "Possibili valori di PEGI: 3,7,12,16,18"],
    "voto": [/^([0-5]{1}|[0-4]{1}\.[1-9]{1})$/, "Voto da 0 a 5"],
    "prequel": [/^([\w\s]){2,20}$/, "Inserire il nome del prequel"],
    "sequel": [/^([\w\s]){2,20}$/, "Inserire il nome del sequel"],
    "dlc": [/^([\w\s]){2,20}$/, "Inserire il nome del dlc"],
    "data": [/./, "Data non valida"],

    "descrizione": [/.{25,}/, "Inserire la descrizione"],
    "recensione": [/.{25,}/, "Inserire la recensione"],
    "alternativo": [/^([\w\s]){0,50}$/, "Alt lungo massimo 50 caratteri"],

    "titolo": [/^([\w\s\'\,\.\"]){10,40}$/, "Inserire il titolo della notizia"],
    "testo": [/.{25,}/, "Inserire il testo della notizia"],
    "immagine": [/./, "Nessun file selezionato"],

    "nomeUtente": [/^([\w]){4,15}$/, "Inserire il nome utente"],
    "password": [ /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,20}$|^user$|^admin$/, "Inserire la password"],
    "repeatpassword": [ /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,20}$|^user$|^admin$/, "Le password non combaciano"],
    "email": [/^\w{2}\w*(\.?\w+)*@\w{2}\w*(\.?\w+)*(\.\w{2,3})+$/, "Inserire la mail"]
};

/******************* Script dalle pagine contenenti form *******************/

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
    // controlla se e' gia' presente il messaggio d'errore e lo rimuove
    var parent = input.parentNode;
    if (parent.children.length == 2) {
        parent.removeChild(parent.children[1]);
    }

    var regex = dettagli_form[input.id][0];
    var text = input.value;

    // 
    if(input.id == "immagine") {
        if (input.files.length == 0)
            return mostraMessaggio(input,false);
        else
            return mostraMessaggio(input,true);
    }
    if (input.id == "data") {
        var data = Date.parse(input.value);
        if (data > Date.UTC(1947) && data < Date.UTC(2077))
            return mostraMessaggio(input, true);
        else 
            return mostraMessaggio(input, false);
    }
    if(input.id == "repeatpassword")
        return checkPassword();


    // se il campo input e' opzionale e vuoto non manda nessun messaggio
    if (input.className === "opzionale" && !text) {
        return true;
    }

    // se il contenuto non rispetta l'espr. regolare mosta l'errore
    if (text.search(regex) != 0)
        return mostraMessaggio(input, false);
    else
        return mostraMessaggio(input, true);
}

// Controllo dell'input delle form
function validateForm() {
    var corretto = true;
    for (var key in dettagli_form) {
        var input = document.getElementById(key);
        if (input) {
            var risultato = validazioneCampo(input);
            corretto = corretto && risultato;
        }
    }
    var spuntate = checkChecked();
    corretto = corretto && spuntate;
    return corretto;
}

function mostraMessaggioCheckbox(sezioni) {
    var corretto = true;
    for(var key in sezioni) {
        sezione = document.getElementById(spuntabili[key][0]);
        if(sezione) {
            // controlla se e' gia' presente il messaggio d'errore e lo rimuove
            var parent = sezione.parentNode;
            if (parent.children.length == 2) {
                parent.removeChild(parent.children[1]);
            }

            corretto = sezioni[key];
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

var spuntabili = { 
    "genere[]" : ["labelGenere", "Selezionare almeno un genere"],
    "console[]" : ["labelConsole", "Selezionare almeno una console"],
    "tipologia" : ["labelTipologia", "Selezionare una tipologia"]
};

// Controllo che i filtri degli anni includano un intervallo temporale valido
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
    var tmp = mostraMessaggioCheckbox(sezioni);
    return tmp;
}
//listaInput[i].type === spuntabili[key][0] && 

function checkPassword() {
    var pw = document.getElementById("password").value;
    if(pw) {
        var pw2 = document.getElementById("repeatpassword");
        if (pw == pw2.value)
            return mostraMessaggio(pw2,true);
        else
            return mostraMessaggio(pw2,false);
    }
}

// Fa comparire il campo "gioco" se viene selezionata la categoria giochi in FormNotizie
function handleClick() {
    var radio = document.getElementById("handler");
    var barraDiRicerca = document.getElementById("cercaTitolo");
    if (radio)
        radio.onchange = function(event) {
            if(event.target.value == "Giochi")
                barraDiRicerca.className = "visibile";
            else
                barraDiRicerca.className = "invisibile";
        }

    // Gestisce pagina modifica notizia => uno dei radio puo' avere attributo checked="checked"
    // se lo possiede l'input 'Giochi' => rendere visibile barra di ricerca titolo
    var preSelezionato = document.getElementById("Giochi");
    if(preSelezionato.checked)
        barraDiRicerca.className = "visibile";
}

/******************* Script presenti in tutte le pagine *******************/

/* Controlla che l'input del campo input con id='id' non sia vuoto o composto da spazi */
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