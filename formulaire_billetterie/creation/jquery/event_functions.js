/**
 * Cette fonction a pour but d'initialiser le formulaire.
 * Il faut donc enlever les évènements par défault non souhaités, et cacher tous les éléments html définis, mais qui ne doivent apparaitre que sur des éléments particulier.
 */
function initialisation_formulaire()
{
    function prepare_accessibility_add_buttons(type)
    {
        $("#ajout_" + type).click(function()//Fait référence au bouton "Ajouter ce/ces sites/promos"
        {
            $("#specific_message").children().remove();//On vire les messages affichés auparavant pour pas en avoir une blinde.
            prepare_event_accessibility_row_addition(type);
            $("#options .option_accessibility .option_accessibility_restart").click();//On restart toutes les accessibilités des options, vu qu'on ajoute une promo.
        });
    }

    $("form")[0].reset(); //On reset le formulaire, pour ne pas garder les infos dans le cache du navigateur.

    $("#specific_message").hide();//On cache le div contenant de futurs messages d'erreurs.
    $("#table_row_example").hide();//On cache l'example servant uniquement à être cloné et afficher les infos sur les promos venant dans le tableau
    $("#graduated").hide();//On cache le select contenant toutes les promos des diplomés ayant encore PI. (il sera cloné)
    $("#site_only_guest_number").fadeOut();
    $("#promo_only_guest_number").fadeOut();
    $("#site_and_promo_only_guest_number").fadeOut();
    $("#graduated_only_guest_number").fadeOut();


    $("#options").hide();//On cache la partie traitant des options
    $("#submit_form_div").hide();//On cache le bouton de submit
    $("#input_additions").hide();//On cache les inputs hidden
    $("#message_submit").hide();

    $("select[multiple]").each(function()
    {
        $(this).attr('size', $(this).children("option").length);
    });//On définit pile la taille qu'il faut à tous les select de type multiple.

    $(".availability #specification_table tbody tr").children(":nth-child(7)").children().click(function()
    {
        var confirm_delete = window.confirm("Voulez vous vraiment supprimer cette promo ? ");
        if(confirm_delete)
        {
            $(this).parent().parent().remove();
            $("#options .option_accessibility .option_accessibility_restart").click();
        }
    });

    question_change();

    prepare_accessibility_add_buttons('site');
    prepare_accessibility_add_buttons('promo');
    prepare_accessibility_add_buttons('site_and_promo');
    prepare_accessibility_add_buttons('graduated');

    $("#options #add_option_button").click(function()
    {
        add_option(get_last_html_option_id()+1);
    });

    $("form").submit(check_then_submit_form);//On fait appel à la fonction qui permet de check tout le form, et le submit
}
function arrondi_centieme(nombre)
{
    return Math.round(100*nombre)/100;
}
/**
 * * Fonction appelée sur un trigger de la fonction change sur les input du formulaire dans la première partie.
 *
 * Elle détermine d'abord si tous les boutons sont cochés.
 * Ensuite, si c'est le cas, elle applique un traitement particulier selon les boutons cochés.
 */
