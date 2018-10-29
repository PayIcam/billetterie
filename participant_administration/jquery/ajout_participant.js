/**
 * L'ajout de participants se fait en Ajax, on vérifie que les données ont l'air bonnes, et on les envoie vers php/ajout_participants.php
 *
 * Comme d'habitude, on affiche les erreurs potentielles dans le div#alerts, on utilise add_alert pour en ajouter une
 * La vérification n'est pas si importante en Js direct pour une fois, de toute façon, vu que ça se fait en Ajax, ça ira vite quoi qu'il soit.
 *
 * Les données envoyées sont toutes les données du formulaire, gr$ace à $('form').serialize() pour plus de facilité.
 */

$(document).ready(function()
{
    $("#message_submit").hide();

    promos = JSON.parse(promos);
    sites = JSON.parse(sites);

    $('.typeahead-user').typeahead({
        source: function (query, process) {
            return $.get('../autocomplete.php', { query: query, dataType: 'json' }, function (data) {
                map = {};
                usernames = [];

                $.each(JSON.parse(data), function (i, user) {
                    if($.inArray(user.promo, promos)!==-1 && $.inArray(user.site, sites)!==-1) {
                        map[user.name + ' (' + user.mail + ')'] = user;
                        usernames.push(user.name + ' (' + user.mail + ')');
                    }
                });
                process(usernames);

                return process(usernames);
            });
        },
        updater: function(display) {
            user = map[display];
            $('input[name=prenom]').val(user.firstname).attr('readonly', '');
            $('input[name=nom]').val(user.lastname).attr('readonly', '');
            $('input[name=email]').val(user.mail).attr('readonly', '');
            $('select[name=promo] > option').each(function() {
                if($(this).text() == user.promo) {
                    $('input[name=promo]').attr('type', 'text').attr('readonly', '').val($(this).val());
                    $(this).parent().hide().attr('disabled', '');
                    return false;
                }
            });
            $('select[name=site] > option').each(function() {
                if($(this).text() == user.site) {
                    $('input[name=site]').attr('type', 'text').attr('readonly', '').val($(this).val());
                    $(this).parent().hide().attr('disabled', '');
                    return false;
                }
            });

            $('.typeahead-user').text('');
            return user;
        }
    });

    $('form').submit(function(submit)
    {
        function add_alert(message, alert_type="danger")
        {
            var message_displayed = '<div class="alert alert-'+alert_type+' alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Attention ! </strong>' + message + '</div>'
            $("#alerts").append(message_displayed);
        }

        function check_urls()
        {
            var action_url = $('form').prop('action');
            var current_path = window.location.pathname;

            if(current_path == base_path + "participant_administration/ajout_participant.php")
            {
                if(action_url.indexOf(public_url + "participant_administration/php/ajout_participant.php?event_id=" + event_id) == -1)
                {
                    add_alert("A quoi joues tu ? Arrète de trafiquer les redirections.");
                    return false;
                }
            }
            else
            {
                add_alert("Il y a une erreur, l'indication du chemin est mauvaise");
                return false;
            }
            return true;
        }

        function check_form()
        {
            $error = false;
            if($('input[name=prenom]').val().length > 45)
            {
                add_alert('Le prénom est trop long');
            }
            if($('input[name=nom]').val().length > 45)
            {
                add_alert('Le nom est trop long');
            }
            if($('input[name=email]').length)
            {
                if($('input[name=email]').val().length > 255)
                {
                    add_alert('Le prénom est trop long');
                }
            }
            if($('input[name=bracelet_identification]').val().length > 25)
            {
                add_alert("L'identifiant de bracelet  est trop long");
            }
            return !$error;
        }

        $("#alerts").empty();

        if(check_urls())
        {
            if(check_form())
            {
                $("#erreurs_submit").empty();
                $("#message_submit").show();
                $('.waiting').show();
                $("#submit_form").prop('disabled', 'disabled');
                action_url = $('form').prop('action');

                function error_ajax(jqXHR, textStatus,errorThrown)
                {
                    console.log(jqXHR);
                    console.log();
                    console.log(textStatus);
                    console.log();
                    console.log(errorThrown);
                    add_alert('La requête Ajax permettant de submit les informations et ajouter le participant a échoué');
                    $("#submit_form").prop('disabled', '');
                    $('.waiting').hide();
                }

                function ajax_success(data)
                {
                    if(data.message=="L'ajout a bien été effectué")
                    {
                        var message_displayed = '<div class="alert alert-success alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Parfait ! </strong>' + data.message + '</div>';
                        $("#alerts").append(message_displayed);
                        $('form').off('submit').submit(function(submit)
                        {
                            submit.preventDefault();
                        });
                        setTimeout(function()
                        {
                            document.location.href = public_url + 'participant_administration/ajout_options.php?event_id=' + event_id + '&participant_id=' + data.participant_id;
                        }, 1000);
                    }
                    else
                    {
                        $("#alerts").append(data.message);
                        $("#submit_form").prop('disabled', '');
                        $('.waiting').hide();
                    }
                }

                $.post(
                {
                    url: action_url,
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: ajax_success,
                    error: error_ajax
                });

                // function ajax_success(data)
                // {
                //     if(data=="L'ajout a bien été effectué")
                //     {
                //         var message_displayed = '<div class="alert alert-success alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Parfait ! </strong>' + data + '</div>';
                //         $("#alerts").append(message_displayed);
                //         $('form').off('submit').submit(function(submit)
                //         {
                //             submit.preventDefault();
                //         });
                //         // setTimeout(function()
                //         // {
                //         //     document.location.href = public_url + 'participant_administration/edit_participant.php?event_id=' + event_id + '&participant_id=' + data.participant_id;
                //         // }, 1000);
                //     }
                //     else
                //     {
                //         $("#alerts").append(data);
                //         $("#submit_form").prop('disabled', '');
                //     }
                // }

                // $.post(
                // {
                //     url: action_url,
                //     data: $(this).serialize(),
                //     dataType: 'html',
                //     success: ajax_success,
                //     error: error_ajax
                // });
            }
        }

        submit.preventDefault();
    });
});