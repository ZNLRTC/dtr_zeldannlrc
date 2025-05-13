$(function () {
    var Script = {};
    (function (app) {
        var ww = window.innerWidth;

        app.init = function () {
            app.bindings();
        }

        app.bindings = function () {
            app.initializeOvertimeTable();
            app.requestOvertimeModal();
            app.requestOvertime();
            app.approveOtRequestModal();
            app.approveRequest();
            app.viewDeniedOtReqReason();
            app.declineOtRequestModal();
            app.declineOtRequest();
            app.cancelOtRequestModal();
            app.cancelOtRequest();
            app.deleteOtRequestModal();
            app.deleteOtRequest();
            // app.cancelOtRequest();

            app.onChangeDateEmpRange();
            app.clearFilter();

            app.generatePdfModal();

            //new
            app.saveOvertimeModal()
            app.saveOvertimeForm();
            app.otTimeOutForm();
            app.isPaid();
            app.viewEodReportModal();


        }

        app.saveOvertimeModal = function(){
            $(document).on('click', '.ot-time-btn', function(e){
                e.preventDefault();
        
                var userId = $(this).data('userId');
                var otId = $(this).data('otId');
                var action = $(this).data('action');
                var workbase = $('#workbase-options [name="dtr-work-base"]:checked').val();

                console.log(workbase);
    
                switch(action){
                  case 'ot-time-in':

                    if(workbase == undefined){
                        $('#hybrid-schedule-no-workbase-modal').modal('show');
                    }else{
                        var modal = $('#select-ot-pre-post-modal');
                        var form = modal.find('form');
                        form.find('[name="user-id"]').val(userId);
                        form.find('[name="ot-id"]').val(otId);
                        form.find('[name="action"]').val(action);
                        form.find('[name="workbase"]').val(workbase);
                        modal.modal('show');
                    }
                    
                  break;

                  case 'ot-break-in':
                    app.saveOtBreakInOut(userId, otId, 'ot-break-in', workbase)
                  break;

                  case 'ot-break-out':
                    if(workbase == undefined){
                        $('#hybrid-schedule-no-workbase-modal').modal('show');
                    }else{
                        app.saveOtBreakInOut(userId, otId, 'ot-break-out', workbase);
                    }
                  break;
    
                  case 'ot-time-out':
                    var modal = $('#eod-ot-report-modal');
                    modal.find('[name="ot-id"]').val(otId);
                    modal.modal('show');
                  break;
                }
              
            })
        }

        app.saveOtBreakInOut = function(userId, otId, action, workbase){
            $.ajax({
                url: base_url + 'employee/ajax_overtime/break_in_out',
                method: 'POST',
                dataType: 'JSON',
                data: {'user-id' : userId, 'ot-id' : otId, 'action' : action, 'workbase' : workbase},

                success:function(response){
                    switch(response.status){
                        case 'success':
                            window.location.reload();
                        break;
                    }
                }
            })
        }

        app.saveOvertimeForm = function(){
  
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

        app.otTimeOutForm = function(){
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
                        var html = `<div class="alert alert-success">${response.message}</div>`;
                        $("#eod-ot-report-modal").find('.response').html(html);

                        setTimeout(function(){
                            window.location.reload();
                        }, 2000);
                    break;
                  }
                }
              })
              
            })
        }

        app.viewEodReportModal = function(){
            $(document).on('click', '.view-eod-report-btn', function(e){
                e.preventDefault();
                var modal = $("#view-eod-report-modal");
                var otId = $(this).data('otId');
                // console.log('asdasd');

                $.ajax({
                    url: base_url + 'employee/ajax_overtime/fetch_ot_info',
                    method: 'POST',
                    dataType: 'JSON',
                    data: {'request-id' : otId},

                    success: function(response){
                        if(response.status == 'success'){
                            var html = `<p>${response.task}</p>`;
                            modal.find('.modal-body').html(html);
                            modal.modal('show');
                        }
                    }
                })
            })
        }


        //Old

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

        app.formatTime = function (timeRange) {
            function convertTo12HourFormat(time) {
                let [hours, minutes] = time.split(':').map(Number);
                let period = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12 || 12; // Convert 0 to 12 for midnight and 12-hour format
                return `${hours}:${minutes.toString().padStart(2, '0')}${period}`;
            }

            let [startTime, endTime] = timeRange.split('-');
            let convertedStartTime = convertTo12HourFormat(startTime);
            let convertedEndTime = convertTo12HourFormat(endTime);

            return `${convertedStartTime} - ${convertedEndTime}`;
        };

        app.initializeOvertimeTable = function () {
            otTable = $('#ot-leave-list').DataTable({
                bLengthChange: false,
                searching: true,
                info: false,
                iDisplayLength: 25,
                order: [],
                columnDefs: [
                    {
                        targets: [0],
                        orderable: false
                    },
                ]
            });

            $("#ot-leave-list-search").keyup(function () {
                otTable.search($(this).val()).draw();
            });
        }

        app.requestOvertimeModal = function () {
            $(document).on('click', '.request-ot-btn', function () {
                var dtrId = $(this).parent().parent().attr('tr-id');
                var modal = $('#overtime-request-modal');
                modal.find('form').find('[name="dtr-id"]').val(dtrId);
                modal.modal('show');
            });
        }

        app.requestOvertime = function () {
            $(document).on('submit', '#overtime-request-form', function (e) {
                e.preventDefault();
                var form = $(this);

                $.ajax({
                    url: base_url + 'employee/ajax_dtr/request_overtime',
                    method: 'POST',
                    dataType: 'JSON',
                    data: form.serialize(),

                    beforeSend: function () {
                        form.find('.error').removeClass('error');
                        form.find('.error-message, .alert').remove();
                    },

                    success: function (response) {
                        switch (response.status) {
                            case 'form-incomplete':
                                $.each(response.errors, function (e, val) {
                                    if (e == 'invalid-ot') {
                                        form.find('[name="ot-out"]').addClass('error');
                                        form.find('.invalid-ot-response').html('<div class="error-message text-left"><i class="fa-solid fa-xmark"></i> ' + val + '</div>')
                                    } else {
                                        form.find('[name="' + e + '"]').addClass('error');
                                        form.find('[name="' + e + '"]').parent().append('<i class="error-message">' + val + '</i>');
                                    }
                                });
                                break;

                            case 'success':
                                var html = `<div class="alert alert-success">${response.message}</div>`;
                                form.find('.response').html(html);

                                setTimeout(function () {
                                    window.location.reload();
                                }, 2000);
                                break;

                            case 'error':
                                alert('Something went wrong. Please contact IT Administrator.');
                                break;
                        }
                    }
                })
            });
        }

        app.requestOvertimeModal = function () {
            $(document).on('click', '.request-ot-btn', function () {
                var dtrId = $(this).parent().parent().attr('tr-id');
                var modal = $('#overtime-request-modal');
                modal.find('form').find('[name="dtr-id"]').val(dtrId);
                modal.modal('show');
            });
        }

        app.requestOvertime = function () {
            $(document).on('submit', '#overtime-request-form', function (e) {
                e.preventDefault();
                var form = $(this);

                $.ajax({
                    url: base_url + 'employee/ajax_dtr/request_overtime',
                    method: 'POST',
                    dataType: 'JSON',
                    data: form.serialize(),

                    beforeSend: function () {
                        form.find('.error').removeClass('error');
                        form.find('.error-message, .alert').remove();
                    },

                    success: function (response) {
                        switch (response.status) {
                            case 'form-incomplete':
                                $.each(response.errors, function (e, val) {
                                    if (e == 'invalid-ot') {
                                        form.find('[name="ot-out"]').addClass('error');
                                        form.find('.invalid-ot-response').html('<div class="error-message text-left"><i class="fa-solid fa-xmark"></i> ' + val + '</div>')
                                    } else {
                                        form.find('[name="' + e + '"]').addClass('error');
                                        form.find('[name="' + e + '"]').parent().append('<i class="error-message">' + val + '</i>');
                                    }
                                });
                                break;

                            case 'success':
                                var html = `<div class="alert alert-success">${response.message}</div>`;
                                form.find('.response').html(html);

                                setTimeout(function () {
                                    window.location.reload();
                                }, 2000);
                                break;

                            case 'error':
                                alert('Something went wrong. Please contact IT Administrator.');
                                break;
                        }
                    }
                })
            });
        }

        app.approveOtRequestModal = function () {
            $(document).on('click', '.approve-ot-request-btn', function (e) {
                e.preventDefault();
                var reqId = $(this).parent().data('reqId');
                var empName = $(this).parent().data('empName');
                var modal = $('#approve-ot-request-modal');

                $.ajax({
                    url: base_url + 'employee/ajax_overtime/fetch_ot_info',
                    method: 'POST',
                    dataType: 'JSON',
                    data: { 'request-id': reqId },

                    success: function (response) {
                        switch (response.status) {
                            case 'success':
                                modal.find('[name="request-id"]').val(reqId);
                                modal.find('.emp-name').text(empName);
                                modal.find('.request-message').text(response.task);
                                modal.modal('show');
                                break;
                        }
                    }
                });
            });
        }

        app.approveRequest = function () {
            $(document).on('submit', '#approve-ot-request-form', function (e) {
                e.preventDefault();
                var form = $(this);

                $.ajax({
                    url: base_url + 'employee/ajax_overtime/approve_ot_request',
                    method: 'POST',
                    dataType: 'JSON',
                    data: form.serialize(),

                    success: function (response) {
                        switch (response.status) {
                            case 'success':
                                form.find('.response').html(`<div class="alert alert-success">${response.message}</div>`);
                                setTimeout(function () {
                                    window.location.reload();
                                }, 2000);
                                break;

                            case 'error':
                                alert('Something went wrong. Please contact Administrator');
                                break;
                        }
                    }
                })

            });
        }

        app.viewDeniedOtReqReason = function () {
            $(document).on('click', '.denied-otreq-btn', function () {
                reqId = $(this).closest('[data-req-id]').data('reqId');

                $.ajax({
                    url: base_url + 'employee/ajax_overtime/fetch_ot_denied',
                    method: 'POST',
                    dataType: 'JSON',
                    data: { 'id': reqId },

                    success: function (response) {
                        switch (response.status) {
                            case 'success':
                                var modal = $('#view-ot-denied-message-modal');
                                modal.find('.modal-body').html(`<p class="border-bottom"><b>Request denied by:</b> ${response.denied_by}</p><p><b>Reason: </b>${response.reason_denied}</p>`);
                                modal.modal('show');
                                break;
                        }
                    }
                })
            })
        }

        app.declineOtRequestModal = function () {
            $(document).on('click', '.decline-ot-request-btn', function () {
                var reqId = $(this).parent().data('reqId');
                var modal = $('#decline-ot-request-modal');
                var form = modal.find('form');

                form.find('[name="request-id"]').val(reqId);
                modal.modal('show');
            });
        }

        app.declineOtRequest = function () {
            $(document).on('submit', '#decline-ot-request-form', function (e) {
                e.preventDefault();
                var form = $(this);

                $.ajax({
                    url: base_url + 'employee/ajax_overtime/decline_request',
                    method: 'POST',
                    dataType: 'JSON',
                    data: form.serialize(),

                    beforeSend: function () {
                        form.find('.alert').remove();
                    },

                    success: function (response) {
                        switch (response.status) {
                            case 'success':
                                var html = `<div class="alert alert-success">${response.message}</div>`;
                                form.find('.response').html(html);

                                setTimeout(function () {
                                    window.location.reload();
                                }, 2000);
                                break;

                            case 'error':
                                alert('Something went wrong. Please contact Administrator');
                                break;
                        }
                    }
                })

            });
        }

        app.cancelOtRequestModal = function () {
            $(document).on('click', '.cancel-ot-request-btn', function (e) {
                e.preventDefault();
                var reqId = $(this).closest('[data-req-id]').data('reqId');
                var empName = $(this).closest('[data-emp-name]').data('empName');
                var modal = $('#cancel-ot-request-modal');

                // console.log(reqId);

                $.ajax({
                    url: base_url + 'employee/ajax_ot_request/fetch_ot_info',
                    method: 'POST',
                    dataType: 'JSON',
                    data: { 'request-id': reqId },

                    success: function (response) {
                        switch (response.status) {
                            case 'success':
                                modal.find('[name="request-id"]').val(reqId);
                                modal.find('.emp-name').text(empName);
                                modal.find('.request-message').text(response.task);
                                modal.modal('show');
                                break;
                        }
                    }
                });
            });
        }

        app.cancelOtRequest = function () {
            $(document).on('submit', '#cancel-ot-request-form', function (e) {
                e.preventDefault();
                var form = $(this);

                $.ajax({
                    url: base_url + 'employee/ajax_overtime/cancel_ot_request',
                    method: 'POST',
                    dataType: 'JSON',
                    data: form.serialize(),

                    beforeSend: function () {
                        form.find('.alert').remove();
                    },

                    success: function (response) {
                        switch (response.status) {
                            case 'success':
                                var html = `<div class="alert alert-success">${response.message}</div>`;
                                form.find('.response').html(html);

                                setTimeout(function () {
                                    window.location.reload();
                                }, 2000);
                                break;

                            case 'error':
                                alert('Something went wrong. Please contact Administrator');
                                break;
                        }
                    }
                })
            });
        }

        app.deleteOtRequestModal = function () {
            $(document).on('click', '.delete-ot-request-btn', function (e) {
                e.preventDefault();
                var reqId = $(this).closest('[data-req-id]').data('reqId');
                var empName = $(this).closest('[data-emp-name]').data('empName');
                var modal = $('#delete-ot-request-modal');

                // console.log(reqId);

                $.ajax({
                    url: base_url + 'employee/ajax_overtime/fetch_ot_info',
                    method: 'POST',
                    dataType: 'JSON',
                    data: { 'request-id': reqId },

                    success: function (response) {
                        switch (response.status) {
                            case 'success':
                                modal.find('[name="request-id"]').val(reqId);
                                modal.find('.emp-name').text(empName);
                                modal.find('.request-message').text(response.task);
                                modal.modal('show');
                                break;
                        }
                    }
                });
            });
        }

        app.deleteOtRequest = function () {
            $(document).on('submit', '#delete-ot-request-form', function (e) {
                e.preventDefault();
                var form = $(this);

                $.ajax({
                    url: base_url + 'employee/ajax_overtime/delete_ot_request',
                    method: 'POST',
                    dataType: 'JSON',
                    data: form.serialize(),

                    beforeSend: function () {
                        form.find('.alert').remove();
                    },

                    success: function (response) {
                        switch (response.status) {
                            case 'success':
                                var html = `<div class="alert alert-success">${response.message}</div>`;
                                form.find('.response').html(html);
                                var modal = $('#delete-ot-request-modal');
                                var id = form.find('[name="request-id"]').val();

                                $('#ot-request-tr-' + id).fadeOut("slow", function () {
                                    $(this).remove();
                                });

                                setTimeout(function () {
                                    modal.modal('hide');
                                }, 2000);
                                break;

                            case 'error':
                                alert('Something went wrong. Please contact Administrator');
                                break;
                        }
                    }
                })
            });
        }
        app.onChangeDateEmpRange = function () {
            var params = new URLSearchParams(window.location.search);
            $(document).on('click', '.li-emp-range, .li-date-range', function (e) {
                e.preventDefault();

                var urlEmp = $(this).data('urlEmp');
                var urlDate = $(this).data('urlDate');
                var urlStatus = $(this).data('urlStatus');

                if (urlEmp) {
                    if (urlEmp == 'all') {
                        params.delete('emp_id');
                    } else {
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
                    if (urlStatus == 'all') {
                        params.delete('status');
                    } else {
                        params.set('status', urlStatus);
                    }
                }

                var newUrl = `${window.location.pathname}?${params.toString()}`;
                window.location.href = newUrl;
            });
        }

        app.clearFilter = function () {
            $(document).on('click', '.clear-filter-btn', function (e) {
                e.preventDefault();

                var newUrl = window.location.pathname;
                window.location.href = newUrl;

            })
        }

        app.generatePdfModal = function () {

            $(document).on('click', '.generate-pdf-btn', function (e) {
                e.preventDefault();
                var modal = $('#generate-request-modal');
                var reqId = $(this).closest('[data-req-id]').data('reqId');

                $.ajax({
                    url: base_url + 'employee/ajax_overtime/fetch_leave_request',
                    method: 'POST',
                    dataType: 'JSON',
                    data: { id: reqId },

                    success: function (response) {
                        switch (response.response_status) {
                            case 'success':
                                var signatureEmployee = base_url + 'assets/img/signatures/' + response.user_id + '.png';
                                var signatureApprover = base_url + 'assets/img/signatures/' + response.approved_by_id + '.png';

                                let userName = response.user_name;
                                let leaveType = response.leave_type;
                                let date = app.formatDateTime(response.date);
                                let time = app.formatTime(response.time);

                                modal.find('.req-leave-type').html('<h3><b>REQUEST TO FILE ' + leaveType.toUpperCase() + ' OVERTIME</b></h3>');
                                modal.find('.request-label').text('Overtime Request Details');
                                modal.find('.req-date').text(app.formatDateTime(response.date_created));
                                modal.find('.req-name').html('<b>' + userName + '</b>');
                                modal.find('.req-department').text(response.user_department);
                                modal.find('.req-leave-date').html('<div contenteditable="true">' + date + ' | ' + time + '</div>');
                                modal.find('.req-reason').html('<p><b>Reason: </b> <br>' + response.details + '</p>');
                                modal.find('.req-conformed-by').html('<b>' + response.approved_by_name + '</b>');
                                modal.find('.signature-container-employee').html('<img src="' + signatureEmployee + '" alt="' + userName + '`s Signature">');
                                modal.find('.signature-container-approver').html('<img src="' + signatureApprover + '">');

                                modal.modal('show');

                                // Generate PDF on button click
                                $(document).off('click', '.generate-pdf').on('click', '.generate-pdf', function (e) {
                                    e.preventDefault();
                                    app.generateOtPdf(userName, leaveType, date, false); // false for not denied
                                });

                                break;
                        }
                    }
                });
            });


            $(document).on('click', '.denied-generate-pdf-btn', function (e) {
                e.preventDefault();
                var modal = $('#generate-request-modal');
                var reqId = $(this).closest('[data-req-id]').data('reqId');

                $.ajax({
                    url: base_url + 'employee/ajax_overtime/fetch_leave_request',
                    method: 'POST',
                    dataType: 'JSON',
                    data: { id: reqId },

                    success: function (response) {
                        switch (response.response_status) {
                            case 'success':
                                var signatureEmployee = base_url + 'assets/img/signatures/' + response.user_id + '.png';
                                var signatureApprover = base_url + 'assets/img/signatures/' + response.approved_by_id + '.png';

                                let userName = response.user_name;
                                let leaveType = response.leave_type;
                                let date = app.formatDateTime(response.date);
                                let time = app.formatTime(response.time);

                                modal.find('.req-leave-type').html('<h3><b>REQUEST TO FILE ' + leaveType.toUpperCase() + ' OVERTIME</b></h3>');
                                modal.find('.request-label').text('Overtime Request Details');
                                modal.find('.req-date').text(app.formatDateTime(response.date_created));
                                modal.find('.req-name').html('<b>' + userName + '</b>');
                                modal.find('.req-department').text(response.user_department);
                                modal.find('.req-leave-date').html('<div contenteditable="true">' + date + ' | ' + time + '</div>');
                                modal.find('.req-reason').html('<p><b>Reason denied: </b> <br>' + response.reason_denied + '</p>');
                                modal.find('.req-conformed-by').html('<b>' + response.approved_by_name + '</b>');
                                modal.find('.signature-container-employee').html('<img src="' + signatureEmployee + '" alt="' + userName + '`s Signature">');
                                modal.find('.signature-container-approver').html('<img src="' + signatureApprover + '">');

                                modal.modal('show');

                                $(document).off('click', '.generate-pdf').on('click', '.generate-pdf', function (e) {
                                    e.preventDefault();
                                    app.generateOtPdf(userName, leaveType, date, true);
                                });

                                break;
                        }
                    }
                });
            });
        };

        app.generateOtPdf = function (name, type, date, isDenied) {
            var splitName = name.split(' ');
            var split_date = date.replace(' ', '_');
            var status = isDenied ? '_Denied' : '';
            var filename = splitName[0] + status + '_' + type.toUpperCase() + '_OT_' + split_date.replace(', ', '_') + '_' + '.pdf'

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
                    fileName: filename
                });
            });
        };

        app.isPaid = function(){
            $(document).on('change', '[name="is-paid-checker"]', function(){
                var id = $(this).val();

                if($(this).is(':checked')){
                    var isPaid = 1;
                }else{
                    var isPaid = 0;
                }

                $.ajax({
                    url: base_url + 'employee/ajax_overtime/is_paid',
                    method: 'POST',
                    dataType: 'JSON',
                    data: {'id' : id, 'is-paid' : isPaid},

                    success: function(response){
                        switch(response.status){
                            case 'success':
                                
                            break;
                        }
                    }
                })
            })
        }

        app.init();
    })(Script);
});