function question_change()
{
    /**
     * Cette fonction détermine uniquement si toutes les questions sont cochées.
     * Elle affiche notamment le bouton de submit du formulaire.
     * Elle retourne un booléen, selon l'état des boutons radio.
     */
    function all_questions_checked()
    {
        var graduated = $("input:radio[name='graduated_icam']").is(":checked");
        var permanents = $("input:radio[name='permanents']").is(":checked");
        var guests = $("input:radio[name='guests']").is(":checked");
        var options = $("input:radio[name='options']").is(":checked");

        if(graduated && permanents && guests && options)
        {
            $("#submit_form_div").fadeIn();//On affiche le bouton de submit
            return true;
        }
    }

    $("input:radio[name=guests]").change(function()
    {
        all_questions_checked();

        if($("input:radio[name='guests']:checked").val()==1)//Si l'utilisateur répond "Oui" à la question "Voulez vous des invités ?"
        {
            var invites = $("<option></option>");
            invites.text('Invités');
            invites.appendTo("#site_and_promo_choice_promo");

            $("#site_only_guest_number").fadeIn();//... Il faut ajouter la possibilité de réguler leur nombre par promo.
            $("#promo_only_guest_number").fadeIn();//... Il faut ajouter la possibilité de réguler leur nombre par promo.
            $("#site_and_promo_only_guest_number").fadeIn();//... Il faut ajouter la possibilité de réguler leur nombre par promo.
            $("#graduated_only_guest_number").fadeIn();//... Il faut ajouter la possibilité de réguler leur nombre par promo.

            $("#table_availabilities #specification_table tbody tr :nth-child(6)").click(function()
            {
                var current_value =$(this).text();
                $(this).html("<input class='form-control' min=0 type='number'>");
                $(this).children().val(current_value);
                $(this).children().focus();
                $(this).children().blur(function()
                    {
                        var input_value =($(this).val()!="") ? $(this).val() : 0;
                        $(this).parent().text(input_value);
                    });
            });//... Il faut pouvoir modifier facilement le nombre d'invités, en cliquant simplement dans le tableau.

            var message = '<div class="alert alert-info alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Ajoutez</strong> la promo Invités du site que vous souhaitez ! C\'est possible de le faire sur le bouton Ajoutez par promo et site !' + '</div>';//On crée le tooltip ...
            $("#specific_message").fadeIn().append(message);//... Et on l'affiche.

        }
        else//Si l'utilisateur répond "Non" à la question "Voulez vous des invités ?"
        {
            $("#specific_message").empty();
            $("#table_availabilities #specification_table tbody tr").each(function()
            {
                if($(this).children(":nth-child(3)").text()=='Invités')
                {
                    $(this).remove();
                }
            });//Il faut enlever la ligne Invités du tableau, vu qu'il n'y en a plus.
            $("#site_and_promo_choice_promo option").each(function()
            {
                if($(this).text()=='Invités')
                {
                    $(this).remove();
                }
            });//Il faut enlever les lignes d'invités

            $("#table_availabilities #specification_table tbody tr :nth-child(6)").blur();//Si on était en train d'éditer un nombre d'invités, on arrète cet edit.
            $("#table_availabilities #specification_table tbody tr :nth-child(6)").unbind('click');//Et on empèche de cliquer pour modifier la valeur.

            $("#site_only_guest_number").fadeOut();//Il faut faire disparaitre la possibilité de changer ce nombre à la main.
            $("#promo_only_guest_number").fadeOut();//Il faut faire disparaitre la possibilité de changer ce nombre à la main.
            $("#site_and_promo_only_guest_number").fadeOut();//Il faut faire disparaitre la possibilité de changer ce nombre à la main.
            $("#graduated_only_guest_number").fadeOut();//Il faut faire disparaitre la possibilité de changer ce nombre à la main.

            $("#table_availabilities #specification_table tbody tr :nth-child(6)").text(0);//Il faut mettre à 0 dans toutes les lignes le nombre d'invités.
            $("#site_only_input_guest_number").val(0);//Il faut qu'un ajout de promo spécifie le nombre d'invités à 0.
            $("#promo_only_input_guest_number").val(0);//Il faut qu'un ajout de promo spécifie le nombre d'invités à 0.
            $("#site_and_promo_only_input_guest_number").val(0);//Il faut qu'un ajout de promo spécifie le nombre d'invités à 0.
            $("#graduated_only_input_guest_number").val(0);//Il faut qu'un ajout de promo spécifie le nombre d'invités à 0.
        }
    });

    $("input:radio[name=graduated_icam]").change(function()
    {
        all_questions_checked();

        if($("input:radio[name=graduated_icam]:checked").val()==1)//Si l'utilisateur répond "Oui" à la question "Voulez vous des diplomés ayant encore PI ?"
        {
            $("#graduated #graduated_promos_select option").clone().appendTo("#site_and_promo_choice_promo");

            var message = '<div class="alert alert-info alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Ajoutez</strong> les promos diplômées que vous voulez, des sites que vous voulez ! C\'est possible de le faire sur le bouton Ajoutez par promo et site !' + '</div>';//On crée le tooltip ...
            $("#specific_message").fadeIn().append(message);//... Et on l'affiche.
        }
        else//Si l'utilisateur répond "Non" à la question "Voulez vous des diplomés ayant encore PI ?"
        {
            $("#specific_message").empty();
            $("#site_and_promo_choice_promo option").each(function()
            {
                var promo = $(this);
                $("#graduated #graduated_promos_select option").each(function()
                {
                    if(promo.text() == $(this).text())
                    {
                        promo.remove();
                    }
                });
            });//On enlève la possibilité d'ajouter des promos diplomées.

            $("#table_availabilities #specification_table tbody tr").each(function()
            {
                var promo = $(this);
                $("#graduated #graduated_promos_select option").each(function()
                {
                    if(promo.children(":nth(child(3))").text() == $(this).text())
                    {
                        promo.parents("tr").remove();
                    }
                });
            });//On enlève les lignes contenant des promos diplomées.
        }
    });

    $("input:radio[name=permanents]").change(function()
    {
        all_questions_checked();

        if($("input:radio[name=permanents]:checked").val()==1)//Si l'utilisateur répond "Oui" à la question "Voulez vous des permanents ?"
        {
            var permanents = $("<option></option>");
            permanents.text('Permanents');
            permanents.appendTo("#site_and_promo_choice_promo");

            var message = '<div class="alert alert-info alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Ajoutez</strong> la promo Permanents du site que vous souhaitez ! C\'est possible de le faire sur le bouton Ajoutez par promo et site !' + '</div>';//On crée le tooltip ...
            $("#specific_message").fadeIn().append(message);//... Et on l'affiche.
        }
        else//Si l'utilisateur répond "Non" à la question "Voulez vous des permanents ?"
        {
            $("#specific_message").empty();
            $("#site_and_promo_choice_promo option").each(function()
            {
                if($(this).text() == 'Permanents')
                {
                    $(this).remove();
                }
            });

            $("#table_availabilities #specification_table tbody tr").each(function()
            {
                if($(this).children(":nth-child(3)").text()=='Permanents')
                {
                    $(this).remove();
                }
            });//On les vire s'ils sont présents dans le tableau.
        }
    });

    $("input:radio[name=options]").change(function()
    {
        all_questions_checked();

        if($("input:radio[name=options]:checked").val()==1)//Si l'utilisateur répond "Oui" à la question "Voulez vous des options ?"
        {
            $("#options").fadeIn();//On les affiche.

            if($("#options #option_accordion").children().length==0)
            {
                add_option(1);//On ajoute une option de base.
            }
        }
        else//Si l'utilisateur répond "Non" à la question "Voulez vous des options ?"
        {
            $("#options #option_accordion").empty();//On les vide, pour ne pas envoyer des choses non voulues
            $("#options").fadeOut();//On enlève leur affichage.
        }
    });
}


