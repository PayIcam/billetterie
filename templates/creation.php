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
						<label class="col-form-label">Prix Option</label>
						<div class="form-group col-sm-2">
							<input type="number" class="form-control" aria-describedby="sizing-addon2" name="prix_option">
						</div>
						<label class="col-form-label">Nombre de place de l'option</label> <!-- max=max nombre total et uniquement si plusieurs options-->
						<div class="form-group col-sm-2">
							<input type="number" class="form-control" aria-describedby="sizing-addon2" name="place_option">
						</div>	
		   					<button type="button" class="btn btn-outline-secondary" style="padding: 15px; text-align: center;" onclick="addForm()"><font size=12>+</font></button>
				<script>
					function addForm(){
					var f = document.createElement("form");
					f.setAttribute('method',"post");
					f.setAttribute('action',"submit.php");

					var i = document.createElement("input"); //input element, text
					i.setAttribute('type',"text");
					i.setAttribute('name',"username");

					f.appendChild(i);

					//and some more input elements here
					//and dont forget to add a submit button

					document.getElementsByTagName('body')[0].appendChild(f);
					}
				</script>
			</div>

		    
		    <br>
		    </div>
			</fieldset>
		</form>
	</div>
</container>
</body>
</html>