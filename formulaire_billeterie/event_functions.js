/**
 * Cette fonction a pour but d'initialiser le formulaire.
 * Il faut donc enlever les évènements par défault non souhaités, et cacher tous les éléments html définis, mais qui ne doivent apparaitre que sur des éléments particulier.
 */
function initialisation_formulaire()
{
    /**
     * Cette fonction sert à affectuer un évènement change aux boutons radio définis en entrée du formulaire.
     * On leur applique alors la fonction question_change détaillant ce qui doit être fait.
     */
    function add_event_change_to_radio_input(input_name)
    {
        $("input:radio[name="+input_name+"]").change(function()
        {
            question_change();
        });
    }
    function prepare_accessibility_add_buttons(type)
    {
        $("#ajout_" + type).click(function()//Fait référence au bouton "Ajouter ce/ces sites"
            {
                $("#specific_message").children().remove();//On vire les messages affichés auparavant pour pas en avoir une blinde.
                prepare_event_accessibility_row_addition(type);
                $("#options .option_accessibility .option_accessibility_restart").click();//On restart toutes les accessibilités des options, vu qu'on ajoute une promo.
            }
        );
    }

    $("form").submit(function(event)
        {
            event.preventDefault() //On ne veux pas que le formulaire se submit en cliquant sur n'importe quel bouton, un bouton sera affiché pour ça, une fois les bonnes infos saisies
        });
    $("form")[0].reset(); //On reset le formulaire, pour ne pas garder les infos dans le cache du navigateur.

    $("#availability_complement").hide();//On cache la partie indiquant qui aura accès à l'évènement de base, elle sera affichée quand l'utilisateur aura avancé dans le formulaire
    $("#specific_message").hide();//On cache le div contenant de futurs messages d'erreurs.
    $("#table_row_example").hide();//On cache l'example servant uniquement à être cloné et afficher les infos sur les promos venant dans le tableau

    $("#options").hide();//On cache la partie traitant des options

    add_event_change_to_radio_input('graduated_icam');
    add_event_change_to_radio_input('permanents');
    add_event_change_to_radio_input('guests');
    add_event_change_to_radio_input('options');

    prepare_accessibility_add_buttons('site');
    prepare_accessibility_add_buttons('promo');
    prepare_accessibility_add_buttons('site_and_promo');
    prepare_accessibility_add_buttons('graduated');

    $("#options #add_option_button").click(function()
    {
        add_option(get_last_html_option_id()+1);
    })
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
     * Elle retourne un booléen, selon l'état des boutons radio.
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
        $('#availability_complement').fadeIn();//Si toutes les questions sont cochées, l'utilisateur peux passer à la partie traitant d'ajouter des participants à son évènement

        if($("input:radio[name='guests']:checked").val()==1)//Si l'utilisateur répond "Oui" à la question "Voulez vous des invités ?"
        {
            $("#site_only_guest_number").fadeIn();//... Il faut ajouter la possibilité de réguler leur nombre par promo.
            $("#promo_only_guest_number").fadeIn();//... Il faut ajouter la possibilité de réguler leur nombre par promo.
            $("#site_and_promo_only_guest_number").fadeIn();//... Il faut ajouter la possibilité de réguler leur nombre par promo.
            $("#graduated_only_guest_number").fadeIn();//... Il faut ajouter la possibilité de réguler leur nombre par promo.

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
                });//... Il faut pouvoir modifier facilement le nombre d'invités, en cliquant simplement dans le tableau.

            var guests_row_there = false;
            $("#table_availabilities #specification_table tbody tr").each(function()
                {
                    if($(this).children(":nth-child(2)").text()=='Tous' && $(this).children(":nth-child(3)").text()=='Invités')
                    {
                        $(this).children(":nth-child(6)").unbind('click');//On ne veux toutefois pas ajouter d'invités à des invités...
                        return guests_row_there = true;
                    }
                });//On détermine maintenant si la ligne traitant des invités est déjà présente.
            if(!guests_row_there)//Si ce n'est pas le cas, on l'ajoute.
            {
                add_row('Tous', 'Invités', 0, 0, 0);
                $("#table_availabilities #specification_table tbody tr:last :nth-child(7)").children().remove();

                var message = '<div class="alert alert-info alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Cliquez</strong> sur les champs prix, promos et nombre d\'invités pour changer les valeurs de base ! <br>Pour valider les changements, <strong>cliquez autre part</strong>' + '</div>';//On ajoute un joli message expliquant comment éditer.
                $("#specific_message").fadeIn().append(message);
            }
        }
        else//Si l'utilisateur répond "Non" à la question "Voulez vous des invités ?"
        {
            $("#table_availabilities #specification_table tbody tr").each(function()
                {
                    if($(this).children(":nth-child(2)").text()=='Tous' && $(this).children(":nth-child(3)").text()=='Invités')
                    {
                        $(this).remove();
                    }
                });//Il faut enlever la ligne Invités du tableau, vu qu'il n'y en a plus.

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

        if($("input:radio[name=graduated_icam]:checked").val()==1)//Si l'utilisateur répond "Oui" à la question "Voulez vous des diplomés ayant encore PI ?"
        {
            $("#graduated").fadeIn();
        }
        else//Si l'utilisateur répond "Non" à la question "Voulez vous des diplomés ayant encore PI ?"
        {
            $("#graduated").fadeOut();//On enlève la possibilité d'ajouter des promos diplomées.

            var valeurs_promos_diplomees = [];
            var promos_diplomees = $("#graduated_choice_promo option:not(:first)").each(function()
                {
                    valeurs_promos_diplomees.push($(this).val());
                });//On récupère les valeurs des promos diplômées depuis le html

            $("#table_availabilities #specification_table tbody tr").each(function()
                {
                    if($.inArray($(this).children(":nth-child(3)").text(), valeurs_promos_diplomees)!= -1)
                    {
                        $(this).remove();
                    }
                });//On enlève les lignes contenant des promos diplomées.
        }

        if($("input:radio[name=permanents]:checked").val()==1)//Si l'utilisateur répond "Oui" à la question "Voulez vous des permanents ?"
        {
            var permanent_row_there = false;
            $("#table_availabilities #specification_table tbody tr").each(function()
                {
                    if($(this).children(":nth-child(2)").text()=='Tous' && $(this).children(":nth-child(3)").text()=='Permanents')
                    {
                        return permanent_row_there = true;
                    }
                });//On détermine si la ligne des permanents est là ou non
            if(!permanent_row_there)//Si elle n'y est pas
            {
                add_row('Tous', 'Permanents', 0, 0, 0);//On l'ajoute
                $("#table_availabilities #specification_table tbody tr:last :nth-child(7)").children().remove();//On enlève la possibilité de supprimer la ligne

                var message = '<div class="alert alert-info alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Cliquez</strong> sur les champs prix, promos et nombre d\'invités pour changer les valeurs de base ! <br>Pour valider les changements, <strong>cliquez autre part</strong>' + '</div>';//On crée le tooltip ...
                $("#specific_message").fadeIn().append(message);//... Et on l'affiche.
            }
        }
        else//Si l'utilisateur répond "Non" à la question "Voulez vous des permanents ?"
        {
            $("#table_availabilities #specification_table tbody tr").each(function()
                {
                    if($(this).children(":nth-child(2)").text()=='Tous' && $(this).children(":nth-child(3)").text()=='Permanents')
                    {
                        $(this).remove();
                    }
                });//On les vire s'ils sont présents dans le tableau.
        }

        if($("input:radio[name=options]:checked").val()==1)//Si l'utilisateur répond "Oui" à la question "Voulez vous des options ?"
        {
            $("#options").fadeIn();//On les affiche.

            if($("#options #option_accordion").children().length==0)
            {
                add_option(1);//On ajoute une option de base.
            }

            $("input:radio[name=options]").unbind('change');//On empèche de rechanger bêtement.
            $("input:radio[name=options][value=0]:not(.bound_click)").addClass('bound_click').click(function()//Si on clique sur le bouton "pas d'options".
                {

                    var confirm_click = window.confirm("Si vous cliquez sur confirmer, vous allez annuler tout ce que vous avez fait sur vos options, est-ce ce que vous souhaitez ?");//On affiche une demande de confirmation
                    if(confirm_click)//S'il confirme
                    {
                        question_change();//On le laisse faire, et il va vider ses options (redirigé vers le else de 10 lignes plus bas).
                        $("input:radio[name=options][value=1]").click(function()
                            {
                                question_change();//On rajoute quand même la possibilité d'en ajouter de nouveau pour le mec indécis.
                            });
                        $(this).removeClass('bound_click').unbind('click');//On enlève la classe bound_click qu'on avait ajouté pour savoir si on avait déjà le confirm.
                    }
                    else
                    {
                        $("input:radio[name=options][value=1]").prop('checked', true);//Sinon, on recheck le bouton oui, puisque ce blaireau voulait juste faire joujou, pour que ça repasse comme avant.
                    }
                });
        }
        else//Si l'utilisateur répond "Non" à la question "Voulez vous des options ?"
        {
            $("#options #option_accordion").empty();//On les vide (prend effet que si il y en a donc osef)
            $("#options").fadeOut();//On enlève leur affichage.
        }
    }
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
        case 'site_and_promo'://Le traitement pour site_and_promo et graduated est exactement le même. Nous pouvons donc leur appliquer le même code.
        case 'graduated'://Le traitement pour site_and_promo et graduated est exactement le même. Nous pouvons donc leur appliquer le même code.
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
 * @param {float} prix
 * @param {int} quota
 * @param {int} guest_number
 */
