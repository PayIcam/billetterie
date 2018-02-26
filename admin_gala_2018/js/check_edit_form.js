var bracelet_input = document.getElementsByName('bracelet_id');
bracelet_input = bracelet_input[0];
var bracelet_error = document.querySelector('.erreur_saisie_bracelet');
var submit_button = document.querySelector('input[type="submit"]');

var page_form = bracelet_input.parentNode.parentNode.parentNode;

page_form.addEventListener('submit', function(submit) {
    var bracelet_value = bracelet_input.value;
    var regex_bracelet_good_number = /^[12][0-9]{1,3}$|^[0-9]{1,3}$|^3[0-2][0-9]{2}$/;
    if(!regex_bracelet_good_number.test(bracelet_value))
    {
        bracelet_error.style.display='inline';
        console.log(bracelet_error.style.display);
        submit.preventDefault();
    }
});