$(function () {
  var Script = {};
  (function (app) {
      var ww = window.innerWidth;
      let table;

      app.init = function() {
      
        app.comingSoon();
        app.bindings();
        app.dtrModal();
        app.dtr();
        app.request_update();
        //app.request_ot();
        app.dataTables();
        app.leave();
        app.dtrTimeIn();
        app.dtrTimeOut();
        app.dtrRequestResponseBtn();
        app.moveDtrToOt();

        // app.otDtrTimeModal();
        // app.otDtrTimeInForm();
        // app.otDtrTimeOutForm();

      }

      app.comingSoon = function(){
        $(document).on('click', '.coming-soon', function(){
          alert('Development for this feature is in progress.');
        })
      }

      app.bindings = function() {
        $(document).on("click","#dtr-today.disabled",function(){
          var span = $(this).find(".ongoing-dtr-message");
          span.slideDown();
          setTimeout(function(){
            span.slideUp();
          }, 3000);
        });
      }

      app.dataTables = function(){
        table = $("#my-dtr-list").DataTable({
            bLengthChange: false, 
            searching: true,
            info: false,
            iDisplayLength: 50,
            order: [7, 'desc'],
            columnDefs: [
              {
                type: 'date',
                targets: [7]
              },
              {
                visible: false,
                targets: [7]
              },
              {
                targets: [0,1,2,3,4,5,6],
                orderable: false
              }
            ]
        });

        $("#overtime-list-search, #my-dtr-list-search, #leave-list-search").keyup(function(){
          table.search($(this).val()).draw();
        });
      }

      app.dtrModal = function(){
        $(document).on('click', '.create-new-dtr', function(e){
          e.preventDefault();

          if($(this).hasClass('disabled')){
            //do nothing
          }else{
            let userId = $(this).attr('user-id');
            let perHour = $(this).attr('per-hour');
            let modal = $('#dtr-cru-modal');
            let form = modal.find('form');

            form.find('[name="user-id"]').val(userId);
            form.find('[name="per-hour"]').val(perHour);
            modal.modal('show');
          }
        });
      }

      app.dtr = function() {

        $(document).on('click', '.dtr-time-in-out-btn', function(e){
          e.preventDefault();

          var btn = $(this);
          var currentDate = $('#current-date').val();
          var originalDate = new Date(currentDate);
          if((originalDate.getMonth() + 1) < 10){
            var month = '0' + (originalDate.getMonth() + 1);
          }
          var date = originalDate.getFullYear() + '-' + month + '-' + originalDate.getDate();
          var time = $('#current-time').val();
          var form = $('#create-dtr-form');

          $.ajax({
            url: base_url + "employee/Ajax_dtr/add_dtr_beta",
            type: "POST",
            dataType: "JSON",
            data: form.serialize()+ "&time-in=" +time+ "&date=" +date,

            success: function(response){
              switch(response.status){
                case 'success':
                  var html = '<div class="alert alert-success text-center">'+response.message+'</div>';
                  $('#dtr-cru-modal').find('.response').html(html);
                  btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Reloading')

                  setTimeout(() => {
                    window.location.reload();
                  }, 2000);
                break;
              }
            }
          });
          
        });
      }

      app.request_update = function(){
        var modal = $("#request-dtr-update-modal");
        var form  = $("#request-dtr-update-form");
        $(document).on("click",".request-dtr-update", function(){
          var dtr_id = $(this).data("dtr-id");
          form.attr("dtr-id",dtr_id);
        });

        form.on("submit",function(e){
          e.preventDefault();
          var dtr_id  = $(this).attr("dtr-id");
        
          $.ajax({
            url: base_url + "employee/Ajax_request_dtr_update/send_request",
            type: "POST",
            data: form.serialize()+"&dtr_id="+dtr_id,
            dataType: "JSON",
            beforeSend: function(){
              $(".error").removeClass("error");
              $(".error-message, .alert").remove();
              form.find('textarea').attr('readonly',true);
              form.find('button').attr('disabled',true);
            },
            success: function(response){
              if(response.status == "form-incomplete"){
                $.each(response.errors,function(e,val){
                  form.find('[name="'+e+'"]').addClass('error');
                  form.find('[name="'+e+'"]').parent().append('<i class="error-message">'+val+'</i>');                               
                });
              }else if(response.status == "error"){
                form.prepend('<div class="alert alert-danger text-center" role="alert">'+response.message+'</div>');
              }else {
                form.prepend('<div class="alert alert-success text-center" role="alert">'+response.message+'</div>');
                setTimeout(function(){
                  var table = $("#my-dtr-list tbody [tr-id='"+dtr_id+"']");
                  table.find(".request-dtr-update").remove().prepend('<b class="d-block w-100 t-12px">Update Request Sent</b>');
                  table.find("td").last().prepend('<b class="d-block w-100 t-12px">Update Request Sent</b>');
                  form.find("textarea").val("");
                  form.find(".alert").remove();
                  modal.modal("hide");
                }, 1500);
              }
            },
            complete: function(){
              form.find('textarea').removeAttr('readonly');
              form.find('button').removeAttr('disabled');
            }
          });
        });
      }

      app.request_ot = function(){
        var modal = $("#request-ot-cru-modal");
        var table = $("#overtime-list");

        modal.on('hidden.bs.modal',function(){
          modal.find("form").removeAttr("otr-id user-id dtr-id");
          modal.find("input, textarea").val("");
          modal.find(".error").removeClass("error");
          modal.find(".error-message, .alert").remove();
          modal.find("select").prop('selectedIndex',0);
          modal.find(".time-con").addClass("d-none");
          modal.find("input, textarea").removeAttr("disabled");
        });

        $(document).on("change","#request-ot-cru-modal [name='ot-type']",function(){
          var value = $(this).find(":selected").val();
          if(value=="Regular Overtime"){
            modal.find(".time-con").addClass("d-none");
            modal.find(".time-con input").val("");
            modal.find("textarea").attr("placeholder","Separate tasks by next line");
          }else{
            modal.find(".time-con").removeClass("d-none");
            modal.find("textarea").attr("placeholder","Documents checked");
          }
        });

        $(document).on("click","#request-ot, .dtr-request-ot",function(){
          modal.find(".modal-title").html("<i class='request-ot-cru-icon'></i> Request Overtime");
          modal.find("form").attr({"id":"send-ot-request-form","user-id":$(this).attr('user-id')});
        });

        $(document).on("click",".edit-ot-request, .view-ot-request",function(){
          modal.find("form").attr({"id":"edit-ot-request-form","otr-id":$(this).attr('otr-id')});
          var rot_id = $(this).attr("otr-id");

          if($(this).hasClass("view")){
            modal.find(".modal-title").html("<i class='fas fa-eye'></i> Overtime Request");
            modal.find(".modal-footer button").text("Close").removeAttr("type").attr({
              type: "button",
              "data-bs-dismiss": "modal"
            });
            modal.find("input, textarea").attr("disabled","disabled");
          }else{
            modal.find(".modal-title").html("<i class='fas fa-edit'></i> Overtime Request");
            modal.find(".modal-footer button").text("Submit").removeAttr("type data-bs-dismiss").attr("type","submit");
          }

          $.ajax({
            url: base_url + "admin/Ajax_ot_request/fetch_ot_request",
            type: "POST",
            data: {rot_id:rot_id},
            dataType: "JSON",
            success: function(response) {
              if(response.type == "Document Checking"){
                var time = response.time.split("-");
                modal.find("[name='ot-type']").val("Document Checking");
                modal.find(".time-con").removeClass("d-none");
                modal.find("[name='start']").val(time[0]);
                modal.find("[name='end']").val(time[1]);
                modal.find("textarea").attr("placeholder","Documents checked");
              }else{
                modal.find("textarea").attr("placeholder","Separate tasks by next line");
              }

              if(response.status == "approved"){
                modal.find(".ot-type-con").remove();
              }else{
                if(modal.find(".ot-type-con").length == 0){
                  modal.find(".modal-body").prepend('<div class="row input-con ot-type-con">\
                    <div class="col-md-2 offset-md-1"><label>Overtime:* </label></div>\
                    <div class="col-md-8">\
                      <select name="ot-type">\
                        <option value="Regular Overtime">Regular Overtime</option>\
                        <option value="Document Checking">Document Checking</option>\
                      </select>\
                    </div>\
                  </div>');
                }
              }
              if(response.type == "Document Checking"){ modal.find("[name='ot-type']").prop("selectedIndex",1); }
              modal.find("[name='date']").val(response.date);
              modal.find("[name='task']").val(response.task);
            }
          });
        });

        $(document).on("click",".delete-request",function(){
          $("#delete-otr-modal form").attr("otr-id",$(this).attr("otr-id"))
        });

        modal.on("submit","#send-ot-request-form",function(e){
          e.preventDefault();
          var form = $(this);
          modal.find(".error-message").remove();
          modal.find(".error").removeClass("error");
          var user_id  = form.attr("user-id");
          var ot_type  = modal.find("[name='ot-type']").val();
          var ot_date  = modal.find("[name='date']").val();
          var task     = modal.find("[name='task']").val();
          var error    = 0;
          
          if(!ot_date){
            form.find('[name="date"]').addClass('error');
            form.find('[name="date"]').parent().append('<i class="error-message">required</i>');
            error++;   
          }
          
          if(!task){
            form.find('[name="task"]').addClass('error');
            form.find('[name="task"]').parent().append('<i class="error-message">required</i>');
            error++; 
          }

          if(ot_type == "Document Checking"){
            var start = form.find("[name='start']").val();
            var end   = form.find("[name='end']").val();

            if(start == "" || end == ""){
              form.find("[name='start'], [name='end']").addClass('error');
              form.find('.time-in-out-col').append('<i class="error-message">required</i>');
              error++;
            }
          }

          if(error == 0){
            $.ajax({
              url: base_url + "employee/Ajax_ot_request/ot_au_request",
              type: "POST",
              data: form.serialize()+"&user_id="+user_id,
              dataType: "JSON",
              beforeSend: function(){
                $(".error").removeClass("error");
                $(".error-message, .alert").remove();
                form.find("textarea,input").attr("readonly",true);
                form.find('button').attr('disabled',true);
              },
              success: function(response){
                var table = $("#overtime-list tbody");
                if(response.status == "error"){
                  form.prepend('<div class="alert alert-danger text-center" role="alert">'+response.message+'</div>');
                }else {
                  form.prepend('<div class="alert alert-success text-center" role="alert">'+response.message+'</div>');
                  setTimeout(function(){
                    form.find("textarea, input").val("");
                    form.find(".error-message, .alert").remove();

                    table.prepend('<tr tr-id="'+response.otr_id+'">\
                        <td class="date">'+response.date+'</td>\
                        <td class="task w500-hide">'+response.task+'</td>\
                        <td class="ot-type"></td>\
                        <td class="text-center">\
                          <button class="btn edit edit-ot-request" data-toggle="modal" data-target="#request-ot-cru-modal" title="Edit Overtime Request" otr-id="'+response.otr_id+'"><i class="fas fa-edit"></i> Edit\
                          </button>\
                          <button class="btn delete delete-request" data-toggle="modal" data-target="#delete-otr-modal" title="Delete Overtime Request" otr-id="'+response.otr_id+'"><i class="fas fa-trash"></i> Delete\
                          </button>\
                        </td>\
                    </tr>');

                    if(ot_type == "Regular Overtime"){
                      table.find('[tr-id='+response.otr_id+'] .ot-type').html("Regular Overtime<br><span class='t-12px t-green'>Pending</span>");
                    }else{
                      table.find('[tr-id='+response.otr_id+'] .ot-type').html("Document Checking<br><span class='t-12px'>"+response.time+"</span>");
                    }

                    if(form.attr("dtr-id")){
                      $("#my-dtr-list [tr-id='"+form.attr("dtr-id")+"']").find(".overtime").empty();
                      $("#my-dtr-list [tr-id='"+form.attr("dtr-id")+"']").find(".overtime").append('Sent<br><span class="t-12px">Status: pending</span>');
                    }

                    if(ww <= 767){
                      if($("#sidebar, #main").hasClass("toggled")){
                        $("#sidebar, #main").removeClass("toggled");
                        $(".sidebar-toggle").find(".fas").toggleClass("fa-angle-left fa-angle-right");
                      }
                    }
                    modal.modal("hide");
                  }, 1500);
                }
              },
              complete: function(){
                form.find("textarea,input").removeAttr("readonly");
                form.find('button').removeAttr('disabled');
              }
            });
          }
        });

        modal.on("submit","#edit-ot-request-form",function(e){
          e.preventDefault();
          modal.find(".error-message").remove();
          modal.find(".error").removeClass("error");
          var form     = $(this);
          var otr_id   = form.attr("otr-id");
          var table    = $("[tr-id='"+otr_id+"']");

          var ot_type  = "";
          var ot_date  = modal.find("[name='date']").val();
          var task     = modal.find("[name='task']").val();
          var error    = 0;
          
          if(!ot_date){
            form.find('[name="date"]').addClass('error');
            form.find('[name="date"]').parent().append('<i class="error-message">required</i>');
            error++;   
          }
          
          if(!task){
            form.find('[name="task"]').addClass('error');
            form.find('[name="task"]').parent().append('<i class="error-message">required</i>');
            error++; 
          }

          if(modal.find("[name='ot-type']").length > 0){
            ot_type  = modal.find("[name='ot-type']").val();
            if(ot_type == "Document Checking"){
              var start = form.find("[name='start']").val();
              var end   = form.find("[name='end']").val();

              if(start == "" || end == ""){
                form.find("[name='start'], [name='end']").addClass('error');
                form.find('.time-in-out-col').append('<i class="error-message">required</i>');
                error++;
              }
            }
          }

          if(error == 0){
            $.ajax({
            url: base_url + "employee/Ajax_ot_request/ot_au_request",
            type: "POST",
            data: form.serialize()+"&otr_id="+otr_id,
            dataType: "JSON",
            beforeSend: function(){
              $(".error").removeClass("error");
              $(".error-message, .alert").remove();
              form.find("textarea,input").attr("readonly",true);
              form.find('button').attr('disabled',true);
            },
            success: function(response){
              if(response.status == "error"){
                form.prepend('<div class="alert alert-danger text-center" role="alert">'+response.message+'</div>');
              }else {
                form.prepend('<div class="alert alert-success text-center" role="alert">'+response.message+'</div>');
                setTimeout(function(){
                  form.find("textarea, input").val("");
                  form.find(".error-message, .alert").remove();
                  table.find(".task-div").html(response.task);
                  table.find(".date").text(response.date);

                  if(response.time == ""){
                    var t_color = "t-green";
                    if(response.ot_status == "approved"){ t_color = "t-blue"; }
                    else if(response.ot_status == "denied"){ t_color = "t-red"; }
                    table.find('.ot-type').html("Regular Overtime<br><span class='t-12px "+t_color+"'>"+response.ot_status+"</span>");
                  }else{
                    table.find(".ot-type").html("Document Checking<br><span class='t-12px'>"+response.time+"</span>");
                  }

                  modal.modal("hide");
                }, 1500);
              }
            },
            complete: function(){
              form.find("textarea,input").removeAttr("readonly");
              form.find('button').removeAttr('disabled');
            }
          });
          }
        });

        $(document).on("submit","#delete-otr-form",function(e){
          e.preventDefault();
          var otr_id = $(this).attr("otr-id");
          var modal = $("#delete-otr-modal");
          var form = $(this);
          $.ajax({
            url: base_url + "employee/Ajax_ot_request/delete_request",
            type: "POST",
            data: {otr_id:otr_id},
            dataType: "JSON",
            beforeSend: function(){
              modal.find(".alert").remove();
              modal.find("button").attr("disabled",true);
            },
            success: function(response){
              if(response.status == "error"){
                $('<div class="alert alert-danger text-center" role="alert">'+response.message+'</div>').insertAfter("#delete-otr-modal .modal-header");
              }else{
                $('<div class="alert alert-success text-center" role="alert">'+response.message+'</div>').insertAfter("#delete-otr-modal .modal-header");
                setTimeout(function(){
                  modal.find(".alert").remove();
                  modal.find("button").removeAttr("disabled");
                  table.find("[tr-id='"+otr_id+"']").remove();
                  modal.modal("hide");
                }, 2500);
              }
            }
          });
        });
      }

      app.leave = function(){
        var modal = $("#request-leave-modal");

        modal.on('hidden.bs.modal',function(){
          modal.find(".others-row").addClass("d-none");
          modal.find("input[type='text'], textarea").val("");
          modal.find("select").prop('selectedIndex',0);
          modal.find(".error").removeClass("error");
          modal.find(".error-message, .alert").remove();
          modal.find("form").removeAttr("id user-id leave-id");
        });

        $(document).on("click","#request-leave",function(){
          var user_id = $(this).attr("user-id");
          modal.find("form").attr({"id":"request-leave-form","user-id":user_id});
        });

        $(document).on("click",".edit-leave-request",function(){
          var leave_id = $(this).attr("leave-id");
          var user_id = $(this).attr("user-id");
          modal.find("form").attr({"id":"edit-leave-form","leave-id":leave_id,"user-id":user_id});

          $.ajax({
            url: base_url + "employee/Ajax_leave_request/fetch_leave_request",
            type: "POST",
            data: {leave_id:leave_id},
            dataType: "JSON",
            success: function(response){
              modal.find("[name='date']").val(response.date);
              var leave_type = response.leave_type.split(" : ");
              if(leave_type.length > 1 ){
                modal.find("[name='leave-type'] option[value='Others']").prop("selected",true);
                modal.find(".others-row").removeClass("d-none");
                modal.find("[name='others']").val(leave_type[1]);
              }else{
                modal.find("[name='leave-type'] option[value='"+response.leave_type+"']").prop("selected",true);
              }
              modal.find("[name='details']").val(response.details);
            }
          });
        });

        modal.find("[name='leave-type']").on("change",function(){
          var value = $(this).find(":selected").val();
          if(value == "Others"){
            modal.find(".others-row").removeClass("d-none");
          }else{
            modal.find(".others-row").addClass("d-none");
            modal.find("[name='others']").val('');
          }
        });
      }

      app.dtrTimeIn = function(){
        $(document).on('click', '.dtr-time-btn, .dtr-time-btn-mobile', function(e){
          e.preventDefault();

          var clickedButton = $(e.target);

          if (clickedButton.hasClass('dtr-time-btn')) {
            var inputWorkbase = $('[name="dtr-work-base"]:checked');
          } else if (clickedButton.hasClass('dtr-time-btn-mobile')) {
            var inputWorkbase = $('[name="dtr-work-base-mobile"]:checked');
          }

          var workbase = inputWorkbase.val();
          var action = $(this).data('action');
          var userId = $(this).data('userId');
          var dtrId = $(this).data('dtrId');
          var date = $('#current-date').val();
          var eodReport = null;

          if(action == 'time-in'){
            $.ajax({
              url: base_url + 'employee/ajax_dtr/check_time_log_date',
              method: 'POST',
              dataType: 'JSON',
              data: {'user_id': userId, 'date' : date },

              success: function(response){
                switch(response.status){
                  case 'success':
                    if(action != 'eod-report'){
                      app.ajaxSetDtrTime(userId, action, dtrId, eodReport, workbase);
                    }else{
                      let modal = $('#write-eod-report-modal');
                      modal.find('form [name="action"]').val('time-out');
                      modal.find('form [name="user_id"]').val(userId);
                      modal.find('form [name="dtr_id"]').val(dtrId);
                      modal.modal('show');
                    }
                  break;
                  
                  case 'error':
                    $('#time-in-error-modal').modal('show');
                  break
                }
              }
            });
          }else{
            if(action != 'eod-report'){
              app.ajaxSetDtrTime(userId, action, dtrId, eodReport, workbase);
            }else{
              let modal = $('#write-eod-report-modal');
              modal.find('form [name="action"]').val('time-out');
              modal.find('form [name="user_id"]').val(userId);
              modal.find('form [name="dtr_id"]').val(dtrId);
              modal.modal('show');
            }
          }
        });
      }

      app.dtrTimeOut = function(){
        $(document).on('click', '.dtr-time-out-btn', function(e){
          e.preventDefault();
          var form        = $('#eod-report-text-area');
          var action      = form.find('[name="action"]').val();
          var userId      = form.find('[name="user_id"]').val();
          var dtrId       = form.find('[name="dtr_id"]').val();
          var eodReport   = form.find('[name="eod-report-text"]').val();
          var workbase    = null;
          var overtimeFrom = form.find('[name="dtr-ot-from"]').val();
          var overtimeto = form.find('[name="dtr-ot-to"]').val();
          var overtime = [overtimeFrom, overtimeto];

          app.ajaxSetDtrTime(userId, action, dtrId, eodReport, workbase, overtime);

        });
      }

      app.ajaxSetDtrTime = function(userId, action, dtrId, eodReport, workbase, overtime){
        var btn = $('.dtr-time-btn');
        let input = $('.table-search-row');
        var schedInOut = $('[name="sched-in-out"]').val();
        var schedWorkbase = $('[name="sched-workbase"]').val();

        $.ajax({
          url: base_url + 'employee/ajax_dtr/time_logs',
          method: 'POST',
          dataType: 'JSON',
          data: {'user_id': userId, 'action': action, 'dtr_id': dtrId, 'eod_report': eodReport, 'dtr-work-base': workbase, 'overtime': overtime, 'sched-in-out' : schedInOut, 'sched-workbase' : schedWorkbase},

          beforeSend: function(){
            input.find('.error-message').remove();
          },
          
          success: function(response){
            switch(response.status){
              case 'form-incomplete':
                $('#hybrid-schedule-no-workbase-modal').modal('show');
              break;

              case 'success':
                if(action == 'time-out'){
                  $.ajax({
                    url: base_url + 'employee/ajax_dtr/save_undertime',
                    method: 'POST',
                    dataType: 'JSON',

                    success: function(response){
                      switch(response.status){
                        case 'success':
                          window.location.reload();
                        break;
                      }
                    }
                  });
                }else{
                  window.location.reload();
                }
              break;

              case 'break-less-minimum':
                let vModal = $('#less-than-minimum-break-time-modal');
                vModal.find('.time-remaining').html(`<b>${30 - response.break_rendered} min</b>`);
                vModal.modal('show');
              break;
            }
          }
        })
      }

      

      app.dtrRequestResponseBtn = function(){
        $(document).on('click', '.request-response-btn', function(){
          var status = $(this).data('requestStatus');
          var reason = $(this).data('reasonDenied');
          var modal = $('#dtr-request-response-modal');

          if(status == 'approved'){
            modal.find('.result-response').html('<i class="fa-solid fa-face-smile"></i> Your request has been Approved!').addClass('text-success');
          }else{
            modal.find('.result-response').html('<i class="fa-solid fa-face-sad-cry"></i> Your request has been Denied!').addClass('text-danger');
            modal.find('.result-response').parent().append('<div class="details-container"><span><b>Details:</b></span><br><span>'+reason+'</span></div>');
          }

          modal.modal('show');
        });

        $('#dtr-request-response-modal').on('hidden.bs.modal', function(){
          var modal = $('#dtr-request-response-modal');
          modal.find('.details-container').remove();
          modal.find('.result-response').removeClass('text-success, text-danger');
        });
      }

      app.otDtrTimeModal = function(){
        $(document).on('click', '.dtr-ot-time-btn', function(e){
          e.preventDefault();

          var userId = $(this).data('userId');
          var otId = $(this).data('otId');
          var action = $(this).data('action');
          var workbase = $('#workbase-options [name="dtr-work-base"]:checked').val();

          if(!workbase){
            $('#hybrid-schedule-no-workbase-modal').modal('show');
          }else{

            switch(action){
              case 'ot-time-in':
                var modal = $('#select-ot-pre-post-modal');
                var form = modal.find('form');
                form.find('[name="user-id"]').val(userId);
                form.find('[name="ot-id"]').val(otId);
                form.find('[name="action"]').val(action);
                form.find('[name="workbase"]').val(workbase);
                modal.modal('show');
              break;

              case 'ot-time-out':
                var modal = $('#eod-ot-report-modal');
                modal.find('[name="ot-id"]').val(otId);
                modal.modal('show');
              break;
            }
          }
        })
      }

      app.otDtrTimeInForm = function(){
  
        $(document).on('click', '.ot-shift-btn', function(e){
          e.preventDefault();

          var form = $('#select-ot-pre-post-form');
          var userId = form.find('[name="user-id"]').val();
          var dtrId = form.find('[name="dtr-id"]').val();
          var action = form.find('[name="action"]').val();
          var workbase = form.find('[name="workbase"]').val();
          var shift = $(this).val();

          $.ajax({
            url: base_url + 'employee/ajax_overtime/save_overtime',
            method: 'POST',
            dataType: 'JSON',
            data: { 'user-id' : userId, 'workbase' : workbase, 'shift' : shift, 'action' : 'ot-time-in'},

            success:function(response){
              switch(response.status){
                case 'success':
                  window.location.reload();
                break;
              }
            }
          })

        })
      }

      app.otDtrTimeOutForm = function(){
        $(document).on('click', '.ot-time-out-btn-save', function(e){
          e.preventDefault();

          var otReport = $('[name="ot-report"]').val();
          var otId = $('[name="ot-id"]').val();

          $.ajax({
            url: base_url + 'employee/ajax_overtime/save_overtime_to',
            method: 'POST',
            dataType: 'JSON',
            data: {'id' : otId, 'report' : otReport},

            success: function(response){
              switch(response.status){
                case 'success':
                  window.location.reload();
                break;
              }
            }
          })
          
        })
      }

      app.moveDtrToOt = function(){
        $(document).on('click', '.move-to-holiday-btn', function(){
          var dtrId = $(this).data('dtrId');
          var modal = $('#move-dtr-to-ot-modal');

          $.ajax({
            url: base_url + 'employee/ajax_dtr/move_dtr_to_ot',
            method: 'POST',
            dataType: 'JSON',
            data: {id : dtrId},

            beforeSend: function(){},

            success: function(response){
              switch(response.status){
                case 'success':
                  var html = `<div class="alert alert-success">DTR moved successfully to Overtime logs.</div>`;
                  modal.find('.modal-body').html(html);
                  modal.modal('show');

                  setTimeout(() => {
                    window.location.reload();
                  }, 2000);
                break;

                case 'on-going-dtr':
                  var html = `<div class="alert alert-warning">You can move this DTR after eod.</div>`;
                  modal.find('.modal-body').html(html);
                  modal.modal('show');
                break;

                case 'existing-ot-log':
                  var html = `<div class="alert alert-danger">You already have an overtime log on this day.</div>`;
                  modal.find('.modal-body').html(html);
                  modal.modal('show');
                break;

                case 'error':
                  alert('Something went wrong. Please contact IT administrator.');
                break;

              }
            }
          })
        })
      }


      app.init();
  })(Script);
});