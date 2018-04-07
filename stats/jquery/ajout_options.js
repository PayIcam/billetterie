$(document).ready(function()
{
    $("#message_submit").hide();

    $('form').submit(function(submit)
    {
        function add_error(message)
        {
            var message_displayed = '<div class="alert alert-danger alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Attention ! </strong>' + message + '</div>'
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
                    add_error("A quoi joues tu ? Arrète de trafiquer les redirections.");
                    return false;
                }
            }
            else
            {
                add_error("Il y a une erreur, l'indication du chemin est mauvaise");
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
                            add_error("L'id de l'option a été altérée.");
                        }
                    }
                    else
                    {
                        error=true;
                        add_error("Impossible de retrouver de quelle option il s'agit.");
                    }
                });
            }
            else
            {
                error=true;
                add_error("Rien n'a été sélectionné");
            }
            return !error;
        }

        $("#alerts").empty();

        if(check_urls())
        {
            if(check_form())
            {
                var options = [];
                $('#options .option').find('input:checkbox:checked, option:selected').each(function()
                {
                    var option_id = $(this).parents('.option').find('input[name=option_id]').val();
                    if($(this).parents('.option').find('input:checkbox:checked').length)
                    {
                        var type = "Checkbox";
                        var complement = "";
                    }
                    else if($(this).parents('.option').find('option:selected').length)
                    {
                        var type = "Select";
                        var complement = $(this).parents('.option').find(':selected').val();
                    }
                    var option = {option_id: option_id, type: type, complement: complement};
                    options.push(option);
                });

                $("#alerts").empty();
                $("#message_submit").show();
                $('.waiting').show();
                $("#button_submit_form").prop('disabled', 'disabled');
                action_url = $('form').prop('action');

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
                function error_ajax(jqXHR, textStatus,errorThrown)
                {
                    console.log(jqXHR);
                    console.log();
                    console.log(textStatus);
                    console.log();
                    console.log(errorThrown);
                    add_error('La requête Ajax permettant de submit les informations et ajouter le participant a échoué');
                    $("#submit_form").prop('disabled', '');
                    $('.waiting').hide();
                }

                $.post(
                {
                    url: action_url,
                    data: {options:options},
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
                // function error_ajax(jqXHR, textStatus,errorThrown)
                // {
                //     console.log(jqXHR);
                //     console.log();
                //     console.log(textStatus);
                //     console.log();
                //     console.log(errorThrown);
                //     add_error('La requête Ajax permettant de submit les informations et ajouter le participant a échoué');
                //     $("#button_submit_form").prop('disabled', '');
                //     $('.waiting').hide();
                // }

                // $.post(
                // {
                //     url: action_url,
                //     data: {options:options},
                //     dataType: 'html',
                //     success: ajax_success,
                //     error: error_ajax
                // });

            }
        }
        submit.preventDefault();
    });
});