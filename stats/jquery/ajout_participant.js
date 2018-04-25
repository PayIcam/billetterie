$(document).ready(function()
{
    $("#message_submit").hide();

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

            if(current_path == base_path + "stats/ajout_participant.php")
            {
                if(action_url.indexOf(public_url + "stats/php/ajout_participant.php?event_id=" + event_id) == -1)
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
            if($('input[name=telephone]').length)
            {
                if($('input[name=telephone]').val().length > 25)
                {
                    add_alert('Le numéro de téléphone est trop long');
                }
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
                            document.location.href = public_url + 'stats/ajout_options.php?event_id=' + event_id + '&participant_id=' + data.participant_id;
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
                //         //     document.location.href = public_url + 'stats/edit_participant.php?event_id=' + event_id + '&participant_id=' + data.participant_id;
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