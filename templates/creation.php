<?php 

global $bdd_payicam;

$mail=$_SESSION['Auth']['email'];

$select_droit=$bdd_payicam->prepare('SELECT t_fundation_fun.fun_name 
							FROM tj_usr_fun_ufu
							INNER JOIN t_fundation_fun
							ON (t_fundation_fun.fun_id = tj_usr_fun_ufu.fun_id OR tj_usr_fun_ufu.fun_id IS NULL)
							INNER JOIN ts_user_usr
							ON ts_user_usr.usr_id=tj_usr_fun_ufu.usr_id
							WHERE (ts_user_usr.usr_mail= ? AND tj_usr_fun_ufu.ufu_removed IS NULL)');

$select_droit->execute(array($mail));

$ls_nom_droit=[];

while($droit = $select_droit->fetch())
{
	$ls_nom_droit[]=$droit['fun_name'];
}

?>

<html>
<body>
	<container>
	<div class="col-md-offset-1 col-md-6">
		<form method="post" action="<?= $RouteHelper->getPathFor('enreg') ?>" id="formulaire">
			<fieldset>
			<legend>Nouveau Shotgun</legend>
				

				<div class="form-group col-md-6" id="nom">
				<label class="col-form-label">Nom du Shotgun</label>
				<input type="text" class="form-control" aria-describedby="sizing-addon2" name="nom_shotgun">
				</div>
				<br>

				<div class="col-md-10">
    			<label>Description du Shotgun</label>
    			<textarea class="form-control" id="description" rows="4" name="descr"></textarea>
  				</div>
  				<br>	
    </div> 

				<br>
				<div class="form-row col-md-12">
					<div id="div_date_debut" class="form-group col-sm-2">
						<label>Date de début</label>
					    <input id="date_debut" class="form-control" name="date_debut" type="datetime" value="<?php echo date("d-m-Y H:i:s");?>">
					</div>
					<div id="div_date_fin" class="form-group col-sm-2">
						<label>Date de fin</label>
					    <input id="date_fin" type="datetime" class="form-control" name="date_fin" value="<?php $d=strtotime("tomorrow");
					    echo date("d-m-Y H:i:s",$d); ?>">
					</div>
				</div>
				</div>
				<div id="nb_place" class="form-group col-sm-3">
					<label>Nombre de place total</label>
					<input type="number" class="form-control" name="nb_place_tot">
				</div>
				<div id="public_cible" class="form-group col-sm-3">
					<label>Public cible</label>
					<select type="select" class="form-control" id="public_cible" name="public_cible">
				    	<?php 
				    	foreach($ls_nom_droit as $nom)	
				    	{
				    		echo("<option value=".$nom.">".$nom."</option>"); 
				    	}
				    	?>
				    	
    				</select>
				</div>
				<br>
				<div class="form-row col-md-12" name="div_option" id="div_option">
					<label class="col-form-label">Nom option 1</label>
					<div class="form-group col-md-3" id="nom">
						<input type="text" class="form-control" aria-describedby="sizing-addon2" name="nom_option1" id=1>
					</div>
					<label class="col-form-label">Prix option</label>
					<div class="form-group col-sm-2">
						<input type="number" class="form-control" aria-describedby="sizing-addon2" name="prix_option1" id=1>
					</div>
					<label class="col-form-label">Nombre de place de l'option</label> <!-- max=max nombre total et uniquement si plusieurs options-->
					<div class="form-group col-sm-2">
						<input type="number" class="form-control" aria-describedby="sizing-addon2" name="nb_place_option1" id=1>
					</div>	
					<div>	<!-- inputs cachés pour stocker les valeurs des input générées par js, c'est moche mais pas moyen de les récupérer autrement -->
						<input type="hidden" name="nom_option2" id=2>
						<input type="hidden" name="prix_option2" id=2>
						<input type="hidden" name="nb_place_option2" id=2>
						<input type="hidden" name="nom_option3" id=3>
						<input type="hidden" name="prix_option3" id=3>
						<input type="hidden" name="nb_place_option3" id=3>
						<input type="hidden" name="nom_option4" id=4>
						<input type="hidden" name="prix_option4" id=4>
						<input type="hidden" name="nb_place_option4" id=4>
						<input type="hidden" name="nom_option5" id=5>
						<input type="hidden" name="prix_option5" id=5>
						<input type="hidden" name="nb_place_option5" id=5>
						<input type="hidden" name="nom_option6" id=6>
						<input type="hidden" name="prix_option6" id=6>
						<input type="hidden" name="nb_place_option6" id=6>
						<input type="hidden" name="nom_option7" id=7>
						<input type="hidden" name="prix_option7" id=7>
						<input type="hidden" name="nb_place_option7" id=7>
						<input type="hidden" name="nom_option8" id=8>
						<input type="hidden" name="prix_option8" id=8>
						<input type="hidden" name="nb_place_option8" id=8>
						<input type="hidden" name="nom_option9" id=9>
						<input type="hidden" name="prix_option9" id=9>
						<input type="hidden" name="nb_place_option9" id=9>
						<input type="hidden" name="nom_option10" id=10>
						<input type="hidden" name="prix_option10" id=10>
						<input type="hidden" name="nb_place_option10" id=10>
					</div>
	   			</div>
		    </div>
			</fieldset>	
					<div class="form-group col-sm-2">
   					<input type="button" class="btn btn-outline-secondary" id ="ajout" onclick="addForm()" value="Ajouter option"></input>
   					</div>
	<div class="form-row col-md-8">
		<input type="submit" class="btn btn-primary" value="Valider" onclick="ajoutJS()" >
		<br>
	</div>
		</form>
	</div>
</container>
</body>
</html>
