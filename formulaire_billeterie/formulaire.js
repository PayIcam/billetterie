/**
 *
 * On commence par cacher de base les parties responsives selon l'accessibilité de l'évènement.
 *
 */

$("#availability_complement").hide();
$("form").submit(function(event) {event.preventDefault()});

$("form")[0].reset();
$("#specific_message").hide();
$("#table_row_example").hide();
$("#options").hide();
$(".option_accessibility").hide();
$(".option_type_complement").hide();
$(".checkbox_type").hide();
$(".select_type").hide();


function efface_specifications()
{
    $('form #specification_prix_promo_invite')
    .not(':button, :submit, :reset, :hidden')
    .val('');
}

/**
 *  On souhaite afficher le formulaire seulement en fonction de ce que l'utilisateur coche, c'est ce qu'on fait ici.
 */

$("input:radio[name='graduated_icam']").change(function()
{
    question_change();
});
$("input:radio[name='permanents']").change(function()
{
    question_change();
});
$("input:radio[name='guests']").change(function()
{
    question_change();
});
$("input:radio[name='options']").change(function()
{
    question_change();
});

/**
 * [question_change : this function is used on change event on the radio buttons
 * It will determine whether everything was ticked and will then display the appropriate info depending on what was ticked]
 */
function question_change()
{
    /**
     * [all_questions_checked : used to determine whether the user ticked all the questions asked by the app]
     * @return {[boolean]} [true if all questions are checked]
     */
    function all_questions_checked()
    {
        var graduated = $("input:radio[name='graduated_icam']").is(":checked");
        var permanents = $("input:radio[name='permanents']").is(":checked");
        var guests = $("input:radio[name='guests']").is(":checked");
        var options = $("input:radio[name='options']").is(":checked");
        return graduated && permanents && guests && options;
    }
    if(all_questions_checked())
    {
        $('#availability_complement').fadeIn();

        if($("input:radio[name='guests']:checked").val()==1)
        {

            $("#site_only_guest_number").fadeIn();
            $("#promo_only_guest_number").fadeIn();
            $("#site_and_promo_only_guest_number").fadeIn();
            $("#graduated_only_guest_number").fadeIn();

            $("#table_availabilities #specification_table tbody tr :nth-child(6)").click(function()
                {
                    var current_value =$(this).text();
                    $(this).html("<input class='form-control' min=0 type='number' value='" + current_value + "' >");
                    $(this).children().focus();
                    $(this).children().blur(function()
                        {
                            var input_value =($(this).val()!="") ? $(this).val() : 0;
                            $(this).parent().text(input_value);
                        });
                });

            var guests_row_there = false;
            $("#table_availabilities #specification_table tbody tr").each(function()
                {
                    if($(this).children(":nth-child(2)").text()=='Tous' && $(this).children(":nth-child(3)").text()=='Invités')
                    {
                        return guests_row_there = true;
                    }
                });
            if(!guests_row_there)
            {
                add_row('Tous', 'Invités', 0, 0, 0);
                var message = '<div class="alert alert-info alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Cliquez</strong> sur les champs prix, promos et nombre d\'invités pour changer les valeurs de base ! <br>Pour valider les changements, <strong>cliquez autre part</strong>' + '</div>';
                $("#table_availabilities #specification_table tbody tr:last :nth-child(7)").children().remove();
                $("#specific_message").fadeIn().append(message);
            }
        }
        else
        {
            $("#table_availabilities #specification_table tbody tr :nth-child(6)").text(0);
            $("#site_only_input_guest_number").val(0);
            $("#promo_only_input_guest_number").val(0);
            $("#site_and_promo_only_input_guest_number").val(0);
            $("#graduated_only_input_guest_number").val(0);

            $("#site_only_guest_number").fadeOut();
            $("#promo_only_guest_number").fadeOut();
            $("#site_and_promo_only_guest_number").fadeOut();
            $("#graduated_only_guest_number").fadeOut();

            $("#table_availabilities #specification_table tbody tr :nth-child(6)").blur();
            $("#table_availabilities #specification_table tbody tr :nth-child(6)").unbind('click');

            $("#graduated").fadeOut();
            $("#table_availabilities #specification_table tbody tr").each(function()
                {
                    if($(this).children(":nth-child(2)").text()=='Tous' && $(this).children(":nth-child(3)").text()=='Invités')
                    {
                        $(this).remove();
                    }
                });
        }

        if($("input:radio[name=graduated_icam]:checked").val()==1)
        {
            $("#graduated").fadeIn();
        }
        else
        {
            $("#graduated").fadeOut();
        }

        if($("input:radio[name=permanents]:checked").val()==1)
        {
            var permanent_row_there = false;
            $("#table_availabilities #specification_table tbody tr").each(function()
                {
                    if($(this).children(":nth-child(2)").text()=='Tous' && $(this).children(":nth-child(3)").text()=='Permanents')
                    {
                        return permanent_row_there = true;
                    }
                });
            if(!permanent_row_there)
            {
                add_row('Tous', 'Permanents', 0, 0, 0);
                var message = '<div class="alert alert-info alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Cliquez</strong> sur les champs prix, promos et nombre d\'invités pour changer les valeurs de base ! <br>Pour valider les changements, <strong>cliquez autre part</strong>' + '</div>';
                $("#table_availabilities #specification_table tbody tr:last :nth-child(7)").children().remove();
                $("#specific_message").fadeIn().append(message);
            }
        }
        else
        {
            $("#graduated").fadeOut();
            $("#table_availabilities #specification_table tbody tr").each(function()
                {
                    if($(this).children(":nth-child(2)").text()=='Tous' && $(this).children(":nth-child(3)").text()=='Permanents')
                    {
                        $(this).remove();
                    }
                });
        }

        if($("input:radio[name=options]:checked").val()==1)
        {
            $("#options").fadeIn();

            if(get_last_html_option_id()==0)
            {
                add_option(1);
            }

            $("input:radio[name=options]").unbind('change');

            $("input:radio[name=options][value=0]:not(.bound_click)").addClass('bound_click').click(function()
                {
                    var confirm_click = window.confirm("Si vous cliquez sur confirmer, vous allez annuler tout ce que vous avez fait sur vos options, est-ce ce que vous souhaitez ?");
                    if(confirm_click)
                    {
                        question_change();
                        $("input:radio[name=options][value=1]").click(function()
                            {
                                question_change();
                            });
                        $(this).removeClass('bound_click').unbind('click');
                    }
                    else
                    {
                        $("input:radio[name=options][value=1]").prop('checked', true);
                    }
                });
        }
        else
        {
            $("#options #option_accordion").empty();
            $("#options").fadeOut();
        }
    }
}

