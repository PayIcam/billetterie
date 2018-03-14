function submit_inscriptions(submit)
{
    function add_error(message)
    {
        var message_displayed = '<div class="alert alert-danger alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Attention ! </strong>' + message + '</div>'
        $("#errors").append(message_displayed);
    }

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
                    total_icam_price+=option_price;
                }
                else if($(this).parents('#registration_guests')[0] == $("#registration_guests")[0])
                {
                    total_guest_price+=option_price;
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
                    total_icam_price+=option_price;
                }
                else if($(this).parents('#registration_guests')[0] == $("#registration_guests")[0])
                {
                    total_guest_price+=option_price;
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

    var is_icam = 1;
    var prenom = $('input[name=icam_firstname]').val();
    var nom = $('input[name=icam_lastname]').val();
    var email = $('input[name=icam_email]').val();
    var telephone = $('input[name=icam_phone_number]').val();
    var birthdate = $('input[name=icam_birth_date]').val();
    var event_price = parseFloat($('#registration_icam .event_price').text());
    var total_icam_price = event_price;
    var promo_id = $('input[name=icam_promo_id]').val();
    var site_id = $('input[name=icam_site_id]').val();

    var options = [];
    $("#icam_options").children('div').each(prepare_option_data);

    var icam_data = {prenom: prenom, nom: nom, is_icam: is_icam, email: email, price: total_icam_price, telephone: telephone, birthdate: birthdate, options: options, site_id: site_id, promo_id: promo_id};
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
                var is_icam = 0;
                var birthdate = $(this).find('.guest_birthdate').val();
                var event_price = parseFloat($(this).find('.event_price').text());
                total_guest_price = event_price;
                var promo_id = $(this).find('.guest_promo_id').val();
                var site_id = $(this).find('.guest_site_id').val();

                options = [];
                $(this).find(".guest_options div").each(prepare_option_data);

                var guest_data = {prenom: prenom, nom: nom, is_icam: is_icam, price: total_guest_price, birthdate: birthdate, options: options, site_id: site_id, promo_id: promo_id};
                guests_data.push(guest_data);
            }
        }
    });
    if(!$.isEmptyObject(guests_data))
    {
        var json_guests_data = JSON.stringify(guests_data);
        $("#hidden_inputs input[name=guests_informations]").attr('value', json_guests_data);
    }
}