$(function () {
    var Script = {};
    (function (app) {

        let dtrUpdateRequestTable;
        let leaveListTable;
        let holidayListTable;
        let employeeLeaveListTable;

        app.init = function () {
            app.bindings();
            app.dataTables();
            app.initializeAdtrListTable();
            app.initializeEmployeeDtrTable();
            app.initializeEmployeeListTable()
            app.initializeDtrListTable();
            app.initializeDtrUpdateRequest();
            app.initializeCustomHolidays();
            app.initializeLeaveListTable();
            app.initializeHolidayListTable();
            app.initializeEmployeeLeaveListTable();

            app.addEmployeeModal();
            app.employee_cru_modal();
            app.archive_user();
            app.archiveUserForm();
            app.salary_grade_crud_modal();
            app.ot_request();
            app.dtr();
            app.download_dtr();
            app.holiday();

            app.downloadEmployeeLeave();

            app.addLeave();
            app.editLeave();
            app.cancelLeave();
            app.leave();
            app.dtr_update_request();

            app.saveProfileOnly();
            app.saveScheduleOnly();
            app.saveTemporaryScheduleOnly();
            app.editTemporarySchedule();
            app.cancelTemporarySchedule();
            app.viewDeclinedRequestMessage();

            app.generatePdf();
            app.onCloseModal();

            app.onChangeDateEmpRange();
            app.clearFilter();
            app.viewEodReportModal();
            app.isPaid();

            app.editEmployeeProfile();
            app.saveEmployeeProfile();
            app.tempSchedMonthFilter();

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

        app.formatDate = function (inputDate) {
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

        app.convertTimeTo12HourFormat = function (time) {
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

        app.bindings = function () {
            $(document).on("click", "[temp='with-temp'].disabled", function () {
                var span = $(this).find(".ongoing-temp-message");
                span.slideDown();
                setTimeout(function () {
                    span.slideUp();
                }, 3000);
            });

            $(document).on("submit", "#delete-temp-form", function (e) {
                e.preventDefault();
                var button = $("#temp-button");
                $.ajax({
                    url: base_url + "admin/Ajax_users/delete_temp_files",
                    dataType: "JSON",
                    success: function (response) {
                        $("#delete-temp-modal").find(".modal-body").prepend('<div class="alert alert-success text-center" role="alert">' + response.message + '</div>');
                        setTimeout(function () {
                            $("#delete-temp-modal").find(".alert").remove();
                            $("#delete-temp-modal").modal("hide");
                            button.removeAttr("data-toggle data-target").attr("title", "There are no temporary files");
                            button.addClass("disabled");
                            button.append('<span class="ongoing-temp-message">There are no temporary files</span>');
                        }, 1500);
                    }
                });
            });
        }

        app.dataTables = function () {
            table = $("\
          #salary-grade-list, \
          #aotr-list, \
          #odtr-table-list-view,\
          #odtr-group-list-view\
        ").DataTable({
                bLengthChange: false,
                searching: true,
                info: false,
                iDisplayLength: 50,
                order: []
            });

            $("#salary-grade-list-search, \
          #aotr-list-search, \
          #odtr-list-search\
        ").keyup(function () {
                table.search($(this).val()).draw();
            });

        }

        app.initializeAdtrListTable = function () {
            var adtrListTable = $('#adtr-list').DataTable({
                bLengthChange: false,
                searching: true,
                info: false,
                iDisplayLength: -1,
                paging: false,
                order: [1, 'desc'],
                columnDefs: [
                    {
                        targets: [0, 2, 3, 4, 5],
                        orderable: false
                    }
                ]
            });

            $('#adtr-list-search').keyup(function () {
                adtrListTable.search($(this).val()).draw();
            });
        }

        app.initializeEmployeeDtrTable = function () {
            var emp_dtr_table = $('#employee-dtr-list').DataTable({
                bLengthChange: false,
                searching: true,
                info: false,
                iDisplayLength: -1,
                paging: false,
                order: [0, 'desc'],
                columnDefs: [
                    {
                        targets: [1, 2, 3, 4, 5],
                        orderable: false
                    }
                ],

            });

            $('#dtr-list-search').keyup(function () {
                emp_dtr_table.search($(this).val()).draw();
            })
        }

        app.initializeEmployeeListTable = function () {
            var employeeListTable = $('#employee-list').DataTable({
                bLengthChange: false,
                searching: true,
                info: false,
                iDisplayLength: -1,
                paging: false,
                order: [0, 'asc'],
                columnDefs: [
                    {
                        targets: [1, 2],
                        orderable: false
                    }
                ]
            });

            $('#employee-list-search').keyup(function () {
                employeeListTable.search($(this).val()).draw();
            })
        }

        app.initializeDtrListTable = function () {
            var dtrListTable = $('#dtr-list').DataTable({
                bLengthChange: false,
                searching: true,
                info: false,
                iDisplayLength: -1,
                paging: false,
                order: [1, 'desc'],
                columnDefs: [
                    {
                        targets: [0, 2, 3, 4, 5],
                        orderable: false
                    }
                ]
            });

            $('#dtr-list-search, #dtr-list-search-0').keyup(function () {
                dtrListTable.search($(this).val()).draw();
            })
        }

        app.initializeDtrUpdateRequest = function () {
            dtrUpdateRequestTable = $('#request-dtr-update-list').DataTable({
                bLengthChange: false,
                searching: true,
                info: false,
                iDisplayLength: -1,
                paging: false,
                order: [],
                columnDefs: [
                    {
                        targets: [0, 1, 2, 3, 4],
                        orderable: false
                    }
                ]
            });

            $('#request-dtr-update-list-search').keyup(function () {
                dtrUpdateRequestTable.search($(this).val()).draw();
            })
        }

        app.initializeCustomHolidays = function () {
            var customHolidayListTable = $('#custom-holiday-list').DataTable({
                bLengthChange: false,
                searching: true,
                info: false,
                iDisplayLength: -1,
                paging: false,
                order: [4, 'asc'],
                columnDefs: [
                    {
                        targets: [0, 1, 2, 3],
                        orderable: false
                    },
                    {
                        targets: [4],
                        visible: false
                    }
                ]
            });

            $('#custom-holiday-list-search').keyup(function () {
                customHolidayListTable.search($(this).val()).draw();
            })
        }

        app.initializeLeaveListTable = function () {
            leaveListTable = $('#leave-list').DataTable({
                bLengthChange: false,
                searching: true,
                info: false,
                iDisplayLength: 50,
                paging: true,
                order: [],
                columnDefs: [
                    {
                        targets: [0, 2, 3, 4, 5],
                        orderable: false
                    },
                    {
                        targets: [6],
                        visible: false
                    }
                ]
            });

            $('#leave-list-search, #leave-list-search-0').keyup(function () {
                leaveListTable.search($(this).val()).draw();
            })
        }

        app.initializeHolidayListTable = function () {
            holidayListTable = $('#holiday-list').DataTable({
                bLengthChange: false,
                searching: true,
                info: false,
                iDisplayLength: -1,
                paging: false,
                order: [3, 'asc'],
                columnDefs: [
                    {
                        targets: [0, 1, 2],
                        orderable: false
                    },
                    {
                        targets: [3],
                        visible: false
                    }
                ]
            });

            $('#holiday-list-search').keyup(function () {
                holidayListTable.search($(this).val()).draw();
            })
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
                        targets: [1, 2, 3, 4],
                        orderable: false
                    },
                ]
            });

            $('#employee-leave-list-search, #employee-leave-list-search-mobile').keyup(function () {
                employeeLeaveListTable.search($(this).val()).draw();
            })
        }


        app.addEmployeeModal = function () {
            $(document).on('click', '.sidebar-add-employee-btn', function (e) {
                e.preventDefault();
                var modal = $('#employee-cru-modal');

                modal.find('[name="schedule-monday-in"], [name="schedule-tuesday-in"], [name="schedule-wednesday-in"], [name="schedule-thursday-in"], [name="schedule-friday-in"]').val('09:00');
                modal.find('[name="schedule-monday-out"], [name="schedule-tuesday-out"], [name="schedule-wednesday-out"], [name="schedule-thursday-out"], [name="schedule-friday-out"]').val('18:00');
                modal.find('#monday-workbase-office, #tuesday-workbase-office, #wednesday-workbase-office, #thursday-workbase-office, #friday-workbase-office').prop('checked', true);
                modal.find('.temporary-schedule-main-con').addClass('d-none');
                modal.modal('show');

            });
        }

        app.employee_cru_modal = function () {

            var modal = $("#employee-cru-modal");
            $(document).on("click", "#add-employee", function () {
                modal.find(".modal-title").empty().append("<i class='fas fa-plus'></i> Employee");
                modal.find("form").attr("id", "employee-add-form");
                modal.find("form").removeAttr("user-id");
                modal.find(".role-row, .password-row").removeClass("d-none");
                modal.find(".modal-footer button").text("Submit");
            });

            $(document).on("change", "#employee-add-form [name='role']", function () {
                var value = $(this).find(":selected").val();
                var form = $("#employee-add-form")
                if (value !== "" && value !== "1") {//1 = admin
                    form.find(".schedule-row").removeClass("d-none");
                } else {
                    form.find(".schedule-row, .fixed-schedule-row, .salary-grade-row").addClass("d-none");
                }
            });

            $(document).on("change", "[name='schedule']", function () {
                var value = $(this).find(":selected").val();
                if (value == "fixed") {
                    modal.find(".fixed-schedule-row").removeClass("d-none");
                } else {
                    modal.find(".fixed-schedule-row").addClass("d-none");
                }
            });

            $(document).on("submit", "#employee-add-form", function (e) {
                e.preventDefault();
                var form = $(this);

                let mondayWorkbase = [];
                let tuesdayWorkbase = [];
                let wednesdayWorkbase = [];
                let thursdayWorkbase = [];
                let fridayWorkbase = [];

                var mondayWorkbaseChecked = form.find('[name="monday-workbase"]:checked');
                var tuesdayWorkbaseChecked = form.find('[name="tuesday-workbase"]:checked');
                var wednesdayWorkbaseChecked = form.find('[name="wednesday-workbase"]:checked');
                var thursdayWorkbaseChecked = form.find('[name="thursday-workbase"]:checked');
                var fridayWorkbaseChecked = form.find('[name="friday-workbase"]:checked');

                mondayWorkbaseChecked.each(function () {
                    mondayWorkbase.push($(this).val());
                });

                tuesdayWorkbaseChecked.each(function () {
                    tuesdayWorkbase.push($(this).val());
                });

                wednesdayWorkbaseChecked.each(function () {
                    wednesdayWorkbase.push($(this).val());
                });

                thursdayWorkbaseChecked.each(function () {
                    thursdayWorkbase.push($(this).val());
                });

                fridayWorkbaseChecked.each(function () {
                    fridayWorkbase.push($(this).val());
                });

                $.ajax({
                    url: base_url + "admin/Ajax_users/add_user",
                    type: "POST",
                    data: form.serialize() + "&monday-workbase=" + mondayWorkbase.join('/') + "&tuesday-workbase=" + tuesdayWorkbase.join('/') + "&wednesday-workbase=" + wednesdayWorkbase + "&thursday-workbase=" + thursdayWorkbase + "&friday-workbase=" + fridayWorkbase,
                    dataType: "JSON",
                    beforeSend: function () {
                        $(".error").removeClass("error");
                        $(".error-message, .alert").remove();
                        form.find('input, select').attr('readonly', true);
                        form.find('button').attr('disabled', true);
                    },
                    success: function (response) {
                        if (response.status == "form-incomplete") {

                            var responseCon = $('.user-profile-response');
                            $.each(response.errors, function (e, val) {
                                form.find('[name="' + e + '"]').addClass('error');
                                if (e == 'date-of-birth' || e == 'password') {
                                    form.find('[name="' + e + '"]').parent().append('<i class="error-message">' + val + '</i>');
                                }
                            });

                            var html = `<div class="alert alert-danger">Please fill in required fields.</div>`;
                            responseCon.html(html);
                        } else if (response.status == "error") {
                            form.prepend('<div class="alert alert-danger text-center" role="alert">' + response.message + '</div>');
                        } else {
                            form.prepend('<div class="alert alert-success text-center" role="alert">' + response.message + '</div>');
                            setTimeout(function () {
                                window.location.reload();
                            }, 2000);
                        }
                    },
                    complete: function (response) {
                        form.find('input, select').removeAttr('readonly');
                        form.find('button').removeAttr('disabled');
                    }
                });
            });
        }

        app.archive_user = function () {
            var form = $("#archive-user-form");

            $(document).on("click", ".arc-act-user-btn", function () {
                var user_id = $(this).attr("user-id");
                var action = $(this).data('action');
                var empName = $(this).data('empName');

                form.find('[name="user-id"]').val(user_id);
                form.find('[name="action"]').val(action);

                if(action == 'archive'){
                    $("#archive-user-modal .question-row h4").html('Are you sure you want to archive<br><b class="t-maroon">' + empName + '?</b>');
                }else{
                    $("#archive-user-modal .question-row h4").html('Are you sure you want to reactivate<br><b class="t-maroon">' + empName + '?</b>');
                }
            }); 
        }

        app.archiveUserForm = function(){
            $("#archive-user-form").on("submit", function (e) {
                e.preventDefault();

                var form = $(this);
                var userId = form.find('[name="user-id"]').val();
                var action = form.find('[name="action"]').val();

                $.ajax({
                    url: base_url + "admin/Ajax_users/archive_user",
                    type: "POST",
                    data: { 'user-id': userId, 'action': action },
                    dataType: "JSON",

                    beforeSend: function () {
                        form.parent().find(".alert").remove();
                        form.find('button').attr('disabled', true);
                    },

                    success: function (response) {
                        if (response.status == "error") {
                            $("#archive-user-modal .modal-body").prepend('<div class="alert alert-danger text-center" role="alert">' + response.message + '</div>');
                        } else {
                            $("#archive-user-modal .modal-body").prepend('<div class="alert alert-success text-center" role="alert">' + response.message + '</div>');
                            $("#employee-list").find("#emp-" + userId).fadeOut('slow', function(){
                                $(this).remove();
                            });
                            setTimeout(function () {
                                form.parent().find(".alert").remove();
                                $("#archive-user-modal").modal("hide");
                            }, 1500);
                        }
                    },
                    complete: function (response) {
                        form.find('button').removeAttr('disabled');
                    }
                });
            });
        }

        app.ot_request = function () {
            var table = $("#aotr-list");
            var modal = $("#ot-status-modal");
            var form = $("#ot-status-form");

            modal.on('hidden.bs.modal', function () {
                form.find(".statement-row").addClass("d-none");
                form.find("select").prop('selectedIndex', 0);
                form.find("textarea").val("");
            });

            $(document).on("click", ".update-ot-status", function () {
                var rot_id = $(this).attr("rot-id");
                form.attr("rot_id", rot_id);
                $.ajax({
                    url: base_url + "admin/Ajax_ot_request/fetch_ot_request",
                    type: "POST",
                    data: { rot_id: rot_id },
                    dataType: "JSON",
                    success: function (response) {
                        if (response.response_status == "success") {
                            form.find("[name='status'] option[value='" + response.status + "']").prop('selected', true);
                            if (response.status == "denied") {
                                form.find(".statement-row").removeClass("d-none");
                                form.find("[name='reason-denied']").val(response.reason_denied);
                            }
                        }
                    }
                });
            });

            form.find("[name='status']").on("change", function () {
                var val = $(this).val();
                if (val == "denied") {
                    form.find(".statement-row").removeClass("d-none");
                } else {
                    form.find(".statement-row").addClass("d-none").find("[name='reason-denied']").val("");
                }
            });

            form.on("submit", function (e) {
                e.preventDefault();
                var rot_id = form.attr("rot_id");
                var status = form.find("[name='status']").val();
                if (form.find("[name='status']").val() == "denied" && form.find("[name='reason-denied']").val() == "") {
                    form.find("[name='reason-denied']").addClass("error").parent().append('<i class="error-message">required</i>')
                } else {
                    $.ajax({
                        url: base_url + "admin/Ajax_ot_request/update_ot_request",
                        type: 'POST',
                        data: form.serialize() + "&rot_id=" + rot_id,
                        dataType: "JSON",
                        beforeSend: function () {
                            $(".error").removeClass("error");
                            $(".error-message, .alert").remove();
                            form.find('select, textarea').attr('readonly', true);
                            form.find('button').attr('disabled', true);
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                form.prepend('<div class="alert alert-success text-center" role="alert">' + response.message + '</div>');
                                setTimeout(function () {
                                    form.find(".error").removeClass("error");
                                    form.find(".error-message, .alert").remove();
                                    var tr_status, ot_status;
                                    if (status == "pending") {
                                        tr_status = "<span class='t-green t-12px'>Pending</span>";
                                        ot_status = "pending";
                                    } else if (status == "denied") {
                                        tr_status = "<span class='t-red t-12px'>Denied</span>"
                                        ot_status = "denied";
                                    } else {
                                        tr_status = "<span class='t-blue t-12px'>Approved</span>"
                                        ot_status = "approved";
                                    }
                                    table.find("[tr-id='" + rot_id + "'] .ot-status, [tr-id='" + rot_id + "'] .status").find("span").remove();
                                    table.find("[tr-id='" + rot_id + "'] .ot-status, [tr-id='" + rot_id + "'] .status").append(tr_status);
                                    $("#dtr-list").find(".ot-status[rot-id='" + rot_id + "']").text("Status: " + ot_status);
                                    modal.modal("hide");

                                    if (response.pending == 0) {
                                        $("#sidebar .ot-drop-trigger .notif-dot").remove();
                                    } else {
                                        $("#sidebar .ot-drop-trigger .notif-dot").remove();
                                        $("#sidebar .ot-drop-trigger").append('<i class="fas fa-circle notif-dot t-12px"></i>');
                                    }
                                }, 1500);
                            } else {
                                form.prepend('<div class="alert alert-danger text-center" role="alert">' + response.message + '</div>');
                            }
                        },
                        complete: function () {
                            form.find('select, textarea').removeAttr('readonly');
                            form.find('button').removeAttr('disabled');
                        }
                    });
                }
            });
        }

        app.dtr = function () {
            $(document).on("click", ".dtr-details", function () {
                var modal = $("#view-dtr-details-modal");
                var dtr_id = $(this).attr("dtr-id");
                $.ajax({
                    url: base_url + "admin/Ajax_download_dtr_list/dtr_details",
                    type: "POST",
                    data: { dtr_id: dtr_id },
                    success: function (response) {
                        modal.find(".modal-body").html(response);
                    }
                });
            });

            $(document).on("click", ".otr-details", function () {
                var modal = $("#view-otr-details-modal");
                var otr_id = $(this).attr("otr-id");
                $.ajax({
                    url: base_url + "admin/Ajax_ot_request/otr_details",
                    type: "POST",
                    data: { otr_id: otr_id },
                    success: function (response) {
                        modal.find(".modal-body").html(response);
                    }
                });
            });
        }

        app.download_dtr = function () {
            var modal = $("#download-dtr-list-filter-modal");
            var form = $("#download-dtr-list-filter-form");

            modal.on('hidden.bs.modal', function () {
                form.find("input").val("");
            });

            $(document).on("submit", "#download-dtr-list-filter-form", function (e) {
                e.preventDefault();
                $.ajax({
                    url: base_url + "admin/Ajax_download_dtr_list/download_dtr_validation",
                    type: "POST",
                    data: form.serialize(),
                    dataType: "JSON",
                    beforeSend: function () {
                        modal.find(".error").removeClass("error");
                        modal.find(".alert, .error-message").remove();
                        modal.find("input").attr("readonly", true);
                        modal.find("button").attr("disabled", true);
                    },
                    success: function (response) {
                        if (response.status == "form-incomplete") {
                            $.each(response.errors, function (e, val) {
                                form.find('[name="' + e + '"]').addClass('error');
                                form.find('[name="' + e + '"]').parent().append('<i class="error-message">' + val + '</i>');
                            });
                        } else {
                            form.prepend('<div class="alert alert-success text-center" role="alert">' + response.message + '</div>');
                            //window.location.href=base_url+"admin/Ajax_dtr/download_dtr";

                            window.open(base_url + "admin/Ajax_download_dtr_list/download_dtr");
                            setTimeout(function () {
                                modal.find(".error").removeClass("error");
                                modal.find(".alert, .error-message").remove();
                                form.find("input").val("");
                                modal.modal("hide");
                            }, 1500);
                        }
                    },
                    complete: function (response) {
                        modal.find('input').removeAttr('readonly');
                        modal.find('button').removeAttr('disabled');
                    }
                });
            });
        }

        app.holiday = function () {
            var modal = $("#holiday-cru-modal");
            var form = $("#holiday-cru-form");

            modal.on('hidden.bs.modal', function () {
                modal.find(".modal-title").html("<i class='fas fa-plus'></i> Custom Holiday");
                form.removeAttr("h-id");
                form.find("select").prop('selectedIndex', 0);
                form.find("input").val("");
            });

            $(document).on("click", ".update-holiday", function () {
                var h_id = $(this).attr("h-id");
                modal.find(".modal-title").html("<i class='fas fa-edit'></i> Custom Holiday");
                form.attr("h-id", h_id);
                $.ajax({
                    url: base_url + "admin/Ajax_holiday/fetch_holiday",
                    type: "POST",
                    data: { h_id: h_id },
                    dataType: "JSON",
                    success: function (response) {
                        form.find("[name='name']").val(response.name);
                        form.find("[name='date']").val(response.date);
                        form.find("[name='type'] option[value='" + response.type + "']").prop('selected', true);
                    }
                });
            });

            form.on("submit", function (e) {
                e.preventDefault();
                if (form.attr("h-id")) {
                    var h_id = form.attr("h-id");
                    var table = $("#custom-holiday-list");
                    $.ajax({
                        url: base_url + "admin/Ajax_holiday/update_custom_holiday",
                        type: "POST",
                        data: form.serialize() + "&h_id=" + h_id,
                        dataType: "JSON",
                        beforeSend: function () {
                            $(".error").removeClass("error");
                            $(".error-message, .alert").remove();
                            form.find('input, select').attr('readonly', true);
                            form.find('button').attr('disabled', true);
                        },
                        success: function (response) {
                            if (response.status == "form-incomplete") {
                                $.each(response.errors, function (e, val) {
                                    form.find('[name="' + e + '"]').addClass('error');
                                    form.find('[name="' + e + '"]').parent().append('<i class="error-message">' + val + '</i>');
                                });
                            } else if (response.status == "error") {
                                form.prepend('<div class="alert alert-danger text-center" role="alert">' + response.message + '</div>');
                            } else {
                                form.prepend('<div class="alert alert-success text-center" role="alert">' + response.message + '</div>');
                                setTimeout(function () {
                                    form.find("select").prop('selectedIndex', 0);
                                    form.find("input").val("");
                                    form.find(".error").removeClass("error");
                                    form.find(".error-message, .alert").remove();
                                    table.find("[tr-id='" + h_id + "'] .name").text(response.name);
                                    table.find("[tr-id='" + h_id + "'] .date").text(response.date);
                                    table.find("[tr-id='" + h_id + "'] .type").text(response.type);
                                    modal.modal("hide");

                                }, 1500);
                            }
                        },
                        complete: function (response) {
                            form.find('input, select').removeAttr('readonly');
                            form.find('button').removeAttr('disabled');
                        }
                    });
                } else {
                    $.ajax({
                        url: base_url + "admin/Ajax_holiday/add_custom_holiday",
                        type: "POST",
                        data: form.serialize(),
                        dataType: "JSON",
                        beforeSend: function () {
                            $(".error").removeClass("error");
                            $(".error-message, .alert").remove();
                            form.find('input, select').attr('readonly', true);
                            form.find('button').attr('disabled', true);
                        },
                        success: function (response) {
                            if (response.status == "form-incomplete") {
                                $.each(response.errors, function (e, val) {
                                    form.find('[name="' + e + '"]').addClass('error');
                                    form.find('[name="' + e + '"]').parent().append('<i class="error-message">' + val + '</i>');
                                });
                            } else if (response.status == "error") {
                                form.prepend('<div class="alert alert-danger text-center" role="alert">' + response.message + '</div>');
                            } else {
                                form.prepend('<div class="alert alert-success text-center" role="alert">' + response.message + '</div>');
                                setTimeout(function () {
                                    form.find("select").prop('selectedIndex', 0);
                                    form.find("input").val("");
                                    form.find(".error").removeClass("error");
                                    form.find(".error-message, .alert").remove();
                                    modal.modal("hide");
                                    /*add yung append newly added sa table*/
                                }, 1500);
                            }
                        },
                        complete: function (response) {
                            form.find('input, select').removeAttr('readonly');
                            form.find('button').removeAttr('disabled');
                        }
                    });
                }
            });

            $(document).on("click", ".delete-holiday", function () {
                var h_id = $(this).attr("h-id");
                var modal = $("#delete-holiday-modal");
                modal.find("form").attr("h-id", h_id);
                $.ajax({
                    url: base_url + "admin/Ajax_holiday/fetch_holiday",
                    type: "POST",
                    data: { h_id: h_id },
                    dataType: "JSON",
                    success: function (response) {
                        modal.find(".question-row b").text(response.name);
                    }
                });
            });

            $(document).on("submit", "#delete-holiday-form", function (e) {
                e.preventDefault();
                var h_id = $(this).attr("h-id");
                $.ajax({
                    url: base_url + "admin/Ajax_holiday/delete_custom_holiday",
                    type: "POST",
                    data: { h_id: h_id },
                    dataType: "JSON",
                    beforeSend: function () {
                        form.find(".alert").remove();
                        form.find('button').attr('disabled', true);
                    },
                    success: function (response) {
                        if (response.status == "error") {
                            $("#delete-holiday-modal .modal-body").prepend('<div class="alert alert-danger text-center" role="alert">' + response.message + '</div>');
                        } else {
                            $("#delete-holiday-modal .modal-body").prepend('<div class="alert alert-success text-center" role="alert">' + response.message + '</div>');
                            setTimeout(function () {
                                form.find(".alert").remove();
                                $("#delete-holiday-modal").modal("hide");
                                $("#custom-holiday-list").find("tbody tr[tr-id='" + h_id + "']").remove();
                            }, 1500);
                        }
                    },
                    complete: function (response) {
                        form.find('button').removeAttr('disabled');
                    }
                });
            });

            $(document).on("submit", "#update-dynamic-holidays-form", function (e) {
                e.preventDefault();
                var button = $("#update-dynamic-holidays");
                $.ajax({
                    url: base_url + "admin/Ajax_holiday/update_dynamic_holiday",
                    dataType: "JSON",
                    beforeSend: function () {
                        form.find(".alert").remove();
                        form.find('button').attr('disabled', true);
                    },
                    success: function (response) {
                        if (response.status == "error") {
                            $("#update-dynamic-holidays-modal .modal-body").prepend('<div class="alert alert-danger text-center" role="alert">' + response.message + '</div>');
                        } else {
                            $("#update-dynamic-holidays-modal .modal-body").prepend('<div class="alert alert-success text-center" role="alert">' + response.message + '</div>');
                            setTimeout(function () {
                                form.find(".alert").remove();
                                $("#update-dynamic-holidays-modal").modal("hide");
                                button.removeAttr("data-toggle data-target").attr("title", "All holidays are up to date");
                                button.addClass("disabled");
                                button.append('<span class="ongoing-temp-message">All holidays are up to date</span>');
                            }, 1500);
                        }
                    },
                    complete: function (response) {
                        form.find('button').removeAttr('disabled');
                    }
                });
            });
        }

        app.downloadEmployeeLeave = function () {
            $('#employee-leaves-download-layout-table').DataTable();

            $(document).on('click', '.download-employee-leave-btn', function (e) {
                e.preventDefault();

                setTimeout(function () {

                    // var table = $('#employee-leave-list');
                    // table.find('thead tr th:last-child, tbody tr td:last-child, .mobile-show').remove();

                    $('#employee-leave-list').tableExport({
                        type: 'pdf',
                        fileName: 'Test',
                        jspdf: {
                            orientation: 'p',
                            format: 'letter',
                            margins: { left: 50, right: 50, top: 75, bottom: 75 },
                        },
                    });
                    window.location.reload();
                });

                // var url_string = window.location.href;
                // var url = new URL(url_string);
                // var id = url.searchParams.get('id');
                // var year = url.searchParams.get('year');
                // var currentDate = new Date();
                // var currentYear = currentDate.getFullYear();

                // year = year ? parseInt(year, 10) : currentYear;

                // $.ajax({
                //   url: base_url + 'admin/ajax_dtr/download_employee_individual_leaves',
                //   type: 'POST',
                //   dataType: 'JSON',
                //   data: {'id' : id, 'year' : year},

                //   success: function(response){
                //     switch(response.status){
                //       case 'success':
                //         //alert('Download Successful!');
                //       break;
                //     }
                //   }
                // });

            });
        }

        app.addLeave = function () {
            $(document).on('click', '.add-leave-btn', function (e) {
                e.preventDefault();

                var userId = $(this).data('userId');
                var userName = $(this).data('userName');
                let modal = $('#add-leave-modal');

                if (userId != undefined && userName != undefined) {
                    let html = `<input type="hidden" value="${userId}" name="employee-id"><input type="text" value="${userName}" disabled class="opacity-25 form-control">`;
                    modal.find('.leave-employee-name').html(html);
                }

                modal.modal('show');
            });

            $(document).on('submit', '#add-leave-form', function (e) {
                e.preventDefault();

                var $form = $(this);
                var $response = $form.find('.response');

                $.ajax({
                    url: base_url + 'admin/ajax_leave_request/add_leave',
                    method: 'POST',
                    dataType: 'JSON',
                    data: $form.serialize(),

                    beforeSend: function () {
                        $form.find('.error').removeClass('error');
                        $form.find('.error-message').remove();
                    },

                    success: function (response) {
                        switch (response.status) {
                            case 'form-incomplete':
                                $.each(response.errors, function (e, val) {
                                    $form.find('[name="' + e + '"]').addClass('error');
                                    $form.find('[name="' + e + '"]').parent().append('<i class="error-message">' + val + '</i>');
                                });
                                break;

                            case 'success':
                                var birthday = false;
                                var html = [
                                    '<div class="alert alert-success text-center" role="alert">',
                                    response.message,
                                    '<div>'
                                ].join('').trim();
                                $response.html(html);

                                leaveListTable.rows.add(response.table_data).draw().nodes();
                                employeeLeaveListTable.rows.add(response.table_data_0).draw().nodes();

                                $('[name="table-row-leave-id"]').each(function () {
                                    let id = $(this).val();
                                    $(this).parent().parent().attr('tr-id', id);
                                });

                                for (let i = 0; i < response.leave_type.length; i++) {
                                    if (response.leave_type[i] == 'birthday') {
                                        birthday = true;
                                    }
                                }

                                console.log(birthday);

                                if (birthday === true) {
                                    accumulated = (parseInt($('[name="accumulated-leaves"]').val(), 10) + parseInt(response.ids.length, 10)) - 1;
                                } else {
                                    accumulated = parseInt($('[name="accumulated-leaves"]').val(), 10) + parseInt(response.ids.length, 10);
                                }

                                remaining = 24 - accumulated;

                                $('[name="accumulated-leaves"]').val(accumulated);
                                $('[name="remaining-leaves"]').val(remaining);
                                $('.accumulated-leaves').text(accumulated);
                                $('.remaining-leaves').text(remaining);

                                setTimeout(function () {
                                    $response.find('.alert').remove();
                                    $form[0].reset();
                                    $('#add-leave-modal').modal('hide');
                                }, 3000);
                                break;

                            case 'error':
                                var html = [
                                    '<div class="alert alert-danger text-center" role="alert">',
                                    response.message,
                                    '<div>'
                                ].join('').trim();
                                $response.html(html);
                                break;
                        }
                    }

                }).always(function () {

                });
            });
        }

        app.editLeave = function () {
            let modal = $('#add-leave-modal');

            $(document).on('click', '.edit-leaves-btn', function (e) {
                e.preventDefault();

                let leaveId = $(this).data('leaveId');
                let empName = $(this).data('userName');

                $.ajax({
                    url: base_url + 'admin/ajax_leave_request/fetch_leave_info',
                    method: 'POST',
                    dataType: 'JSON',
                    data: { 'id': leaveId },

                    success: function (response) {
                        switch (response.status) {
                            case 'success':
                                modal.find('h5').html('<i class="fa-solid fa-pen-to-square"></i> Edit Leave');
                                modal.find('form').attr('id', 'edit-leave-form');
                                modal.find('form').prepend('<input type="hidden" name="leave-id" value="' + response.data.id + '">');
                                modal.find('#edit-leave-form [name="leave-type"] option.lt-' + response.data.leave_type).prop('selected', true);
                                modal.find('#edit-leave-form [name="employee-id"] option.ei-' + response.data.user_id).prop('selected', true);
                                modal.find('#edit-leave-form .leave-employee-name').html('<input type="text" value="' + empName + '" disabled class="opacity-25 form-control">');
                                modal.find('#edit-leave-form [name="leave-from"]').val(response.data.date.substring(0, 10));
                                modal.find('#edit-leave-form [name="leave-to"]').prop('disabled', true).addClass('opacity-25');
                                modal.find('#edit-leave-form [name="reason"]').val(response.data.details);
                                modal.find('#edit-leave-form [name="remarks"]').val(response.data.remarks);

                                if (response.data.leave_count == 1) {
                                    modal.find('#edit-leave-form #whole-day-radio').prop('checked', true);
                                } else {
                                    modal.find('#edit-leave-form #half-day-radio').prop('checked', true);
                                }

                                modal.modal('show');

                                break;
                        }
                    }
                });
            });

            $(document).on('submit', '#edit-leave-form', function (e) {
                e.preventDefault();
                let $form = $(this);
                let table = $('#leave-list, #employee-leave-list');

                $.ajax({
                    url: base_url + 'admin/ajax_leave_request/edit_leave',
                    method: 'POST',
                    dataType: 'JSON',
                    data: $form.serialize(),

                    success: function (response) {
                        switch (response.status) {
                            case 'success':
                                let html = '<div class="alert alert-success text-center" role="alert"> ' + response.message + ' </div>';
                                let remarks = '';
                                response.data.remarks == '' ? remarks = '<span class="opacity-25">No Remarks</span>' : remarks = response.data.remarks.charAt(0).toUpperCase() + response.data.remarks.slice(1);
                                $form.find('.response').html(html);
                                //   table.find('tr[tr-id="'+response.data.id+'"] .date').text(app.formatDate(response.data.date));
                                //   table.find('tr[tr-id="'+response.data.id+'"] .leave-type').text(response.data.leave_type.charAt(0).toUpperCase() + response.data.leave_type.slice(1) + ' Leave');
                                //   table.find('tr[tr-id="'+response.data.id+'"] .leave-details').text(response.data.details);
                                //   table.find('tr[tr-id="'+response.data.id+'"] .leave-remarks').html(remarks);

                                setTimeout(() => {
                                    // $form[0].reset();
                                    // $form.find('.alert').remove();
                                    // modal.modal('hide');
                                    window.location.reload();
                                }, 2000);



                                break;

                            case 'error':
                                let html2 = '<div class="alert alert-danger text-center" role="alert"> ' + response.message + ' </div>';
                                $form.find('.response').html(html2);

                                setTimeout(() => {
                                    $form.find('.alert').remove();
                                }, 2000);
                                break;
                        }
                    }
                });
            });

            $('#add-leave-modal').on('hidden.bs.modal', function () {
                $(this).find('h5').html('<i class="fa-solid fa-plus"></i> Add Leave');
                $(this).find('form').attr('id', 'add-leave-form');
                $(this).find('form')[0].reset();
                $(this).find('form [name="leave-id"]').remove();
                $(this).find('form [name="employee-id"]').prop('disabled', false).removeClass('opacity-25');
                $(this).find('form [name="leave-to"]').prop('disabled', false).removeClass('opacity-25');
            });
        }

        app.cancelLeave = function () {
            $(document).on('click', '.cancel-leaves-btn', function (e) {
                e.preventDefault();
                var id = $(this).data('leaveId');
                var modal = $('#cancel-leave-modal');

                $.ajax({
                    url: base_url + 'admin/ajax_leave_request/fetch_leave_info',
                    method: 'POST',
                    dataType: 'JSON',
                    data: { 'id': id },

                    success: function (response) {
                        switch (response.status) {
                            case 'success':

                                let formatDate = app.formatDate(response.data.date);

                                modal.find('.employee-name').text(response.data.user_name);
                                modal.find('.leave-type').text(response.data.leave_type);
                                modal.find('.leave-date').text(formatDate);
                                modal.find('#cancel-leave-form [name="leave-id"]').val(response.data.id);
                                modal.modal('show');

                                break;
                        }
                    }
                });
            });

            $(document).on('submit', '#cancel-leave-form', function (e) {
                e.preventDefault();

                let table = $('#leave-list, #employee-leave-list');
                let modal = $('#cancel-leave-modal');
                let $form = $(this);
                let $response = modal.find('.response');

                $.ajax({
                    url: base_url + 'admin/ajax_leave_request/cancel_leave',
                    method: 'POST',
                    dataType: 'JSON',
                    data: $form.serialize(),

                    success: function (response) {
                        switch (response.status) {
                            case 'success':
                                var html = '<div class="alert alert-success text-center" role="alert">' + response.message + '</div>';

                                let accumulated = $('[name="accumulated-leaves"]').val();
                                let remaining = $('[name="remaining-leaves"]').val();
                                let finalAccumulated = accumulated;
                                let finalRemaining = remaining;

                                if (response.leave_type != 'birthday') {
                                    finalAccumulated = parseInt(accumulated, 10) != 0 ? parseInt(accumulated, 10) - 1 : 0;
                                    finalRemaining = parseInt(remaining, 10) != 24 ? parseInt(remaining, 10) + 1 : 24;
                                }

                                $('[name="accumulated-leaves"]').val(finalAccumulated);
                                $('[name="remaining-leaves"]').val(finalRemaining);
                                $('.accumulated-leaves').text(finalAccumulated);
                                $('.remaining-leaves').text(finalRemaining);


                                $response.html(html);
                                table.find('tr[tr-id="' + response.data + '"]').fadeOut('slow');

                                setTimeout(function () {
                                    $form[0].reset();
                                    modal.find('.alert').remove();
                                    modal.modal('hide');
                                }, 2000);
                                break;

                        }
                    }
                });
            });
        }

        app.leave = function () {
            var table = $("#leave-list");
            var modal = $("#leave-status-modal");
            var form = $("#leave-status-form");

            modal.on('hidden.bs.modal', function () {
                form.find(".statement-row").addClass("d-none");
                form.find("select").prop('selectedIndex', 0);
                form.find("textarea").val("");
            });

            $(document).on("click", ".update-leave-status", function () {
                var leave_id = $(this).attr("leave-id");
                form.attr("leave-id", leave_id);
                $.ajax({
                    url: base_url + "employee/Ajax_leave_request/fetch_leave_request",
                    type: "POST",
                    data: { leave_id: leave_id },
                    dataType: "JSON",
                    success: function (response) {
                        if (response.response_status == "success") {
                            form.find("[name='status'] option[value='" + response.status + "']").prop('selected', true);
                            if (response.status == "denied") {
                                form.find(".statement-row").removeClass("d-none");
                                form.find("[name='reason-denied']").val(response.reason_denied);
                            }
                        }
                    }
                });
            });

            form.find("[name='status']").on("change", function () {
                var val = $(this).val();
                if (val == "denied") {
                    form.find(".statement-row").removeClass("d-none");
                } else {
                    form.find(".statement-row").addClass("d-none").find("[name='reason-denied']").val("");
                }
            });

            form.on("submit", function (e) {
                e.preventDefault();
                var leave_id = form.attr("leave-id");
                var status = form.find("[name='status']").val();
                if (form.find("[name='status']").val() == "denied" && form.find("[name='reason-denied']").val() == "") {
                    form.find("[name='reason-denied']").addClass("error").parent().append('<i class="error-message">required</i>')
                } else {
                    $.ajax({
                        url: base_url + "admin/Ajax_leave_request/update_leave_status",
                        type: 'POST',
                        data: form.serialize() + "&leave_id=" + leave_id,
                        dataType: "JSON",
                        beforeSend: function () {
                            $(".error").removeClass("error");
                            $(".error-message, .alert").remove();
                            form.find('select, textarea').attr('readonly', true);
                            form.find('button').attr('disabled', true);
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                form.prepend('<div class="alert alert-success text-center" role="alert">' + response.message + '</div>');
                                setTimeout(function () {
                                    form.find(".error").removeClass("error");
                                    form.find(".error-message, .alert").remove();
                                    var tr_status;
                                    if (status == "pending") {
                                        tr_status = "<span class='t-green'>Pending</span>";
                                    } else if (status == "denied") {
                                        tr_status = "<span class='t-red'>Denied</span>"
                                    } else {
                                        tr_status = "<span class='t-blue'>Approved</span>"
                                    }

                                    if (response.pending == 0) {
                                        $("#sidebar .leave-drop-trigger .notif-dot-leave").remove();
                                    } else {
                                        $("#sidebar .leave-drop-trigger .notif-dot-leave").remove();
                                        $("#sidebar .leave-drop-trigger").append('<i class="fas fa-circle notif-dot-leave t-12px"></i>');
                                    }

                                    table.find("[tr-id='" + leave_id + "'] .status").html(tr_status);
                                    modal.modal("hide");
                                }, 1000);
                            } else {
                                form.prepend('<div class="alert alert-danger text-center" role="alert">' + response.message + '</div>');
                            }
                        },
                        complete: function () {
                            form.find('select, textarea').removeAttr('readonly');
                            form.find('button').removeAttr('disabled');
                        }
                    });
                }
            });
        }

        app.salary_grade_crud_modal = function () {
            var table = $("#salary-grade-list");
            var modal = $("#salary-grade-cru-modal");
            var del = $("#delete-salary-modal");

            $(document).ready(function () {
                modal.on('hidden.bs.modal', function () {
                    modal.find("form").removeAttr("sg-id")
                    modal.find("input").val("").removeClass("error");
                    modal.find(".error-message").remove();
                });

                $("#add-salary-grade").click(function () {
                    modal.find(".modal-title i").removeClass("fa-edit").addClass("fa-add");
                    modal.find("form").attr("id", "add-salary-grade-form");
                });
            });

            $(document).on("click", ".delete-salary-grade", function () {
                var sg_id = $(this).attr("sg-id");
                $.ajax({
                    url: base_url + "admin/Ajax_salary_grade/fetch_salary_grade",
                    type: "POST",
                    data: { sg_id: sg_id },
                    dataType: "JSON",
                    success: function (response) {
                        del.find("form").attr("sg-id", sg_id);
                        del.find(".question-row b").text("salary grade #" + response.grade_number);
                    }
                });
            });

            $(document).on("click", "#salary-grade-list .view", function () {
                var sg_id = $(this).attr("sg-id");
                $.ajax({
                    url: base_url + "admin/Ajax_salary_grade/fetch_salary_grade_employees",
                    type: "POST",
                    data: { sg_id: sg_id },
                    success: function (response) {
                        $("#salary-grade-employee-modal .data-row").html(response);
                    }
                });
            });

            $(document).on("click", ".edit-salary-grade", function () {
                var sg_id = $(this).attr("sg-id");
                modal.find(".modal-title i").removeClass("fa-add").addClass("fa-edit");
                modal.find("form").attr({ "id": "update-salary-grade-form", "sg-id": sg_id });

                $.ajax({
                    url: base_url + "admin/Ajax_salary_grade/fetch_salary_grade",
                    type: "POST",
                    data: { sg_id: sg_id },
                    dataType: "JSON",
                    success: function (response) {
                        modal.find("[name='salary-grade']").val(response.grade_number);
                        modal.find("[name='hourly-rate']").val(response.hourly_rate);
                    }
                });
            });

            $(document).on("submit", "#add-salary-grade-form", function (e) {
                e.preventDefault();
                var form = $(this);
                $.ajax({
                    url: base_url + "admin/Ajax_salary_grade/au_salary_grade",
                    type: "POST",
                    data: form.serialize(),
                    dataType: "JSON",
                    beforeSend: function () {
                        $(".error").removeClass("error");
                        $(".error-message, .alert").remove();
                        form.find('input').attr('readonly', true);
                        form.find('button').attr('disabled', true);
                    },
                    success: function (response) {
                        if (response.status == "form-incomplete") {
                            $.each(response.errors, function (e, val) {
                                form.find('[name="' + e + '"]').addClass('error');
                                form.find('[name="' + e + '"]').parent().append('<i class="error-message">' + val + '</i>');
                            });
                        } else if (response.status == "error") {
                            form.prepend('<div class="alert alert-danger text-center" role="alert">' + response.message + '</div>');
                        } else {
                            form.prepend('<div class="alert alert-success text-center" role="alert">' + response.message + '</div>');
                            setTimeout(function () {
                                form.find("input").val("");
                                form.find(".error").removeClass("error");
                                form.find(".error-message, .alert").remove();
                                modal.modal("hide");

                                table.prepend('<tr tr-id="' + response.sg_id + '">\
                    <td class="grade-number">'+ response.grade_number + '</td>\
                    <td class="hourly-rate">PHP '+ response.hourly_rate + '</td>\
                    <td class="d-flex">\
                      <button class="btn edit edit-salary-grade" sg-id="'+ response.sg_id + '" data-toggle="modal" data-target="#salary-grade-cru-modal" title="Edit grade info"><i class="fas fa-edit"></i> Edit</button>\
                      <button class="btn view" sg-id="'+ response.sg_id + '" data-toggle="modal" data-target="#salary-grade-employee-modal" title="View employees"><i class="fas fa-eye"></i> Employee</button>\
                      <button class="btn delete delete-salary-grade" sg-id="'+ response.sg_id + '" data-toggle="modal" data-target="#delete-salary-modal" title="Delete Salary Grade"><i class="fas fa-trash"></i> Delete</button>\
                      </td>\
                  </tr>');
                            }, 1000);
                        }
                    },
                    complete: function (response) {
                        form.find('input').removeAttr('readonly');
                        form.find('button').removeAttr('disabled');
                    }
                });
            });

            $(document).on("submit", "#update-salary-grade-form", function (e) {
                e.preventDefault();
                var form = $(this);
                var sg_id = form.attr("sg-id");

                $.ajax({
                    url: base_url + "admin/Ajax_salary_grade/au_salary_grade",
                    type: "POST",
                    data: form.serialize() + "&sg_id=" + sg_id,
                    dataType: "JSON",
                    beforeSend: function () {
                        $(".error").removeClass("error");
                        $(".error-message, .alert").remove();
                        form.find('input').attr('readonly', true);
                        form.find('button').attr('disabled', true);
                    },
                    success: function (response) {
                        if (response.status == "form-incomplete") {
                            $.each(response.errors, function (e, val) {
                                form.find('[name="' + e + '"]').addClass('error');
                                form.find('[name="' + e + '"]').parent().append('<i class="error-message">' + val + '</i>');
                            });
                        } else if (response.status == "error") {
                            form.prepend('<div class="alert alert-danger text-center" role="alert">' + response.message + '</div>');
                        } else {
                            form.prepend('<div class="alert alert-success text-center" role="alert">' + response.message + '</div>');
                            setTimeout(function () {
                                form.find("input").val("");
                                form.find(".error").removeClass("error");
                                form.find(".error-message, .alert").remove();
                                modal.modal("hide");
                                table.find("tr[tr-id='" + sg_id + "'] .grade-number").text(response.grade_number);
                                table.find("tr[tr-id='" + sg_id + "'] .hourly-rate").text("PHP " + response.hourly_rate);
                            }, 1500);
                        }
                    },
                    complete: function (response) {
                        form.find('input').removeAttr('readonly');
                        form.find('button').removeAttr('disabled');
                    }
                });
            });

            $(document).on("submit", "#delete-salary-form", function (e) {
                e.preventDefault();
                var sg_id = $(this).attr("sg-id");
                var sg_row = $("#salary-grade-list [tr-id='" + sg_id + "']");
                $.ajax({
                    url: base_url + "admin/Ajax_salary_grade/delete_salary_grade",
                    type: "POST",
                    data: { sg_id: sg_id },
                    dataType: "JSON",
                    success: function (response) {
                        if (response.status == "error") {
                            del.find(".modal-body").prepend('<div class="alert alert-danger text-center" role="alert">' + response.message + '</div>');
                        } else {
                            del.find(".modal-body").prepend('<div class="alert alert-success text-center" role="alert">' + response.message + '</div>');
                            setTimeout(function () {
                                del.find(".alert").remove();
                                sg_row.remove();
                                del.modal("hide");
                            }, 750);
                        }
                    }
                });
            });
        }

        app.dtr_update_request = function () {
            var modal = $("#dtr-update-request-status-modal");
            var form = $("#dtr-update-request-status-form");

            modal.on('hidden.bs.modal', function () {
                form.find(".statement-row, .approved-contents").addClass("d-none");
                form.find("select").prop('selectedIndex', 0);
                form.find("textarea").val("");
            });

            form.find("[name='status']").on("change", function () {
                var val = $(this).val();
                if (val == "denied") {
                    form.find(".statement-row").removeClass("d-none");
                    form.find('.approved-contents').addClass('d-none');
                } else if (val == 'approved') {
                    form.find(".statement-row").addClass("d-none");
                    form.find('.approved-contents').removeClass('d-none');
                } else {
                    form.find(".statement-row, .approved-contents").addClass("d-none").find("[name='reason-denied']").val("");
                }
            });

            $(document).on("click", ".update-rdtr-status", function () {
                var rdtr_id = $(this).attr("rdtr-id");
                form.attr({ "rdtr-id": rdtr_id, "data-updater": $(this).data("updater") });
                $.ajax({
                    url: base_url + "employee/Ajax_request_dtr_update/read_full_dtr_update_request",
                    type: "POST",
                    data: { rdtr_id: rdtr_id },
                    dataType: "JSON",
                    success: function (response) {

                        let tiWorkbase = response.time_in_work_base;
                        let boWorkbase = response.break_out_work_base;
                        let breaks = response.break.split('-');
                        let overtime = response.overtime !== null ? response.overtime.split('-') : 0;

                        form.find(".name-col").text(response.name);
                        form.find(".message-col").text(response.message);
                        form.find('#ti-wb-' + tiWorkbase).prop('checked', true);
                        form.find('#bo-wb-' + boWorkbase).prop('checked', true);
                        form.find('[name="time-in"]').val(response.time_in);
                        form.find('[name="break-in"]').val(breaks[0]);
                        form.find('[name="break-out"]').val(breaks[1]);
                        form.find('[name="time-out"]').val(response.time_out);
                        form.find('[name="overtime-in"]').val(overtime[0]);
                        form.find('[name="overtime-out"]').val(overtime[1]);
                        form.find('[name="eod-report"]').val(response.end_of_day);
                        form.find("[name='status'] option[value='" + response.request_status + "']").prop('selected', true);

                        if (response.reason_denied) {
                            form.find(".statement-row").removeClass("d-none");
                            form.find("[name='reason-denied']").val(response.reason_denied);
                        }
                    }
                });
            });

            form.on("submit", function (e) {
                e.preventDefault();
                var rdtr_id = $(this).attr("rdtr-id");
                var user_id = $(this).data("updater");

                $.ajax({
                    url: base_url + "employee/Ajax_request_dtr_update/update_status",
                    type: "POST",
                    dataType: "JSON",
                    data: form.serialize() + "&rdtr_id=" + rdtr_id + "&user_id=" + user_id,

                    beforeSend: function () {
                        form.find(".error-message, .alert").remove();
                        form.find(".form-check-input.error").removeClass(".error");
                    },

                    success: function (response) {

                        switch (response.status) {
                            case 'form-incomplete':
                                $.each(response.errors, function (index, val) {
                                    form.find('[name="' + index + '"]').addClass('error');
                                    form.find('[name="' + index + '"]').parent().append('<i class="error-message pt-2">' + val + '</i>');
                                });
                                break;

                            case 'success':
                                form.prepend('<div class="alert alert-success text-center" role="alert">' + response.message + '</div>');
                                setTimeout(function () {

                                    // var table         = $("#request-dtr-update-list [tr-id='"+rdtr_id+"']");
                                    // var status        = form.find("[name='status']").val();
                                    // var reason_denied = form.find("[name='reason-denied']").val();
                                    // var viewDtrBtn = '<a href="http://nlrc_local.com/admin/employee_dtr?user_id=99" class="me-1"><button class="btn view"><i class="fa-solid fa-eye"></i> <span class="mobile-hide">View DTR</span></button></a> Reason Denied: ' + reason_denied;
                                    // table.find(".status").text(status.charAt(0).toUpperCase() + status.slice(1));
                                    // if(status == "denied"){
                                    //   table.find("td").last().html(viewDtrBtn);
                                    // }

                                    // if(status != "pending"){
                                    //   table.find('.text-danger').removeClass('text-danger');
                                    // }

                                    // if(response.pending_count == 0) $('#sidebar #dtr-update-request .dtr_request_count').remove();

                                    // form.find(".error-message, .alert").remove();
                                    // form.find(".error").removeClass(".error");
                                    // modal.modal("hide");
                                    window.location.reload();
                                }, 1500);

                                break;

                            case 'error':
                                form.prepend('<div class="alert alert-danger text-center" role="alert">' + response.message + '</div>');
                                break;
                        }
                    },
                });
            });
        }

        app.saveProfileOnly = function () {
            $(document).on('click', '.emp-cru-update-profile-btn', function (e) {
                e.preventDefault();

                var form = $('#employee-update-form');

                var userId = form.find('[name="user-id"]').val();
                var name = form.find('[name="name"]').val();
                var email = form.find('[name="email"]').val();
                var phoneNumber = form.find('[name="mobile-number"]').val();
                var gender = form.find('[name="gender"]').val();
                var birthdate = form.find('[name="date-of-birth"]').val();
                var role = form.find('[name="role"]').val();
                var branch = form.find('[name="branch"]').val();
                var userName = form.find('[name="username"]').val();
                var data = {
                    'user-id': userId,
                    'name': name,
                    'email': email,
                    'mobile-number': phoneNumber,
                    'gender': gender,
                    'date-of-birth': birthdate,
                    'role': role,
                    'branch': branch,
                    'username': userName
                };

                $.ajax({
                    url: base_url + 'admin/ajax_users/update_user_profile_only',
                    method: 'POST',
                    dataType: 'JSON',
                    data: data,

                    beforeSend: function () {
                        $('.emp-cru-update-profile-btn').html('<i class="fa-solid fa-spin fa-spinner"></i> Updating...');
                    },

                    success: function (response) {
                        switch (response.status) {
                            case 'form-incomplete':
                                $.each(response.errors, function (index, val) {
                                    form.find('[name="' + index + '"]').addClass('error');
                                    form.find('[name="' + index + '"]').parent().append('<i class="error-message pt-2">' + val + '</i>');
                                });
                                $('.emp-cru-update-profile-btn').html('Save Profile');
                                break;

                            case 'success':
                                var html = `<div class="row input-con"><div class="alert alert-success col-md-10 offset-md-1">${response.message}</div></div>`;
                                var responseDiv = $('.user-profile-response');
                                responseDiv.html(html);

                                setTimeout(function () {
                                    form.find('.alert, .error-message').remove();
                                    form.find('.error').removeClass('error');
                                    $('.emp-cru-update-profile-btn').html('Save Profile');

                                }, 2000);
                                break;
                        }

                    }
                });

            });
        }

        app.saveScheduleOnly = function () {
            $(document).on('click', '.emp-cru-update-schedule-btn', function (e) {
                e.preventDefault();

                var btn = $(this);
                var form = $('#employee-update-form');
                var responseDiv = $('.user-fixed-schedule-response');

                var days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
                var workbase = [];
                days.forEach(function (day) {
                    let i = [];
                    $('[name="' + day + '-workbase"]:checked').each(function () {
                        i.push($(this).val());
                    });
                    workbase.push(i.join('/'));
                });

                var data = {
                    'schedule-monday-in': $('[name="schedule-monday-in"]').val(),
                    'schedule-monday-out': $('[name="schedule-monday-out"]').val(),
                    'schedule-tuesday-in': $('[name="schedule-tuesday-in"]').val(),
                    'schedule-tuesday-out': $('[name="schedule-tuesday-out"]').val(),
                    'schedule-wednesday-in': $('[name="schedule-wednesday-in"]').val(),
                    'schedule-wednesday-out': $('[name="schedule-wednesday-out"]').val(),
                    'schedule-thursday-in': $('[name="schedule-thursday-in"]').val(),
                    'schedule-thursday-out': $('[name="schedule-thursday-out"]').val(),
                    'schedule-friday-in': $('[name="schedule-friday-in"]').val(),
                    'schedule-friday-out': $('[name="schedule-friday-out"]').val(),
                    'monday-workbase': workbase[0],
                    'tuesday-workbase': workbase[1],
                    'wednesday-workbase': workbase[2],
                    'thursday-workbase': workbase[3],
                    'friday-workbase': workbase[4],
                    'user-id': $('[name="user-id"]').val()
                };

                $.ajax({
                    url: base_url + 'admin/ajax_users/update_user_schedule_only',
                    method: 'POST',
                    dataType: 'JSON',
                    data: data,

                    beforeSend: function () {
                        btn.html('<i class="fa-solid fa-spin fa-spinner"></i> Updating...');
                        form.find('.error').removeClass('error');
                    },

                    success: function (response) {
                        switch (response.status) {
                            case 'form-incomplete':
                                $.each(response.errors, function (index, val) {
                                    form.find('[name="' + index + '"]').addClass('error');
                                });
                                btn.html('Save Schedule');
                                break;

                            case 'success':
                                var html = `<div class="input-con row"><div class="col-md-11 alert alert-success">${response.message}</div></div>`;
                                responseDiv.html(html);

                                setTimeout(function () {
                                    form.find('.alert').remove();
                                    $('.schedule-info-column').find('.alert').remove();
                                    btn.html('Save Schedule');
                                }, 2000);
                                break;
                        }
                    }
                });
            });
        }

        app.saveTemporaryScheduleOnly = function () {
            $(document).on('click', '.emp-cru-save-temp-schedule-btn', function (e) {
                e.preventDefault();

                var btn = $(this);
                var form = $('#employee-update-form');
                var responseDiv = $('.user-temporary-schedule-response');
                var workbase = [];
                $('[name="temp-workbase"]:checked').each(function () {
                    workbase.push($(this).val());
                });

                //console.log(workbase.join('/'));

                var data = {
                    'temp-schedule-date-from': $('[name="temp-schedule-date-from"]').val(),
                    'temp-schedule-date-to': $('[name="temp-schedule-date-to"]').val(),
                    'temp-schedule-in': $('[name="temp-schedule-in"]').val(),
                    'temp-schedule-out': $('[name="temp-schedule-out"]').val(),
                    'temp-workbase': workbase.join('/'),
                    'user-id': $('[name="user-id"]').val()
                };

                $.ajax({
                    url: base_url + 'admin/ajax_users/add_temporary_schedule_only',
                    method: 'POST',
                    dataType: 'JSON',
                    data: data,

                    beforeSend: function () {
                        form.find('.alert').remove();
                        form.find('.error').removeClass('error');

                    },

                    success: function (response) {
                        switch (response.status) {
                            case 'form-incomplete':
                                $.each(response.errors, function (index, val) {
                                    form.find('[name="' + index + '"]').addClass('error');
                                });

                                var html = `<div class="alert alert-danger">Please fill in required fields.</div>`;
                                responseDiv.html(html);
                                break;

                            case 'success':
                                var html = `<div class="alert alert-success">${response.message}</div>`;
                                responseDiv.html(html);

                                setTimeout(function () {
                                    window.location.reload();
                                }, 2000);
                                break;
                        }
                    }
                });
            });
        }

        app.editTemporarySchedule = function () {
            $(document).on('click', '#temporary-schedule-table .btn.edit', function (e) {
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

                parent.find('td:first-child').html('<input type="date" name="temp-sched-date" value="' + schedDate + '">');
                parent.find('td:nth-child(2)').html('<input type="time" name="temp-sched-in" value="' + schedTimeIn + '">');
                parent.find('td:nth-child(3)').html('<input type="time" name="temp-sched-out" value="' + schedTimeOut + '">');
                parent.find('td:nth-child(4)').html('<select name="temp-sched-workbase"><option value="WFH" ' + workbaseWfh + '>WFH</option><option value="Office" ' + workbaseOffice + '>Office</option><option value="WFH/Office" ' + workbaseBoth + '>WFH/Office</option></select>');
                parent.find('td:last-child').html('<button class="edit-temporary-schedule-btn border-0" data-user-id="' + userId + '" data-sched-id="' + schedId + '">Save</button>')
            });

            $(document).on('click', '.edit-temporary-schedule-btn', function (e) {
                e.preventDefault();
                var schedId = $(this).data('schedId');
                var userId = $(this).data('userId');
                var row = $('#temp-sched-row-' + schedId);
                var responseDiv = $('.user-temporary-schedule-response');
                var tempSchedData = {
                    'temp-sched-date': row.find('[name="temp-sched-date"]').val(),
                    'temp-sched-in': row.find('[name="temp-sched-in"]').val(),
                    'temp-sched-out': row.find('[name="temp-sched-out"]').val(),
                    'temp-sched-workbase': row.find('[name="temp-sched-workbase"]').val(),
                    'schedule-id': schedId,
                    'user-id': userId
                };

                $.ajax({
                    url: base_url + 'admin/ajax_users/edit_temporary_schedule',
                    method: 'POST',
                    dataType: 'JSON',
                    data: tempSchedData,

                    beforeSend: function () {
                        row.find('.error').removeClass('error');
                        row.find('.alert').remove();
                    },

                    success: function (response) {
                        switch (response.status) {
                            case 'form-incomplete':
                                $.each(response.errors, function (index, val) {
                                    row.find('[name="' + index + '"]').addClass('error');
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

                                setTimeout(function () {

                                    responseDiv.find('.alert').fadeOut('slow', function () {
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

        app.cancelTemporarySchedule = function () {
            $(document).on('click', '#temporary-schedule-table .btn.cancel', function (e) {
                e.preventDefault();

                var schedId = $(this).data('schedId');
                var responseDiv = $('.user-temporary-schedule-response');
                var table = $('#temporary-schedule-table');

                $.ajax({
                    url: base_url + 'admin/ajax_users/cancel_temporary_schedule',
                    method: 'POST',
                    dataType: 'JSON',
                    data: { 'sched-id': schedId },

                    beforeSend: function () {
                        table.find('.alert').remove();
                    },

                    success: function (response) {
                        switch (response.status) {
                            case 'success':
                                var html = `<div class="alert alert-success">${response.message}</div>`;
                                responseDiv.html(html);

                                var row = table.find('#temp-sched-row-' + schedId);
                                row.fadeOut('slow', function () {
                                    $(this).remove();
                                });

                                if ((table.find('tbody tr').length - 1) <= 0) {
                                    table.fadeOut('slow', function () {
                                        table.addClass('d-none');
                                    });
                                }

                                setTimeout(function () {
                                    responseDiv.find('.alert').fadeOut('slow', function () {
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

        app.viewDeclinedRequestMessage = function () {
            $(document).on('click', '.leave-request-denied-btn', function () {
                var requestId = $(this).closest('[data-request-id]').data('request-id');

                console.log(requestId);
                $.ajax({
                    url: base_url + 'employee/ajax_leave_request/fetch_leave_request',
                    method: 'POST',
                    dataType: 'JSON',
                    data: { 'leave_id': requestId },

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



        // added

        function fetchLeaveRequest(reqId) {
            var modal = $('#generate-request-modal');
            var userName = "";
            var leaveType = "";
            var date = "";

            $.ajax({
                url: base_url + 'employee/ajax_leave_request/fetch_leave_request',
                method: 'POST',
                dataType: 'JSON',
                data: { 'leave_id': reqId },

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
                        modal.find('.signature-container-approver').find('img').attr('src', base_url + 'assets/img/signatures/'+response.approved_by_id+'.png');
                        modal.find('.signature-container-employee').find('img').attr('src', base_url + 'assets/img/signatures/'+response.user_id+'.png');

                        if (response.status == 'denied') {
                            modal.find('.req-reason').html('<p><b>Reason denied: </b> <br>' + response.reason_denied + '</p>');
                        } else {
                            modal.find('.req-reason').html('<p><b>Reason: </b> <br>' + response.details + '</p>');
                        }

                        modal.find('.req-conformed-by').html('<b>' + response.approved_by_name + '</b>');
                    }
                }
            });
        }

        app.generatePdf = function () {
            $(document).on('click', '.generate-pdf', function (e) {
                e.preventDefault();

                var modal = $('#generate-request-modal');
                var name = modal.find('.req-name b').text();
                var type = modal.find('.req-leave-type h3 b').text();
                var date = modal.find('.req-leave-date div').text();
                var splitName = name.split(' ');
                var split_date = date.replace(' ', '_');
                var splitType = type.split(' ');
                var splitTypeAgain = splitType[3].split('');
                var replaceDateSpacer = split_date.replace(', ', '_');

                var filename = splitName[0] + '_' + splitTypeAgain[0] + 'L_' + replaceDateSpacer;

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

        $(document).ready(function () {
            $(document).on('click', '.generate-admin-pdf-btn', function (e) {
                e.preventDefault();
                fetchLeaveRequest($(this).closest('[data-req-id]').data('reqId'));
                $('#generate-request-modal').modal('show');
            });

            $(document).on('click', '.generate-admin-pdf-btn-denied', function (e) {
                e.preventDefault();
                fetchLeaveRequest($(this).closest('[data-req-id]').data('reqId'));
                $('#generate-request-modal').modal('show');
            });
        });

        app.onCloseModal = function () {
            $(document).on('hidden.bs.modal', '#generate-request-modal', function () {
                var modal = $(this);
                modal.find('.req-reason').html('<p><b>Reason: </b>');
            })
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

        app.editEmployeeProfile = function(){
            $(document).on('click', '.edit-employee-profile-btn', function(){
                var container = $('#profile-content-container');
                container.find('.on-edit-d-none').addClass('d-none');
                container.find('[type="text"], select, [type="email"], [type="number"]').removeClass('d-none');
                $(this).removeClass('edit-employee-profile-btn').addClass('save-employee-profile-btn').html('<h4 title="Save Profile" class="mb-0 pb-0"><i class="fa-solid fa-check"></i> Save </h4>');
            })
        }

        app.saveEmployeeProfile = function(){
            $(document).on('click', '.save-employee-profile-btn', function(){
                var container = $('#profile-content-container');
                var department = container.find('[name="department"]').val();
                var username = container.find('[name="user-name"]').val();
                var email = container.find('[name="email"]').val();
                var mobileNumber = container.find('[name="mobile-number"]').val();
                var branch = container.find('[name="branch"]').val();
                var gender = container.find('[name="gender"]').val();
                var name = container.find('.employee-name').text();
                var userId = container.data('userId');
                var btn = $(this);

                $.ajax({
                    url: base_url + 'admin/ajax_users/update_employee_profile',
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        'user-id' : userId,
                        'name' : name, 
                        'department' : department, 
                        'user-name' : username, 
                        'email' : email, 
                        'mobile-number' : mobileNumber, 
                        'gender' : gender, 
                        'branch' : branch
                    },

                    beforeSend: function(){
                        btn.html('<h4 title="Save Profile" class="mb-0 pb-0 text-primary"><i class="fa-solid fa-spinner fa-spin"></i> Saving... </h4>');

                        setTimeout(function(){
                            btn.html('<h4 title="Edit Profile" class="mb-0 pb-0"><i class="fa-solid fa-pen-to-square"></i> Edit </h4>');
                        }, 2000)
                    },

                    success: function(response){
                        switch(response.status){
                            case 'success':
                                btn.html('<h4 title="Save Profile" class="mb-0 pb-0 text-success"><i class="fa-solid fa-check"></i> Saved</h4>');

                                setTimeout(function(){
                                    btn.html('<h4 title="Edit Profile" class="mb-0 pb-0"><i class="fa-solid fa-pen-to-square"></i> Edit </h4>');
                                    window.location.reload();
                                }, 2000)
                                
                            break;

                            case 'form-incomplete':
                                $.each(response.errors, function (e, val) {
                                    if(e == 'name') {
                                        container.find('.employee-name').append('<div class="alert alert-danger">'+val+' is Required</div>');
                                    }else{
                                        container.find('[name="'+ e +'"]').addClass('border-danger').parent().append('<span class="text-danger">'+val+'</span>');
                                    }
                                });
                            break;
                        }
                    }
                })
                
            })
        }

        app.tempSchedMonthFilter = function(){
            $(document).on('change', '[name="temp-sched-month-filter"]', function(){
                var monthYear = $(this).val();
                var splitMY = monthYear.split('-');
                var empId = splitMY[0];
                var month = splitMY[1];
                var year = splitMY[2];

                window.location.href = base_url + 'admin/view_profile?id=' + empId + '&month=' + month + '&year=' + year;
            })
        }

        app.init();
    })(Script);
});