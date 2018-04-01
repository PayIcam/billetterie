$('form').submit(function(submit)
{
    function add_error(message)
    {
        var message_displayed = '<div class="alert alert-danger alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Attention ! </strong>' + message + '</div>'
        $("#alerts").append(message_displayed);
    }

    $('#alerts').empty();
    $('#alerts').show();

    var error = false;

    if($('input[name=nom]').length)
    {
        var nom = $('input[name=nom]').val();
        if(nom.length > 45)
        {
            error = true;
            add_error('Le nouveau nom est trop grand');
        }
    }
    if($('input[name=prenom]').length)
    {
        var prenom = $('input[name=prenom]').val();
        if(prenom.length > 45)
        {
            error = true;
            add_error('Le nouveau prenom est trop grand');
        }
    }
    var bracelet_identification = $('input[name=bracelet_identification]').val();
    if(bracelet_identification.length > 25)
    {
        error = true;
        add_error("L'identifiant de bracelet est trop grand");
    }
    else if(bracelet_identification=='')
    {
        bracelet_identification = null;
    }

    var post_url = $('form').prop('action');
    if($.trim(post_url.split('?')[0]) != $.trim(public_url + 'stats/php/update_participant.php'))
    {
        error = true;
        add_error('As tu joué avec ma page ?');
    }
    if(!error)
    {
        function ajax_success(data)
        {
            if(data=='Vos modifications ont bien été ajoutées')
            {
                var message_displayed = '<div class="alert alert-success alert-dismissible">' + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + '<strong>Parfait ! </strong>' + data + '</div>';
                $("#alerts").append(message_displayed);

                $('table:first td[class=nom]').text(nom);
                $('h1 .nom').text(nom);
                $('table:first td[class=prenom]').text(prenom);
                $('h1 .prenom').text(prenom);
                $('table:first td[class=bracelet_identification]').text(bracelet_identification);
                $("#submit_participant_update").prop('disabled', '');

                setTimeout(function()
                {
                    $('#alerts').fadeOut(1000);
                }, 1000);
            }
            else
            {
                $("#alerts").append(data);
                $("#submit_participant_update").prop('disabled', '');
            }
        }
        function error_ajax()
        {
            add_error("La requête Ajax permettant d'éditer les infos du participant a échoué");
            $("#submit_participant_update").prop('disabled', '');
        }

        submit.preventDefault();
        $("#submit_participant_update").prop('disabled', 'disabled');

        $.post(
        {
            url: post_url,
            data: {nom: nom, prenom: prenom, bracelet_identification: bracelet_identification},
            dataType: 'html',
            success: ajax_success,
            error: error_ajax,
        });
    }
    else
    {
        submit.preventDefault();
    }
});
