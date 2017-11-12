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
			var text2 = document.createTextNode("Prix option");
			label2.appendChild(text2)

			var prix = document.createElement("input"); //input prix option
			prix.setAttribute('class',"form-control");
			prix.setAttribute('type',"number");
			prix.setAttribute('name',"prix_option_js" + compteur);
			prix.setAttribute('id',"prix_option_js" + compteur);
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
			place.setAttribute('name',"nb_place_option_js" + compteur);
			place.setAttribute('id',"nb_place_option_js" + compteur);
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

	// for (var iter = 2; iter<=compteur; iter++){   Marche pas pour raison inconnue
	// 	var opt = window['nom_option' + iter];
	// 	document.formulaire.opt.value = document.getElementById('nom_option_js'+iter).value;
	// }
	if (compteur>=2) { //OUI C'EST MOCHE T'AS QU'A FAIRE MIEUX
		document.formulaire.nom_option2.value = document.getElementById('nom_option_js2').value;
		document.formulaire.prix_option2.value = document.getElementById('prix_option_js2').value;
		document.formulaire.nb_place_option2.value = document.getElementById('nb_place_option_js2').value;
		if (compteur>=3){
			document.formulaire.nom_option3.value = document.getElementById('nom_option_js3').value;
			document.formulaire.prix_option3.value = document.getElementById('prix_option_js3').value;
			document.formulaire.nb_place_option3.value = document.getElementById('nb_place_option_js3').value;
			if (compteur>=4){
				document.formulaire.nom_option4.value = document.getElementById('nom_option_js4').value;
				document.formulaire.prix_option4.value = document.getElementById('prix_option_js4').value;
				document.formulaire.nb_place_option4.value = document.getElementById('nb_place_option_js4').value;
				if (compteur>=5){
					document.formulaire.nom_option5.value = document.getElementById('nom_option_js5').value;
					document.formulaire.prix_option5.value = document.getElementById('prix_option_js5').value;
					document.formulaire.nb_place_option5.value = document.getElementById('nb_place_option_js5').value;
					if (compteur>=6) {
						document.formulaire.nom_option6.value = document.getElementById('nom_option_js6').value;
						document.formulaire.prix_option6.value = document.getElementById('prix_option_js6').value;
						document.formulaire.nb_place_option6.value = document.getElementById('nb_place_option_js6').value;
						if (compteur>=7){
							document.formulaire.nom_option7.value = document.getElementById('nom_option_js7').value;
							document.formulaire.prix_option7.value = document.getElementById('prix_option_js7').value;
							document.formulaire.nb_place_option7.value = document.getElementById('nb_place_option_js7').value;
							if (compteur>=8){
								document.formulaire.nom_option8.value = document.getElementById('nom_option_js8').value;
								document.formulaire.prix_option8.value = document.getElementById('prix_option_js8').value;
								document.formulaire.nb_place_option8.value = document.getElementById('nb_place_option_js8').value;
								if (compteur>=9){
									document.formulaire.nom_option9.value = document.getElementById('nom_option_js9').value;
									document.formulaire.prix_option9.value = document.getElementById('prix_option_js9').value;
									document.formulaire.nb_place_option9.value = document.getElementById('nb_place_option_js9').value;
									if (compteur>=10){
										document.formulaire.nom_option10.value = document.getElementById('nom_option_js10').value;
										document.formulaire.place_option10.value = document.getElementById('prix_option_js10').value;
										document.formulaire.nb_place_option10.value = document.getElementById('nb_place_option_js10').value;
										
									}
								}
							}
						}
					}
				}
			}
		}
	}
}