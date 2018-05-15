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

            if(current_path == base_path + "super_admin/edit_config.php")
            {
                if(action_url.indexOf(public_url + "super_admin/php/edit_config.php") == -1)
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

        $("#alerts").empty();

        if(check_urls())
        {
            var ticketing = $("#ticketing_available").prop('checked') ? 1:0 ;
            var event_administration = $("#event_administration_available").prop('checked') ? 1:0 ;
            var inscriptions = $("#inscriptions_available").prop('checked') ? 1:0 ;
            var participant_administration = $("#participant_administration_available").prop('checked') ? 1:0;

            var post_array = {ticketing:ticketing, event_administration:event_administration, inscriptions:inscriptions, participant_administration:participant_administration};

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
                console.log(data);
                $('.waiting').hide();
                if(data=="La mise à jour a bien été effectuée")
                {
                    $("#button_submit_form").prop('disabled', '');
                    var message_displayed = '<div class="alert alert-success alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Parfait ! </strong>' + data + '</div>';
                    $("#alerts").append(message_displayed);
                }
                else
                {
                    $("#alerts").append(data);
                    $("#button_submit_form").prop('disabled', '');
                }
            }

            $.post(
            {
                url: action_url,
                data: post_array,
                dataType: 'html',
                success: ajax_success,
                error: error_ajax
            });
        }
        submit.preventDefault();
    });
});