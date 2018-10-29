/**
 * Fonction faisant toute la gestion du submit : vérification des champs, puis préparation des données, puis envoi en Ajax
 */
function check_then_submit_form(event)
{
    function add_alert(message, alert_type="danger")
    {
        var message_displayed = '<div class="alert alert-'+alert_type+' alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Attention ! </strong>' + message + '</div>'
        $("#erreurs_submit").append(message_displayed);
    }
    /**
     * Fonction qui va déterminer si les infos sont les bonnes
     * @return {boolean} [true si les infos du form est valide]
     */
    function check_form()
    {
        var form_is_correct = true;
        if($('input[name=event_name]').val()=='')
        {
            add_alert('Le nom de votre évènement n\'est pas défini...');
            form_is_correct = false;
        }
        else if($('input[name=event_name]').val().length>100)
        {
            add_alert("Le nom de l'évènement est bien trop long");
            form_is_correct = false;
        }
        if($('textarea[name=event_description]').val()=='')
        {
            add_alert('La description de votre évènement n\'est pas définie...');
            form_is_correct = false;
        }
        if($('input[name=event_quota]').val()=='')
        {
            add_alert('Le quota total de votre évènement n\'est pas défini...');
            form_is_correct = false;
        }
        if($('input[name=ticketing_start_date]').val()=='')
        {
            add_alert('La date d\'ouverture de votre billetterie n\'est pas définie...');
            form_is_correct = false;
        }
        if($('input[name=ticketing_end_date]').val()=='')
        {
            add_alert('La date de fermeture de votre billetterie n\'est pas définie...');
            form_is_correct = false;
        }
        if($('#specification_table tbody tr').length==0)
        {
            add_alert('Vous ne visez aucune promo...');
            form_is_correct = false;
        }
        if($('input[name=guests]').val()==1)
        {
            should_have_guests = false;
            guests_row = false;
            $('#specification_table tbody tr').each(function()
            {
                if($(this).children(":nth-child(6)").text()>0)
                {
                    should_have_guests = true;
                }
                if($(this).children(":nth-child(3)").text()=='Invités')
                {
                    guests_row = true;
                }
            });
            if(should_have_guests && !guests_row)
            {
                add_alert("Vous avez donné la possibilité d'avoir des invités à au moins une promo, mais vous n'avez pas spécifié de promos 'Invités'. Allez le faire !");
                form_is_correct = false;
            }
        }
        $("#options .panel-default").each(function()
        {
            var option_name = ($(this).find("input[name=option_name]").val()=='') ? 'Option sans nom' : $(this).find("input[name=option_name]").val();
            if($(this).find("input[name=option_name]").val()=='')
            {
                add_alert('L\'option ' + option_name + ' est incomplète : Le nom de l\'option n\'est pas défini');
                form_is_correct = false;
            }
            else if($('input[name=option_name]').val().length>100)
            {
                add_alert("Le nom de l'option : " + option_name + " est bien trop long");
                form_is_correct = false;
            }
            if($(this).find("textarea[name=option_description]").val()=='')
            {
                add_alert('L\'option ' + option_name + ' est incomplète : La description de l\'option n\'est pas définie');
                form_is_correct = false;
            }
            if(!$(this).find("input[class=option_type_input]").is(":checked"))
            {
                add_alert('L\'option ' + option_name + ' est incomplète : Vous n\'avez pas coché le type de l\'option');
                form_is_correct = false;
            }
            if(!$(this).find("input[class=option_active_input]").is(":checked"))
            {
                add_alert('L\'option ' + option_name + ' est incomplète : Vous n\'avez pas précisé si votre option devait être active dès maintenant');
                form_is_correct = false;
            }
            if(!$(this).find("input[class=option_accessibility_input]").is(":checked"))
            {
                add_alert('L\'option ' + option_name + ' est incomplète : Vous n\'avez pas coché l\'accessibilité de l\'option');
                form_is_correct = false;
            }
            if($(this).find("input:radio[class=option_type_input]:checked").val()=='Checkbox')
            {
                if($(this).find("input[name=checkbox_price]").val()=='')
                {
                    add_alert('L\'option ' + option_name + ' est incomplète : Le prix de l\'option Checkbox n\'est pas défini');
                    form_is_correct = false;
                }
            }
            if($(this).find("input:radio[class=option_type_input]:checked").val()=='Select')
            {
                if($(this).find('.select_table tbody tr').length ==0)
                {
                    add_alert('L\'option ' + option_name + ' est incomplète : Il n\'y a rien dans votre select');
                    form_is_correct = false;
                }
                if($(this).find('.select_table tbody tr :nth-child(2)').text().length>100)
                {
                    add_alert("Le sous-nom de l'option : " + option_name + " est bien trop long");
                    form_is_correct = false;
                }
                if($(this).find('.select_table tbody tr').length ==1)
                {
                    add_alert('L\'option ' + option_name + ' est incomplète : Votre select ne contient qu\'une sous option. Utilisez plutôt un Checkbox...');
                    form_is_correct = false;
                }
                if(!$(this).find('.select_type .select_option_mandatory_input').is(":checked"))
                {
                    add_alert('L\'option ' + option_name + ' est incomplète : Vous n\'avez pas précisé si le select était obligatoire.');
                    form_is_correct = false;
                }
            }
            if($(this).find("input:radio[class=option_accessibility_input]:checked").val()==0)
            {
                if($(this).find('.option_accessibility_table tbody tr').length ==0)
                {
                    add_alert('L\'option ' + option_name + ' est incomplète : Votre option ne s\'adresse à personne');
                    form_is_correct = false;
                }
            }
        });
        return form_is_correct;
    }

    /**
     * La fonction permet de récupérer toutes les infos sur l'évènement et de tout mettre dans un object JS
     * @return {object} [l'objet contenant les infos de l'event]
     */
    function get_event_infos()
    {
        var name = $('input[name=event_name]').val();
        var description = $('textarea[name=event_description]').val();
        var conditions = $('textarea[name=event_conditions]').val();
        var quota = $('input[name=event_quota]').val();
        var ticketing_start_date = $('input[name=ticketing_start_date]').val();
        var ticketing_end_date = $('input[name=ticketing_end_date]').val();
        var is_active = $('.general_infos .toggle').hasClass('off') ? 0 : 1;
        var fundation_id = $('input[name=fundation_id]').val();

        var event_json = {name: name, description: description, conditions: conditions, quota: quota, ticketing_start_date: ticketing_start_date, ticketing_end_date: ticketing_end_date, is_active: is_active, fundation_id: fundation_id};
        return event_json;
    }
    /**
     * La fonction permet de récupérer toutes les infos sur l'accessibilité de l'event (promos ayant accès) et de tout mettre dans un object JS
     * @return {object} [l'objet contenant les infos de l'event]
     */
    function get_accessibility_infos()
    {
        var rows = [];
        $('#specification_table tbody tr').not('.removed').each(function()
        {
            var site = $(this).children(':nth-child(2)').text();
            var promo = $(this).children(':nth-child(3)').text();
            var price = $(this).children(':nth-child(4)').text().slice(0,-1);//On vire le symbole €
            var quota = $(this).children(':nth-child(5)').text();
            var guest_number = $(this).children(':nth-child(6)').text();

            var row = {site: site, promo: promo, price: price, quota: quota, guest_number:guest_number};
            rows.push(row);
        });
        return rows;
    }
    /**
     * La fonction permet de récupérer toutes les infos sur les options de l'event et de tout mettre dans un object JS
     * @return {object} [l'objet contenant les infos de l'event]
     */
    function get_options_infos()
    {
        /**
         * Permet de créer les infos sur les choix du select possible
         */
        function get_select_infos(rows)
        {
            var select_options = [];
            rows.each(function()
            {
                var choice_id = $(this).data('choice_id');
                var name = $(this).children(':nth-child(2)').text();
                var price = $(this).children(':nth-child(3)').text().slice(0,-1);//On vire le symbole €
                var quota = $(this).children(':nth-child(4)').text();

                var select_option = {choice_id:choice_id, name:name, price:price, quota:quota};
                select_options.push(select_option);
            });
            return select_options;
        }
        /**
         * Permet de créer les infos sur l'accessibilité d'une option
         */
        function get_option_accessibility_info(rows)
        {
            var promos_have_option = [];
            rows.each(function()
            {
                var site = $(this).children(':nth-child(2)').text();
                var promo = $(this).children(':nth-child(3)').text();

                var promo_has_option = {site:site, promo:promo};
                promos_have_option.push(promo_has_option);
            });
            return promos_have_option;
        }

        var options = [];
        $("#options .panel-default").each(function()
        {
            var name = $(this).find("input[name=option_name]").val();
            var description = $(this).find("textarea[name=option_description]").val();
            var quota = $(this).find("input[name=option_quota]").val();
            var type = $(this).find("input[class=option_type_input]:checked").val();
            var is_active = $(this).find(".option_active_input:checked").val();
            var is_mandatory = $(this).find(".select_option_mandatory_input:checked").val();

            event.preventDefault();

            if(type=='Checkbox')
            {
                var choice_id = $(this).find("input[name=choice_id]").val();
                var price = $(this).find("input[name=checkbox_price]").val();
                var specification = {choice_id:choice_id, price:price};
            }
            else
            {
                var specification = get_select_infos($(this).find('.select_table tbody tr'));
            }

            var option_accessibility = get_option_accessibility_info($(this).find('.option_accessibility_table tbody tr'));//Affichée ou non, elle est tt le tps correcte

            var option_id = $(this).find(".option_id_value").val();

            var option = {option_id: option_id, name: name, description: description, quota: quota, is_active: is_active, is_mandatory: is_mandatory, type: type, type_specification: specification, accessibility: option_accessibility};

            options.push(option);
        });
        return options;
    }

    function check_urls()
    {
        var current_path = window.location.pathname;
        var action_url = $('form').prop('action');

        if(current_path == base_path + "event_administration/" || current_path == base_path + "event_administration/new_ticketing.php")
        {
            if(action_url != public_url + "event_administration/php/ajout_billetterie.php")
            {
                add_alert("A quoi joues tu ? Trafiquer les url est passible instantanné de permaban de PayIcam.");
                return false;
            }
        }
        else if(current_path == base_path + "event_administration/edit_ticketing.php")
        {
            if(action_url != public_url + "event_administration/php/edit_billetterie.php?event_id=" + event_id)
            {
                add_alert("A quoi joues tu ? Trafiquer les url est passible instantanné de permaban de PayIcam.");
                return false;
            }
        }
        return true;
    }

    $("#erreurs_submit").empty(); //On vide les erreurs s'il y en avait qui restait d'un précédent Ajax

    if(check_urls())
    {
        if(check_form())
        {
            //Tout est bon, il ne reste plus qu'à préparer les données

            $("#message_submit").show();
            $('.waiting').show();//On affiche un petit message montrant que la modification est en cours

            var event_data = get_event_infos();
            var event_data_json = JSON.stringify(event_data);//On récupère les données de l'event, que l'on met sous format JSON, pour le passer en text

            var event_accessibility = get_accessibility_infos();
            var event_accessibility_json = JSON.stringify(event_accessibility);//Pareil pour l'accessiblité

            if($("input:radio[name=options]:checked").val()==1)
            {
                var option_details = get_options_infos();
                var option_details_json = JSON.stringify(option_details);//Pareil pour les options
            }
            else
            {
                var option_details_json = '';
            }

            var post_url = $('form').prop('action');

            function ajax_success(data)
            {
                //On cache le message disant qu'on est en attente
                $('.waiting').hide();
                //Si les infos envoyées correspondent au message envoyé en cas de succès par le php en Ajax
                if(data=='Les informations ont bien été pris en compte !' | data == 'Les modifications ont bien été pris en compte !')
                {
                    var message_displayed = '<div class="alert alert-success alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Parfait ! </strong>' + data + '</div>';
                    $("#erreurs_submit").append(message_displayed);
                    console.log(message_displayed);
                    $('form').off('submit').submit(function(submit)
                    {
                        //On empèche de resubmit le form vu que tout est bon
                        submit.preventDefault();
                    });
                    setTimeout(function()
                    {
                        //Et on redirige vers la page d'accueil de l'administration
                        document.location.href = public_url + 'event_administration';
                    }, 1000);
                }
                else
                {
                    //Sinon, le message n'est pas le bon, c'est un message d'erreur, on l'affiche
                    $("#erreurs_submit").append(data);
                    //On permet de submit quelque chose
                    $("#submit_form").prop('disabled', '');
                }
            }

            function error_ajax(jqXHR, textStatus, errorThrown)
            {
                //S'il y a une erreur Ajax, on met dans la console les erreurs rencontrées, et on affiche un message d'erreur, disant que l'Ajax a échoué
                console.log(jqXHR);
                console.log();
                console.log(textStatus);
                console.log();
                console.log(errorThrown);
                add_alert('La requête Ajax permettant de submit les informations et ajouter la billetterie a échoué');
                $("#submit_form").prop('disabled', '');
                $('.waiting').hide();
            }

            //On ne veux pas envoyer plusieurs requetes Ajax en même temps alors qu'il y a a déjà une en cours
            $("#submit_form").prop('disabled', 'disabled');

            //On envoie en post, les infos nécessaires. Le retour attendu est html
            //Les données sont envoyées sous forme de 3 objets, contenant des informations spécifiques à chaque fois.
            $.post(
            {
                url: post_url,
                data: {event_data_json: event_data_json, event_accessibility_json: event_accessibility_json, option_details_json: option_details_json},
                dataType: 'html',
                success: ajax_success,
                error: error_ajax,
            });

            //On ne veux pas envoyer le formulauire, on fait donc un preventDefault pour empécher de le submit.
            event.preventDefault();
        }
        else
        {
            event.preventDefault();
        }
    }
    else
    {
        event.preventDefault();
    }
}