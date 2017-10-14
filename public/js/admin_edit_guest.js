(function($){
	// ---------- Variables propres au formulaire ---------- //
	var nom    = $( "#inputnom" );
	var prenom = $( "#inputprenom" );
	var promo  = $( "#selectpromo" );
	var sexe   = $( "#selectsexe" );
	var email  = $( "#inputemail" );
	var form   = $( "form" );

	// ------------------------------ Fonctions pour les requetes Ajax ------------------------------ //
	var xhr = new Array();

	function checkXhr(xhrName){
		if(xhr[xhrName]){
			xhr[xhrName].abort();
			delete xhr[xhrName];
		}
	}

	// ------------------------------ Fonctions pour vérifier le numéro de bracelet ------------------------------ //

	function IscheckboxName (inputName,checkboxName) { // jQuery.inArray(checkboxName, checkboxNamesRepas) >= 0

		if ( inputName == checkboxName )
			return true;
		else if (inputName == "repas" && (checkboxName == "repas" || checkboxName.match(/guests\[([0-9]+)\]\[repas\]/)!= null))
			return true;
		else if (inputName == "buffet" && (checkboxName == "buffet" || checkboxName.match(/guests\[([0-9]+)\]\[buffet\]/) != null))
			return true;
		else if (inputName == "bracelet_id" && (checkboxName == "bracelet_id" || checkboxName.match(/guests\[([0-9]+)\]\[bracelet_id\]/) != null))
			return true;
		// else if (checkboxName.match(/^guests\[[0-9]+\]\[" + inputName + "\]/))
		// 	return true;
		else
			return false;
	}

	/**
	 * Une personne peut aller soit au buffet ou soit au repas ou à aucun des deux.
	 */
	function checkCheckbox(checkbox) {
		// var checkboxName = checkbox.attr("name");
		// if(checkbox.is (':checked')){
		// 	if (IscheckboxName('repas',checkboxName)) {
		// 		var autreCheckBox = $(':checkbox[name="'+checkboxName.replace('repas', 'buffet')+'"]');
		// 	} else if(IscheckboxName('buffet',checkboxName)){
		// 		var autreCheckBox = $(':checkbox[name="'+checkboxName.replace('buffet', 'repas')+'"]');
		// 	};
		// 	if (autreCheckBox.is(':checked')) {
		// 		autreCheckBox.removeAttr('checked');
		// 	};
		// }
	    return $(this);
	}

	function checkBraceletNumber(input) {
		var checkboxName = input.attr('name');
		if (IscheckboxName('repas',checkboxName)) {
			var repas_checkbox   = input;
			var buffet_checkbox  = $('input:checkbox[name="'+checkboxName.replace('repas', 'buffet')+'"]');
			var bracelet_idInput = $('input[name="'+checkboxName.replace('repas', 'bracelet_id')+'"]');
			var guestId          = $('input[name="'+checkboxName.replace('repas', 'id')+'"]').val();
			var guestId2          = $('input[name="'+checkboxName.replace('repas', 'id')+'"]');
		} else if(IscheckboxName('buffet',checkboxName)){
			var repas_checkbox   = $('input:checkbox[name="'+checkboxName.replace('buffet', 'repas')+'"]');
			var buffet_checkbox  = input;
			var bracelet_idInput = $('input[name="'+checkboxName.replace('buffet', 'bracelet_id')+'"]');
			var guestId          = $('input[name="'+checkboxName.replace('buffet', 'id')+'"]').val();
			var guestId2          = $('input[name="'+checkboxName.replace('buffet', 'id')+'"]');
		} else if(IscheckboxName('bracelet_id',checkboxName)){
			var repas_checkbox   = $('input:checkbox[name="'+checkboxName.replace('bracelet_id', 'repas')+'"]');
			var buffet_checkbox  = $('input:checkbox[name="'+checkboxName.replace('bracelet_id', 'buffet')+'"]');
			var bracelet_idInput = input;
			var guestId          = $('input[name="'+checkboxName.replace('bracelet_id', 'id')+'"]').val();
			var guestId2          = $('input[name="'+checkboxName.replace('bracelet_id', 'id')+'"]');
		}

		var bracelet_id = bracelet_idInput.val();
		// if (promo.val() == "113"){
			bracelet_idInput.parent().parent().removeClass('error');
			bracelet_idInput.next().remove();
			braceletNbExists(bracelet_id,guestId,bracelet_idInput);
		// }else if (repas_checkbox.is(':checked') || buffet_checkbox.is(':checked')){
			// if (bracelet_id > 300) {
				// bracelet_idInput.parent().parent().addClass('error');
				// bracelet_idInput.next().remove();
				// bracelet_idInput.parent().append('<span class="help-inline">Les numéros de bracelet doivent être <= 300<br> pour le repas ou buffet</span>');
			// }else if(bracelet_id > 0 && bracelet_id <= 300){
				// bracelet_idInput.parent().parent().removeClass('error');
				// bracelet_idInput.next().remove();
				// braceletNbExists(bracelet_id,guestId,bracelet_idInput);
			// }else{
				// bracelet_idInput.parent().parent().addClass('error');
				// bracelet_idInput.next().remove();
				// bracelet_idInput.parent().append('<span class="help-inline">Erreur du numero</span>');
			// };
		// }else if(!repas_checkbox.is(':checked') && !buffet_checkbox.is(':checked')){
			// if (bracelet_id <= 300) {
				// bracelet_idInput.parent().parent().addClass('error');
				// bracelet_idInput.next().remove();
				// bracelet_idInput.parent().append('<span class="help-inline">Les numéros de bracelet doivent être > 300<br> pour la soirée uniquement</span>');
			// }else{
				// bracelet_idInput.parent().parent().removeClass('error');
				// bracelet_idInput.next().remove();
				// braceletNbExists(bracelet_id,guestId,bracelet_idInput);
			// };
		// };
	}

	/**
	 * Une personne peut aller soit au buffet ou soit au repas ou à aucun des deux.
	 */
	function braceletNbExists(numero,guestId,bracelet_idInput) {
		bracelet_idInput.next('help-inline').remove();
		if (numero == '' || numero == 0) { return false;};
		bracelet_idInput.parent().append('<small class="loader" style="margin-left:10px;"><img src="img/icons/spinner.gif" alt="loader"></small>');
		checkXhr(guestId);
		xhr[guestId] = $.ajax({
			type : "POST",
			url : "verifier_guest",
			data : 'bracelet_id='+numero+'&id='+guestId,
			success: function(server_response){
				console.log(server_response);
			  	if (server_response == '0') {
			  		bracelet_idInput.parent().parent().removeClass('error');
					bracelet_idInput.next().remove();
			  	}else{
			  		bracelet_idInput.parent().parent().addClass('error');
					bracelet_idInput.next().remove();
			  	};
			  	$('.loader').fadeOut(500,function(event){$(this).remove();});
			}
		});
	}

	// ------------------------------ Varification du numéro de bracelet ------------------------------ //

	/**
	 * Une personne peut aller soit au buffet ou soit au repas ou à aucun des deux.
	 */
	// Initialisation
	$('input[type="checkbox"]').each(function(event) {
		checkCheckbox($(this));
		checkBraceletNumber($(this));
	});
	// Vérifier à chaque fois que l'on clique sur une checkbox
	$('input[type="checkbox"]').click(function(event) {
		checkCheckbox($(this));
		checkBraceletNumber($(this));
	});
	// Vérifier à chaque fois que l'on édite le numero
	$('input.bracelet_id').keyup(function(event) {
		$(this).each(function(event) {
			checkBraceletNumber($(this));
		});
	});

	// ------------------------------ Vérification de la promo pour les 114 & 115 ------------------------------ //

	/**
	 * Afficher ou cacher le formulaire pour un deuxième invité.
	 */

	var guests = $('.guest');
	var guest1 = $('#guest1');
	var guest2 = $('#guest2');
	var guest3 = $('#guest3');
	// var guest4 = $('#guest4');
	// var guest5 = $('#guest5');
	// var guest6 = $('#guest6');
	// var guest7 = $('#guest7');
	// var guest8 = $('#guest8');
	// var guest9 = $('#guest9');
	// var guest10 = $('#guest10');
	function checkPromoGuestFields () {
		// var promoVal = promo.val();
		// guests.each(function(index, elem) {
		// 	valInputNom = $('input[name="guests['+index+'][nom]"]').val();
		// 	if (valInputNom == undefined || valInputNom == "") {
		// 		$(elem).hide();
		// 	};
		// });
		// if (promoVal == 'Artiste Icam') {guest1.fadeIn();guest2.fadeIn();guest3.fadeIn();}
		// else if (promoVal == '119') {guest1.fadeIn();guest2.fadeIn();guest3.fadeIn();/*guest4.fadeIn();guest5.fadeIn();guest6.fadeIn();guest7.fadeIn();guest8.fadeIn();guest9.fadeIn();guest10.fadeIn();*/}
		// else if (promoVal == '118' || promoVal == 'Artiste Icam' || promoVal == '117') {guest1.fadeIn();guest2.fadeIn();}
		// else{guest1.fadeIn();};
	}
	checkPromoGuestFields ();
	promo.change(function(event) {checkPromoGuestFields ();});

	// ------------------------------ Fonctions pour vérifier l'email ------------------------------ //

	function checkEmailChange () {
		var promoVal  = promo.val().trim();
		var prenomVal = prenom.val().trim();
		var nomVal    = nom.val().trim();
		var emailVal  = email.val().trim();
		if (((promoVal <= 121 && promoVal >= 116) || (promoVal <= 2021 && promoVal >= 2017) || promoVal == "Permanent" || promoVal == "permanent")
			&& nomVal != '' && prenomVal != ''
			&& (emailVal == '' || emailVal.search('icam.fr') > 0 )
		){
			var promoNum = (promoVal <= 121 && promoVal >= 116)?(1900+promoVal*1)+".":((promoVal <= 2021 && promoVal >= 2017)?promoVal+".":"");
			var emailValue = emailEncode((prenom.val()+"."+nom.val()+"@"+promoNum+"icam.fr"));
			email.val(emailValue);
		}else{
			// email.val('');
		}
		email.checkEmail();
	}

	/**
	 * retirer les caractères interdits dans une addresse email
	 */
	function emailEncode(email){
		var returnEmail = email.toLowerCase();
		returnEmail = returnEmail.replace(/[éèêë]/g,'e');
		returnEmail = returnEmail.replace(/[àâä]/g,'a');
		returnEmail = returnEmail.replace(/[ïî]/g,'i');
		returnEmail = returnEmail.replace(/[öô]/g,'o');
		returnEmail = returnEmail.replace(/[ç]/g,'c');
		returnEmail = returnEmail.replace(/[ \']/g,'-');
		return returnEmail;
	}

	/**
	 * Vérifier l'intégrité de l'addresse email
	 */
	jQuery.fn.checkEmail = function() {
	    var o = $(this[0]);
	    var emailRegex = '^[a-z0-9._-]+@[a-z0-9.-]+\.[a-z]{2,4}$';
	    if (!o.val().match(emailRegex)) {
	    	o.parent().parent().parent().addClass('error');
	    }else{
	    	o.parent().parent().parent().removeClass('error');
	    };
	};

	// ------------------------------ Vérifications de l'email ------------------------------ //
	/**
	 * compléter le champ email tout seul si on a un numero de promo, un nom, un prénom !
	 */
	$('#inputnom,#inputprenom,#selectpromo').keyup(function(event) {
		checkEmailChange();
	});
	form.change(function(event) {
		checkEmailChange();
	});
	form.keyup(function(event) {
		email.checkEmail();
	});

	/**
	 * Helper for current Icams
	 */
	var retour = [];
	function autocompleteSelect (ui) {
		for (var i = retour.length - 1; i >= 0; i--) {
    		if (retour[i].value == ui.item.value) {
	        	var infos = retour[i];
	        	if (ui.item.value == infos.id+'-'+infos.nom) {ui.item.value = infos.nom;}
	        	else{ui.item.value = infos.prenom;};
	        	nom.val(infos.nom);
				prenom.val(infos.prenom);
				sexe.val(infos.sexe);
				if (infos.promo < 117) {promo.val("Ingenieur");} // Si il nous reste des 112 et autre Ingé ds la base..
				else{promo.val(infos.promo);};
    		};
    	};
    	checkEmailChange ();
    	checkPromoGuestFields();
	}
	function autocompleteSource (data) {
		if (data == "" || data == null || data.length == 0) {return [];} else{
	    	retour = data;
	    	var retourajax = [];
	    	for (var i = data.length - 1; i >= 0; i--) {
	    		retourajax[i] = {"value":data[i].value,"label":data[i].label};
	    	};
	    	return retourajax ;
		};
	}
    nom.autocomplete({
        source: function( request, response ) {
        	checkXhr('inputnom');
			xhr['inputnom'] = $.ajax({
                url: "resultat_guest",
                dataType: "json",
                data: { nom: nom.val(), prenom: prenom.val() },
                success: function( data ) { response( autocompleteSource(data) ); }
            });
        },
        minLength: 2,
        select: function( event, ui ) {autocompleteSelect (ui)}
    });
	prenom.autocomplete({
        source: function( request, response ) {
            checkXhr('inputprenom');
			xhr['inputprenom'] = $.ajax({
                url: "resultat_guest",
                dataType: "json",
                data: { nom: nom.val(), prenom: prenom.val(), prenomFirst : 1},
                success: function( data ) { response( autocompleteSource(data) ); }
            });
        },
        minLength: 2,
        select: function( event, ui ) {autocompleteSelect (ui)}
    });

})(jQuery);
