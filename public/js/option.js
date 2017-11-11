function addForm(){

	$("button").hide();//cache le bouton (provisoire, il faut afficher un bouton permettant de supprimer une option)
	// f.setAttribute('method',"post");
	// f.setAttribute('action',"submit.php");
	var divrow = document.createElement("div");//mise en page pour que ça tienne sur 1 ligne
	divrow.setAttribute('class',"form-row col-md-12");

	var div1 = document.createElement("div");
	div1.setAttribute('class',"form-group col-md-3");

	var label1 = document.createElement("label"); //input element, text
	label1.setAttribute('class',"col-form-label");
	var text1 = document.createTextNode("Nom option #");//RAJOUTER COMPTEUR D'OPTION
	label1.appendChild(text1)

	var nom = document.createElement("input"); //input nom option
	nom.setAttribute('type',"text");
	nom.setAttribute('class',"form-control");
	nom.setAttribute('name',"nom_option#");
	div1.appendChild(nom);

	var div2 = document.createElement("div");
	div2.setAttribute('class',"form-group col-sm-2");

	var label2 = document.createElement("label"); //input element, text
	label2.setAttribute('class',"col-form-label");
	var text2 = document.createTextNode("Prix option");
	label2.appendChild(text2)

	var prix = document.createElement("input"); //input prix option
	prix.setAttribute('class',"form-control");
	prix.setAttribute('type',"number");
	prix.setAttribute('name',"prix#");
	div2.appendChild(prix);

	var div3 = document.createElement("div");
	div3.setAttribute('class',"form-group col-sm-2");

	var label3 = document.createElement("label"); //input element, text
	label3.setAttribute('class',"col-form-label");
	var text3 = document.createTextNode("Nombre de place de l'option");
	label3.appendChild(text3)

	var place = document.createElement("input"); //input nombre place option
	place.setAttribute('class',"form-control");
	place.setAttribute('type',"number");
	place.setAttribute('name',"place#");
	div3.appendChild(place);

	var bouton = document.createElement("button");
	bouton.setAttribute('type',"button");
	bouton.setAttribute('class',"btn btn-outline-secondary");
	bouton.setAttribute('style',"padding: 15px; text-align: center;");
	bouton.setAttribute('onclick',"addForm()");
	var textBouton = document.createTextNode("+");//font size marche pas pour le "+" ici
	bouton.appendChild(textBouton);

	divrow.appendChild(label1);
	divrow.appendChild(div1);
	divrow.appendChild(label2);
	divrow.appendChild(div2);
	divrow.appendChild(label3);
	divrow.appendChild(div3);
	divrow.appendChild(bouton);
	div_option.appendChild(divrow);

	//and some more input elements here
	//and dont forget to add a submit button

	nom.focus();
}


function suppr_option(){

}

function send_option(){
	var x = document.getElementsByClassName("form-control");

}

function ajoutJS(){
	
var pseudo = document.getElementById("nom_option#");
var g = y + "-" + mo + "-" + d + " " + h + ":" + m + ":" + s;
//on charge la valeur dans le champ cache
document.date_g.value = pseudo;
}