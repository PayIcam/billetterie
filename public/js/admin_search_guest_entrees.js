var xhr = new Array();
var searchForm = jQuery('#form');
var searchInput1 = jQuery('#recherche1');
var pageHiddenInput = jQuery('input[name="page"]');

function checkXhr(xhrName){
  if(xhr[xhrName]){
    xhr[xhrName].abort();
    delete xhr[xhrName];
  }
}

function checkPopover () {
  jQuery(function($){
    $('#guestsList a[rel="popover"]').each(function(){
      var title = $(this).next('.infos').find('.title').html();
      var message = $(this).next('.infos').find('.message').html();
      $(this).popover({
        'content':message,
        'title':title,
        'placement':'left',
        'trigger':'hover',
        'html':true
      });
    });
  });
}
checkPopover();

var loader = jQuery('.loader');
function showLoader () {
  loader.each(function(event) {
    jQuery(this).fadeIn();
  });
}
function hideLoader () {
  loader.each(function(event) {
    jQuery(this).fadeOut();
  });
}

function refreshGuestList() {
  showLoader();
  checkXhr('guestId');
  xhr['guestId'] = jQuery.ajax({
    type : "POST",
    url : "resultat_guest_soiree.php",
    data : searchForm.serialize(),
    success: function(server_response){
      jQuery("#resultat").empty().html(server_response).show();
      checkPopover();
      hideLoader();
    }
  });
}

/**
 * Fonction pour cocher toutes les chekboxes d'un coup
**/
function toggleChecked(status) {
  jQuery(".checkbox").each( function() {
    jQuery(this).attr("checked",status);
  });
}

function arrivalMarkUpCheck() {
  $('.arrivalMarkUp').each(function(event) {
    var btn = $(this);
    btn.click(function(event) {
      console.log(btn);
      var id = $(btn).attr('id');
      btn.addClass('disabled');
      checkXhr('arrival'+id);
      xhr['arrival'+id] = jQuery.ajax({
        type : "POST",
        url : "soiree_validee.php",
        data : 'id='+id+'&action='+((btn.hasClass('btn-success'))?'ok':'remove'),
        success: function(server_response){
          if (btn.hasClass('btn-success')) {
            btn.removeClass('btn-success').addClass('btn-danger').attr('title','Marquer l\'invité comme NON arrivé au Gala');
            btn.find('i').removeClass('icon-ok').addClass('icon-remove');
          }else{
            btn.removeClass('btn-danger').addClass('btn-success').attr('title','Marquer l\'invité comme arrivé au Gala');
            btn.find('i').removeClass('icon-remove').addClass('icon-ok');
          };
          btn.removeClass('disabled');
          searchInput1.focus();
          $('popover').remove();
        }
      });
    });
  });
}

(function($){

  /**
   * Fonction pour uniformiser les select (qu'ils soient égaux)
  **/
  $("#action1").change(function() {
    $("#action2").val($(this).val()).attr("selected","selected");
  });
  $("#action2").change(function() {
    $("#action1").val($(this).val()).attr("selected","selected");
  });


  /**
   * Fonction de recherche
  **/
  $('.search-query').each(function(event) {
    var searchInput = $(this);
    var searchInputVal = $(this).val();
    searchInput.keyup(function() {
      var recherche = searchInput.val();
      $('.search-query').each(function(event) {
        $(this).val(recherche)
      });
      refreshGuestList();
    });
  });

  searchForm.submit(function(event) {
    refreshGuestList();
    return false;
  });

  $(".page").click(function(event){
    pageHiddenInput.val($(this).attr('id').replace('p',''));
    refreshGuestList();
    return false;
  });

})(jQuery);