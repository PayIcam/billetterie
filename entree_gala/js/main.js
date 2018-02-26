// Fonction qui vide le tableau bootstrap
function vider(){
    var element = document.getElementById("tableau");
    while (element.firstChild) {
      element.removeChild(element.firstChild);
    }
}

// Envoi de la requête AJAX de recherche
function chercher(recherche) {
    var xhr = getXMLHttpRequest();
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {

            // une fois la requête AJAX effectuée, on appelle la fonction callback qui analyse le fichier xml généré et construit le tableau
            callback(xhr.responseXML);
        }
    };

    // Exécution de la requête AJAX, qui ouvre recherche.php
    xhr.open("GET", "recherche.php?recherche=" + recherche, true);
    xhr.send(null);
}

// Fonction de callback AJAX
function callback(oData) {
    var tableau = document.getElementById("tableau");

    // on vide le tableau avant de le reremplir
    vider();

    // Création des noms de cellules du tableau
    var head = tableau.createTHead();
    var ligne = head.insertRow(0);  
    var cellule = ligne.insertCell(0);
    cellule.outerHTML = "<th>Bracelet</th>";
    var cellule = ligne.insertCell(1);
    cellule.outerHTML = "<th>Prénom</th>";
    var cellule = ligne.insertCell(2);
    cellule.outerHTML = "<th>Nom</th>";
    var cellule = ligne.insertCell(3);
    cellule.outerHTML = "<th>Tickets Boisson</th>";
    var cellule = ligne.insertCell(4);
    cellule.outerHTML = "<th>Promo</th>";
    var cellule = ligne.insertCell(5);
    cellule.outerHTML = "<th>Créneau</th>";
    var cellule = ligne.insertCell(6);
    cellule.outerHTML = "<th>Nb Invités</th>";
    var cellule = ligne.insertCell(7);
    cellule.outerHTML = "<th>Inscription</th>";
    var cellule = ligne.insertCell(8);
    cellule.outerHTML = "<th>Infos</th>";
    var cellule = ligne.insertCell(9);
    cellule.outerHTML = "<th>Est entré</th>";


    // récupération des éléments row du xml
    var rows = oData.getElementsByTagName("row");
    var i;
    for(i = rows.length - 1; i >= 0; i--) {

        // Ajout d'un ligne à la fin du tableau
        var ligne = tableau.insertRow(-1);
        var cellule = ligne.insertCell(0);
        cellule.innerHTML = affichage_bracelet(rows[i].getAttribute('bracelet'));
        var cellule = ligne.insertCell(1);
        cellule.innerHTML = rows[i].getAttribute('prenom');
        var cellule = ligne.insertCell(2);
        cellule.innerHTML = rows[i].getAttribute('nom');
        var cellule = ligne.insertCell(3);
        cellule.innerHTML = affichage_tickets_boisson(rows[i].getAttribute('tickets_boisson'));
        var cellule = ligne.insertCell(4);
        cellule.innerHTML = rows[i].getAttribute('promo');
        var cellule = ligne.insertCell(5);
        cellule.innerHTML = affichage_creneau(rows[i].getAttribute('creneau'));
        var cellule = ligne.insertCell(6);
        cellule.innerHTML = rows[i].getAttribute('nb_invites');
        var cellule = ligne.insertCell(7);
        cellule.innerHTML = rows[i].getAttribute('inscription');
        var cellule = ligne.insertCell(8);

        // Création d'un modal bootstrap contenant les invités et la participation conférence/repas
        var modal = `
    <button role="button" href="#infoModal` + i + `"   id="infoBtn"   data-toggle="modal" class="btn btn-sm btn-info">ℹ</button>

    <div class="modal fade" id="infoModal` + i + `">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                      <h1 class="text-center">` + rows[i].getAttribute('prenom') + " " + rows[i].getAttribute('nom') + `</h1></br>
                      <p>` + rows[i].getAttribute('table_title') + `</p>
                      <table class="table table-striped" id="tblGrid">
                        <thead id="tblHead">
                          <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                          </tr>
                        </thead>
                        <tbody>`;

        for(j = (rows[i].getAttribute('invites').split(";").length - 1); j > 0; j--){
            modal += `
                <tr><td>` + rows[i].getAttribute('invites').split(";")[j - 1].split(":")[1] + `</td>
                <td>` + rows[i].getAttribute('invites').split(";")[j - 1].split(":")[0] + `</td>
                </tr>`;
        }

        modal += `
                        </tbody>
                      </table>
                      <p>` + rows[i].getAttribute('repas') + `</p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-primary " data-dismiss="modal">Fermer</button>
                    </div>
                        
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->`;

        cellule.innerHTML = modal;
    
        var cellule = ligne.insertCell(9);
        cellule.innerHTML = affichage_est_arrive(rows[i].getAttribute('est_arrive'), rows[i].getAttribute('id'));
    }

    // mise a jour des compteurs
    document.getElementById("nb_invites").innerHTML = oData.getElementsByTagName("nb_result")[0].getAttribute('name');
    document.getElementById("nb_entrees").innerHTML = oData.getElementsByTagName("nb_entrees")[0].getAttribute('name');
}

