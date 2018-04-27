/**
 * La fonction qui appelle toutes les autres
 */
function initialisation_inscriptions()
{
    $("form")[0].reset();

    filling_form_behaviour();

    $('.guest_options').find('input, select').attr('disabled', 'true');

    try//Si une option est obligatoire, il faut l'initialiser comme ceci.
    {//Mais ça "bug", si justement l'option ne l'est pas, on met donc dans un try avec un catch vide
        $(".select_option select").change();
    }
    catch(error)
    {

    }

    $("form").submit(submit_inscriptions);
    $("#message_submit").hide();

    if(typeof(ticketing_state) == "undefined")
    {
        $('form').off('submit').submit(function(submit)
        {
            console.log('nice try');
            submit.preventDefault();
        });
    }
}

/**
 * Fonction qui permet de modifier le prix en bas de page, pour le faire correspondre à ce qu'il faut
 * @param  {int} price
 * @param  {string} target ['icam' ou 'guests']
 */
function change_recap_prices(price, target)
{
    if(target == 'icam')
    {
        var previous_icam_price = parseFloat($("#icam_total_price").text());
        var new_icam_price = previous_icam_price + price;
        $("#icam_total_price").text(new_icam_price+'€');
    }
    else if(target == 'guests')
    {
        var previous_guests_price = parseFloat($("#guests_total_prices").text());
        var new_guests_price = previous_guests_price + price;
        $("#guests_total_prices").text(new_guests_price+'€');
    }
    var previous_total_price = parseFloat($("#total_price").text());
    var new_total_price = previous_total_price + price;
    $("#total_price").text(new_total_price+'€');
}


/**
 * Précise ce qu'il doit se passer quand on touche au formulaire
 * Il faut actualiser le prix en bas de page
 * Il faut débloquer les options si on commence à entrer un invité
 * Ajuster son nom juste au dessus, c'est plus joli
 * etc ...
 */
