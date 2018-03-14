function edit_initialisation()
{
    function input_not_empty()
    {
        return this.value.length >0;
    }

    var previous_guests = $(".guest_firstname").filter(input_not_empty).closest('.guest_form');

    previous_guests.find(".guest_firstname, .guest_lastname").keyup().off('keyup').keyup(function()
    {
        var prenom = $(this).parents('.guest_inputs').find('.guest_firstname').val();
        var nom = $(this).parents('.guest_inputs').find('.guest_lastname').val();
        $(this).parents('.guest_form').find('.actual_guest_title').text(prenom + " " + nom);
    });
    $("input:checkbox:checked").change().off('change').prop('disabled', true);

    $("#total_price, #icam_total_price, #guests_total_prices").text("0€");
}