console.log($("input:radio[name=options][value=0]"));

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

function add_option(option_number)
{
    $.ajax('add_option.php', {
        type: 'GET',
        data: {option_number: option_number},
        dataType: 'html',
        timeout: 1000,
        success: function(data)
        {
            console.log('supposed to have worked');
            $("#options #option_accordion").append(data);
        },
        error: function()
        {
            console.log('supposed not to have worked');
            alert('La requête Ajax pour récupérer le formulaire des options a échoué. Contactez PayIcam si vous êtes un utilisateur, sinon, contactez Grégoire Giraud.');
        }
    });
}

$("#table_availabilities #specification_table tbody tr :nth-child(7)").children().click(function()
    {
        var confirm_delete = window.confirm("Voulez vous vraiment supprimer cette promo ?");
        if(confirm_delete)
        {
            $(this).parent().parent().remove();
        }
    });

function add_row(site, promo, prix=null, quota=null, guest_number=0)
{
    function check_not_present(site, promo)
    {
        error = false;
        $("#specification_table tbody tr").each(function()
        {
            var ligne_etudiee = $(this);
            var site_etudiee = ligne_etudiee.children(":nth-child(2)").text();
            var promo_etudiee= ligne_etudiee.children(":nth-child(3)").text();

            if(site_etudiee == site && promo_etudiee == promo)
            {
                var message = '<div class="alert alert-danger alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Attention ! </strong> La promo ' + promo + ' du site '+ site + ' est déjà présente dans le tableau. Nous ne l\'avons donc pas ajoutée.' + '</div>';
                $("#specific_message").fadeIn().append(message);
                return error = true;
            }
        });
        if(!error)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    var row_not_present_yet = check_not_present(site, promo);

    if(!row_not_present_yet)
    {
        return false;
    }

    var table_body = $("#table_availabilities #specification_table tbody");
    var previous_row =$("#table_availabilities #specification_table tbody tr:last");

    if(previous_row.length ==0)
    {
        previous_row = $("#table_availabilities #table_row_example tr");
        if(previous_row.length ==0)
        {
            $("#specific_message").fadeIn().append('<div class="alert alert-danger alert-dismissible"> Il y a eu un problème, rechargez la page, et contactez nous ! Merci ! </div>')
        }
    }

    var previous_index =$("#table_availabilities tbody tr:last th").text();
    var new_row = previous_row.clone();

    new_row.children(":nth-child(1)").text(parseInt(previous_index)+1);
    new_row.children(":nth-child(2)").text(site);
    new_row.children(":nth-child(3)").text(promo);
    new_row.children(":nth-child(4)").text(prix+"€");
    new_row.children(":nth-child(5)").text(quota);
    new_row.children(":nth-child(6)").text(guest_number);
    new_row.children(":nth-child(7)").html('<button id="add_site_promo" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></button>');
    new_row.children(":nth-child(7)").children().click(function()
    {
        var confirm_delete = window.confirm("Voulez vous vraiment supprimer cette promo ?");
        if(confirm_delete)
        {
            $(this).parent().parent().remove();
            $("#options .option_accessibility .option_accessibility_restart").click();
        }
    });

    new_row.children(":nth-child(4)").click(function() {
        var current_value =$(this).text();
        $(this).html("<input class='form-control' type='number' min=0 step=0.01 value='" + current_value.slice(0,-1) + "' >");
        $(this).children().focus();
        $(this).children().blur(function()
            {
                var input_value =($(this).val()!="") ? $(this).val() : 0;
                $(this).parent().text(input_value+'€');
            });
        });

    new_row.children(":nth-child(5)").click(function() {
        var current_value =$(this).text();
        $(this).html("<input class='form-control' type='number' min=0 value='" + current_value + "' >");
        $(this).children().focus();
        $(this).children().blur(function()
            {
                var input_value =($(this).val()!="") ? $(this).val() : 0;
                $(this).parent().text(input_value);
            });
        });

    if($("input:radio[name='guests']:checked").val()==1)
    {
        new_row.children(":nth-child(6)").click(function() {
            var current_value =$(this).text();
            $(this).html("<input class='form-control' type='number' min=0 value='" + current_value + "' >");
            $(this).children().focus();
            $(this).children().blur(function()
                {
                    var input_value =($(this).val()!="") ? $(this).val() : 0;
                    $(this).parent().text(input_value);
                });
            });
    }

    table_body.append(new_row);
}

$("#ajout_site").click(function()
    {
        $("#specific_message").children().remove();
        ajout_site();
        $("#options .option_accessibility .option_accessibility_restart").click();
    }
);
$("#ajout_promo").click(function()
    {
        $("#specific_message").children().remove();
        ajout_promo();
        $("#options .option_accessibility .option_accessibility_restart").click();
    }
);
$("#ajout_site_and_promo").click(function()
    {
        $("#specific_message").children().remove();
        ajout_site_and_promo();
        $("#options .option_accessibility .option_accessibility_restart").click();
    }
);
$("#ajout_graduated").click(function()
    {
        $("#specific_message").children().remove();
        ajout_graduated();
        $("#options .option_accessibility .option_accessibility_restart").click();
    }
);

function ajout_site()
{
    var price = $("#site_only_input_price").val();
    var quota = $("#site_only_input_quota").val();
    var guest_number = $("#site_only_input_guest_number").val();

    $("#site_choice option:selected").each(function()
    {
        var site = $(this).val();
        var promos = ['122', '121', '120', '119', '118', '2022', '2021', '2020', '2019', '2018'];

        promos.forEach(function(promo)
        {
            add_row(site, promo, price, quota, guest_number);
        });
    });
}
function ajout_promo()
{
    var price = $("#promo_only_input_price").val();
    var quota = $("#promo_only_input_quota").val();
    var guest_number = $("#promo_only_input_guest_number").val();

    $("#promo_choice option:selected").each(function()
    {
        var promo = $(this).val();
        var sites = ['Lille', 'Toulouse'];

        sites.forEach(function(site)
        {
            add_row(site, promo, price, quota, guest_number);
        });
    });
}
function ajout_site_and_promo()
{
    var price = $("#site_and_promo_only_input_price").val();
    var quota = $("#site_and_promo_only_input_quota").val();
    var guest_number = $("#site_and_promo_only_input_guest_number").val();

    $("#site_and_promo_choice_site option:selected").each(function()
    {
        var site = $(this).val();
        $("#site_and_promo_choice_promo option:selected").each(function()
        {
            var promo = $(this).val();
            add_row(site, promo, price, quota, guest_number);
        });
    });
}

function ajout_graduated()
{
    var price = $("#graduated_only_input_price").val();
    var quota = $("#graduated_only_input_quota").val();
    var guest_number = $("#graduated_only_input_guest_number").val();

    $("#graduated_choice_site option:selected").each(function()
    {
        var site = $(this).val();
        $("#graduated_choice_promo option:selected").each(function()
        {
            var promo = $(this).val();
            add_row(site, promo, price, quota, guest_number);
        });
    });
}

$("#options .option_generalities input[name=option_type]").change(function()
    {
        console.log($(this));
        console.log($(this).parents(".option_generalities"));
        console.log($(this).parents(".option_generalities").siblings(".option_type_complement"));
        console.log($(this).parents(".option_generalities").siblings(".option_type_complement").children(".checkbox_type"));

        $(this).parents(".option_generalities").siblings(".option_type_complement").fadeIn();

        if($(this).val()=='Checkbox')
        {
            console.log('check');

            $(this).parents(".option_generalities").siblings(".option_type_complement").children(".checkbox_type").fadeIn();
            $(this).parents(".option_generalities").siblings(".option_type_complement").children(".select_type").fadeOut();
        }
        else
        {
            console.log('select');
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