/**
 * Cette fonction sert à appeler la fonction add_row en lui donnant les bons paramètres.
 * Elle doit donc savoir s'il faut ajouter 6 promos de 3 sites, donc 18 lignes, et faire appel 18 fois à add_row
 *
 * @param {string} [type] : doit être dans {'site','promo','site_and_promo','graduated'} sinon ça échouera.
 * Les ids et classes des parties html sont très similaires : elles sont quasi copiées collées, avec juste un changement d'id, en gardant une base commune.
 * Ainsi, en sachant juste quelle partie de l'id a changé, et la redonnant, on peux faire tout appliquer à notre fonction.
 */
function prepare_event_accessibility_row_addition(type)
{
    var price = $("#" + type + "_only_input_price").val();//Partie facile: on récupère simplement les prix, quotas et nombres d'invités
    var quota = $("#" + type + "_only_input_quota").val();//Partie facile: on récupère simplement les prix, quotas et nombres d'invités
    var guest_number = $("#" + type + "_only_input_guest_number").val();//Partie facile: on récupère simplement les prix, quotas et nombres d'invités

    switch(type)
    {
        case 'site':
            var sites = $("#" + type + "_choice").val();//On récupère les sites donnés par le select.
            sites.forEach(function(site)//Est renvoyé un tableau javascript, on le parcourt donc
            {
                var promos_possibles = $("#promo_choice option:not(:first)");//On récupère les promos possibles depuis le html
                promos_possibles.each(function()
                {
                    promo = $(this).val();
                    add_row(site, promo, price, quota, guest_number);//On ajoute la ligne, une fois toutes les infos obtenues.
                });
            });
            break;
        case 'promo':
        {
            var promos = $("#" + type + "_choice").val();//On récupère les promos données par le select.
            promos.forEach(function(promo)//Est renvoyé un tableau javascript, on le parcourt donc
            {
                var sites_possibles = $("#site_choice option:not(:first)");//On récupère les promos possibles depuis le html
                sites_possibles.each(function()
                {
                    site = $(this).val();
                    add_row(site, promo, price, quota, guest_number);
                });
            });
            break;
        }
        case 'site_and_promo':
        {
            var sites = $("#" + type + "_choice_site").val();
            sites.forEach(function(site)
            {
                var promos = $("#" + type + "_choice_promo").val();
                promos.forEach(function(promo)
                {
                    add_row(site, promo, price, quota, guest_number);
                });
            });
            break;
        }
        default:
        {
            alert('Il y a eu un problème : Le paramètre d\'entrée est faux. Contactez PayIcam si vous êtes utilisateur, sinon contactez Grégoire Giraud si vous ne savez pas résoudre le problème.');//description du problème.
        }
    }
}

