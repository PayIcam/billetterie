var quota_current_spans = document.querySelectorAll('.db_quota_data');

var quota_spans_length = quota_current_spans.length;


for(var i=0; i<quota_spans_length; i++)
{
    var quota_span = quota_current_spans[i];
    var table_cell = quota_span.parentNode;
    var input_new_quota = quota_span.nextElementChild;

    table_cell.addEventListener('dblclick', function() {
        var data = this.firstElementChild;
        var input = this.lastElementChild;

        data.style.display ='none';
        input.style.display = 'inline';
    });

}