/**
 * Petite requête ajax basique pour ajouter une option avec une id différente des id précédentes.
 *
 * Pour rappel, dans la page add_option.php, si le $_GET['option_name'] est défini, on affiche l'accordéon.
 * Du coup, avec l'ajax on le récupère, et on précise que les données sont du html.
 */
function add_option(option_number)
{
    $.ajax('templates/add_option.php', {
        type: 'GET', //C'est bien du GET qu'il faut envoyer
        data: {option_number: option_number}, //On oublie pas de définir l'id à donner
        dataType: 'html', //C'est bien du html qu'on récupère
        success: function(data)
        {
            $("#options #option_accordion").append(data); //Si ça a marché, on ajoute juste les données à la page dans l'accordéon.
            cache_parties_option();
            attach_option_events();
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            console.log(jqXHR);
            console.log();
            console.log(textStatus);
            console.log();
            console.log(errorThrown);
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
    $(".panel-default:not(.already_added) .option_accessibility").hide();
    $(".panel-default:not(.already_added) .option_type_complement").hide();
    $(".panel-default:not(.already_added) .checkbox_type").hide();
    $(".panel-default:not(.already_added) .select_type").hide();
}
/**
 * Cette fonction sert à attacher tous les éléments nécessaires à la bonne marche de la partie option du code.
 *
 * Elle est appelée lors du succès de la requête ajax.
 *
 * Je vous laisse en faire le tour
 */
function attach_option_events()
{
    /**
     * Cette fonction gère l'ajout d'une ligne de select au tableau récapitulant le tout.
     *
     * C'est assez basique, si vous avez suivi ce que j'ai fait avant c'est bon
     *
     * @param {string} name
     * @param {int} price
     */
    function add_select_row(index, name, price, quota)
    {
        quota = (quota==0 | quota<0 | isNaN(quota)) ? '' : Math.round(quota);
        price = (price== '' | price<0 | isNaN(price)) ? 0 : arrondi_centieme(price);

        var row = $("<tr></tr>");//On crée de toute pièce notre ligne à ajouter, en effet, elle est pas bien grande, ça évite d'avoir des problèmes en clonant, s'il n'y a rien auparavant.
        $("<th></th>").text(index).appendTo(row);
        $("<td></td>").text(name).appendTo(row);
        $("<td></td>").text(price+'€').appendTo(row);
        $("<td></td>").text(quota).appendTo(row);

        var delete_button = $("<td></td>").html('<button type="button" id="add_site_promo" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></button>')//Comme d'hab, un bouton servant à supprimer la ligne
        delete_button.children().click(function()
        {
            var confirm_delete = window.confirm("Voulez vous vraiment enlever cette sous-option ?");
            if(confirm_delete)
            {
                $(this).parents(".select_table").siblings(".select_example").find("select option").each(function()//On remarque juste qu'il cherche l'option dans le select montré, pour la virer aussi, c'est plus joli
                {
                    if($(this).text() == name + '(' + price + '€)')
                    {
                        $(this).remove();
                    }
                });
                $(this).parents("tr").remove();
            }
        });
        delete_button.appendTo(row);
        return row;
    }

    $("#options .panel-default:not(.already_added) .option_generalities input[name=option_name]").keyup(function()//On veux changer le nom affiché sur le header de l'accordéon (de base, c'était Option sans nom)
    {
        var option_name = $(this).val();

        $(this).parents(".panel-default").find(".panel-title a")[0].firstChild.textContent = option_name;//On se fait chier avec un peu de Js natif parce qu'on veux pas virer le bouton supprimer, donc pas de text()
    });
    $("#options #option_accordion .panel-default:not(.already_added) .panel-title .glyphicon-trash").click(function()//Justement, on en parlait, pourquoi pas virer toute une option ?
    {
        var confirm_delete = window.confirm("Voulez vous vraiment supprimer entièrement cette option ?");
        if(confirm_delete)
        {
            $(this).parents(".panel-default").remove();
        }
    });

    $("#options .panel-default:not(.already_added) .option_generalities input[class=option_type_input]").change(function()//Si on change le type de l'option, il y a du traitement à faire.
    {
        $(this).parents(".option_generalities").siblings(".option_type_complement").fadeIn();//De base, rien n'est coché, on va donc afficher la partie générale (les 2 autres sont encore cachées)

        if($(this).val()=='Checkbox')
        {
            $(this).parents(".option_generalities").siblings(".option_type_complement").children(".checkbox_type").fadeIn();//Rien de fou, on affiche l'une et enlève l'autre
            $(this).parents(".option_generalities").siblings(".option_type_complement").children(".select_type").fadeOut();//Rien de fou, on affiche l'une et enlève l'autre
        }
        else
        {
            $(this).parents(".option_generalities").siblings(".option_type_complement").children(".select_type").fadeIn();
            $(this).parents(".option_generalities").siblings(".option_type_complement").children(".checkbox_type").fadeOut();
        }
    });

    $('#options .panel-default:not(.already_added) .option_type_complement .checkbox_type input[name=checkbox_price]').on('change keyup', function()//On affiche en dynamique la gueule du label du checkbox ! Stylé !
    {
        $("#options .option_type_complement .checkbox_type .checkbox_example label").text(function()
        {
            var option_name = $(this).parents(".option_type_complement").prev().find("input[name=option_name]").val();
            var option_price = $(this).parent().prev().children('input[name=checkbox_price]').val()
            return option_name + '(+' + option_price + '€)';//Quand on passe une fonction de callback à un texte, ce qu'on return est la nouvelle valeur. Voilà voilà
        });
    });

    $(".panel-default:not(.already_added) .ajout_select").click(function()//En gros, je veux ajouter une ligne quand on clique sur ajouter une ligne. Et je l'ajoute aussi dans le select de l'exemple pour faire joli
    {
        var name = $(this).prev().find("input[name=select_name]").val();
        var price = $(this).prev().find("input[name=select_price]").val();
        var quota = $(this).prev().find("input[name=select_quota]").val();
        var index = $(this).siblings(".select_table").find("tbody").children().length==0 ? 1 : parseInt($(".select_table tbody tr:last th").text())+1;//Un petit ternaire, ça fait jamais de mal (L'index doit valoir soit 1, soit le précédent index +1);
        var row = add_select_row(index, name, price, quota);
        row.appendTo($(this).siblings(".select_table").find("tbody"));

        $("<option></option>").text(name + '(' + price + '€)').appendTo($(this).siblings('.select_example').find("select"));
        $(this).prev().find("input[name=select_name]").val('');//A la fin, je vide les inputs, et je refocus sur le name, comme ça il peux aller vite
        $(this).prev().find("input[name=select_name]").focus();
        $(this).prev().find("input[name=select_price]").val('');
        $(this).prev().find("input[name=select_quota]").val('');
    });

    $("#options .panel-default:not(.already_added) .option_generalities input[name=option_name]").keyup(function()//On change le label du select entier de l'exemple sur un keyup du nom de l'option
    {
        $("#options .option_type_complement .select_type .select_example label").text($(this).val() + ': ');
    });

    $("#options .panel-default:not(.already_added) .option_generalities input[class=option_accessibility_input]").change(function()//Si on fait joujou avec le bouton "tout le monde a accès à l'option", on reset l'accessibilité
    {
        $(this).parents(".option_generalities").siblings('.option_accessibility').children(".option_accessibility_restart").click();

        if($(this).val()==0)
        {
            $(this).parents(".option_generalities").siblings(".option_accessibility").fadeIn();//Et on affiche ce qu'il faut
        }
        else
        {
            $(this).parents(".option_generalities").siblings(".option_accessibility").fadeOut();
        }
    });

    $("#options .panel-default:not(.already_added) .option_accessibility_restart").click(function()//On crée un putain de bouton génial, qui permet de restart l'accessibilité.
    {
        var old_rows= $("#specification_table tbody tr");//En effet, on se sert des promos qui ont déjà accès à l'évènement, et c'est beaucoup mieux, donc l'utilisateur vire celles qu'il ne veux pas.
        var new_rows = old_rows.clone();//Mais du coup, il peux se planter et en virer trop... Pas de problème, il y a un bouton restart pour ça.

        new_rows.children(":nth-child(6)").remove();
        new_rows.children(":nth-child(5)").remove();
        new_rows.children(":nth-child(4)").remove();

        new_rows.children(":nth-child(4)").html('<button type="button" id="add_site_promo" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></button>');
        new_rows.children(":nth-child(4)").children().click(function()//Et voilà, si on clique ici, on vire bien la promo.
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

    $("#options #option_accordion .panel-default:not(.already_added)").addClass('already_added');//C'est assez tricky ici... Si vous avez suivi, vous avez vu partout un .panel-default:not(.already_added). En fait, il y avait un petit truc chiant avant. Chaque ajout d'option rajoutait un event partout. Et du coup, ils se stackaient alors qu'il étaient exactement identiques ! C'est pas ce qu'on veux, et du coup, on le résoud avec ça. On applique les nouveaux trucs qu'à ceux qui ont pas la classe "already_added", et une fois qu'on a tout mis, on ajoute cette classe.
}