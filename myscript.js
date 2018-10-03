function loadMore(page, id, type, srt){
   var dataString = 'page=' + page + '&id=' + id + '&type=' + type + '&srt=' + srt;
   var nextPage = +page+1;
   // $("#pag_flash").show();
   // $("#pag_flash").delay(400).fadeOut(400).html('<img src="../images/like_loader.gif" />');
   
   $.ajax({
             type: "POST",
             url: "paginate.php",
            data: dataString,
            cache: false,
            async: false,
            success: function(result){          
            
              var position=result.indexOf("||");
                    var warningMessage=result.substring(0,position);
                    if(warningMessage=='error'){
                      var errorMessage=result.substring(position+2);
                        $("#pag_link").html(errorMessage);                         
                    }else {
                        $("#pag_link").html('<a id="loadMore" onclick=loadMore("'+nextPage+'","'+id+'","'+type+'","'+srt+'") class="more">Load More</a>');
                        $("#content").append(result);
                    }
            
      }
  });
}

// $(document).ready(function(){
//     $('.st_tr').hide();
//     $('.vid_box').mouseover(function() {
//        $("#"+this.id).css("background-color", "#EDEDED");
//        var p ="#tr_"+this.id;
//        $(p).show();
//     });
//     $('.vid_box').mouseout(function() {
//        var p ="#tr_"+this.id;
//        $(p).hide();
//        $("#"+this.id).css("background-color", "#fff");
//     });
// });

$(document).ready(function(){
    $('.st_tr').hide();
});

$(document).on("mouseover",".vid_box", function () {
  $("#"+this.id).css("background-color", "#EDEDED");
       var p ="#tr_"+this.id;
       $(p).show();
});

$(document).on("mouseout",".vid_box", function () {
  var p ="#tr_"+this.id;
       $(p).hide();
       $("#"+this.id).css("background-color", "#fff");
});
$(document).ajaxComplete(function(){
   $('.st_tr').hide();
});




/* Open the sidenav */
// function openNav() {
//     document.getElementById("mySidenav").style.display = "block";
// }

/* Close/hide the sidenav */
// function closeNav() {
//     document.getElementById("mySidenav").style.display = "none";
// }
