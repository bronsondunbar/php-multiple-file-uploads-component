
/* Upload files directly to server */

$(document).ready(function () {

  var dragAndDrop = $(".drag-and-drop");
  var droppedFiles = false;

  var userDroppedFiles = dragAndDrop.find("input[type='file']");
  var displaySelectedFiles = dragAndDrop.find("label");

  var showFiles = function(files) {
    displaySelectedFiles.text(files.length > 1 ? (userDroppedFiles.attr("data-multiple-caption") || "").replace("{count}", files.length) : files[0].name);
  };
  
  dragAndDrop.on("drag dragstart dragend dragover dragenter dragleave drop", function(e) {
    e.preventDefault();
    e.stopPropagation();
  })

  .on("dragover dragenter", function() {
    dragAndDrop.addClass('is-dragover');
  })

  .on("dragleave dragend drop", function() {
    dragAndDrop.removeClass("is-dragover");
  })

  .on("drop", function(e) {
    droppedFiles = e.originalEvent.dataTransfer.files;
    showFiles( droppedFiles );
  });

  userDroppedFiles.on("change", function(e) {
    showFiles(e.target.files);
  });

  $("form#server").submit(function (event) {

    event.preventDefault();

    var form = $("form#server");
    var userUpload = $("#userServerUpload")[0];
    var userDetails = form.serializeArray();
    var userSelectedFiles = userUpload.files;
    var formData = new FormData();

    var dragAndDrop = $(".drag-and-drop");
    var userDroppedFiles = dragAndDrop.find("input[type='file']");

    if (droppedFiles) {

      $.each( droppedFiles, function(i, file) {
        formData.append( userDroppedFiles.attr("name"), file );
      });

    } else {

      for (var i = 0; i < userSelectedFiles.length; i++) {

        var file = userSelectedFiles[i];

        formData.append("files[]", file, file.name);

      }

    };

    $(userDetails).each(function (index, element) {

      formData.append(element.name, element.value);

    });

    $.ajax({
      url: "services/serverUpload.php",
      type: "POST",
      data: formData,
      dataType: "json",
      contentType: false,
      processData: false,

      beforeSend: function (data) {
        $("body").animate({
            scrollTop: 0
        }, "fast");

        $(".loader").fadeIn();
        $("body").css("overflow", "hidden");
      },

      error: function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
      },

      complete: function (data) {

        if (data.responseJSON.nameError != undefined) {

          $(".message").removeClass("success info");
          $(".message").fadeOut(function () {
            $(".message").html(data.responseJSON.nameError).addClass("error").fadeIn();
          });
          grecaptcha.reset();

        } else if (data.responseJSON.emailError != undefined) {

          $(".message").removeClass("success info");
          $(".message").fadeOut(function () {
            $(".message").html(data.responseJSON.emailError).addClass("error").fadeIn();
          });
          grecaptcha.reset();

        } else if (data.responseJSON.captchaError != undefined) {

          $(".message").removeClass("success info");
          $(".message").fadeOut(function () {
            $(".message").html(data.responseJSON.captchaError).addClass("error").fadeIn();
          });
          grecaptcha.reset();

        } else if (data.responseJSON.uploadError != undefined) {

          $(".message").removeClass("success info");
          $(".message").fadeOut(function () {
            $(".message").html(data.responseJSON.uploadError).addClass("error").fadeIn();
          });
          grecaptcha.reset();

        } else if (data.responseJSON.uploadReport != undefined) {

          $(".message").removeClass("error info");
          $(".message").fadeOut(function () {
            $(".message").html(data.responseJSON.uploadReport).addClass("info").fadeIn();
          });
          grecaptcha.reset();

          $("#name").val("");
          $("#email").val("");

        }

        $(".loader").fadeOut();
        $("body").css("overflow", "scroll");

      }
    });

  });

});

