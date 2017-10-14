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
    $('div.popover').fadeOut(500,function(event){$(this).remove();});
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
  checkXhr('guestsId');
  xhr['guestsId'] = jQuery.ajax({
    type : "POST",
    url : "resultat_recherche.php",
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

(function($){


  /**
   * Fonction pour uniformiser les select (qu'ils soient égaux)
  **/
  $(function($){
    $("#action1").change(function() {
      $("#action2").val($(this).val()).attr("selected","selected");
    });
    $("#action2").change(function() {
      $("#action1").val($(this).val()).attr("selected","selected");
    });
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
  
  // jQuery(this).attr('href','export_liste_participants.php?'+searchForm.serialize());return false;
  $("#export").click(function(event){
    // $.ajax({type:"POST", data: searchForm.serialize(), url:"export_liste_participants.php",
    //   success: function(data){
    //     console.log(data);
    //     $("#post").html(data);
    //   },
    //   error: function(){
    //     $("#post").html('Une erreur est survenue.');
    //   }
    // });
    document.location.href="export_liste_participants.php?"+searchForm.serialize();
    return false;
  });

  var buttonsRadio = $('.buttons-radio');
  buttonsRadio.change(function(event) {
    var thisAttr = $(this).attr("checked");
    $('.buttons-radio').not('selector expression').each(function(event) {
      $(this).removeAttr("checked");
    });
    $(this).attr("checked",thisAttr);
  });


  var selectPromos = $('#selectPromos');
  var selectAllPromosCheckbox = $('#selectAllPromos');

  // Quand on édite le select, on décoche sélectionner toutes les promos
  selectPromos.change(function(event) {
    selectAllPromosCheckbox.removeAttr("checked");
    selectPromos.find('option:not(:selected)').each(function() {
      $(this).removeAttr("selected");
    });
  });
  selectAllPromosCheckbox.click(function(event) {
    if ($(this).attr("checked")){
      selectPromos.find('option').each(function() {
        $(this).attr("selected","selected");
      });
    }else{
      selectPromos.find('option').each(function() {
        $(this).removeAttr("selected");
      });
    };
  });

})(jQuery);