$(function () {
    var Script = {};
    (function (app) {
        var ww = window.innerWidth;
        let leavesTable;

        app.init = function () {
            app.bindings();
        }

        app.bindings = function () {
            app.initializeEmployeeLeaveList();
            app.initializeEmployeeLeaveListTable();
            app.initializePendingLeaveList();
            app.requestLeaveModal();
            app.requestLeaveForm();
            app.requestDeclineModal();
            app.requestDecline();
            app.viewRequestMessage();
            app.viewDeclinedRequestMessage();
            app.requestApproveModal();
            app.requestApproveForm();
            app.requestCancelModal();
            app.requestCancelForm();
            app.requestDeleteModal();
            app.requestDeleteForm();
            app.cancelmyRequest();

            app.onChangeDateEmpRange();
            app.clearFilter();
            app.generatePdfModals();

            app.retractRequestModal();
            app.retractRequest();
            app.requestRetractionApproveModal();
            app.requestRetractionApproveForm();
            app.requestRetractionDeclinedModal();
            app.requestRetractionDeclinedForm();
            app.viewRetractionDeniedMsg();

            app.generatePdfModal();
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

        app.initializeEmployeeLeaveList = function () {
            leavesTable = $('#my-leave-list').DataTable({
                bLengthChange: false,
                searching: true,
                info: false,
                iDisplayLength: 50,
                order: []
            });

            $("#my-leave-list-search").keyup(function () {
                leavesTable.search($(this).val()).draw();
            });
        }

        app.initializeEmployeeLeaveListTable = function () {
            employeeLeaveListTable = $('#employee-leave-list').DataTable({
                bLengthChange: false,
                searching: true,
                info: false,
                iDisplayLength: -1,
                paging: false,
                order: [],
                columnDefs: [
                    {
                        targets: [1, 2, 3],
                        orderable: false
                    },
                ]
            });

            $('#employee-leave-list-search, #employee-leave-list-search-mobile').keyup(function () {
                employeeLeaveListTable.search($(this).val()).draw();
            })
        }

        app.initializePendingLeaveList = function () {
            let pendingLeaveList = $('#pending-leave-list').DataTable({
                bLengthChange: false,
                searching: true,
                info: false,
                iDisplayLength: -1,
                paging: false,
                order: [],
            });

            $('#pending-leave-list-search').on('keyup', function () {
                pendingLeaveList.search($(this).val()).draw();
            })
        }

        app.requestLeaveModal = function () {
            $(document).on('click', '.request-leave-btn', function (e) {
                e.preventDefault();

                $('#request-leave-modal').modal('show');
            });
        }

        app.requestLeaveForm = function () {
            $(document).on('submit', '#request-leave-form', function (e) {
                e.preventDefault();

                var form = $(this);

                $.ajax({
                    url: base_url + 'employee/ajax_leave_request/add_leave',
                    method: 'POST',
                    dataType: 'JSON',
                    data: form.serialize(),

                    beforeSend: function () {
                        form.find('.error').removeClass('error');
                        form.find('.error-message').remove();
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

                            case 'form-incomplete':
                                $.each(response.errors, function (e, val) {
                                    form.find('[name="' + e + '"]').addClass('error');
                                    form.find('[name="' + e + '"]').parent().append('<i class="error-message">' + val + '</i>');
                                });
                                break;

                            case 'error':
                                break;
                        }
                    }
                });
            });
        }

        app.requestDeclineModal = function () {
            $(document).on('click', '.decline-leave-request-btn', function () {
                var id = $(this).parent().data('reqId');
                var modal = $('#decline-leave-request-modal');

                modal.find('[name="request-id"]').val(id);
                modal.modal('show');
            });
        }

        app.requestDecline = function () {
            $(document).on('submit', '#decline-leave-request-form', function (e) {
                e.preventDefault();

                var form = $(this);

                $.ajax({
                    url: base_url + 'employee/ajax_leave_request/decline_leave',
                    method: 'POST',
                    dataType: 'JSON',
                    data: form.serialize(),

                    success: function (response) {
                        switch (response.status) {
                            case 'success':
                                var html = `<div class="alert alert-success">${response.message}</div>`;
                                form.find('.response').html(html);

                                setTimeout(function () {
                                    form.find('.alert').fadeOut('slow', function () {
                                        $(this).remove();
                                    });

                                    window.location.reload();
                                }, 2000);
                                break;

                            case 'error':
                                var html = `<div class="alert alert-danger">${response.message}</div>`;
                                form.find('.response').html(html);
                                break;
                        }
                    }
                })
            });
        }

        app.viewRequestMessage = function () {
            $(document).on('click', '.view-sup-message-click', function () {
                var reqId = $(this).data('requestId');

                $.ajax({
                    url: base_url + 'employee/ajax_leave_request/fetch_leave_request',
                    method: 'POST',
                    dataType: 'JSON',
                    data: { 'leave_id': reqId },

                    success: function (response) {
                        switch (response.response_status) {
                            case 'success':
                                var modal = $('#view-request-leave-message-modal');
                                var html = `<p class="border-bottom"><b>Denied by:</b> ${response.user_name}</p> <p><b>Message:</b> ${response.reason_denied}</p>`;
                                modal.find('.modal-body').html(html);
                                modal.modal('show');
                                break;
                        }
                    }
                })
            });
        }

        app.viewDeclinedRequestMessage = function () {
            $(document).on('click', '.leave-request-denied-btn', function () {
                var reqId = $(this).closest('[data-req-id]').data('reqId');

                $.ajax({
                    url: base_url + 'employee/ajax_leave_request/fetch_leave_request',
                    method: 'POST',
                    dataType: 'JSON',
                    data: { 'leave_id': reqId },

                    success: function (response) {
                        switch (response.response_status) {
                            case 'success':
                                var modal = $('#view-denied-leave-request-modal');
                                var html = `<p class="border-bottom"><b>Denied by:</b> ${response.user_name}</p> <p><b>Message:</b> ${response.reason_denied}</p>`;
                                modal.find('.modal-body').html(html);
                                modal.modal('show');
                                break;
                        }
                    }
                })
            });
        }

        app.requestApproveModal = function () {
            $(document).on('click', '.approve-leave-request-btn', function (e) {
                e.preventDefault();

                var reqId = $(this).parent().data('reqId');
                var empName = $(this).parent().data('empName');

                $.ajax({
                    url: base_url + 'employee/ajax_leave_request/fetch_leave_request',
                    method: 'POST',
                    dataType: 'JSON',
                    data: { 'leave_id': reqId },

                    success: function (response) {
                        switch (response.response_status) {
                            case 'success':
                                var modal = $('#approve-leave-request-modal');
                                modal.find('[name="request-id"]').val(reqId);
                                modal.find('.emp-name').text(empName);
                                modal.find('.request-message').text(response.details);
                                modal.modal('show');
                                break;
                        }
                    }
                });
            })
        }



        app.requestApproveForm = function () {
            $(document).on('submit', '#approve-leave-request-form', function (e) {
                e.preventDefault();

                var form = $(this);

                $.ajax({
                    url: base_url + 'employee/ajax_leave_request/approve_leave',
                    method: 'POST',
                    dataType: 'JSON',
                    data: form.serialize(),

                    success: function (response) {
                        switch (response.status) {
                            case 'success':
                                var html = `<div class="alert alert-success">${response.message}</div>`;
                                form.find('.response').html(html);

                                setTimeout(function () {
                                    form.find('.alert').fadeOut('slow', function () {
                                        $(this).remove();
                                    });

                                    window.location.reload();
                                }, 2000);
                                break;

                            case 'error':
                                var html = `<div class="alert alert-danger">${response.message}</div>`;
                                form.find('.response').html(html);
                                break;
                        }
                    }
                })
            });
        }


        app.requestCancelModal = function () {
            $(document).on('click', '.cancel-request-btn', function (e) {
                e.preventDefault();

                var reqId = $(this).closest('[data-request-id]').data('requestId');
                var empName = $(this).closest('[data-emp-name]').data('empName');

                console.log([empName, reqId]);

                $.ajax({
                    url: base_url + 'employee/ajax_leave_request/fetch_leave_request',
                    method: 'POST',
                    dataType: 'JSON',
                    data: { 'leave_id': reqId },

                    success: function (response) {
                        switch (response.response_status) {
                            case 'success':
                                var modal = $('#cancel-leave-request-modal');
                                modal.find('[name="request-id"]').val(reqId);
                                modal.find('.emp-name').text(empName);
                                modal.modal('show');
                                break;
                        }
                    }
                });
            })
        }

        app.requestCancelForm = function () {
            $(document).on('submit', '#cancel-leave-request-form', function (e) {
                e.preventDefault();

                var form = $(this);

                $.ajax({
                    url: base_url + 'employee/ajax_leave_request/cancel_leave',
                    method: 'POST',
                    dataType: 'JSON',
                    data: form.serialize(),

                    success: function (response) {
                        switch (response.status) {
                            case 'success':
                                var html = `<div class="alert alert-success">${response.message}</div>`;
                                form.find('.response').html(html);

                                setTimeout(function () {
                                    form.find('.alert').fadeOut('slow', function () {
                                        $(this).remove();
                                    });

                                    window.location.reload();
                                }, 2000);

                                break;

                            case 'error':
                                var html = `<div class="alert alert-danger">${response.message}</div>`;
                                form.find('.response').html(html);
                                break;
                        }
                    }
                })
            });
        }

        app.requestDeleteModal = function () {

            $(document).on('click', '.delete-request-btn', function (e) {

                e.preventDefault();
                var reqId = $(this).closest('[data-request-id]').data('requestId');
                var empName = $(this).closest('[data-emp-name]').data('empName');
                $.ajax({
                    url: base_url + 'employee/ajax_leave_request/fetch_leave_request',
                    method: 'POST',
                    dataType: 'JSON',
                    data: { 'leave_id': reqId },

                    success: function (response) {
                        switch (response.response_status) {
                            case 'success':
                                var modal = $('#delete-leave-request-modal');
                                modal.find('[name="request-id"]').val(reqId);
                                modal.find('.emp-name').text(empName);
                                modal.modal('show');
                                break;
                        }
                    }
                });
            })
        }

        app.requestDeleteForm = function () {
            $(document).on('submit', '#delete-leave-request-form', function (e) {
                e.preventDefault();

                var form = $(this);

                $.ajax({
                    url: base_url + 'employee/ajax_leave_request/delete_cancelled_leave',
                    method: 'POST',
                    dataType: 'JSON',
                    data: form.serialize(),

                    success: function (response) {
                        switch (response.status) {
                            case 'success':
                                var html = `<div class="alert alert-success">${response.message}</div>`;
                                form.find('.response').html(html);

                                var modal = $('#delete-leave-request-modal');
                                var id = form.find('[name="request-id"]').val();

                                $('#leave-req-tr-' + id).fadeOut("slow", function () {
                                    $(this).remove();
                                });

                                setTimeout(function () {
                                    modal.modal('hide');
                                }, 2000);
                                break;

                            case 'error':
                                var html = `<div class="alert alert-danger">${response.message}</div>`;
                                form.find('.response').html(html);
                                break;
                        }
                    }
                })
            });
        }

        app.cancelmyRequest = function () {
            $(document).on('click', '.cancel-my-request-btn', function () {
                var reqId = $(this).closest('[data-request-id]').data('requestId');

                $.ajax({
                    url: base_url + 'employee/ajax_leave_request/delete_cancelled_leave',
                    method: 'POST',
                    dataType: 'JSON',
                    data: { 'request-id': reqId },

                    success: function (response) {
                        switch (response.status) {
                            case 'success':
                                $('#leave-req-tr-' + reqId).fadeOut('slow', function () {
                                    $(this).remove();
                                });
                                break;

                            case 'error':
                                alert('Something went wrong. Please contact IT Administrator.');
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

        app.fetchLeaveRequest = function(reqId, isDenied) {
            var modal = $('#generate-request-modal');
            var userName = "";
            var leaveType = "";
            var date = "";

            //console.log(reqId);

            $.ajax({
                url: base_url + 'employee/ajax_leave_request/fetch_leave_request',
                method: 'POST',
                dataType: 'JSON',
                data: { leave_id: reqId },

                success: function (response) {
                    if (response.response_status === 'success') {
                        userName = response.user_name;
                        leaveType = response.leave_type;
                        date = app.formatDateTime(response.date);


                        modal.find('.req-leave-type').html('<h3><b>REQUEST TO FILE ' + leaveType.toUpperCase() + ' LEAVE</b></h3>');
                        modal.find('.req-date').text(app.formatDateTime(response.date_created));
                        modal.find('.req-name').html('<b>' + userName + '</b>');
                        modal.find('.req-department').text(response.user_department);
                        modal.find('.req-leave-date').html('<div contenteditable="true">' + date + '<div>');
                        modal.find('.signature-container-employee').find('img').attr('src', base_url + 'assets/img/signatures/' + response.user_id + '.png');
                        modal.find('.signature-container-approver').find('img').attr('src', base_url + 'assets/img/signatures/' + response.approved_by_id + '.png');
                        if(response.salary_deduction == 1) modal.find('.req-leave-type').append('<span class="bg-lred d-block">Leave without pay</span>');

                        if (isDenied) {
                            modal.find('.req-reason').html('<p><b>Reason denied: </b> <br>' + response.reason_denied + '</p>');
                        } else {
                            modal.find('.req-reason').html('<p><b>Reason: </b> <br>' + response.details + '</p>');
                        }

                        modal.find('.req-conformed-by').html('<b>' + response.approved_by_name + '</b>');
                        modal.modal('show');

                        app.generatePdf(userName, leaveType, date, isDenied);
                    }
                }
            });
        }

        app.generatePdfModal = function () {
        $(document).on('click', '.generate-pdf-btn-retracted-leaves', function (e) {
            e.preventDefault();
            var modal = $('#generate-request-modal');
            var reqId = $(this).closest('[data-req-id]').data('reqId');
            var userName = "";
            var leaveType = "";
            var date = "";

            $.ajax({
                url: base_url + 'employee/ajax_leave_request/fetch_leave_request',
                method: 'POST',
                dataType: 'JSON',
                data: { leave_id: reqId },

                success: function (response) {
                    switch (response.response_status) {
                        case 'success':
                            var signatureEmployee = base_url + 'assets/img/signatures/' + response.user_id + '.png';
                            var signatureApprover = base_url + 'assets/img/signatures/' + response.approved_by_id + '.png';

                            userName = response.user_name;
                            leaveType = response.leave_type;
                            date = app.formatDateTime(response.date);

                            modal.find('.req-leave-type').html('<h3><b>REQUEST TO RETRACT ' + leaveType.toUpperCase() + ' LEAVE</b></h3>');
                            modal.find('.req-date').text(app.formatDateTime(response.date_created));
                            modal.find('.req-name').html('<b>' + userName + '</b>');
                            modal.find('.req-department').text(response.user_department);
                            modal.find('.req-leave-date').html('<div contenteditable="true">' + date + '<div>');
                            modal.find('.req-reason').html('<p><b>Retraction Reason: </b> <br>' + response.reason_retracted + '</p>');
                            modal.find('.req-conformed-by').html('<b>' + response.approved_by_name + '</b>');
                            modal.find('.signature-container-employee').html('<img src="' + signatureEmployee + '">');
                            modal.find('.signature-container-approver').html('<img src="' + signatureApprover + '">');

                            app.generatePdf(userName, leaveType, date);

                            break;
                    }
                }
            })
            modal.modal('show');
        });
        }

        // Function to generate PDF
        app.generatePdf = function (name, type, date, isDenied) {
            $(document).on('click', '.generate-pdf', function (e) {
                e.preventDefault();

                var splitName = name.split(' ');
                var splitType = type.split('');
                var split_date = date.replace(' ', '_');

                var filename = splitName[0] + (isDenied ? '_Denied_' + splitType[0].toUpperCase() + 'L_' : '_' + splitType[0].toUpperCase() + 'L_') + split_date.replace(', ', '_');

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
            });
        };

        app.generatePdfModals = function(){
            $(document).on('click', '.generate-pdf-btn-leaves', function (e) {
                e.preventDefault();
                var id = $(this).closest('[data-req-id]').data('reqId');
                app.fetchLeaveRequest(id, false);
            });

            $(document).on('click', '.generate-pdf-btn-leaves-denied', function (e) {
                e.preventDefault();
                var id = $(this).closest('[data-req-id]').data('reqId');
                app.fetchLeaveRequest(id, true);
            });
        }


        app.retractRequestModal = function () {
            $(document).on('click', '.retract-request-btn', function () {
                var id = $(this).parent().data('request-id');
                var modal = $('#retract-leave-request-modal');

                modal.find('[name="request-id"]').val(id);
                modal.modal('show');
            });
        }

        app.retractRequest = function () {
            $(document).on('submit', '#retract-leave-request-form', function (e) {
                e.preventDefault();
                
                var form = $(this);

                $.ajax({
                    url: base_url + 'employee/ajax_leave_request/retract_leave',
                    method: 'POST',
                    dataType: 'JSON',
                    data: form.serialize(),

                    success: function (response) {
                        switch (response.status) {
                            case 'success':
                                var html = `<div class="alert alert-success">${response.message}</div>`;
                                form.find('.response').html(html);

                                setTimeout(function () {
                                    form.find('.alert').fadeOut('slow', function () {
                                        $(this).remove();
                                    });

                                    window.location.reload();
                                }, 2000);
                                break;

                            case 'error':
                                var html = `<div class="alert alert-danger">${response.message}</div>`;
                                form.find('.response').html(html);
                                break;
                        }
                    }
                })
            });
        }

        app.requestRetractionApproveModal = function () {
            $(document).on('click', '.approve-leave-retraction-request-btn', function (e) {
                e.preventDefault();

                var reqId = $(this).parent().data('reqId');
                var empName = $(this).parent().data('empName');

                $.ajax({
                    url: base_url + 'employee/ajax_leave_request/fetch_leave_request',
                    method: 'POST',
                    dataType: 'JSON',
                    data: { 'leave_id': reqId },

                    success: function (response) {
                        switch (response.response_status) {
                            case 'success':
                                var modal = $('#leave-retraction-request-modal');
                                modal.find('[name="request-id"]').val(reqId);
                                modal.find('.emp-name').text(empName);
                                modal.find('.reason-retracted').text(response.reason_retracted);
                                modal.modal('show');
                                break;
                        }
                    }
                });
            })
        }

        app.requestRetractionApproveForm = function () {
            $(document).on('submit', '#leave-retraction-request-form', function (e) {
                e.preventDefault();

                var form = $(this);

                $.ajax({
                    url: base_url + 'employee/ajax_leave_request/approve_leave_retraction',
                    method: 'POST',
                    dataType: 'JSON',
                    data: form.serialize(),

                    success: function (response) {
                        switch (response.status) {
                            case 'success':
                                var html = `<div class="alert alert-success">${response.message}</div>`;
                                form.find('.response').html(html);

                                setTimeout(function () {
                                    form.find('.alert').fadeOut('slow', function () {
                                        $(this).remove();
                                    });

                                    window.location.reload();
                                }, 2000);
                                break;

                            case 'error':
                                var html = `<div class="alert alert-danger">${response.message}</div>`;
                                form.find('.response').html(html);
                                break;
                        }
                    }
                })
            });
        }

        app.requestRetractionDeclinedModal = function(){
            $(document).on('click', '.decline-retract-leave-request-btn', function(){
                var modal = $('#decline-retract-leave-request-modal');
                var reqId = $(this).closest('[data-req-id]').data('reqId');
                modal.find('[name="request-id"]').val(reqId);
                modal.modal('show');
            })
        }

        app.requestRetractionDeclinedForm = function(){
            $(document).on('submit', '#decline-retract-leave-request-form', function(e){
                e.preventDefault();
                var form = $(this);

                $.ajax({
                    url: base_url + 'employee/ajax_leave_request/decline_leave_retraction',
                    method: 'POST',
                    dataType: 'JSON',
                    data: form.serialize(),

                    success:function(response){
                        switch(response.status){
                            case 'success':
                                var html = `<div class="alert alert-success">${response.message}</div>`;
                                form.find('.response').html(html);

                                setTimeout(function () {
                                    window.location.reload();
                                }, 2000);
                            break;
                        }
                    }
                })
            })
        }

        app.viewRetractionDeniedMsg = function(){
            $(document).on('click', '.view-retraction-denied-msg', function(){
                var modal = $   ('#view-denied-leave-request-modal');
                var reqId = $(this).closest('[data-req-id]').data('reqId');

                $.ajax({
                    url: base_url + 'employee/ajax_leave_request/fetch_leave_request',
                     method: 'POST',
                    dataType: 'JSON',
                    data: {'leave_id' : reqId}, 

                    success:function(response){
                        switch(response.response_status){
                            case 'success':
                                modal.find('.modal-body').html(`<b>Reason for denied retraction:</b><br><p>${response.retract_reason_denied}</p>`);
                                modal.modal('show');
                            break;
                        }
                    }
                })
            });
        }

        app.init();
    })(Script);
});

