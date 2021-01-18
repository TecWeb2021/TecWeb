var dettagli_form = {
    "titolo": ["Titolo", /^([\w\s]){2,20}$/, "Inserire il titolo del gioco"],
    "anno": ["Anno di Rilascio", /^(19|20)\d{2}$/, "Inserire l'anno di rilascio del gioco"],
    "descrizione": ["Descrizione del gioco", /.{25,}/, "Inserire la descrizione"],
    "recensione": ["Recensione del gioco", /.{25,}/, "Inserire la recensione"],
    "alternativo": ["testo alternativo", /^([\w\s]){0,50}$/, "Inserire il testo alternativo dell'immagine"],

    "titoloNews": ["Titolo", /^([\w\s\'\,\.\"]){10,40}$/, "Inserire il titolo della notizia"],
    "testo": ["Testo della notizia", /.{25,}/, "Inserire il testo della notizia"],
    "searchbar": ["Cerca gioco...", /./, ""],
    "searchnews": ["Cerca notizia...", /./, ""]
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
/*
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
*/
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

function caricamento(elementi) {
    carica_placeholder();
    /*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
    autocomplete(document.getElementById("searchbar"), elementi);
}

function autocomplete(inp, arr) {
    /*the autocomplete function takes two arguments,
    the text field element and an array of possible autocompleted values:*/
    var currentFocus;
    /*execute a function when someone writes in the text field:*/
    inp.addEventListener("input", function(e) {
        var a, b, i, val = this.value;
        /*close any already open lists of autocompleted values*/
        closeAllLists();
        if (!val) { return false;}
        currentFocus = -1;
        /*create a DIV element that will contain the items (values):*/
        a = document.createElement("DIV");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        /*append the DIV element as a child of the autocomplete container:*/
        this.parentNode.appendChild(a);
        /*for each item in the array...*/
        for (i = 0; i < arr.length; i++) {
          /*check if the item starts with the same letters as the text field value:*/
          if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
            /*create a DIV element for each matching element:*/
            b = document.createElement("DIV");
            /*make the matching letters bold:*/
            b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
            b.innerHTML += arr[i].substr(val.length);
            /*insert a input field that will hold the current array item's value:*/
            b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
            /*execute a function when someone clicks on the item value (DIV element):*/
            b.addEventListener("click", function(e) {
                /*insert the value for the autocomplete text field:*/
                inp.value = this.getElementsByTagName("input")[0].value;
                /*close the list of autocompleted values,
                (or any other open lists of autocompleted values:*/
                closeAllLists();
            });
            a.appendChild(b);
          }
        }
    });
    /*execute a function presses a key on the keyboard:*/
    inp.addEventListener("keydown", function(e) {
        var x = document.getElementById(this.id + "autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
        if (e.keyCode == 40) {
          /*If the arrow DOWN key is pressed,
          increase the currentFocus variable:*/
          currentFocus++;
          /*and and make the current item more visible:*/
          addActive(x);
        } else if (e.keyCode == 38) { //up
          /*If the arrow UP key is pressed,
          decrease the currentFocus variable:*/
          currentFocus--;
          /*and and make the current item more visible:*/
          addActive(x);
        } else if (e.keyCode == 13) {
          /*If the ENTER key is pressed, prevent the form from being submitted,*/
          e.preventDefault();
          if (currentFocus > -1) {
            /*and simulate a click on the "active" item:*/
            if (x) x[currentFocus].click();
          }
        }
    });
    function addActive(x) {
      /*a function to classify an item as "active":*/
      if (!x) return false;
      /*start by removing the "active" class on all items:*/
      removeActive(x);
      if (currentFocus >= x.length) currentFocus = 0;
      if (currentFocus < 0) currentFocus = (x.length - 1);
      /*add class "autocomplete-active":*/
      x[currentFocus].classList.add("autocomplete-active");
    }
    function removeActive(x) {
      /*a function to remove the "active" class from all autocomplete items:*/
      for (var i = 0; i < x.length; i++) {
        x[i].classList.remove("autocomplete-active");
      }
    }
    function closeAllLists(elmnt) {
      /*close all autocomplete lists in the document,
      except the one passed as an argument:*/
      var x = document.getElementsByClassName("autocomplete-items");
      for (var i = 0; i < x.length; i++) {
        if (elmnt != x[i] && elmnt != inp) {
          x[i].parentNode.removeChild(x[i]);
        }
      }
    }
    /*execute a function when someone clicks in the document:*/
    document.addEventListener("click", function (e) {
        closeAllLists(e.target);
    });
  }