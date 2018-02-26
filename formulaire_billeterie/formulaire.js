/**
 *
 * On commence par cacher de base les parties responsives selon l'accessibilité de l'évènement.
 *
 */


$("#availability_complement").hide();
$("form").submit(function(event) {event.preventDefault()});

$("form")[0].reset();
$("#errors").hide();

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
        $('#availability_complement').show();

        if($("input:radio[name='guests']:checked").val()==0)
        {
            $("#site_only_input_guest_number").val(0);
            $("#promo_only_input_guest_number").val(0);
            $("#site_and_promo_only_input_guest_number").val(0);

            $("#site_only_guest_number").hide();
            $("#promo_only_guest_number").hide();
            $("#site_and_promo_only_guest_number").hide();
        }
        else
        {
            $("#site_only_guest_number").show();
            $("#promo_only_guest_number").show();
            $("#site_and_promo_only_guest_number").show();
        }
    }
}

$("input:radio[name='all_students']").is("checked");

function add_row(site, promo, prix, quota=null, guest_number=0)
{
    function check_not_present(site, promo)
    {
        error = false;
        $("#table_availabilities tbody tr").each(function()
        {
            var ligne_etudiee = $(this);
            var site_etudiee = ligne_etudiee.children(":nth-child(2)").text();
            var promo_etudiee= ligne_etudiee.children(":nth-child(3)").text();

            if(site_etudiee == site && promo_etudiee == promo)
            {
                var message = '<div class="alert alert-danger alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Attention ! </strong> La promo ' + promo + ' du site '+ site + ' est déjà présente dans le tableau. Nous ne l\'avons donc pas ajoutée.' + '</div>';
                $("#errors").show().append(message);
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

    var table_body = $("#table_availabilities tbody");
    var previous_row =$("#table_availabilities tbody tr:last");
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
        $(this).html("<input class='form-control' type='number' step='0.01' value='" + current_value.slice(0,-1) + "' >");
        $(this).focus();
        $(this).children().blur(function()
            {
                var input_value = $(this).val();
                $(this).parent().text(input_value+'€');
            });
        });

    new_row.children(":nth-child(5)").dblclick(function() {
        var current_value =$(this).text();
        $(this).html("<input class='form-control' type='number' value='" + current_value + "' >");
        $(this).focus();
        $(this).children().blur(function()
            {
                var input_value = $(this).val();
                $(this).parent().text(input_value);
            });
        });

    new_row.children(":nth-child(6)").dblclick(function() {
        var current_value =$(this).text();
        $(this).html("<input class='form-control' type='number' value='" + current_value + "' >");
        $(this).focus();
        $(this).children().blur(function()
            {
                var input_value = $(this).val();
                $(this).parent().text(input_value);
            });
        });

    table_body.append(new_row);
}

$("#ajout_site").click(function()
    {
        $("#errors").children().remove();
        ajout_site();
    }
);
$("#ajout_promo").click(function()
    {
        $("#errors").children().remove();
        ajout_promo();
    }
);
$("#ajout_site_and_promo").click(function()
    {
        $("#errors").children().remove();
        ajout_site_and_promo();
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
    console.log('mdr');
    console.log($("#site_and_promo_only_input_guest_number"));
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