/**
 * Petite requête ajax basique pour ajouter une option avec une id différente des id précédentes.
 *
 * Pour rappel, dans la page add_option.php, si le $_GET['option_name'] est défini, on affiche l'accordéon.
 * Du coup, avec l'ajax on le récupère, et on précise que les données sont du html.
 */
function add_option(option_number)
{
    $.ajax('add_option.php', {
        type: 'GET', //C'est bien du GET qu'il faut envoyer
        data: {option_number: option_number}, //On oublie pas de définir l'id à donner
        dataType: 'html', //C'est bien du html qu'on récupère
        timeout: 1000,
        success: function(data)
        {
            $("#options #option_accordion").append(data); //Si ça a marché, on ajoute juste les données à la page dans l'accordéon.
        },
        error: function()
        {
            alert('La requête Ajax pour récupérer le formulaire des options a échoué. Contactez PayIcam si vous êtes un utilisateur, sinon, contactez Grégoire Giraud.'); //Sinon, on alerte qu'il y a un problème.
        }
    });
}

/**
 * On récupère juste l'id de la dernière option sur le panel body.
 * Elle est de base contenue dedans sous la forme id="option_XXXX".
 * Ce qui nous intéresse est le XXXX pour en ajouter un différent.
 *
 * On rapelle, on en a besoin afin de bien afficher l'accordéon en ayant toujours des id différentes.
 */
function get_last_html_option_id()
{
    if($("#options #option_accordion").children().length ==0)
    {
        return 0;
    }
    else
    {
        var id_containing_last_html_id_number = $("#options #option_accordion .panel-body:last").attr('id');
        var id_number = parseInt(id_containing_last_html_id_number[id_containing_last_html_id_number.length-1]);
        return id_number;
    }
}

/**
 * Le gros du formulaire des options est récupéré en ajax, et n'est donc pas présent au chargement de la page.
 * Il y a toutefois des parties du formulaire à cacher, affichées ensuite selon les actions de l'utilisateur.
 * Nous créons donc une fonction permettant de les cacher, à appeler quand on fait apparaitre la page php en ajax.
 */
function cache_parties_option()
{
    console.log('mdr');
    $(".option_accessibility").hide();
    $(".option_type_complement").hide();
    $(".checkbox_type").hide();
    $(".select_type").hide();
}

function attach_option_events()
{
    function add_select_row(name, price)
    {
        if($(".specification_table tbody").children().length==0)
        {
            var row = $("<tr></tr>");
            $("<th></th>").text(1).appendTo(row);
            $("<td></td>").text(name).appendTo(row);
            $("<td></td>").text(price).appendTo(row);
            var delete_button = $("<td></td>").html('<button id="add_site_promo" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></button>')
            delete_button.children().click(function()
                {
                    var confirm_delete = window.confirm("Voulez vous vraiment enlever cette sous-option ?");
                    if(confirm_delete)
                    {
                        $(this).parents("tr").remove();
                    }
                });
            delete_button.appendTo(row);

            row.appendTo(".specification_table tbody");

        }
        else
        {
            var previous_index = parseInt($(".specification_table tbody tr:last th").text());

            var row = $("<tr></tr>");
            $("<th></th>").text(previous_index+1).appendTo(row);
            $("<td></td>").text(name).appendTo(row);
            $("<td></td>").text(price+'€').appendTo(row);
            $("<td></td>").html('<button id="add_site_promo" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></button>').appendTo(row);
            row.appendTo(".specification_table tbody");
        }
    }


    $("#options .option_generalities input[name=option_type]").change(function()//Si on change le type de l'option, il y a du traitement à faire.
    {
        $(this).parents(".option_generalities").siblings(".option_type_complement").fadeIn();

        if($(this).val()=='Checkbox')
        {
            $(this).parents(".option_generalities").siblings(".option_type_complement").children(".checkbox_type").fadeIn();
            $(this).parents(".option_generalities").siblings(".option_type_complement").children(".select_type").fadeOut();
        }
        else
        {
            $(this).parents(".option_generalities").siblings(".option_type_complement").children(".select_type").fadeIn();
            $(this).parents(".option_generalities").siblings(".option_type_complement").children(".checkbox_type").fadeOut();
        }
    });

    $(document).on('input', '#options .option_type_complement .checkbox_type input[name=checkbox_price]', function()
    {
        $("#options .option_type_complement .checkbox_type .checkbox_example label").text(function()
        {
            var option_name = $(this).parents(".option_type_complement").prev().find("input[name=option_name]").val();
            var option_price = $(this).parent().prev().children('input[name=checkbox_price]').val()
            return option_name + '(+' + option_price + '€)';
        });
    });

    $(".ajout_select").click(function()
    {
        var name = $(this).prev().find("input[name=select_name]").val();
        var price = $(this).prev().find("input[name=select_price]").val();
        add_select_row(name, price);
        $("<option></option>").text(name + '(' + price + '€)').appendTo("#options .option_type_complement .select_type .select_example select");
    });

    $(document).on('input', "#options .option_generalities input[name=option_name]", function()
    {
        $("#options .option_type_complement .select_type .select_example label").text($(this).val() + ': ');
    });

    $("#options .option_generalities input[name=everyone_has_option]").change(function()
    {
        $(this).parents(".option_generalities").siblings('.option_accessibility').children(".option_accessibility_restart").click();

        if($(this).val()==0)
        {
            $(this).parents(".option_generalities").siblings(".option_accessibility").fadeIn();
        }
        else
        {
            $(this).parents(".option_generalities").siblings(".option_accessibility").fadeOut();
        }
    });

    $("#options .option_accessibility_restart").click(function()
    {
        var old_rows= $("#specification_table tbody tr");
        var new_rows = old_rows.clone();

        new_rows.children(":nth-child(6)").remove();
        new_rows.children(":nth-child(5)").remove();
        new_rows.children(":nth-child(4)").remove();

        new_rows.children(":nth-child(4)").html('<button id="add_site_promo" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></button>');
        new_rows.children(":nth-child(4)").children().click(function()
        {
            var confirm_delete = window.confirm("Voulez vous vraiment enlever l'accès à votre option à cette promo ?");
            if(confirm_delete)
            {
                $(this).parents("tr").remove();
            }
        });
        $(this).siblings(".option_accessibility_table").find("tbody").empty();
        $(this).siblings(".option_accessibility_table").find("tbody").append(new_rows);
    });
}