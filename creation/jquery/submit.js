function check_then_submit_form(event)
{
    function add_error(message)
    {
        var message_displayed = '<div class="alert alert-danger alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Attention ! </strong>' + message + '</div>'
        $("#erreurs_submit").append(message_displayed);
    }
    function check_form()
    {
        var form_is_correct = true;
        if($('input[name=event_name]').val()=='')
        {
            add_error('Le nom de votre évènement n\'est pas défini...');
            form_is_correct = false;
        }
        else if($('input[name=event_name]').val().length>100)
        {
            add_error("Le nom de l'évènement est bien trop long");
            form_is_correct = false;
        }
        if($('textarea[name=event_description]').val()=='')
        {
            add_error('La description de votre évènement n\'est pas définie...');
            form_is_correct = false;
        }
        if($('input[name=event_quota]').val()=='')
        {
            add_error('Le quota total de votre évènement n\'est pas défini...');
            form_is_correct = false;
        }
        if($('input[name=ticketing_start_date]').val()=='')
        {
            add_error('La date d\'ouverture de votre billetterie n\'est pas définie...');
            form_is_correct = false;
        }
        if($('input[name=ticketing_end_date]').val()=='')
        {
            add_error('La date de fermeture de votre billetterie n\'est pas définie...');
            form_is_correct = false;
        }
        if($('#specification_table tbody tr').length==0)
        {
            add_error('Vous ne visez aucune promo...');
            form_is_correct = false;
        }
        $("#options .panel-default").each(function()
        {
            var option_name = ($(this).find("input[name=option_name]").val()=='') ? 'Option sans nom' : $(this).find("input[name=option_name]").val();
            if($(this).find("input[name=option_name]").val()=='')
            {
                add_error('L\'option ' + option_name + ' est incomplète : Le nom de l\'option n\'est pas défini');
                form_is_correct = false;
            }
            else if($('input[name=option_name]').val().length>100)
            {
                add_error("Le nom de l'option : " + option_name + " est bien trop long");
                form_is_correct = false;
            }
            if($(this).find("textarea[name=option_description]").val()=='')
            {
                add_error('L\'option ' + option_name + ' est incomplète : La description de l\'option n\'est pas définie');
                form_is_correct = false;
            }
            if(!$(this).find("input[class=option_type_input]").is(":checked"))
            {
                add_error('L\'option ' + option_name + ' est incomplète : Vous n\'avez pas coché le type de l\'option');
                form_is_correct = false;
            }
            if(!$(this).find("input[class=option_active_input]").is(":checked"))
            {
                add_error('L\'option ' + option_name + ' est incomplète : Vous n\'avez pas précisé si votre option devait être active dès maintenant');
                form_is_correct = false;
            }
            if(!$(this).find("input[class=option_accessibility_input]").is(":checked"))
            {
                add_error('L\'option ' + option_name + ' est incomplète : Vous n\'avez pas coché l\'accessibilité de l\'option');
                form_is_correct = false;
            }
            if($(this).find("input:radio[class=option_type_input]:checked").val()=='Checkbox')
            {
                if($(this).find("input[name=checkbox_price]").val()=='')
                {
                    add_error('L\'option ' + option_name + ' est incomplète : Le prix de l\'option Checkbox n\'est pas défini');
                    form_is_correct = false;
                }
            }
            if($(this).find("input:radio[class=option_type_input]:checked").val()=='Select')
            {
                if($(this).find('.select_table tbody tr').length ==0)
                {
                    add_error('L\'option ' + option_name + ' est incomplète : Il n\'y a rien dans votre select');
                    form_is_correct = false;
                }
                if($(this).find('.select_table tbody tr :nth-child(2)').text().length>100)
                {
                    add_error("Le sous-nom de l'option : " + option_name + " est bien trop long");
                    form_is_correct = false;
                }
                if($(this).find('.select_table tbody tr').length ==1)
                {
                    add_error('L\'option ' + option_name + ' est incomplète : Votre select ne contient qu\'une sous option. Utilisez plutôt un Checkbox...');
                    form_is_correct = false;
                }
                if(!$(this).find('.select_type .select_option_mandatory_input').is(":checked"))
                {
                    add_error('L\'option ' + option_name + ' est incomplète : Vous n\'avez pas précisé si le select était obligatoire.');
                    form_is_correct = false;
                }
            }
            if($(this).find("input:radio[class=option_accessibility_input]:checked").val()==0)
            {
                if($(this).find('.option_accessibility_table tbody tr').length ==0)
                {
                    add_error('L\'option ' + option_name + ' est incomplète : Votre option ne s\'adresse à personne');
                    form_is_correct = false;
                }
            }
        });
        return form_is_correct;
    }

    function get_event_infos()
    {
        var name = $('input[name=event_name]').val();
        var description = $('textarea[name=event_description]').val();
        var quota = $('input[name=event_quota]').val();
        var ticketing_start_date = $('input[name=ticketing_start_date]').val();
        var ticketing_end_date = $('input[name=ticketing_end_date]').val();
        var is_active = $('.general_infos .toggle').hasClass('off') ? 0 : 1;
        var fundation_id = $('input[name=fundation_id]').val()==undefined ? -50 : $('input[name=fundation_id]').val();

        var event_json = {name: name, description: description, quota: quota, ticketing_start_date: ticketing_start_date, ticketing_end_date: ticketing_end_date, is_active: is_active, fundation_id: fundation_id};
        return event_json;
    }
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
    function get_options_infos()
    {
        function get_select_infos(rows)
        {
            var select_options = [];
            rows.each(function()
            {
                var name = $(this).children(':nth-child(2)').text();
                var price = $(this).children(':nth-child(3)').text().slice(0,-1);//On vire le symbole €
                var quota = $(this).children(':nth-child(4)').text();

                var select_option = {name:name, price:price, quota:quota};
                select_options.push(select_option);
            });
            return select_options;
        }
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
            var is_active = $(this).find(".option_active_input").val();
            var is_mandatory = $(this).find(".select_option_mandatory_input:checked").val();

            event.preventDefault();

            if(type=='Checkbox')
            {
                var specification = {price: $(this).find("input[name=checkbox_price]").val()};
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

        if(current_path == base_path + "creation/" || current_path == base_path + "creation/new_ticketing.php")
        {
            if(action_url != public_url + "creation/php/ajout_billetterie.php")
            {
                add_error("A quoi joues tu ? Trafiquer les url est passible instantanné de permaban de PayIcam.");
                return false;
            }
        }
        else if(current_path == base_path + "creation/edit_ticketing.php")
        {
            if(action_url != public_url + "creation/php/edit_billetterie.php?event_id=" + event_id)
            {
                add_error("A quoi joues tu ? Trafiquer les url est passible instantanné de permaban de PayIcam.");
                return false;
            }
        }
        return true;
    }

    $("#erreurs_submit").empty();

    if(check_urls())
    {
        if(check_form())
        {
            $("#message_submit").show();
            $('.waiting').show();

            var event_data = get_event_infos();
            var event_data_json = JSON.stringify(event_data);

            var event_accessibility = get_accessibility_infos();
            var event_accessibility_json = JSON.stringify(event_accessibility);
            var event_accessibility_input = $("<input type='hidden' name='event_accessibility_json'>");
            event_accessibility_input.val(event_accessibility_json);
            $("#input_additions").append(event_accessibility_input);

            if($("input:radio[name=options]:checked").val()==1)
            {
                var option_details = get_options_infos();
                var option_details_json = JSON.stringify(option_details);
                var option_details_input = $('<input type="hidden" name="option_details_json" >');
                option_details_input.val(option_details_json);
                $("#input_additions").append(option_details_input);
            }
            else
            {
                var option_details_json = '';
            }

            var post_url = $('form').prop('action');

            function ajax_success(data)
            {
                $('.waiting').hide();
                if(data=='Les informations ont bien été pris en compte !' | data == 'Les modifications ont bien été pris en compte !')
                {
                    var message_displayed = '<div class="alert alert-success alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Parfait ! </strong>' + data + '</div>';
                    $("#erreurs_submit").append(message_displayed);
                    console.log(message_displayed);
                    $('form').off('submit').submit(function(submit)
                    {
                        submit.preventDefault();
                    });
                    setTimeout(function()
                    {
                        document.location.href = public_url + 'creation';
                    }, 1000);
                }
                else
                {
                    $("#erreurs_submit").append(data);
                    $("#submit_form").prop('disabled', '');
                }
            }

            function error_ajax(jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR);
                console.log();
                console.log(textStatus);
                console.log();
                console.log(errorThrown);
                add_error('La requête Ajax permettant de submit les informations et ajouter la billetterie a échoué');
                $("#submit_form").prop('disabled', '');
                $('.waiting').hide();
            }

            $("#submit_form").prop('disabled', 'disabled');

            $.post(
            {
                url: post_url,
                data: {event_data_json: event_data_json, event_accessibility_json: event_accessibility_json, option_details_json: option_details_json},
                dataType: 'html',
                success: ajax_success,
                error: error_ajax,
            });
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