/**
 * La fonction add_row ajoute simplement une ligne au tableau d'accessibilité des promos.
 * Elle check également que la promo n'est pas déjà présente sinon c'est pas drôle.
 * Je vous laisse découvrir ça
 *
 * @param {string} site
 * @param {string} promo
 * @param {float} price
 * @param {int} quota
 * @param {int} guest_number
 */
function add_row(site, promo, price=0, quota='', guest_number=0)
{
    /**
     * On vérifie simplement que la promo n'est pas déjà présente.
     * @param  {string} site
     * @param  {string} promo
     * @return {boolean} true si la promo n'est pas présente, et qu'il faut faire un ajout, false sinon
     */
    function check_not_present(site, promo)
    {
        not_present = true;
        $("#specification_table tbody tr").each(function()
        {
            var ligne_etudiee = $(this);
            var site_etudiee = ligne_etudiee.children(":nth-child(2)").text();
            var promo_etudiee= ligne_etudiee.children(":nth-child(3)").text();

            if(site_etudiee == site && promo_etudiee == promo)
            {
                var message = '<div class="alert alert-danger alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Attention ! </strong> La promo ' + promo + ' du site '+ site + ' est déjà présente dans le tableau. Nous ne l\'avons donc pas ajoutée.' + '</div>';
                $("#specific_message").fadeIn().append(message);
                return not_present = false;
            }
        });
        if(not_present)
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
        return false; //On arrète tout, le message d'erreur a déjà été envoyé, il est temps de se barrer
    }

    price = (price== '' | price<0 | isNaN(price)) ? 0 : arrondi_centieme(price);//On corrige les inputs avec du ternaire pour ne pas avoir de conneries.
    quota = (quota==0 | quota<0 | isNaN(quota)) ? '' : Math.round(quota);
    guest_number = (guest_number== '' | guest_number<0 | isNaN(guest_number) | promo=='Invités') ? 0 : Math.round(guest_number);

    var table_body = $("#table_availabilities #specification_table tbody");
    var previous_row =$("#table_availabilities #specification_table tbody tr:last");//On récupère la ligne précédente dans la table AFFICHEE, celle dans laquelle on ajoute

    if(previous_row.length ==0)//Si il n'y en a pas déjà
    {
        previous_row = $("#table_availabilities #table_row_example tr");//On récupère la ligne cachée, présente juste ce cas, servant à être clonée.
        if(previous_row.length ==0)//Si on la trouve pas elle, c'est qu'on l'a supprimée, pour une raison inconnue, ou qu'autre chose n'a pas fonctionné.
        {
            $("#specific_message").fadeIn().append('<div class="alert alert-danger alert-dismissible"> Il y a eu un problème, rechargez la page, et contactez nous ! Merci ! </div>')//On dit que c'est la merde.
        }
    }

    var previous_index =$("#table_availabilities tbody tr:last th").text();
    var new_row = previous_row.clone();//On clone la ligne, et on change toutes les valeurs

    new_row.children(":nth-child(1)").text(parseInt(previous_index)+1);
    new_row.children(":nth-child(2)").text(site);
    new_row.children(":nth-child(3)").text(promo);
    new_row.children(":nth-child(4)").text(price+"€");
    new_row.children(":nth-child(5)").text(quota);
    new_row.children(":nth-child(6)").text(guest_number);
    new_row.children(":nth-child(7)").html('<button type="button" id="add_site_promo" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></button>'); //Potentiellement, il n'y avait pas de bouton supprimer (si on l'avait justement enlevé pour les promos permanents & invités), il faut donc le rajouter au cas où.
    new_row.children(":nth-child(7)").children().click(function()
    {
        var confirm_delete = window.confirm("Voulez vous vraiment supprimer cette promo ?");
        if(confirm_delete)
        {
            $(this).parent().parent().remove();
            $("#options .option_accessibility .option_accessibility_restart").click();
        }
    });//On ajoute, avec un confirm, la possibilité de supprimer une promo, en cliquant sur le bouton poubelle.

    new_row.children(":nth-child(4)").click(function() {
        var current_value =$(this).text();
        $(this).html("<input class='form-control' type='number' min=0 step=0.01 value='" + current_value.slice(0,-1) + "' >");//Ajout d'un input.
        $(this).children().focus();//focus sur l'input pour que l'évènement blur(perte de focus) ait un sens.
        $(this).children().blur(function()//Quand on perd le focus.
            {
                var input_value =($(this).val()!="" && $(this).val()>0) ? arrondi_centieme($(this).val()) : 0; //On arrondit au centime au cas ou l'utilisateur soit vicieux. On a pensé à toi celui qui met des négatifs fdp
                $(this).parent().text(input_value+'€');//On l'affiche à la place de l'input.
            });//Disparition de l'input si on clique ailleurs.
        });//On ajoute la possibilité de changer les valeurs des lignes une fois inscrites en cliquant simplement dessus. L'input disparait une fois qu'on clique ailleurs. Ceci s'applique au changement de prix.

    new_row.children(":nth-child(5)").click(function() {
        var current_value =$(this).text();
        $(this).html("<input class='form-control' type='number' min=0 value='" + current_value + "' >");
        $(this).children().focus();
        $(this).children().blur(function()
            {
                var input_value =($(this).val()!="" && $(this).val()>0) ? Math.round($(this).val(), 0) : '';
                $(this).parent().text(input_value);
            });
        });//same pour le changement de quota

    if($("input:radio[name='guests']:checked").val()==1 && promo!='Invités')//On ne veux pas modifier la valeur de base (0), si il n'y a pas d'invités pour l'évènement. On empèche quoi qu'il arrive la promo Invités de changer cette valeur.
    {
        new_row.children(":nth-child(6)").click(function() {
            var current_value =$(this).text();
            $(this).html("<input class='form-control' type='number' min=0 value='" + current_value + "' >");
            $(this).children().focus();
            $(this).children().blur(function()
                {
                    var input_value =($(this).val()!="" && $(this).val()>0) ? Math.round($(this).val(), 0) : 0;
                    $(this).parent().text(input_value);
                });
            });//same pour le changement de nombre d'invités
    }
    table_body.append(new_row); //Evidemment on ajoute tout ça.
}