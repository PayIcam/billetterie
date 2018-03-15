function prepare_edit_submit()
{
    function edit_submit(submit)
    {
        function prepare_option_data()
        {
            if($(this).attr('class')=='checkbox_option form-check')
            {
                if($(this).find('.has_option').is(':checked'))
                {
                    var id = $(this).find('input[name=option_id]').val();
                    var name = $(this).find('.option_name').text();

                    var option_price = parseFloat($(this).find('input[name=option_price]').val());

                    if($(this).parent()[0] == $("#icam_options")[0])
                    {
                        icam_price_addition+=option_price;
                    }
                    else if($(this).parents('#registration_guests')[0] == $("#registration_guests")[0])
                    {
                        guest_price_addition+=option_price;
                    }
                    option = {id: id, type: 'Checkbox', name: name, price: option_price};
                    options.push(option);
                }
            }
            else if($(this).attr('class')=='select_option form-group')
            {
                if($(this).find('select option:not(:first):selected').length == 1)
                {
                    var id = $(this).find('input[name=option_id]').val();
                    var selection_name = $.trim($(this).find('select option:selected').text());

                    var option_text = $.trim($(this).find('select option:selected').text());
                    var regExp = /\(([0-9]+)€\)$/;
                    var option_price = parseFloat(regExp.exec(option_text)[1]);

                    var regExp_name = /(\([0-9]+€\))$/;
                    var name = selection_name.replace(regExp_name, '');

                    if($(this).parent()[0] == $("#icam_options")[0])
                    {
                        icam_price_addition+=option_price;
                    }
                    else if($(this).parents('#registration_guests')[0] == $("#registration_guests")[0])
                    {
                        guest_price_addition+=option_price;
                    }

                    option = {id: id, type: 'Select', name: name, price: option_price};
                    options.push(option);
                }
            }
        }

        $("#errors").empty();

        $("input[name=icam_informations]").val('');
        $("input[name=guests_informations]").val('');
        $("input[name=total_transaction_price]").val('');
        $("input[name=total_transaction_price]").val(parseFloat($("#total_price").text()));

        var icam_id = $("input[name=icam_id]").val();
        var telephone = $('input[name=icam_phone_number]').val();
        var birthdate = $('input[name=icam_birth_date]').val();
        var icam_price_addition = 0;
        var promo_id = $('input[name=icam_promo_id]').val();
        var site_id = $('input[name=icam_site_id]').val();

        var options = [];
        $("#icam_options").children('div:not(div[data-payed=1])').each(prepare_option_data);

        var icam_data = {icam_id: icam_id, price: icam_price_addition, telephone: telephone, birthdate: birthdate, options: options, site_id: site_id, promo_id: promo_id};
        var json_icam_data = JSON.stringify(icam_data);
        $("#hidden_inputs input[name=icam_informations]").attr('value', json_icam_data);

        var previous_guests_data =[];
        var new_guests_data =[];
        $("#registration_guests .guest_form").each(function()
        {
            var prenom = $(this).find('.guest_firstname').val();
            var nom = $(this).find('.guest_lastname').val();
            if(!(prenom == '' && nom == ''))
            {
                if(prenom == '')
                {
                    add_error("Vous n'avez pas défini le prénom de l'" + $(this).find(".guest_title_default_text").text());
                    submit.preventDefault();
                }
                else if(nom == '')
                {
                    add_error("Vous n'avez pas défini le nom de l'" + $(this).find(".guest_title_default_text").text());
                    submit.preventDefault();
                }
                else
                {
                    if($(this).hasClass('previous_guest'))
                    {
                        var guest_id = $(this).find("input[name=guest_id]").val();
                        var birthdate = $(this).find('.guest_birthdate').val();
                        guest_price_addition = 0;
                        var promo_id = $(this).find('.guest_promo_id').val();
                        var site_id = $(this).find('.guest_site_id').val();

                        options = [];
                        $(this).find(".guest_options").children("div:not(div[data-payed=1])").each(prepare_option_data);

                        var guest_data = {guest_id: guest_id, prenom: prenom, nom: nom, price: guest_price_addition, birthdate: birthdate, options: options, site_id: site_id, promo_id: promo_id};
                        previous_guests_data.push(guest_data);
                    }
                    else
                    {
                        var is_icam = 0;
                        var birthdate = $(this).find('.guest_birthdate').val();
                        var event_price = parseFloat($(this).find('.event_price').text());
                        guest_price_addition = event_price;
                        var promo_id = $(this).find('.guest_promo_id').val();
                        var site_id = $(this).find('.guest_site_id').val();

                        options = [];
                        $(this).find(".guest_options div").each(prepare_option_data);

                        var guest_data = {prenom: prenom, nom: nom, is_icam: is_icam, price: guest_price_addition, birthdate: birthdate, options: options, site_id: site_id, promo_id: promo_id};
                        new_guests_data.push(guest_data);
                    }
                }
            }
        });

        guests_data = {previous_guests_data: previous_guests_data, new_guests_data: new_guests_data};

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

        function ajax_success(data)
        {
            if(data=='Votre édition a bien été prise en compte !')
            {
                var message_displayed = '<div class="alert alert-success alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Parfait ! </strong>' + data + '</div>';
                $("#alerts").append(message_displayed);

                $('form').off('submit').submit(function(submit)
                {
                    submit.preventDefault();
                });
                setTimeout(function()
                {
                    document.location.href = '../';
                }, 1000);
            }
            else
            {
                $("#alerts").append(data);
            }
        }
        function error_ajax()
        {
            add_error('La requête Ajax permettant de submit les informations et ajouter le participant a échoué');
        }

        $.post(
        {
            url: post_url,
            data: {icam_informations: json_icam_data, guests_informations: json_guests_data, total_transaction_price: parseFloat($("#total_price").text())},
            dataType: 'html',
            success: ajax_success,
            error: error_ajax
        });

        submit.preventDefault();
        }

    $("form").off('submit');
    $("form").submit(edit_submit);
}
