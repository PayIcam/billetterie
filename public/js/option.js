	var compteur=1;

	function addForm(){
		if (compteur<10){
			compteur++;
			var divrow = document.createElement("div");//mise en page pour que ça tienne sur 1 ligne
			divrow.setAttribute('class',"form-row col-md-12");

			var div1 = document.createElement("div");
			div1.setAttribute('class',"form-group col-md-3");

			var label1 = document.createElement("label"); //input element, text
			label1.setAttribute('class',"col-form-label");
			var text1 = document.createTextNode("Nom option " + compteur);//RAJOUTER COMPTEUR D'OPTION
			label1.appendChild(text1)

			var nom = document.createElement("input"); //input nom option
			nom.setAttribute('type',"text");
			nom.setAttribute('class',"form-control");
			nom.setAttribute('name',"nom_option_js" + compteur);
			nom.setAttribute('id',"nom_option_js" + compteur);
			div1.appendChild(nom);

			var div2 = document.createElement("div");
			div2.setAttribute('class',"form-group col-sm-2");

			var label2 = document.createElement("label"); //input element, text
			label2.setAttribute('class',"col-form-label");
			var text2 = document.createTextNode("Prix option ");
			label2.appendChild(text2)

			var prix = document.createElement("input"); //input prix option
			prix.setAttribute('class',"form-control");
			prix.setAttribute('type',"number");
			prix.setAttribute('name',"prix" + compteur);
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
			place.setAttribute('name',"place" + compteur);
			div3.appendChild(place);

			divrow.appendChild(label1);
			divrow.appendChild(div1);
			divrow.appendChild(label2);
			divrow.appendChild(div2);
			divrow.appendChild(label3);
			divrow.appendChild(div3);
			div_option.appendChild(divrow);
			nom.focus();
		}
		else{
			alert("Nombre max d'option atteint!");
		}
	}


function suppr_option(){

}

function ajoutJS(){
		alert(nom_option2.value);
		alert(nom_option_js2.value);

	for (var iter = 2; iter<=compteur; iter++){
		nom_option2.value=nom_option_js2.value;
	}
}