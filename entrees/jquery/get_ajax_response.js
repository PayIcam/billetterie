function error_ajax(jqXHR, textStatus, errorThrown)
{
    $('table .is_out, table .is_in').prop('disabled', '');
    console.log(jqXHR);
    console.log();
    console.log(textStatus);
    console.log();
    console.log(errorThrown);
}

function participant_arrives(button_row)
{
    $('button[aria-describedby]').click();
    console.log($('table .is_out, table .is_in'));
    $('table .is_out, table .is_in').prop('disabled', 'disabled');
    $.get(
    {
        url: public_url + 'entrees/php/gestion_entrees.php',
        data: {event_id:event_id, action:'arrival', participant_id:button_row.parents('tr').data('participant_id')},
        dataType: 'json',
        success: function(data)
        {
            console.log(data);
            adjust_button_participant_arrives(button_row, data);
            $('table .is_out, table .is_in').prop('disabled', '');
        },
        error: error_ajax
    });
}
function adjust_button_participant_arrives(button_row, data)
{
    $('#nombre_entrees').text(data.arrival_number);
    $('#alerts').text(data.message);
    button_row.removeClass('is_out, btn-success').addClass('is_in, btn-danger').text('✘').off('click').click(function()
        {
            participant_leaves(button_row);
        });
}

function participant_leaves(button_row)
{
    $('button[aria-describedby]').click();
    $('table .is_out, table .is_in').prop('disabled', 'disabled');
    $.get(
    {
        url: public_url + 'entrees/php/gestion_entrees.php',
        data: {event_id:event_id, action:'departure', participant_id:button_row.parents('tr').data('participant_id')},
        dataType: 'json',
        success: function(data)
        {
            console.log(data);
            adjust_button_participant_leaves(button_row, data);
            $('table .is_out, table .is_in').prop('disabled', '');
        },
        error: error_ajax
    });
}
function adjust_button_participant_leaves(button_row, data)
{
    $('#nombre_entrees').text(data.arrival_number);
    $('#alerts').text(data.message);
    button_row.removeClass('is_in, btn-danger').addClass('is_out, btn-success').text('✔').off('click').click(function()
        {
            participant_arrives(button_row);
        });
}

$('input[name=recherche]').keyup(function()
{
    function ajax_success(data)
    {
        $('table tbody').html(data);
        $('[data-toggle="popover"]').popover();
        $('.is_in').click(function() { participant_leaves($(this))});
        $('.is_out').click(function() { participant_arrives($(this))});
    }

    setTimeout(function()
    {
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

$('input[name=recherche]').keyup();