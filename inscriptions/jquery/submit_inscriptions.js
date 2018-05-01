/**
 * Cette page contient la fonction à appeler au moment de submit le formulaire.
 * Le principe est le même que dans toutes mes pages, notamment dans event_administration ou je suis rentré dans les détails.
 * - Utilisation de fonctions pour gérer les grandes parties, et "peu" de "programme principal" à la fin
 * - Vérification du formulaire, annulation à la moindre erreur, affichage de l'erreur
 * - Préparation des informations à envoyer en Ajax, en créant peu d'objets à partir de tous les champs, dans lesquels tout est déjà rangé
 * - Envoi en Ajax de la requête, cette fois ci en JSON, parce que je dois récupérer des infos supplémentaires (url de la transaction pour rediriger vers le payement)
 * - Empécher de faire plusieurs Ajax en même temps, et affichage d'un message demandant de patienter
 * - Afficher les erreurs trouvées en Php s'il y en a, sinon, afficher le message de succcès et rediriger vers le moyen de payement
 */

/**
 * Fonction gérant tout letraitement à faire sur un submit pour les premières inscriptions.
 */
function submit_inscriptions(submit)
{
    function add_alert(message, alert_type="danger")
    {
        var message_displayed = '<div class="alert alert-'+alert_type+' alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Attention ! </strong>' + message + '</div>'
        $("#alerts").append(message_displayed);
    }

    function prepare_option_data()
    {
        if($(this).attr('class')=='checkbox_option form-check')
        {
            if($(this).find('.has_option').is(':checked'))
            {
                var option_id = $(this).find('input[name=option_id]').val();
                var choice_id = $(this).find('.has_option').val();
                var name = $(this).find('.option_name').text();
                var option_price = parseFloat($(this).find('input[name=option_price]').val());

                if($(this).parent()[0] == $("#icam_options")[0])
                {
                    total_icam_price+=option_price;
                }
                else if($(this).parents('#registration_guests')[0] == $("#registration_guests")[0])
                {
                    total_guest_price+=option_price;
                }

                option = {option_id: option_id, choice_id: choice_id, type: 'Checkbox', name: name, price: option_price};
                options.push(option);
            }
        }
        else if($(this).attr('class')=='select_option form-group')
        {
            if($(this).find('select option:not(:first):selected').length == 1)
            {
                var option_id = $(this).find('input[name=option_id]').val();
                var choice_id = $(this).find('select option:not(:first):selected').val();
                var option_text = $.trim($(this).find('select option:not(:first):selected').text());
                var regExp = /\(([0-9]+)€\)$/;
                var option_price = parseFloat(regExp.exec(option_text)[1]);

                var regExp_name = /(\([0-9]+€\))$/;
                var name = option_text.replace(regExp_name, '');

                if($(this).parent()[0] == $("#icam_options")[0])
                {
                    total_icam_price+=option_price;
                }
                else if($(this).parents('#registration_guests')[0] == $("#registration_guests")[0])
                {
                    total_guest_price+=option_price;
                }

                var option_article_id= $(this).find('select option:not(:first):selected').val();

                option = {option_id: option_id, choice_id: choice_id, type: 'Select', name: name, price: option_price};
                options.push(option);
            }
        }
    }

    if(!check_urls(add_alert))
    {
        submit.preventDefault();
        throw("problèmes d'urls");
    }

    $("#alerts").empty();

    $("input[name=icam_informations]").val('');
    $("input[name=guests_informations]").val('');
    $("input[name=total_transaction_price]").val(parseFloat($("#total_price").text()));

    var telephone = $('input[name=icam_phone_number]').val();
    var event_price = parseFloat($('#registration_icam .event_price').text());
    var total_icam_price = event_price;
    var promo_id = $('input[name=icam_promo_id]').val();
    var site_id = $('input[name=icam_site_id]').val();

    var options = [];
    $("#icam_options").children('div').each(prepare_option_data);

    var icam_data = {event_price: event_price, total_participant_price: total_icam_price, telephone: telephone, options: options, site_id: site_id, promo_id: promo_id};
    var json_icam_data = JSON.stringify(icam_data);
    $("#hidden_inputs input[name=icam_informations]").attr('value', json_icam_data);

    var guests_data = [];
    $("#registration_guests .guest_form").each(function()
    {
        var prenom = $(this).find('.guest_firstname').val();
        var nom = $(this).find('.guest_lastname').val();
        if(!(prenom == '' && nom == ''))
        {
            if(prenom == '')
            {
                add_alert("Vous n'avez pas défini le prénom de l'" + $(this).find(".guest_title_default_text").text());
                submit.preventDefault();
            }
            else if(nom == '')
            {
                add_alert("Vous n'avez pas défini le nom de l'" + $(this).find(".guest_title_default_text").text());
                submit.preventDefault();
            }
            else
            {
                var event_price = parseFloat($(this).find('.event_price').text());
                total_guest_price = event_price;
                var promo_id = $(this).find('.guest_promo_id').val();
                var site_id = $(this).find('.guest_site_id').val();

                options = [];
                $(this).find(".guest_options div").each(prepare_option_data);

                var guest_data = {prenom: prenom, nom: nom, event_price: event_price, total_participant_price: total_guest_price, options: options, site_id: site_id, promo_id: promo_id};
                guests_data.push(guest_data);
            }
        }
    });
    if(!$.isEmptyObject(guests_data))
    {
        var json_guests_data = JSON.stringify(guests_data);
        $("#hidden_inputs input[name=guests_informations]").attr('value', json_guests_data);
    }
    else
    {
        var json_guests_data = '';
    }

    var post_url = $('form').prop('action');
    $('#message_submit').show();
    $("#button_submit_form").prop('disabled', 'disabled');

    function error_ajax(jqXHR, textStatus, errorThrown)
    {
        console.log(jqXHR);
        console.log();
        console.log(textStatus);
        console.log();
        console.log(errorThrown);
        $('#message_submit').hide();
        $("#button_submit_form").prop('disabled', '');
        add_alert('La requête Ajax permettant de submit les informations et ajouter les participants a échoué');
    }

    function ajax_success(data)
    {
        if(data.message=='Votre réservation a bien été prise en compte ! <br>Vous allez être redirigé pour payer !')
        {
            var message_displayed = '<div class="alert alert-success alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Parfait ! </strong>' + data.message + '</div>';
            $("#alerts").append(message_displayed);

            $('form').off('submit').submit(function(submit)
            {
                submit.preventDefault();
            });
            setTimeout(function()
            {
                document.location.href = data.transaction_url;
            }, 1000);
        }
        else
        {
            $('#message_submit').hide();
            $("#button_submit_form").prop('disabled', '');
            $("#alerts").append(data.message);
        }
    }

    $.post(
    {
        url: post_url,
        data: {icam_informations: json_icam_data, guests_informations: json_guests_data, total_transaction_price: parseFloat($("#total_price").text())},
        dataType: 'json',
        success: ajax_success,
        error: error_ajax
    });

    // function ajax_success(data)
    // {
    //     console.log(data);
    //     if(data=='Votre réservation a bien été prise en compte ! <br>Vous allez être redirigé pour payer !')
    //     {
    //         var message_displayed = '<div class="alert alert-success alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Parfait ! </strong>' + data.message + '</div>';
    //         console.log(message_displayed);
    //         $("#alerts").append(message_displayed);

    //         $('form').off('submit').submit(function(submit)
    //         {
    //             submit.preventDefault();
    //         });
    //         setTimeout(function()
    //         {
    //             document.location.href = data.transaction_url;
    //         }, 1000);
    //     }
    //     else
    //     {
    //         $('#message_submit').hide();
    //         $("#button_submit_form").prop('disabled', '');
    //         $("#alerts").append(data);
    //     }
    // }

    // $.post(
    // {
    //     url: post_url,
    //     data: {icam_informations: json_icam_data, guests_informations: json_guests_data, total_transaction_price: parseFloat($("#total_price").text())},
    //     dataType: 'html',
    //     success: ajax_success,
    //     error: error_ajax
    // });

    submit.preventDefault();
}