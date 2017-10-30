<html>
<body>
	<container>
	<div class="col-md-offset-1 col-md-6">
		<form method="post" action="edit_invite.php">
			<fieldset>
			<legend>Nouveau Shotgun</legend>
				

				<div class="form-group col-md-6" id="nom">
				<label class="col-form-label">Nom du Shotgun</label>
				<input type="text" class="form-control" aria-describedby="sizing-addon2" name="nom_shotgun">
				</div>
				<br>

				<div class="col-md-10">
    			<label>Description du Shotgun</label>
    			<textarea class="form-control" id="description" rows="4"></textarea>
  				</div>
  				<br>
    </div>
			    <!-- <div class="input-append date form_datetime">
			        <input size="16" type="text" value="" readonly>
			        <span class="add-on"><i class="icon-th"></i></span>
			    </div>
			     
			    <script type="text/javascript">
			        $(".form_datetime").datetimepicker({
			            format: "dd MM yyyy - hh:ii"
			        });
			    </script>    -->   

				<br>
				<div class="form-row col-md-8">
					<div id="date_debut" class="form-group col-sm-3">
						<label>Date de début</label>
					<div>
					    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
					    <script src="http://code.gijgo.com/1.5.1/js/gijgo.js" type="text/javascript"></script>
					    <link href="http://code.gijgo.com/1.5.1/css/gijgo.css" rel="stylesheet" type="text/css" />
					</div>
					    <input id="datepicker" width="276" />
					    <script>
					        $('#datepicker').datepicker({
					            uiLibrary: 'bootstrap4',
					            iconsLibrary: 'fontawesome'
					        });
					    </script>       
					</div>
					<div id="date_fin" class="form-group col-sm-3">
						<label>Date de fin</label>
					<div>
					    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
					    <script src="http://code.gijgo.com/1.5.1/js/gijgo.js" type="text/javascript"></script>
					    <link href="http://code.gijgo.com/1.5.1/css/gijgo.css" rel="stylesheet" type="text/css" />
					</div>
					    <input id="datepicker2" width="276" />
					    <script>
					        $('#datepicker2').datepicker({
					            uiLibrary: 'bootstrap4',
					            iconsLibrary: 'fontawesome'
					        });
					    </script>       
					</div>
				</div>
				<div id="nb_place" class="form-group col-sm-3">
					<label>Nombre de place total</label>
					<input type="number" class="form-control">
				</div>
				<br>
				<div class="form-row col-md-8">
					<label class="col-form-label">Nom option 1</label>
					<div class="form-group col-md-3" id="nom">
						<input type="text" class="form-control" aria-describedby="sizing-addon2" name="nom_option">
					</div>
					<label class="col-form-label">Prix option</label>
					<div class="form-group col-sm-2">
						<input type="number" class="form-control" aria-describedby="sizing-addon2" name="prix_option">
					</div>
					<label class="col-form-label">Nombre de place de l'option</label> <!-- max=max nombre total et uniquement si plusieurs options-->
					<div class="form-group col-sm-2">
						<input type="number" class="form-control" aria-describedby="sizing-addon2" name="place_option">
					</div>	
   					<button type="button" class="btn btn-outline-secondary" style="padding: 15px; text-align: center;" onclick="addForm()"><font size=12>+</font></button>
	   			</div>
				<script>//oui c'est dégueu ici mais pour l'instant c'est plus pratique on le bougera plus tard
					// var nboption = 1;
					function addForm(){

						$("button").hide();//cache le bouton (provisoire, il faut afficher un bouton permettant de supprimer une option)
						var f = document.createElement("form"); //creation nouveau formulaire (BESOIN?)
						// f.setAttribute('method',"post");
						// f.setAttribute('action',"submit.php");
						var divrow = document.createElement("div");//mise en page pour que ça tienne sur 1 ligne
						divrow.setAttribute('class',"form-row col-md-8");

						var div1 = document.createElement("div");
						div1.setAttribute('class',"form-group col-md-3");

						var label1 = document.createElement("label"); //input element, text
						label1.setAttribute('class',"col-form-label");
						var text1 = document.createTextNode("Nom option #");//RAJOUTER COMPTEUR D'OPTION
						label1.appendChild(text1)

						var nom = document.createElement("input"); //input nom option
						nom.setAttribute('type',"text");
						nom.setAttribute('class',"form-control");
						nom.setAttribute('name',"nom_option");
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
						f.appendChild(divrow);

						//and some more input elements here
						//and dont forget to add a submit button

						document.getElementsByTagName('body')[0].appendChild(f);
						nom.focus();
					}


					function suppr_option(){

					}
				</script>

		    
		    <br>
		    </div>
			</fieldset>
		</form>
	</div>
</container>
</body>
</html>