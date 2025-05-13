//

// Module Name: Login | Forgot | Reset

// Author: Jushua FF

// Date: 09.11.2022

//



$(function () {

  var Script = {};

  (function (app) {

      var wWidth = $( window ).width();

      app.init = function() {

        app.passwordToggle();

        app.captchaVerify();

        app.forgot_password();

        app.reset_password();



        localStorage.clear();

      }



      app.passwordToggle = function() {

        $(".icon-right").click(function(){

          var attr= $(this).prev().attr("type");

          $(this).toggleClass("fa-eye fa-eye-slash");

          $(this).prev().attr("type", (attr === "password")?("text"):("password"));

        });

      }

      app.captchaVerify = function(){
        $(document).on('submit', '#login-form', function(e){
          e.preventDefault();

          var $form = $(this);
          app.goToLogin($form);
          // grecaptcha.ready(function() {
          //   grecaptcha.execute(captchaToken, {action: 'form_submission'}).then(function(token) {
          //       $.ajax({
          //         url: base_url+"login/ajax_login/validate_captcha_token",
          //         method: 'POST',
          //         dataType: 'json',
          //         data: {'g-recaptcha-response' : token},
  
          //         success: function (response) {
          //           switch(response.status){ 
  
          //             case 'error':
          //               var html = [
          //                 '<div class="alert alert-danger">', 
          //                     '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>',
          //                     response.message, 
          //                 '<div>'
          //                 ].join('').trim();
          //                 $form.prepend('<div class="alert alert-danger text-center" role="alert">'+response.message+'</div>');
                      
          //             break;
          //             case 'success':
          //               app.goToLogin($form);
          //             break;
          //             default:
          //           }
          //         }
          //       });
          //   });
          // });

        });
      }

      app.goToLogin = function($form) {

          $.ajax({

            url: base_url + "login/Ajax_login",

            type: "POST",

            data: $form.serialize(),

            dataType: "JSON",

            beforeSend: function(){

              $(".error").removeClass("error");

              $(".error-message, .alert").remove();

              $form.find('input').attr('readonly',true);

              $form.find('button').attr('disabled',true).text('Logging In').prepend('<i class="fa-solid fa-spinner fa-spin me-1"></i>');

            },

            success: function(response){

              if(response.status == "form-incomplete"){

                $.each(response.errors,function(e,val){

                  $('input[name="'+e+'"]').parent().addClass('error');

                  $('input[name="'+e+'"]').parent().parent().find("label").append('<i class="error-message">'+val+'</i>');                               

                });

              }else if(response.status == "error"){

                $(".input-con").find("div").addClass('error');

                $form.prepend('<div class="alert alert-danger text-center" role="alert">'+response.message+'</div>');

              }else {

                $form.prepend('<div class="alert alert-success text-center" role="alert">'+response.message+'</div>');

                window.location.href = response.redirect;

              }

            },

            complete: function(response){

                $form.find('input').removeAttr('readonly');

                $form.find('button').removeAttr('disabled').text('Log In');

                $form.find('button').find('i.fa-spinner').remove();

            }

          });

      }



      app.forgot_password = function(){

        $("#fp-form").submit(function(e){

          e.preventDefault();
          var $form = $(this);

          $.ajax({

            url: base_url + "forgot_password/Ajax_forgot_password",

            type: "POST",

            data: $(this).serialize(),

            dataType: "JSON",

            beforeSend: function(){

              $(".error").removeClass("error");

              $(".error-message, .alert").remove();

              $form.find('input').attr('readonly',true);

              $form.find('button').attr('disabled',true).text('Sending Email').prepend('<i class="fa-solid fa-spinner fa-spin me-1"></i>');

            },

            success: function(response){

              if(response.status == "form-incomplete"){

                $.each(response.errors,function(e,val){

                  $('input[name="'+e+'"]').parent().addClass('error');

                  $('input[name="'+e+'"]').parent().parent().find("label").append('<i class="error-message">'+val+'</i>');                               

                });

              }else if(response.status == "error"){

                $(".input-con").find("div").addClass('error');

                $form.prepend('<div class="alert alert-danger text-center" role="alert">'+response.message+'</div>');

              }else {

                $(this).find('input').val("");

                $form.prepend('<div class="alert alert-success text-center" role="alert">'+response.message+'</div>');

                setTimeout(function(){

                  window.location.href = base_url;

                }, 3000);

              }

            },

            complete: function(response){

                $form.find('input').removeAttr('readonly');

                $form.find('button').removeAttr('disabled').text('Submit').find('i.fa-spin').remove();

            }

          });

        });

      }



      app.reset_password = function(){

        $(document).on("submit","#rp-form",function(e){

          e.preventDefault();

          var user_id = $(this).attr("user-id");

          var form = $(this);

          $.ajax({

            url: base_url + "reset_password/Ajax_reset_password",

            type: "POST",

            data: $(this).serialize()+"&user_id="+user_id,

            dataType: "JSON",

            beforeSend: function(){

              $(".error").removeClass("error");

              $(".error-message, .alert").remove();

              form.find('input').attr('readonly',true);

              form.find('button').attr('disabled',true);

            },

            success: function(response){

              if(response.status == "form-incomplete"){

                $.each(response.errors,function(e,val){

                  $('input[name="'+e+'"]').parent().addClass('error');

                  $('input[name="'+e+'"]').parent().parent().find("label").append('<i class="error-message">'+val+'</i>');                               

                });

              }else if(response.status == "error"){

                $(".input-con").find("div").addClass('error');

                form.prepend('<div class="alert alert-danger text-center" role="alert">'+response.message+'</div>');

              }else {

                form.find('input').val("");

                form.prepend('<div class="alert alert-success text-center" role="alert">'+response.message+'</div>');

                setTimeout(function(){

                  window.location.href = base_url;

                }, 6000);

              }

            },

            complete: function(response){

              $(".error").removeClass("error");

              $(".error-message, .alert").remove();

              form.find('input').removeAttr('readonly');

              form.find('button').removeAttr('disabled');

            }

          });

        });

      }

      app.init();

  })(Script);

});