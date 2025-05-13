$(function () {
  var Script = {};
  (function (app) {
      var ww = window.innerWidth;

      app.init = function() {
        app.bindings();
      }

      app.bindings = function() {
          app.initializeChangeTimeRequestTable();
          app.changeTimeRequestModal();
          app.changeTimeRequest();
          app.approveCTRequestModal();
          app.approveCTRequest();
          app.declineCTRequestModal();
          app.declineCTRequest();
          app.cancelCTRequestModal();
          app.cancelCTRequest();
          app.deleteCTRequestModal();
          app.deleteCTRequest();
          app.viewDeclineMessageModal();

          app.cancelmyChangeTimeRequest();

          app.saveScheduleOnly();
          app.saveTemporaryScheduleOnly();
          app.editTemporarySchedule();
          app.cancelTemporarySchedule();


          app.onChangeDateEmpRange();
          app.clearFilter();

          app.generatePdfModal();

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

      app.formatDateTime = function formatDate(dateString) {
        // Parse the date string
        const date = new Date(dateString);
        
        // Check if the date is valid
        if (isNaN(date.getTime())) {
            return 'Invalid date';
        }

        // Array of month names
        const monthNames = [
            "January", "February", "March", "April", "May", "June", 
            "July", "August", "September", "October", "November", "December"
        ];

        // Extract month, day, and year
        const month = monthNames[date.getMonth()];
        const day = date.getDate();
        const year = date.getFullYear();

        // Return formatted date string
        return `${month} ${day}, ${year}`;
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

      app.initializeChangeTimeRequestTable = function(){
          ctrTable = $('#ctr-leave-list').DataTable({
            bLengthChange: false, 
            searching: true,
            info: false,
            iDisplayLength: -1,
            paging: false,
            order: []
          
          });
  
          $('#ctr-leave-list-search').keyup(function(){
              ctrTable.search($(this).val()).draw();
          })
      }

      app.changeTimeRequestModal = function(){
          $(document).on('click', '.change-time-request-btn', function(e){
              e.preventDefault();

              var modal = $('#approve-leave-request-modal');
              modal.modal('show');
          });
      }

      app.changeTimeRequest = function(){
          $(document).on('submit', '#change-time-request-form', function(e){
              e.preventDefault();
              var form = $(this);

              $.ajax({
                  url: base_url + 'employee/ajax_ct_request/add_request',
                  method: 'POST',
                  dataType: 'JSON',
                  data: form.serialize(),

                  success: function(response){
                      switch(response.status){
                          case 'success':
                              form.find('.response').html(`<div class="alert alert-success">${response.message}</div>`);
                              setTimeout(function(){
                                  window.location.reload();
                              }, 2000)
                          break;
                      }
                      
                  }
              });
          });
      }

      app.approveCTRequestModal = function(){
          $(document).on('click', '.approve-ctr-btn', function(){
              var reqId = $(this).parent().parent().parent().data('requestId');
              var userId = $(this).closest('[data-request-id]').data('requestId');
              
              $.ajax({
                  url: base_url + 'employee/ajax_ct_request/get_request_data',
                  method: 'POST',
                  dataType: 'JSON',
                  data: {'id' : userId},

                  success: function(response){
                      switch(response.status){
                          case 'success':
                              var modal = $('#approve-change-time-request-modal');
                              modal.find('[name="request-id"]').val(reqId);
                              modal.find('.emp-name').text(response.data.user);
                              modal.find('.request-message').html(`<p>${response.data.details}</p>`);
                              modal.modal('show');
                          break;
                      }
                      
                  }
              })
          })
      }

      app.approveCTRequest = function(){
          $(document).on('submit', '#approve-change-time-request-form', function(e){
              e.preventDefault();

              var form = $(this);
              $.ajax({
                  url: base_url + 'employee/ajax_ct_request/approve_request',
                  method: 'POST',
                  dataType: 'JSON',
                  data: form.serialize(),

                  success: function(response){
                      switch(response.status){
                          case 'success':
                              form.find('.response').html(`<div class="alert alert-success">${response.message}</div>`);
                              setTimeout(function(){
                                  window.location.reload();
                              }, 2000);
                          break;

                          case 'error':
                              alert('Something went wrong. Please contact IT Administrator');
                          break;
                      }
                      
                  }
              })
          })
      }

      app.declineCTRequestModal = function(){
          $(document).on('click', '.decline-ctr-btn', function(e){
              e.preventDefault();

              var reqId = $(this).parent().parent().parent().data('requestId');
              var modal = $('#decline-ct-request-modal');
              modal.find('[name="request-id"]').val(reqId);
              modal.modal('show');
          });
      }

      app.declineCTRequest = function(){
          $(document).on('submit', '#decline-ct-request-form', function(e){
              e.preventDefault();

              var form = $(this);
              $.ajax({
                  url: base_url + 'employee/ajax_ct_request/decline_request',
                  method: 'POST',
                  dataType: 'JSON',
                  data: form.serialize(),

                  success: function(response){
                      switch(response.status){
                          case 'success':
                              form.find('.response').html(`<div class="alert alert-success">${response.message}</div>`);
                              setTimeout(function(){
                                  window.location.reload();
                              }, 2000);
                          break;

                          case 'error':
                              alert('Something went wrong. Please contact IT Administrator');
                          break;
                      }
                  }
              })
          })
      }

      app.cancelCTRequestModal = function(){
        $(document).on('click', '.cancel-ctr-request', function(){
            var reqId = $(this).closest('[data-user-id]').data('requestId');
            var userId = $(this).closest('[data-request-id]').data('requestId');
            $.ajax({
                url: base_url + 'employee/ajax_ct_request/get_request_data',
                method: 'POST',
                dataType: 'JSON',
                data: {'id' : userId},

                success: function(response){
                    switch(response.status){
                        case 'success':
                            var modal = $('#cancel-change-time-request-modal');
                            modal.find('[name="request-id"]').val(reqId);
                            modal.find('.emp-name').text(response.data.user);
                            modal.find('.request-message').html(`<p>${response.data.details}</p>`);
                            modal.modal('show');
                        break;
                    }
                    
                }
            })
        });
    }

    app.cancelCTRequest = function(){
        $(document).on('submit', '#cancel-ct-request-form', function(e){
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: base_url + 'employee/ajax_ct_request/cancel_ct_request',
                method: 'POST',
                dataType: 'JSON',
                data: form.serialize(),

                success: function(response){
                    switch(response.status){
                        case 'success':
                            form.find('.response').html(`<div class="alert alert-success">${response.message}</div>`);
                            setTimeout(function(){
                                window.location.reload();
                            }, 2000);
                        break;

                        case 'error':
                            alert('Something went wrong. Please contact IT Administrator');
                        break;
                    }
                }
            })
        })
    }

    app.deleteCTRequestModal = function(){
        $(document).on('click', '.delete-my-ctr-request', function(){
            var reqId = $(this).closest('[data-user-id]').data('requestId');
            var userId = $(this).closest('[data-request-id]').data('requestId');
            $.ajax({
                url: base_url + 'employee/ajax_ct_request/get_request_data',
                method: 'POST',
                dataType: 'JSON',
                data: {'id' : userId},

                success: function(response){
                    switch(response.status){
                        case 'success':
                            var modal = $('#delete-change-time-request-modal');
                            modal.find('[name="request-id"]').val(reqId);
                            modal.find('.emp-name').text(response.data.user);
                            modal.find('.request-message').html(`<p>${response.data.details}</p>`);
                            modal.modal('show');
                        break;
                    }
                    
                }
            })
        });
    }

    app.deleteCTRequest = function(){
        $(document).on('submit', '#delete-ct-request-form', function(e){
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: base_url + 'employee/ajax_ct_request/delete_ct_request',
                method: 'POST',
                dataType: 'JSON',
                data: form.serialize(),

                success: function(response){
                    switch(response.status){
                        case 'success':
                            form.find('.response').html(`<div class="alert alert-success">${response.message}</div>`);
                            var modal = $('#delete-change-time-request-modal');
                            var id = form.find('[name="request-id"]').val();
      
                          $('#ctr-leave-list-tr-' + id).fadeOut( "slow", function() {
                              $(this).remove();
                            });
      
                          setTimeout(function(){
                              modal.modal('hide');
                          }, 2000);
                        break;

                        case 'error':
                            alert('Something went wrong. Please contact IT Administrator');
                        break;
                    }
                }
            })
        })
    }

      app.viewDeclineMessageModal = function(){
          $(document).on('click', '.denied-ctr-btn', function(){
              var reqId = $(this).closest('[data-request-id]').data('requestId');
              $.ajax({
                  url: base_url + 'employee/ajax_ct_request/get_request_data',
                  method: 'POST',
                  dataType: 'JSON',
                  data: {'id' : reqId},

                  success: function(response){
                      switch(response.status){
                          case 'success':
                              var modal = $('#view-denied-ct-request-modal');
                              var html = `<p class="border-bottom"><b>Denied By: ${response.data.updated_by}</b></p><p>${response.data.reason_denied}</p>`
                              modal.find('.modal-body').html(html);
                              modal.modal('show');
                          break;

                          case 'error':
                              alert('Something went wrong. Please contact IT Administrator');
                          break;
                      }
                  }
              })
          })
      }

      app.saveScheduleOnly = function(){
          $(document).on('click', '.emp-cru-update-schedule-btn', function(e){
            e.preventDefault();
            
            var btn = $(this);
            var form = $('#employee-update-form');
            var responseDiv = $('.user-fixed-schedule-response');
  
            var days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
            var workbase = [];
            days.forEach(function(day) {
                let i = [];
                $('[name="'+day+'-workbase"]:checked').each(function(){
                  i.push($(this).val());
                });
                workbase.push(i.join('/'));
            });
  
            var data = {
                          'schedule-monday-in'      : $('[name="schedule-monday-in"]').val(),
                          'schedule-monday-out'     : $('[name="schedule-monday-out"]').val(),
                          'schedule-tuesday-in'     : $('[name="schedule-tuesday-in"]').val(),
                          'schedule-tuesday-out'    : $('[name="schedule-tuesday-out"]').val(),
                          'schedule-wednesday-in'   : $('[name="schedule-wednesday-in"]').val(),
                          'schedule-wednesday-out'  : $('[name="schedule-wednesday-out"]').val(),
                          'schedule-thursday-in'    : $('[name="schedule-thursday-in"]').val(),
                          'schedule-thursday-out'   : $('[name="schedule-thursday-out"]').val(),
                          'schedule-friday-in'      : $('[name="schedule-friday-in"]').val(),
                          'schedule-friday-out'     : $('[name="schedule-friday-out"]').val(),
                          'monday-workbase'         : workbase[0],
                          'tuesday-workbase'        : workbase[1],
                          'wednesday-workbase'      : workbase[2],
                          'thursday-workbase'       : workbase[3],
                          'friday-workbase'         : workbase[4],
                          'user-id'                 : $('[name="user-id"]').val()
                        };
            
            $.ajax({
              url: base_url + 'admin/ajax_users/update_user_schedule_only',
              method: 'POST',
              dataType: 'JSON',
              data: data,
  
              beforeSend: function(){
                btn.html('<i class="fa-solid fa-spin fa-spinner"></i> Updating...');
                form.find('.error').removeClass('error');
              },
  
              success: function(response){
                switch(response.status){
                  case 'form-incomplete':
                    $.each(response.errors,function(index, val){
                      form.find('[name="'+index+'"]').addClass('error');                             
                    });
                    btn.html('Save Schedule');
                  break;
  
                  case 'success':
                    var html = `<div class="input-con row"><div class="col-md-11 alert alert-success">${response.message}</div></div>`;
                    responseDiv.html(html);
  
                    setTimeout(function(){
                      form.find('.alert').remove();
                      btn.html('Save Schedule');
                    }, 2000);
                  break;
                }
              }
            });
          });
        }
  
        app.saveTemporaryScheduleOnly = function(){
          $(document).on('click', '.emp-cru-save-temp-schedule-btn', function(e){
            e.preventDefault();
            
            var btn = $(this);
            var form = $('#employee-update-form');
            var responseDiv = $('.user-temporary-schedule-response');
            var workbase = [];
            $('[name="temp-workbase"]:checked').each(function(){
              workbase.push($(this).val());
            });
  
            //console.log(workbase.join('/'));
  
            var data = {
              'temp-schedule-date'  : $('[name="temp-schedule-date"]').val(),
              'temp-schedule-in'    : $('[name="temp-schedule-in"]').val(),
              'temp-schedule-out'   : $('[name="temp-schedule-out"]').val(),
              'temp-workbase'       : workbase.join('/'),
              'user-id'             : $('[name="user-id"]').val()
            };
  
            $.ajax({
              url: base_url + 'admin/ajax_users/add_temporary_schedule_only',
              method: 'POST',
              dataType: 'JSON',
              data: data,
  
              beforeSend: function(){
                form.find('.alert').remove();
                form.find('.error').removeClass('error');
  
              },
  
              success: function(response){
                switch(response.status){
                  case 'form-incomplete':
                    $.each(response.errors,function(index, val){
                      form.find('[name="'+index+'"]').addClass('error');                              
                    });
  
                    var html = `<div class="alert alert-danger">Please fill in required fields.</div>`;
                    responseDiv.html(html);
                  break;
  
                  case 'success':
                    $('#temporary-schedule-table').removeClass('d-none');
                    var table = $('#temporary-schedule-table').DataTable();
                    var html = `<div class="alert alert-success">${response.message}</div>`;
                    var date = $('[name="temp-schedule-date"]').val();
                    var time = $('[name="temp-schedule-in"]').val() + '-' + $('[name="temp-schedule-out"]').val();
                    var log = time.split('-');
                    let tableHtml = `<tr>
                                        <td>${app.formatDate(date)}</td>
                                        <td>${app.convertTimeTo12HourFormat(log[0])}</td>
                                        <td>${app.convertTimeTo12HourFormat(log[1])}</td>
                                        <td>${workbase.join('/')}</td>
                                        <td class="text-right sched-${response.data}"><button class="btn edit" data-sched-id="${response.data}" data-date="${$('[name="temp-schedule-date"]').val()}" data-time-in="${$('[name="temp-schedule-in"]').val()}" data-time-out="${$('[name="temp-schedule-out"]').val()}" data-workbase="${workbase.join('/')}">Edit</button><button class="btn cancel" data-sched-id="${response.data}">Cancel</button></td>
                                      </tr>`;
                    table.row.add($(tableHtml)).draw();
                    $('#temporary-schedule-table').find('.sched-'+response.data).closest('tr').attr('id', 'temp-sched-row-'+response.data);
                    responseDiv.html(html);
  
                    setTimeout(function(){
  
                      form.find('.alert').fadeOut('slow', function(){
                        $(this).remove();
                      });
                      form.find('.error').removeClass('error');
  
                      //clear temporary schedule inputs
                      $('[name="temp-workbase"]:checked').prop('checked', false);
                      $('[name="temp-schedule-date"]').val('');
                      $('[name="temp-schedule-in"]').val('');
                      $('[name="temp-schedule-out"]').val('');
                    }, 2000);
                  break;
                }
              }
            });
          });
        }
  
        app.editTemporarySchedule = function(){
          $(document).on('click', '#temporary-schedule-table .btn.edit', function(e){
            e.preventDefault();
            
            var schedId = $(this).data('schedId');
            var userId = $(this).data('userId');
            var schedDate = $(this).data('date');
            var schedTimeIn = $(this).data('timeIn');
            var schedTimeOut = $(this).data('timeOut');
            var schedWorkbase = $(this).data('workbase');
            var table = $('#temporary-schedule-table');
            var parent = table.find('.sched-' + schedId).parent();
  
            var workbaseWfh = (schedWorkbase == 'WFH') ? 'selected' : '';
            var workbaseOffice = (schedWorkbase == 'Office') ? 'selected' : '';
            var workbaseBoth = (schedWorkbase == 'WFH/Office') ? 'selected' : '';
  
            parent.find('td:first-child').html('<input type="date" name="temp-sched-date" value="'+schedDate+'">');
            parent.find('td:nth-child(2)').html('<input type="time" name="temp-sched-in" value="'+schedTimeIn+'">');
            parent.find('td:nth-child(3)').html('<input type="time" name="temp-sched-out" value="'+schedTimeOut+'">');
            parent.find('td:nth-child(4)').html('<select name="temp-sched-workbase"><option value="WFH" '+workbaseWfh+'>WFH</option><option value="Office" '+workbaseOffice+'>Office</option><option value="WFH/Office" '+workbaseBoth+'>WFH/Office</option></select>');
            parent.find('td:last-child').html('<button class="edit-temporary-schedule-btn border-0" data-user-id="'+userId+'" data-sched-id="'+schedId+'">Save</button>')
          });
  
          $(document).on('click', '.edit-temporary-schedule-btn', function(e){
            e.preventDefault();
            var schedId = $(this).data('schedId');
            var userId = $(this).data('userId');
            var row = $('#temp-sched-row-'+schedId);
            var responseDiv = $('.user-temporary-schedule-response');
            var tempSchedData = {
              'temp-sched-date' : row.find('[name="temp-sched-date"]').val(),
              'temp-sched-in' : row.find('[name="temp-sched-in"]').val(),
              'temp-sched-out' : row.find('[name="temp-sched-out"]').val(),
              'temp-sched-workbase' : row.find('[name="temp-sched-workbase"]').val(),
              'schedule-id' : schedId,
              'user-id' : userId
            };
  
            $.ajax({
              url: base_url + 'admin/ajax_users/edit_temporary_schedule',
              method: 'POST',
              dataType: 'JSON',
              data: tempSchedData,
  
              beforeSend: function(){
                row.find('.error').removeClass('error');
                row.find('.alert').remove();
              },
  
              success: function(response){
                switch(response.status){
                  case 'form-incomplete':
                    $.each(response.errors,function(index, val){
                      row.find('[name="'+index+'"]').addClass('error');                          
                    });
  
                    var html = `<div class="alert alert-danger">Please fill in required fields.</div>`;
                    responseDiv.html(html);
                  break;
  
                  case 'success':
                    var html = `<div class="alert alert-success">${response.message}</div>`;
                    responseDiv.html(html);
                    row.find('td:first-child').html(app.formatDate(response.data.date));
                    row.find('td:nth-child(2)').html(app.convertTimeTo12HourFormat(response.data.time_in));
                    row.find('td:nth-child(3)').html(app.convertTimeTo12HourFormat(response.data.time_out));
                    row.find('td:nth-child(4)').html(response.data.workbase);
                    row.find('td:last-child').html(`<button class="btn edit" data-sched-id="${schedId}" data-date="${response.data.date}" data-time-in="${response.data.time_in}" data-time-out="${response.data.time_out}" data-workbase="${response.data.workbase}">Edit</button><button class="btn cancel" data-sched-id="${schedId}">Cancel</button>`);
  
                    setTimeout(function(){
  
                      responseDiv.find('.alert').fadeOut('slow', function(){
                        $(this).remove();
                      });
                    }, 2000);
                  break;
  
                  case 'error':
                  break;
                }
              }
            });
          });
        }
  
        app.cancelTemporarySchedule = function(){
          $(document).on('click', '#temporary-schedule-table .btn.cancel', function(e){
            e.preventDefault();
  
            var schedId = $(this).data('schedId');
            var responseDiv = $('.user-temporary-schedule-response');
            var table = $('#temporary-schedule-table');
            
            $.ajax({
              url: base_url + 'admin/ajax_users/cancel_temporary_schedule',
              method: 'POST',
              dataType: 'JSON',
              data: {'sched-id' : schedId},
  
              beforeSend: function(){
                table.find('.alert').remove();
              },
  
              success: function(response){
                switch(response.status){
                  case 'success':
                    var html = `<div class="alert alert-success">${response.message}</div>`;
                    responseDiv.html(html);
  
                    var row = table.find('#temp-sched-row-'+schedId);
                    row.fadeOut('slow', function(){
                      $(this).remove();
                    });
  
                    if((table.find('tbody tr').length - 1) <= 0){
                      table.fadeOut('slow', function(){
                        table.addClass('d-none');
                      });
                    }
  
                    setTimeout(function(){
                      responseDiv.find('.alert').fadeOut('slow', function(){
                        $(this).remove();
                      });
                    }, 2000);
                  break;
  
                  case 'error':
                    var html = `<div class="alert alert-success">${response.message}</div>`;
                    responseDiv.html(html);
                  break;
  
                }
              }
            });
          });
        }

        app.cancelmyChangeTimeRequest = function(){
          $(document).on('click', '.cancel-my-ctr-request', function(){
            var reqId = $(this).closest('[data-ctr-id]').data('ctrId');

            $.ajax({
              url: base_url + 'employee/ajax_ct_request/delete_ct_request',
              method: 'POST',
              dataType: 'JSON',
              data: {'request-id': reqId},

              success: function(response){
                switch(response.status){
                  case 'success':
                    $('#ctr-leave-list-tr-'+reqId).fadeOut('slow', function(){
                      $(this).remove();
                    })
                  break;
                }
              }
            })
          })
        }

        app.onChangeDateEmpRange = function(){
          var params = new URLSearchParams(window.location.search);
          $(document).on('click', '.li-emp-range, .li-date-range', function(e) {
            e.preventDefault();

            var urlEmp = $(this).data('urlEmp');
            var urlDate = $(this).data('urlDate');
            var urlStatus = $(this).data('urlStatus');

            if (urlEmp) {
              if(urlEmp == 'all'){
                params.delete('emp_id');
              }else{
                params.set('emp_id', urlEmp);
              }
            }

            if (urlDate) {
              var dateParts = urlDate.split('-');
              if (dateParts.length === 2) {
                  params.set('month', dateParts[0]);
                  params.set('year', dateParts[1]);
              }
            }

            if (urlStatus) {
              if(urlStatus == 'all'){
                params.delete('status');
              }else{
                params.set('status', urlStatus);
              }
            }
            
            var newUrl = `${window.location.pathname}?${params.toString()}`;
            window.location.href = newUrl;
          });
        }

        app.clearFilter = function(){
          $(document).on('click', '.clear-filter-btn', function(e){
            e.preventDefault();

            var newUrl = window.location.pathname;
            window.location.href = newUrl;

          })
        }

        app.generatePdfModal = function(){
          $(document).on('click', '.generate-pdf-btn', function(e){
            e.preventDefault();
            var modal = $('#generate-request-modal');
            var reqId = $(this).closest('[data-request-id]').data('requestId');
            var userName = "";

            $.ajax({
              url: base_url + 'employee/ajax_ct_request/fetch_ct_request',
              method: 'POST',
              dataType: 'JSON',
              data: {id : reqId},

              success: function(response){
                switch(response.response_status){
                  case 'success':
                    var signatureEmployee = base_url + 'assets/img/signatures/' + response.user_id + '.png';  
                    var signatureApprover = base_url + 'assets/img/signatures/' + response.approved_by_id + '.png';  

                    userName = response.user_name;

                    modal.find('.req-leave-type').html('<h3><b>REQUEST TO FILE CHANGE SCHEDULE</b></h3>');
                    modal.find('.request-label').text('Change-time Request Details');
                    modal.find('.req-date').text(app.formatDateTime(response.date_created));
                    modal.find('table th:last-child').text('Details');
                    modal.find('.req-name').html('<b>' + userName + '</b>');
                    modal.find('.req-department').text(response.user_department);
                    modal.find('.req-leave-date').html(response.details);
                    modal.find('.req-reason').html('');
                    modal.find('.req-conformed-by').html('<b>' + response.approved_by_name + '</b>');
                    modal.find('.signature-container-employee').html('<img src="'+signatureEmployee+'">');
                    modal.find('.signature-container-approver').html('<img src="'+signatureApprover+'">');

                    modal.modal('show');

                    app.generatePdf(userName);
                    
                  break;
                }
              }
            })
            
          });

          $(document).on('click', '.generate-pdf-denied-btn', function(e){
            e.preventDefault();
            var modal = $('#generate-request-modal');
            var reqId = $(this).closest('[data-request-id]').data('requestId');
            var userName = "";

            $.ajax({
              url: base_url + 'employee/ajax_ct_request/fetch_ct_request',
              method: 'POST',
              dataType: 'JSON',
              data: {id : reqId},

              success: function(response){
                switch(response.response_status){
                  case 'success':
                    var signatureEmployee = base_url + 'assets/img/signatures/' + response.user_id + '.png';  
                    var signatureApprover = base_url + 'assets/img/signatures/' + response.approved_by_id + '.png';  

                    userName = response.user_name;

                    modal.find('.req-leave-type').html('<h3><b>REQUEST TO FILE CHANGE SCHEDULE</b></h3>');
                    modal.find('.request-label').text('Change-time Request Details');
                    modal.find('.req-date').text(app.formatDateTime(response.date_created));
                    modal.find('table th:last-child').text('Details');
                    modal.find('.req-name').html('<b>' + userName + '</b>');
                    modal.find('.req-department').text(response.user_department);
                    modal.find('.req-leave-date').html(response.details);
                    modal.find('.req-reason').html('<p><b>Reason Denied:</b> <br> ' + response.reason_denied + '</p>');
                    modal.find('.req-conformed-by').html('<b>' + response.approved_by_name + '</b>');
                    modal.find('.signature-container-employee').html('<img src="'+signatureEmployee+'">');
                    modal.find('.signature-container-approver').html('<img src="'+signatureApprover+'">');

                    modal.modal('show');

                    app.generatePdf(userName);
                    
                  break;
                }
              }
            })
            
          });
        }

        app.generatePdf = function(name){
          $(document).on('click', '.generate-pdf', function(e){
            e.preventDefault();
            
            var splitName = name.split(' ');
            var filename = splitName[0] + '_' + 'CTR';

            $('.truly-yours-p').addClass('pt-5');
            kendo.drawing.drawDOM($("#pdf-request-content"), {
              paperSize: "Letter",
              margin: { left: 20, top: 0, right: 20, bottom: 0 },
              template: $("#page-template").html(),
              scale: .70,
              multiPage: true
            }).then(function (group) {
                return kendo.drawing.exportPDF(group);
            }).done(function (data) {
              $('.truly-yours-p').removeClass('pt-5');
              kendo.saveAs({
                dataURI: data,
                fileName: filename + ".pdf"       
              });
            });
          })
        }

      app.init();
  })(Script);
});