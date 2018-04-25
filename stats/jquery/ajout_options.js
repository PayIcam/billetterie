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

            if(current_path == base_path + "stats/ajout_options.php")
            {
                if(action_url.indexOf(public_url + "stats/php/ajout_options.php?event_id=" + event_id) == -1)
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
            error = false;
            if($('#options .option').find('input:checkbox:checked, option:selected').not('[disabled]').length)
            {
                $('#options .option').each(function()
                {
                    if($(this).find('input[name=option_id]').length)
                    {
                        if(!(Number.isInteger(parseInt($(this).find('input[name=option_id]').val())) && $(this).find('input[name=option_id]').val()>0))
                        {
                            error=true;
                            add_alert("L'id de l'option a été altérée.");
                        }
                    }
                    else
                    {
                        error=true;
                        add_alert("Impossible de retrouver de quelle option il s'agit.");
                    }
                });
            }
            else
            {
                error=true;
                add_alert("Rien n'a été sélectionné");
            }
            return !error;
        }

        $("#alerts").empty();

        if(check_urls())
        {
            if(check_form())
            {
                var choice_ids = [];
                $('#options .option').find('input:checkbox:checked, option:selected:not(:disabled)').each(function()
                {
                    console.log($(this));
                    if($(this).parents('div').hasClass('checkbox_option'))
                    {
                        var choice_id = $(this).parents('.option').find('input[name=choice_id]').val();
                    }
                    else if($(this).parents('div').hasClass('select_option'))
                    {
                        var choice_id = $(this).parents('.option').find(':selected').val();
                    }
                    var choice_id = {choice_id};
                    choice_ids.push(choice_id);
                });

                $("#alerts").empty();
                $("#message_submit").show();
                $('.waiting').show();
                $("#button_submit_form").prop('disabled', 'disabled');
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
                    throw('on va pas envoyer');

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
                            document.location.href = public_url + 'stats/participants.php?event_id=' + event_id;
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
                    data: {choice_ids:choice_ids},
                    dataType: 'json',
                    success: ajax_success,
                    error: error_ajax
                });

                // function ajax_success(data)
                // {
                //     console.log(data);
                //     $('.waiting').hide();
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
                //         $("#button_submit_form").prop('disabled', '');
                //     }
                // }

                // $.post(
                // {
                //     url: action_url,
                //     data: {choice_ids:choice_ids, payement: $('select[name=payement] option:selected').val(), price: $('input[name=price]').val()},
                //     dataType: 'html',
                //     success: ajax_success,
                //     error: error_ajax
                // });

            }
        }
        submit.preventDefault();
    });
});