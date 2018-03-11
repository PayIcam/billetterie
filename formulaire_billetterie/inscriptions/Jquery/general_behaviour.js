function initialisation_inscriptions()
{
    $("form")[0].reset();

    $('[data-toggle="popover"]').popover();

    filling_form_behaviour();

    try//Si une option est obligatoire, il faut l'initialiser comme ceci.
    {//Mais ça "bug", si justement l'option ne l'est pas, on met donc dans un try avec un catch vide
        $(".select_option select").change();
    }
    catch(error)
    {

    }

    $("form").submit(submit_inscriptions);
}

function filling_form_behaviour()
{
    $(".checkbox_option input:checkbox").change(function()
    {
        if($(this).is(":checked"))
        {
            $(this).siblings("label").find(".checkbox_price").attr("style", "background-color: #468847");
        }
        else
        {
            $(this).siblings("label").find(".checkbox_price").attr("style", "background-color: #3a87ad");
        }
    });
    $("#icams_own_place input:radio").change(function()
    {
        if($("input:checked").val()==1)
        {
            $(this).parents('#icams_own_place').find(".event_price").attr("style", "background-color: #468847");
        }
        else
        {
            $(this).parents('#icams_own_place').find(".event_price").attr("style", "background-color: #b94a48");
        }
    });

    $("#registration_guests .guest_form input[class='form-control guest_firstname'], #registration_guests .guest_form input[class='form-control guest_lastname']").keyup(function()
    {
        var prenom = $(this).parents('.guest_inputs').find('.guest_firstname').val();
        var nom = $(this).parents('.guest_inputs').find('.guest_lastname').val();
        if(!(prenom == '' && nom == ''))
        {
            $(this).parents('.guest_form').find('.event_price').attr('style', 'background-color: #468847');
            $(this).parents('.guest_form').find('.actual_guest_title').text(prenom + " " + nom);
        }
        else
        {
            $(this).parents('.guest_form').find('.event_price').attr('style', 'background-color: #b94a48');
            $(this).parents('.guest_form').find('.actual_guest_title').text($(this).parents(".guest_informations").find(".guest_title_default_text").text());
        }
    });
    $(".select_option select").change(function()
    {
        var option_text = $.trim($(this).find('option:selected').text());
        var regExp = /\(([0-9]+€)\)$/;
        var price = regExp.exec(option_text)[1];
        $(this).parents(".select_option").find(".select_price").text(price);
    });
}