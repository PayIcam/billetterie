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
		
	<div class="container">
    <div class="row">
        <div class='col-sm-6'>
            <div class="form-group">
                <div class='input-group date' id='datetimepicker1'>
                    <input type='text' class="form-control" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(function () {
                $('#datetimepicker1').datetimepicker();
            });
        </script>
    </div>
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
	<div class="form-row col-md-12">
		<div id="date_debut" class="form-group col-sm-3">
			<label>Date de début</label>
			<input type="date" class="form-datetime">
		</div>
	    

		<div id="date_fin" class="form-group col-sm-3">
			<label>Date de fin</label>
			<input type="date" class="form-datetime">           
		</div>
	</div>
	<div id="nb_place" class="form-group col-sm-3">
		<label>Nombre de place total</label>
		<input type="number" class="form-control">
	</div>
	<br>
	<label class="col-form-label">Nom option 1</label>
	<div class="form-row">
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

	</div>

<br>
</div>
</fieldset>