function filling_form_behaviour()
{
    //Quand on entre quelque chose dans le nom ou le prénom des invités
    $("#registration_guests .guest_form input[class='form-control guest_firstname'], #registration_guests .guest_form input[class='form-control guest_lastname']").on("keyup change", function()
    {
        var prenom = $(this).parents('.guest_inputs').find('.guest_firstname').val();
        var nom = $(this).parents('.guest_inputs').find('.guest_lastname').val();

        if(!(prenom == '' && nom == ''))
        {
            $(this).parents('.guest_form').find('.actual_guest_title').text(prenom + " " + nom); //On affiche le bon nom juste au dessus
            if(!$(this).parents('.guest_form').find('.event_price').hasClass('already_counted')) //Si on l'a pas déjà fait
            {
                $(this).parents('.guest_form').find('.guest_options').find('input, select').removeAttr('disabled'); //On permet de toucher aux options
                $(this).parents('.guest_form').find('.event_price').attr('style', 'background-color: #468847'); //On met le prix de la bonne couleur, pour montrer qu'on est en train de prendre l'event
                $(this).parents('.guest_form').find('select').change();

                var guest_price = parseFloat($(this).parents('.guest_form').find('.event_price').text());
                change_recap_prices(guest_price, 'guests');//On change le récap des prix

                $(this).parents('.guest_form').find('.event_price').addClass('already_counted');//On ajoute la classe already_counted pour ne plus refaire ça 10000 fois
            }
        }
        else
        {
            if($(this).parents('.guest_form').find('.event_price').hasClass('already_counted'))//Si il était compté, c'est que le champ n'était pas vide avant, et qu'il faut faire du changement
            {
                $(this).parents('.guest_form').find('select').trigger('remove_price');//On trigger le remove_price pour remettre le bon prix
                $(this).parents('.guest_form').find('.guest_options').find('input, select').attr('disabled', 'true');//On disable les options
                $(this).parents('.guest_form').find('.guest_options').find('input:checkbox:checked').each(function()
                {
                    $(this).prop('checked', false);
                    $(this).change();//On trigger le change des checkbox pour que les prix se mettent à jour
                });
                $(this).parents('.guest_form').find('.event_price').attr('style', 'background-color: #b94a48');//On remet le badge en rouge
                $(this).parents('.guest_form').find('.actual_guest_title').text($(this).parents(".guest_informations").find(".guest_title_default_text").text());//On remet un nom du genre Invité 3
                var guest_price = parseFloat($(this).parents('.guest_form').find('.event_price').text());
                change_recap_prices(-guest_price, 'guests');//On remet le bon recap
                $(this).parents('.guest_form').find('.event_price').removeClass('already_counted');//On enlève la classe already_counted
            }
        }
    });

    $(".checkbox_option input:checkbox").change(function()
    {
        var option_price = parseFloat($(this).siblings("label").find(".checkbox_price").text());
        if($(this).is(":checked"))//Si ça devient checked
        {
            $(this).siblings("label").find(".checkbox_price").attr("style", "background-color: #468847");
            if($(this).closest(".container").attr("id")=='registration_icam')
            {
                change_recap_prices(option_price, 'icam');//On ajuste le prix, et on met en vert le prix
            }
            else if($(this).closest(".container").attr("id")=='registration_guests')
            {
                change_recap_prices(option_price, 'guests');
            }
        }
        else
        {//Sinon
            $(this).siblings("label").find(".checkbox_price").addClass('badge-info');
            if($(this).closest(".container").attr("id")=='registration_icam')
            {
                change_recap_prices(-option_price, 'icam');//On remet le prix de l'autre couleur, et on l'enlève du recap
            }
            else if($(this).closest(".container").attr("id")=='registration_guests')
            {
                change_recap_prices(-option_price, 'guests');
            }
        }
    });

    $(".select_option select").change(function()//Même genre ici
    {
        if(!$(this).is(":disabled"))
        {
            var previous_price = parseFloat($(this).parents(".select_option").find(".select_price").text());
            previous_price = isNaN(previous_price) ? 0 : previous_price;
            var option_text = $.trim($(this).find('option:selected').text());
            var regExp = /\(([0-9]+€)\)$/;
            try
            {
                var new_price = parseFloat(regExp.exec(option_text)[1]);
            }
            catch(err)
            {
                new_price = 0;
            }

            $(this).parents(".select_option").find(".select_price").text(new_price+'€');

            if($(this).closest(".container").attr("id")=='registration_icam')
            {
                change_recap_prices(new_price-previous_price, 'icam');
            }
            else if($(this).closest(".container").attr("id")=='registration_guests')
            {
                change_recap_prices(new_price-previous_price, 'guests');
            }

            $(this).on('remove_price', function()
            {
                var previous_price = parseFloat($(this).parents(".select_option").find(".select_price").text());
                previous_price = isNaN(previous_price) ? 0 : previous_price;
                $(this).parents(".select_option").find(".select_price").text('');
                change_recap_prices(-previous_price, 'guests');
                $(this).off('remove_price');
            });
        }
    });
}

/**
 * Fonction qui permet de déterminer si les urls des forms sont bonnes ou non
 * Elle affiche une erreur et cancel le submit si c'est le cas
 * Vilain utilisateur qui veux faire n'importe quoi en frontend
 *
 * Renvoie true si les urls sont bonnes false sinon
 * @return {boolean}
 */
function check_urls(add_alert)
{
    var current_path = window.location.pathname;
    var action_url = $('form').prop('action');

    if(current_path == base_path + "inscriptions/inscriptions.php")
    {
        if(action_url != public_url + "inscriptions/php/ajout_reservation.php?event_id=" + event_id)
        {
            add_alert("A quoi joues tu ? Arrète de trafiquer les redirections.");
            return false;
        }
    }
    else if(current_path == base_path + "inscriptions/edit_reservation.php")
    {
        if(action_url != public_url + "inscriptions/php/edition_reservation.php?event_id=" + event_id)
        {
            add_alert("A quoi joues tu ? Arrète de trafiquer les redirections.");
            return false;
        }
    }
    return true;
}