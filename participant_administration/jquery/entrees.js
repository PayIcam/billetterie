/**
 * La gestion des entrées se fait uniquement en Ajax.
 *
 * Il faut aller chercher les participants correspondant à la recherche en Ajax.
 * Ensuite, sur chaque ligne, il est possible de faire entrer ou sortir un participant. Cela se passe également en Ajax.
 *
 */

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
    $('button[aria-describedby]').click();//On veux enlever les pop-overs quand on clique quelque part.
    $('table .is_out, table .is_in').prop('disabled', 'disabled');//On empèche de recliquer quelque part
    $.get(
    {
        url: public_url + 'participant_administration/php/gestion_entrees.php',
        data: {event_id:event_id, action:'arrival', participant_id:button_row.parents('tr').data('participant_id')},
        dataType: 'json',
        success: function(data)
        {
            adjust_button_participant_arrives(button_row, data);//Si la modification a marché, on ajuste le bouton
            $('table .is_out, table .is_in').prop('disabled', '');
        },
        error: error_ajax
    });
}
function adjust_button_participant_arrives(button_row, data)
{
    $('#nombre_entrees').text(data.arrival_number);//Ajustement du nombre d'entrées
    $('#alerts').text(data.message);
    button_row.removeClass('is_out, btn-success').addClass('is_in, btn-danger').text('✘').off('click').click(function()
        {//On inverse les classes, et on inverse la fonction sur un click
            participant_leaves(button_row);
        });
}

function participant_leaves(button_row)
{
    $('button[aria-describedby]').click();//On veux enlever les pop-overs quand on clique quelque part.
    $('table .is_out, table .is_in').prop('disabled', 'disabled');//On empèche de recliquer quelque part
    $.get(
    {
        url: public_url + 'participant_administration/php/gestion_entrees.php',
        data: {event_id:event_id, action:'departure', participant_id:button_row.parents('tr').data('participant_id')},
        dataType: 'json',
        success: function(data)
        {
            adjust_button_participant_leaves(button_row, data);//Si la modification a marché, on ajuste le bouton
            $('table .is_out, table .is_in').prop('disabled', '');
        },
        error: error_ajax
    });
}
function adjust_button_participant_leaves(button_row, data)
{
    $('#nombre_entrees').text(data.arrival_number);//Ajustement du nombre d'entrées
    $('#alerts').text(data.message);
    button_row.removeClass('is_in, btn-danger').addClass('is_out, btn-success').text('✔').off('click').click(function()
        {//On inverse les classes, et on inverse la fonction sur un click
            participant_arrives(button_row);
        });
}

function ajax_success(data)
{
    $('table tbody').html(data);//On remplit le tableau
    $('[data-toggle="popover"]').popover();//Il faut activer les popovers à chaque fois
    $('.is_in').click(function() { participant_leaves($(this))});//Pour les participants déjà entrées, si on clique, on sortira
    $('.is_out').click(function() { participant_arrives($(this))});//Pour les participants pas encore arrivés, si on clique, on entrera
    if($('table tbody tr').length ==1) {
        $('table tbody tr .options[data-toggle="popover"]').click();
    }
}

$('input[name=recherche]').keyup(function()
{
    clearTimeout(timeout);

    var timeout = setTimeout(function()
    {
        $('button[aria-describedby]').click();//On veux enlever les pop-overs quand on clique quelque part.
        $('.popover').popover('hide');;//On veux enlever les pop-overs quand on clique quelque part.
        $.post(
        {
            url: public_url + 'participant_administration/php/creation_tableau.php?event_id=' + event_id,
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