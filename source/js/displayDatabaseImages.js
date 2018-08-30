
/* Display images from database */

$("#displayDatabaseImages").click(function (event) {
		
	event.preventDefault();

	$.ajax({
      url: "services/displayDatabaseImages.php",
      type: "POST",
      dataType: "json",
      contentType: false,
      processData: false,

      beforeSend: function (data) {
        $("body").animate({
            scrollTop: 0
        }, "fast");

        $(".loader").css("visibility", "visible");
        $("body").css("overflow", "hidden");
      },

      error: function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
      },

      complete: function (data) {

        if (data.responseJSON.displayError != undefined) {

        	$("#databaseImages").html(data.responseJSON.displayError).fadeIn();

        } else if (data.responseJSON.displaySuccess != undefined) {

       		$("#databaseImages").html(data.responseJSON.displaySuccess).fadeIn();

        }

        $(".loader").css("visibility", "hidden");
        $("body").css("overflow", "scroll");

      }
    });

})