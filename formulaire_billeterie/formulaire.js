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
        return graduated && permanents && guests;
    }
    if(all_questions_checked())
    {
        $('#availability_complement').fadeIn();

        if($("input:radio[name='guests']:checked").val()==0)
        {
            $("#site_only_input_guest_number").val(0);
            $("#promo_only_input_guest_number").val(0);
            $("#site_and_promo_only_input_guest_number").val(0);
            $("#graduated_only_input_guest_number").val(0);

            $("#site_only_guest_number").fadeOut();
            $("#promo_only_guest_number").fadeOut();
            $("#site_and_promo_only_guest_number").fadeOut();
            $("#graduated_only_guest_number").fadeOut();

            $("#table_availabilities #specification_table tbody tr :nth-child(6)").blur();
            $("#table_availabilities #specification_table tbody tr :nth-child(6)").unbind('dblclick');
        }
        else
        {
            $("#site_only_guest_number").fadeIn();
            $("#promo_only_guest_number").fadeIn();
            $("#site_and_promo_only_guest_number").fadeIn();
            $("#graduated_only_guest_number").fadeIn();

            $("#table_availabilities #specification_table tbody tr :nth-child(6)").dblclick(function()
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
                        console.log('already present');
                        return permanent_row_there = true;
                    }
                });
            if(permanent_row_there)
            {
                console.log('already present ffs')
            }
            else
            {
                console.log('addition');
                add_row('Tous', 'Permanents', 0, 0, 0);
                var message = '<div class="alert alert-info alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Double-cliquez</strong> sur les champs prix, promos et nombre d\'invités pour changer les valeurs de base ! <br>Pour valider les changements, <strong>cliquez autre part</strong>' + '</div>';
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
                        console.log('removed the permanent row');
                        $(this).remove();
                    }
                    else
                    {
                        console.log('not present already');
                    }
                });
        }
    }
}

$("#table_availabilities #specification_table tbody tr :nth-child(7)").children().click(function()
    {
        var confirm_delete = window.confirm("Voulez vous vraiment supprimer cette promo ?");
        if(confirm_delete)
        {
            $(this).parent().parent().remove();
        }
    });

function add_row(site, promo, prix, quota=null, guest_number=0)
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
    new_row.children(":nth-child(7)").children().click(function()
    {
        var confirm_delete = window.confirm("Voulez vous vraiment supprimer cette promo ?");
        if(confirm_delete)
        {
            $(this).parent().parent().remove();
        }
    });

    new_row.children(":nth-child(4)").dblclick(function() {
        var current_value =$(this).text();
        $(this).html("<input class='form-control' type='number' min=0 step=0.01 value='" + current_value.slice(0,-1) + "' >");
        $(this).children().focus();
        $(this).children().blur(function()
            {
                var input_value =($(this).val()!="") ? $(this).val() : 0;
                $(this).parent().text(input_value+'€');
            });
        });

    new_row.children(":nth-child(5)").dblclick(function() {
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
        new_row.children(":nth-child(6)").dblclick(function() {
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
    }
);
$("#ajout_promo").click(function()
    {
        $("#specific_message").children().remove();
        ajout_promo();
    }
);
$("#ajout_site_and_promo").click(function()
    {
        $("#specific_message").children().remove();
        ajout_site_and_promo();
    }
);
$("#ajout_graduated").click(function()
    {
        $("#specific_message").children().remove();
        ajout_graduated();
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