var options = document.querySelectorAll('option');
var select = options[0].parentNode;
var form = select.parentNode;

for(var i=0, len = options.length; i<len; i++)
{
    var option = options[i];
    option.addEventListener('click', function() {
        form.submit();
    })
}