function add_row(site, promo, prix=null, quota=null, guest_number=0)
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
    new_row.children(":nth-child(4)").text(prix+"€");
    new_row.children(":nth-child(5)").text(quota);
    new_row.children(":nth-child(6)").text(guest_number);
    new_row.children(":nth-child(7)").html('<button id="add_site_promo" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></button>'); //Potentiellement, il n'y avait pas de bouton supprimer (si on l'avait justement enlevé pour les promos permanents & invités), il faut donc le rajouter au cas où.
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
                var input_value =($(this).val()!="" && $(this).val()>0) ? Math.round($(this).val(), 2) : 0; //On arrondit au centime au cas ou l'utilisateur soit vicieux. On a pensé à toi celui qui met des négatifs fdp
                $(this).parent().text(input_value+'€');//On l'affiche à la place de l'input.
            });//Disparition de l'input si on clique ailleurs.
        });//On ajoute la possibilité de changer les valeurs des lignes une fois inscrites en cliquant simplement dessus. L'input disparait une fois qu'on clique ailleurs. Ceci s'applique au changement de prix.

    new_row.children(":nth-child(5)").click(function() {
        var current_value =$(this).text();
        $(this).html("<input class='form-control' type='number' min=0 value='" + current_value + "' >");
        $(this).children().focus();
        $(this).children().blur(function()
            {
                var input_value =($(this).val()!="" && $(this).val()>0) ? Math.round($(this).val(), 0) : 0;
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