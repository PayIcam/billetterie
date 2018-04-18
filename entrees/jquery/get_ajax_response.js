function participant_arrives()
{
    // $.get(
    // {
    //     url: public_url + 'entrees/php/gestion_entrees.php',
    //     data: {event_id:event_id, action:'arrival', participant_id:this.parent().prop('data-participant_id')},
    //     success: adjust_button_participant_arrives
    // });
    adjust_button_participant_arrives($(this));
}
function adjust_button_participant_arrives(button_row)
{
    console.log(button_row);
    button_row.removeClass('is_out, btn-success').addClass('is_in, btn-danger').text('✘').off('click').click(participant_leaves);
}

function participant_leaves()
{
    // $.get(
    // {
    //     url: public_url + 'entrees/php/gestion_entrees.php',
    //     data: {event_id:event_id, action:'departure', participant_id:$(this).parent().prop('data-participant_id')},
    //     success: adjust_button_participant_leaves
    // });
    adjust_button_participant_leaves($(this));
}
function adjust_button_participant_leaves(button_row)
{
    console.log(button_row);
    button_row.removeClass('is_in, btn-danger').addClass('is_out, btn-success').text('✔').off('click').click(participant_arrives);
}

$('input[name=recherche]').keyup(function()
{
    function ajax_success(data)
    {
        console.log('success');
        console.log(data);
        $('table tbody').html(data);
        $('[data-toggle="popover"]').popover();
        $('.is_in').click(participant_leaves);
        $('.is_out').click(participant_arrives);
    }

    function error_ajax(jqXHR, textStatus, errorThrown)
    {
        console.log(jqXHR);
        console.log();
        console.log(textStatus);
        console.log();
        console.log(errorThrown);
    }

    setTimeout(function()
    {
        console.log(public_url + 'entrees/php/creation_tableau.php?event_id=' + event_id);

        $.post(
        {
            url: public_url + 'entrees/php/creation_tableau.php?event_id=' + event_id,
            data: {recherche: $('input[name=recherche]').val()},
            dataType: 'html',
            success: ajax_success,
            error: error_ajax,
        });
    }, 100);
});

$('form').submit(function(submit)
{
    event.preventDefault();
});