// Envoi de la requête AJAX lors de l'appui sur le bouton est_entre
function entree(id, arrived) {
    var xhr = getXMLHttpRequest();
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {

            // Une fois la bdd modifiée, on rafraichi le tableau en exécutant la recherche
            chercher(document.getElementById("recherche").value);
        }
    };

    // envoie de la requete AJAX, exécution de entree.php
    requete = "entree.php?id=" + id + "&arrived=" + arrived;
    xhr.open("GET", requete, true);
    xhr.send(null);
}

// ajout de 0 devant le numero de bracelet
function add_zeros(number){
    if(number == 0){
        return "0000";
    }else if(number < 10){
        return "000" + number;
    }else if(number < 100){
        return "00" + number;
    }else if(number < 1000){
        return "0" + number;
    }else{
        return "" + number;
    }
}

// définition des couleurs de bracelet selon son id
function affichage_bracelet(id){
    if(id == 0){
        return "<span class='badge badge-warning'>" + add_zeros(id) + "</span>";
    }else if(id <= 1050){
        return "<span class='badge badge-primary'>" + add_zeros(id) + "</span>";
    }else if(id <= 1900){
        return "<span class='badge badge-success'>" + add_zeros(id) + "</span>";
    }else if(id <= 2850){
        return "<span class='badge badge-danger'>"  + add_zeros(id) + "</span>";
    }
    return "<span class='badge badge-warning'>"     + add_zeros(id) + "</span>";
}

// définition des couleurs de span pour les tickets boissons
function affichage_tickets_boisson(nb){
    if(nb == 0){
        return "<span class='badge badge-danger'>"  + nb + "</span>";
    }else if(nb == 10){
        return "<span class='badge badge-success'>" + nb + "</span>";
    }else if(nb == 20){
        return "<span class='badge badge-warning'>" + nb + "</span>";
    }else if(nb == 30){
        return "<span class='badge badge-info'>"    + nb + "</span>";
    }else if(nb == 40){
        return "<span class='badge badge-default'>" + nb + "</span>";
    }
    return "<span class='badge badge-primary'>"     + nb + "</span>";
}

// définition de la couleur du créneau et changement des noms
function affichage_creneau(creneau){
    if(creneau == '21h-21h45'){
        return "<span class='badge badge-primary'>21h-21h35 </span>";
    }else if(creneau == '21h45-22h30'){
        return "<span class='badge badge-danger'>21h50-22h25 </span>";
    }else if(creneau == '22h30-23h'){
        return "<span class='badge badge-success'>22h40-23h10 </span>";
    }
    return "";
}

// définition du bouton est arrivé
function affichage_est_arrive(est_arrive, id){
    if(est_arrive == 0){
        return  `<button role="button" class="btn btn-sm btn-danger" onclick="entree(` + id + `, 1);">✘</button>`;
    }
    return `<button role="button" class="btn btn-sm btn-success" onclick="entree(` + id + `, 0);">✔</button>`;    
}

chercher(document.getElementById("recherche").value);