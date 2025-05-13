$(function () {
  var Script = {};
  (function (app) {
      var ww = window.innerWidth;

      let temporaryScheduleTable;

      app.init = function() {
        app.bindings();
        app.initializeTemporaryScheduleTable();
        app.employee_cru_modal();
        app.dtr();
        app.download_dtr();
        app.cancel_dtr();
        app.update_password();
        app.viewEod();

      }
      app.formatDate = function(inputDate){
        const date = new Date(inputDate);
        const options = {
          month: 'short',
          day: '2-digit',
          year: 'numeric',
          weekday: 'short'
        };

        const formattedDate = date.toLocaleString('en-PH', options);
        let splitDate = formattedDate.split(', ');
        let finalDate = splitDate[1] + ', ' + splitDate[2] + ' (' + splitDate[0] + ')';
        return finalDate;
      }

      app.convertTimeTo12HourFormat = function(time) {
        // Split the time into hours and minutes
        var parts = time.split(':');
        var hours = parseInt(parts[0]);
        var minutes = parseInt(parts[1]);
    
        // Format hours to 12-hour format with AM/PM
        var period = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // Handle midnight (00:00) as 12:00 AM
        var formattedTime = hours.toString().padStart(2, '0') + ':' + minutes.toString().padStart(2, '0') + ' ' + period;
    
        return formattedTime;
      }

      app.initializeTemporaryScheduleTable = function(){
        temporaryScheduleTable = $('#temporary-schedule-table').DataTable({
          bLengthChange: false, 
          info: false,
          paging: false,
          columnDefs: [
            {
              targets: [1,2,3,4],
              orderable: false
            },
          ]
        });
      }

      app.bindings = function() {
        /*This is when the page is reloaded*/
        if(ww <= 767){
          if($("#sidebar, #main").hasClass("toggled")){
            $("#sidebar, #main").removeClass("toggled");
            $(".sidebar-toggle").find(".fas").toggleClass("fa-angle-left fa-angle-right");
          }
        }else{
          if(!$("#sidebar, #main").hasClass("toggled")){
            $("#sidebar, #main").addClass("toggled");
            $(".sidebar-toggle").find(".fas").toggleClass("fa-angle-right fa-angle-left");
          }
        }

        /*This is when your adjusting the screen*/
        window.addEventListener("resize", function(){
          if(ww <= 767){
            if($("#sidebar, #main").hasClass("toggled")){
              $("#sidebar, #main").removeClass("toggled");
              $(".sidebar-toggle").find(".fas").toggleClass("fa-angle-left fa-angle-right");
            }
          }else{
            if(!$("#sidebar, #main").hasClass("toggled")){
              $("#sidebar, #main").addClass("toggled");
              $(".sidebar-toggle").find(".fas").toggleClass("fa-angle-right fa-angle-left");
            }
          }
        });

        $(document).on("click",".sidebar-toggle",function(){
          var side_bar = $(".sidebar-toggle");
          $("#sidebar, #main").toggleClass("toggled");
          side_bar.find(".fas").toggleClass("fa-angle-left fa-angle-right");
          if(side_bar.attr("title") == "Hide sidebar"){
            side_bar.attr("title","Display sidebar");
          }else{
            side_bar.attr("title","Hide sidebar");
          }
        });

        $("#sidebar button.drop-button").on("click", function(){
          var target = $(this).attr("target");
          $(this).find("i.right").toggleClass("fa-angle-down fa-angle-up");
          $("#sidebar "+target).slideToggle();
        });

        $(document).on("click", "[data-toggle='modal']", function(){
          var modal = $(this).attr("data-target");
          $(modal).modal("show");
        });
      }

      app.employee_cru_modal = function(){
        var modal = $("#employee-cru-modal");

        $("#employee-cru-modal").on('hidden.bs.modal',function(){
          var filename = modal.find("[name='filename']").val();
          if(filename){
            var split = filename.split("-");
            if(split[0] == "Temporary"){
              $.ajax({
                url: base_url + "admin/Ajax_users/delete_temp_image",
                type: "POST",
                data: {filename:filename},
                success: function(response){
                  //do nothing
                }
              });
            }
          }
          modal.find("form").removeAttr("user-type own-profile user-id");
          modal.find(".schedule-row, .fixed-schedule-row, .emp-cru-update-profile-container, .emp-cru-update-schedule-container, .emp-cru-save-temp-schedule-container").addClass("d-none");
          modal.find("img").attr("src","");
          modal.find("select").prop('selectedIndex',0);
          modal.find("input[type='text'], input[type='date'], input[type='time'], select").val("");
          modal.find(".error").removeClass("error");
          modal.find(".error-message, .alert").remove();
          modal.find("[name='filename']").removeAttr("old-profile-name");
          modal.find('.emp-cru-main-submit-btn, .temporary-schedule-main-con').removeClass('d-none');
          modal.find('[name="monday-workbase"], [name="tuesday-workbase"], [name="wednesday-workbase"], [name="thursday-workbase"], [name="friday-workbase"]').prop('checked', false);
          temporaryScheduleTable.clear().draw();
        });

        $(document).on("click", ".edit-profile", function(){
          var user_id = $(this).attr("user-id");
          modal.find(".modal-title").empty().append("<i class='fas fa-user'></i> Profile");
          modal.find("form").attr("id","employee-update-form");
          modal.find("form").attr("user-id",user_id);
          modal.find(".password-row").addClass("d-none");
          modal.find(".modal-footer button").text("Update");
          modal.find("form").attr("user-type",$(this).attr("user-type"));
          modal.find('[name="user-id"]').val(user_id);
          var own_profile = false;

          if($(this).hasClass("own-profile")){ 
            modal.find("form").attr("own-profile",true);
            own_profile = true;
          }else{
            modal.find("form").removeAttr("own-profile"); 
          }

          if($(this).hasClass("hide-from-th")){ 
            modal.find(".emp-cru-update-profile-btn").addClass('d-none disabled');
          }else{
            modal.find(".emp-cru-update-profile-btn").removeClass('d-none disabled');
          }

          $.ajax({
            url: base_url + "admin/Ajax_users/fetch_user",
            type: "POST",
            data: {user_id:user_id},
            dataType: "JSON",

            success: function(response){
              if(response.status == "success"){

                ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'].forEach(function(day) {
                  var workbase = response.user_schedule[day + '_workbase'].split('/');
                  if (workbase) {
                      workbase.forEach(function(index) {
                          if (index == 'WFH') modal.find('#' + day + '-workbase-wfh').prop('checked', true);
                          if (index == 'Office') modal.find('#' + day + '-workbase-office').prop('checked', true);
                      });
                  }
                });

                modal.find("[name='name']").val(response.name);
                modal.find("[name='email']").val(response.email);
                modal.find("[name='mobile-number']").val(response.mobile_number);
                modal.find("[name='date-of-birth']").val(response.date_of_birth);
                modal.find("[name='gender'] option[value='"+response.gender+"']").prop('selected', true);
                modal.find("[name='username']").val(response.username);
                
                modal.find('.emp-cru-main-submit-btn').addClass('d-none');
                modal.find('.emp-cru-update-profile-container, .emp-cru-update-schedule-container, .emp-cru-save-temp-schedule-container').removeClass('d-none');

                if(response.profile_pic){
                  modal.find("img").attr("src",base_url+"assets_module/user_profile/"+response.profile_pic);
                  modal.find("[name='filename']").val(response.profile_pic).attr("old-profile-name",response.profile_pic);
                }

                if(own_profile == false && response.user_type !== "1"){
                  modal.find(".schedule-row").removeClass('d-none');
                  modal.find("[name='salary-grade'] option[value='"+response.salary_grade+"']").prop('selected',true);
                  modal.find("[name='branch'] option[value='"+response.branch+"']").prop('selected',true);
                  modal.find("[name='role'] option[value='"+response.user_type+"']").prop('selected',true);
                }

                if(response.temp_sched.length != 0){
                  $('#temporary-schedule-table').removeClass('d-none');
                  response.temp_sched.forEach(function(sched){
                    var log = sched.time.split('-');
                    
                    let tableHtml = `<tr>
                                      <td>${app.formatDate(sched.date)}</td>
                                      <td>${app.convertTimeTo12HourFormat(log[0])}</td>
                                      <td>${app.convertTimeTo12HourFormat(log[1])}</td>
                                      <td>${sched.workbase}</td>
                                      <td class="text-right sched-${sched.id}">
                                        <button class="btn edit" data-sched-id="${sched.id}" data-user-id="${user_id}" data-date="${sched.date}" data-time-in="${log[0]}" data-time-out="${log[1]}" data-workbase="${sched.workbase}">Edit</button> 
                                        <button class="btn cancel" data-sched-id="${sched.id}">Cancel</button></td>
                                    </tr>`;
                    
                    temporaryScheduleTable.row.add($(tableHtml)).draw();
                    $('.sched-'+sched.id).parent().attr('id', 'temp-sched-row-'+sched.id);
                  });
                  
                }else{
                  $('#temporary-schedule-table').addClass('d-none');
                }

                schedules = [];
                for (let index in response.user_schedule) {
                    if (response.user_schedule.hasOwnProperty(index)) {
                        var val = response.user_schedule[index];
                        var log = val.split('-');
                        modal.find('[name="schedule-' + index + '-in"]').val(log[0]);
                        modal.find('[name="schedule-' + index + '-out"]').val(log[1]);
                        schedules.push(index + ' : ' + val);
                    }
                }
              }else{
                alert(response.message);
              }
            }
          });
        });

        $(".modal .preview-con").on("click", function(){
          $('#upload-profile').trigger('click');
        });

        $('#upload-profile').change(function(e){
          e.preventDefault();

          let validExt = ['jpg', 'jpeg', 'png'];
          var file_type = this.files[0].type.split('/')[1];
          var file_size = this.files[0].size;
          var error_message = "";
          if(validExt.indexOf(file_type) == -1){
            error_message = "Uploaded file must be an image";
          }else if(file_size > 15000000){
            error_message = "Image size must not exceed 15mb";
          }

          if(error_message !== ""){
            $(".preview-con").addClass("error");
            $(".preview-con").parent().prepend("<span class='t-red'>"+error_message+"</span>")
          }else{
            var fd = new FormData();
            var files = $(this)[0].files;

            if(files.length > 0){
              fd.append('file',files[0]);
              $.ajax({
                url: base_url + 'admin/Ajax_users/profile_image',
                type: 'POST',
                data: fd,
                contentType: false,
                processData: false,
                beforeSend: function(){
                  $(".error").removeClass("error");
                  $(".preview-con").parent().remove("span");
                },
                success: function(response){
                  var split = response.split("/").pop();
                  var src = base_url + "assets_module/user_profile/"+split;
                  if(response !== 0){
                    $(".preview-con img").attr("src",src);
                    $("[name='filename']").val(split);
                  }else{
                    alert('file not uploaded');
                  }
                },
              });
            }
          }
        });

        $(document).on("submit", "#employee-update-form", function(e){
          e.preventDefault();
          var form = $(this);
          var user_id = form.attr("user-id");
          var file = form.find("[name='filename']");
          var old_profile_name = "";
          var user_type = $(this).attr("user-type");
          if( file.attr('old-profile-name')){ old_profile_name = file.attr('old-profile-name'); }

          $.ajax({
            url: base_url + "admin/Ajax_users/update_user",
            type: "POST",
            data: form.serialize()+"&user_id="+user_id+"&old_profile_name="+old_profile_name+"&user_type="+user_type,
            dataType: "JSON",
            beforeSend: function(){
              $(".error").removeClass("error");
              $(".error-message, .alert").remove();
              form.find('input, select').attr('readonly',true);
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
                  form.find("img").attr("src","");
                  form.find("select").prop('selectedIndex',0);
                  form.find("input").val("");
                  form.find(".error").removeClass("error");
                  form.find(".error-message, .alert").remove();
                  form.find("[name='filename']").removeAttr("old-profile-name");
                  form.removeAttr("user-id");
                  $("#employee-cru-modal").modal("hide");

                  if(form.attr("own-profile")){
                    if(response.profile_pic){
                      $("#sidebar .profile-con img").attr("src",base_url+"/assets_module/user_profile/"+response.profile_pic);
                    }
                    $("#sidebar .name").attr("title",response.name).text(response.name);
                  }else{
                    var table = $("#employee-list [tr-id='"+user_id+"']");
                    if(response.profile_pic !== ""){
                      table.find(".profile_pic").attr("src",base_url+"/assets_module/user_profile/"+response.profile_pic);
                    }
                    table.find(".name").text(response.name);
                    table.find(".username").text(response.username);
                    table.find(".email").text(response.email);
                    table.find(".mobile_number").text(response.mobile_number);
                    table.find(".user_type").text(response.user_type);
                    table.find(".branch").text(response.branch);
                  }
                }, 1500);
              }
            },
            complete: function(){
              form.find('input, select').removeAttr('readonly');
              form.find('button').removeAttr('disabled');
            }
          });
        });
      }

      app.dtr = function(){
        var modal = $("#dtr-cru-modal");
        $(document).ready(function(){
          modal.on('hidden.bs.modal',function(){
            modal.find(".break-row, .time-out-row, .ot-cb-row, .overtime-row, .eod-con").addClass("d-none");
            modal.find(".check-con div").first().addClass("d-none");
            modal.find("input[type='text'], input[type='time']").val("");
            modal.find("select").prop('selectedIndex',0);
            modal.find("input[type='checkbox']").prop("checked",false);
            modal.find(".error").removeClass("error");
            modal.find(".error-message, .alert").remove();
          });

          modal.find("#cb-overtime").on("change",function(){
            if($(this).is(":checked")){
              modal.find(".overtime-row").removeClass("d-none");
            }else{
              modal.find(".overtime-row").addClass("d-none");
              modal.find(".overtime-row input").val('');
            }
          });
        });

        $(document).on("click", ".edit-dtr", function(){
          var dtr_id  = $(this).attr("dtr-id");
          var user_id = $(this).attr("user-id");
          modal.find(".check-con .d-none").removeClass("d-none");
          modal.find("form").attr({
            "id"        : "edit-dtr-form",
            "dtr-id"    : dtr_id,
            "user-id"   : user_id,
            "data-rdtr" : $(this).data("rdtr")
          });

          if($(this).find('i').hasClass("fa-rotate")){ 
            modal.find(".modal-title").html("<i class='fas fa-rotate'></i> Update DTR");
          }else{ 
            modal.find(".modal-title").html("<i class='fas fa-edit'></i> Edit DTR");
          }

          modal.find(".break-row, .time-out-row, .eod-con, .ot-cb-row").removeClass("d-none");

          $.ajax({
            url: base_url + "employee/Ajax_dtr/read_dtr",
            type: "POST",
            data: {dtr_id:dtr_id},
            dataType: "JSON",
            success: function(response) {
              if(response.status == "success"){
                if(response.break){
                  const _break = response.break.split("-");
                  modal.find("[name='break-in']").val(_break[0]);
                  modal.find("[name='break-out']").val(_break[1]);
                }

                if(response.overtime){
                  modal.find("#cb-overtime").prop("checked",true);
                  modal.find(".overtime-row").removeClass("d-none");
                  const _ot = response.overtime.split("-");
                  modal.find("[name='ot-in']").val(_ot[0]);
                  modal.find("[name='ot-out']").val(_ot[1]);
                }
                
                modal.find("[name='date']").val(response.date_input);
                modal.find("[name='time-in']").val(response.time_in);
                
                modal.find("[name='time-out']").val(response.time_out);
                modal.find("[name='work-base']").val(response.work_base);
                modal.find("[name='end-of-day']").val(response.end_of_day);
              }else{
                modal.find("form").prepend('<div class="alert alert-danger text-center" role="alert">'+response.message+'</div>');
              }
            }
          });
        }); 

        modal.on("submit","#edit-dtr-form", function(e){
          e.preventDefault();
          var form = $("#edit-dtr-form");
          form.find(".error-message").remove();
          form.find(".error").removeClass('error');

          var user_id = form.attr('user-id');
          var dtr_id  = form.attr("dtr-id");
          var table   = $("#my-dtr-list [tr-id='"+dtr_id+"'], #dtr-list [tr-id='"+dtr_id+"'], #employee-dtr-list #"+dtr_id);
          var emp_table   = $("#my-dtr-list [tr-id='"+dtr_id+"']");
          var error   = 0;
          var break_time = "";
          var break_in   = form.find('[name="break-in"]').val();
          var break_out  = form.find('[name="break-out"]').val();
          var rdtr_id = $(this).data("rdtr");

          var b_in  = break_in.split(":");
          var b_out = break_out.split(":");
          var a = new Date();
          a.setHours(b_in[0], b_in[1], 0);

          var b = new Date();
          b.setHours(b_out[0], b_out[1], 0);
          var hourDifference = Math.abs(b - a) / 36e5;

          if(break_in == "" || break_out == ""){
            form.find('.break-div').parent().append('<i class="error-message">Both fields are required</i>'); 
            form.find('[name="break-in"], [name="break-out"]').addClass('error');
            error++; 
          }else if(break_in > break_out){
            form.find('.break-div').parent().append('<i class="error-message">break-in should be lesser than break-out</i>');
            form.find('[name="break-in"], [name="break-out"]').addClass('error');
            error++; 
          }/*else if(form.find("[name='work-base']").val() == "WFH/Office" && hourDifference < 1.5){
            form.find('.break-div').parent().append('<i class="error-message">1 hour 30 minutes - required for WFH/Office</i>');
            form.find('[name="break-in"], [name="break-out"]').addClass('error');
            error++;
          }*/else if(hourDifference < 1){
            form.find('.break-div').parent().append('<i class="error-message">1 hour break is required</i>');
            form.find('[name="break-in"], [name="break-out"]').addClass('error');
            error++;
          }else{
            break_time = break_in+"-"+break_out;
          }

          if(form.find("#cb-overtime").is(":checked")){
            var ot_in  = form.find("[name='ot-in']").val();
            var ot_out = form.find("[name='ot-out']").val();
            if(ot_in == "" || ot_out == ""){
              form.find('.ot-div').parent().append('<i class="error-message">Both fields are required</i>'); 
              form.find('[name="ot-in"], [name="ot-out"]').addClass('error');
              error++;
            }/*else if(ot_in > ot_out){
              form.find('.ot-div').parent().append('<i class="error-message">Invalid overtime in</i>'); 
              form.find('[name="ot-in"], [name="ot-out"]').addClass('error');
              error++;
            }*/
          }

          if(error == 0){
            $.ajax({
              url: base_url + "employee/Ajax_dtr/update_dtr",
              type: "POST",
              data: form.serialize()+"&dtr_id="+dtr_id+"&break_time="+break_time+"&user_id="+user_id,
              dataType: "JSON",
              beforeSend: function(){
                $(".error").removeClass("error");
                $(".error-message, .alert").remove();
                form.find('input, select').attr('readonly',true);
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
                  $.ajax({
                    url: base_url + "employee/Ajax_request_dtr_update/update_status",
                    type: "POST",
                    data: {user_id:user_id,rdtr_id:rdtr_id},
                    dataType: "JSON",
                    success: function(response){
                    }
                  });

                  form.prepend('<div class="alert alert-success text-center success-message mb-0px" role="alert">'+response.message+'</div>');
                  setTimeout(function(){
                    form.find(".success-message").remove();
                  }, 3000);

                  setTimeout(function(){
                    table.removeClass("active");;
                    table.find(".date").text(response.date);
                    var ch = response.check_holiday.length;
                    if(ch>0){table.addClass("bg-lgreen");
                    }else{table.removeClass("bg-lgreen");}
                    if(ch == 1){
                      table.find(".date").append('<br><i class="t-12px">- '+response.check_holiday[0]['name']+'</i>');
                    }else if(ch > 1){
                      for(var a=0;a<ch; a++){
                        table.find(".date").append('<br><i class="t-12px">- '+response.check_holiday[a]['name']+'</i>');
                      }
                    }

                    table.find(".time").text(response.time).append('<br><span class="t-12px">Total: '+response.total_hours+' hrs</span>');
                    table.find(".break").text(response.break);
                    table.find(".work").text(response.work_base);
                    table.find(".dtu-span").text("Updated: "+response.date_updated);

                    table.find(".overtime").empty();
                    if(response.overtime){ table.find(".overtime").append(response.overtime);
                    }else{ table.find(".overtime").text("---"); }

                    emp_table.find(".edit, .cancel").remove();
                    emp_table.find("td").last().html('<button class="btn edit request-dtr-update" data-toggle="modal" data-target="#request-dtr-update-modal" title="Request Update" data-dtr-id="'+dtr_id+'"> Request Update</button><br><span class="d-block w-100 t-12px dtu-span" title="date and time updated">'+response.date_updated+'</span>');
                    $("#sidebar #dtr-today").removeClass("disabled").attr({"data-toggle":"modal","data-target":"#dtr-cru-modal"}).removeAttr("title");
                    $("#dtr-cru-modal").modal("hide");
                  }, 1000);  
                }
              },
              complete: function(){
                form.find('input, select').removeAttr('readonly');
                form.find('button').removeAttr('disabled');
              }
            });
          }
        });

        $(document).on("click",".dtr-request-ot", function(){
          $("#request-ot-cru-modal").find("form").attr({"dtr-id":$(this).attr("dtr-id")});
          $("#request-ot-cru-modal").find("[name='date']").val($(this).data("date"));
        });
      }

      app.download_dtr = function(){
        var modal = $("#download-my-dtr-list-filter-modal");
        var form  = $("#download-my-dtr-list-filter-form");

        $(document).on("click","#download-one-employee-dtr",function(){
          form.attr({"user-id":$(this).data("user-id")});
        });

        form.on("submit",function(e){
          e.preventDefault();
          var user_id = form.attr("user-id");
          $.ajax({
            url: base_url + "admin/Ajax_dtr/download_dtr_validation",
            type: "POST",
            data: form.serialize()+"&user_id="+user_id,
            dataType: "JSON",
            beforeSend: function(){
              modal.find(".error").removeClass("error");
              modal.find(".alert, .error-message").remove();
              modal.find("input").attr("readonly",true);
              modal.find("button").attr("disabled",true);
            },
            success: function(response){
              if(response.status == "form-incomplete"){
                $.each(response.errors,function(e,val){
                  form.find('[name="'+e+'"]').addClass('error');
                  form.find('[name="'+e+'"]').parent().append('<i class="error-message">'+val+'</i>');                               
                });
              }else {
                form.prepend('<div class="alert alert-success text-center" role="alert">'+response.message+'</div>');
                window.open(base_url+"employee/Ajax_dtr/download_employee_dtr");
                setTimeout(function(){
                  modal.find(".error").removeClass("error");
                  modal.find(".alert, .error-message").remove();
                  form.find("input").val("");
                  modal.modal("hide");
                }, 1500);
              }
            },
            complete: function(response){
              modal.find('input').removeAttr('readonly');
              modal.find('button').removeAttr('disabled');
            }
          });
        });
      }

      app.cancel_dtr = function(){
        $(document).on("hidden.bs.modal","#cancel-ongoing-dtr-modal",function(){
          $(this).find(".alert").remove();
        });

        $(document).on("click", ".cancel", function(){
          var dtr_id = $(this).attr("dtr-id");
          $("#cancel-ongoing-dtr-form").attr("dtr-id",dtr_id);
        });

        $(document).on("submit", "#cancel-ongoing-dtr-form",function(e){
          e.preventDefault();
          var dtr_id = $(this).attr("dtr-id");
          
          $.ajax({
            url: base_url + "employee/Ajax_dtr/cancel_dtr",
            type: "POST",
            data: {dtr_id:dtr_id},
            dataType: "JSON",
            success: function(response) {
              if(response.status == "success"){
                // $("#cancel-ongoing-dtr-modal").find("form").prepend('<div class="alert alert-success text-center" role="alert">'+response.message+'</div>');
                // $("#my-dtr-list").find("tr[tr-id='"+dtr_id+"']").remove();
                // $("#adtr-list").find("tr[tr-id='"+dtr_id+"']").remove();
                // $("#sidebar #dtr-today").removeClass("disabled").attr({"data-toggle":"modal","data-target":"#dtr-cru-modal"}).removeAttr("title");
                // $('button.create-new-dtr').removeClass('opacity-25').prop('disabled', false);
                // $("#cancel-ongoing-dtr-modal").modal("hide");
                window.location.reload();
              }else{
                $("#cancel-ongoing-dtr-modal").find("form").prepend('<div class="alert alert-danger text-center" role="alert">'+response.message+'</div>');
              }

            }
          });
        });
      }

      app.update_password = function(){
        $(document).on("click",".update-password",function(){
          var modal = $("#update-password-modal");
          var user_id = $(this).attr('user-id');
          modal.find("form").attr('user-id',user_id);

          if($(this).hasClass("own-password")){
            modal.find(".current-row, .confirm-row").removeClass("d-none");
          }else{
            modal.find(".current-row, .confirm-row").addClass("d-none");
          }
        });

        $(document).on("submit","#update-password-form",function(e){
          e.preventDefault();
          var form = $(this);
          var user_id = $(this).attr("user-id");
          $.ajax({
            url:base_url + "admin/Ajax_users/update_password",
            type: "POST",
            data: form.serialize()+"&user_id="+user_id,
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
                  form.find('[name="'+e+'"]').addClass('error');
                  form.find('[name="'+e+'"]').parent().append('<i class="error-message">'+val+'</i>');                               
                });
              }else if(response.status == "error"){
                form.prepend('<div class="alert alert-danger text-center" role="alert">'+response.message+'</div>');
              }else {
                form.prepend('<div class="alert alert-success text-center" role="alert">'+response.message+'</div>');
                setTimeout(function(){
                  form.find("input").val("");
                  $("#update-password-modal").modal("hide");
                }, 1500);
              }
            },
            complete: function(){
              form.find('input, select').removeAttr('readonly');
              form.find('button').removeAttr('disabled');
            }
          });
        });
      }

      app.viewEod = function(){
        $(document).on('click', '.eod-report-view-btn', function(){
          var id = $(this).data('userId');
          var date = $(this).data('eodDate');

          $.ajax({
            url: base_url + 'employee/ajax_dtr/get_user_eod',
            method: 'POST',
            dataType: 'JSON',
            data: {'user_id' : id, 'eod_date' : date},

            success: function(response){
              switch(response.status){
                case 'success':
                  let modal = $('#eod-report-modal');
                  modal.find('.modal-body').html(response.eod);
                  modal.modal('show');
                break;
              }
            }
          });
        });
      }

      app.init();
  })(Script);
});