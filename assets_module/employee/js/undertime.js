$(function () {
    var Script = {};
    (function (app) {
        var ww = window.innerWidth;
        let leavesTable;
  
        app.init = function() {
          app.bindings();
        }
  
        app.bindings = function() {
          app.onChangeDateEmpRange();
          app.utIsLeave();
          app.utIsCompensate();
          app.utForSalaryDeduction();

          app.addUndertimeModal();
          app.addUndertimeForm();
          app.deleteUndertimeModal();
          app.deleteUndertimeForm();
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

        app.utIsLeave = function(){
            $(document).on('click', '[name="is-leave-checker"]', function() {
                var id = $(this).val();
                if($(this).is(':checked')){
                    app.utIsLeaveUpdate(id, 1);
                }else{
                    app.utIsLeaveUpdate(id, 0);
                }
            })
        }

        app.utIsCompensate = function(){
            $(document).on('click', '[name="is-compensate-checker"]', function() {
                var id = $(this).val();
                if($(this).is(':checked')){
                    app.utIsCompensateUpdate(id, 1);
                }else{
                    app.utIsCompensateUpdate(id, 0);
                }
            })
        }

        app.utForSalaryDeduction = function(){
            $(document).on('click', '[name="is-salary-deduction-checker"]', function() {
                var id = $(this).val();
                if($(this).is(':checked')){
                    app.utForSalaryDeductionUpdate(id, 1);
                }else{
                    app.utForSalaryDeductionUpdate(id, 0);
                }
            })
        }

        app.utIsLeaveUpdate = function(id, status){
            $.ajax({
                url: base_url + 'employee/ajax_undertime/is_leave',
                method: 'POST',
                dataType: 'JSON',
                data: {'id' : id,'status' : status}, 

                success: function(response) {
                    switch(response.status){
                        case 'success':
                            $('#total-undertime-text').text(response.remaining);
                        break;
                    }
                }
            })
        }

        app.utIsCompensateUpdate = function(id, status){
            $.ajax({
                url: base_url + 'employee/ajax_undertime/is_compensated',
                method: 'POST',
                dataType: 'JSON',
                data: {'id' : id,'status' : status}, 

                success: function(response) {
                    switch(response.status){
                        case 'success':
                            $('#total-undertime-text').text(response.remaining);
                        break;
                    }
                }
            })
        }

        app.utForSalaryDeductionUpdate = function(id, status){
            $.ajax({
                url: base_url + 'employee/ajax_undertime/for_salary_deduction',
                method: 'POST',
                dataType: 'JSON',
                data: {'id' : id,'status' : status}, 

                success: function(response) {
                    switch(response.status){
                        case 'success':
                            $('#total-undertime-text').text(response.remaining);
                        break;
                    }
                }
            })
        }

        app.addUndertimeModal = function(){
            $(document).on('click', '.add-undertime-btn', function(e) {
                e.preventDefault();
                
                var modal = $('#add-undertime-modal').modal('show');

            });
        }

        app.addUndertimeForm = function(){
            $(document).on('submit', '#add-undertime-form', function(e) {
                e.preventDefault();

                var form = $(this);
                var responseDiv = form.find('.response');

                $.ajax({
                    url: base_url + 'employee/ajax_undertime/add_undertime',
                    method: 'POST',
                    dataType: 'JSON',
                    data: form.serialize(),

                    beforeSend: function(){
                        form.find('.error').removeClass('error');
                        form.find('.error-message').remove();
                    },

                    success: function(response){
                        switch(response.status){
                            case 'form-incomplete':
                                form.find('.alert').remove();
                                $.each(response.errors, function (e, val) {
                                    form.find('[name="' + e + '"]').addClass('error');
                                    form.find('[name="' + e + '"]').parent().append('<i class="error-message">' + val + '</i>');
                                });
                            break; 

                            case 'success':
                                var html = `<div class="alert alert-success">${response.message}</div>`;
                                form.find('.response').html(html);

                                setTimeout(function () {
                                    window.location.reload();
                                }, 2000)
                            break;

                            case 'dtr-not-found':
                                var html = `<div class="alert alert-danger">${response.message}</div>`;
                                form.find('.response').html(html);
                            break;

                            case 'existing-record':
                                var html = `<div class="alert alert-danger">${response.message}</div>`;
                                form.find('.response').html(html);
                            break;
                        }
                    }
                })
            });
        }

        app.deleteUndertimeModal = function(){
            $(document).on('click', '.delete-undertime-btn', function(){
                var utId = $(this).data('undertimeId');
                var modal = $('#delete-undertime-modal');
                modal.find('form').find('[name="ut-id"]').val(utId);
                modal.modal('show');
            });
        }

        app.deleteUndertimeForm = function(){
            $(document).on('submit', '#delete-undertime-form', function(e){
                e.preventDefault();

                var form = $(this);
                var modal = $('#delete-undertime-modal');
                var id = form.find('[name="ut-id"]').val();

                $.ajax({
                    url: base_url + 'employee/ajax_undertime/delete_undertime',
                    method: 'POST',
                    dataType: 'JSON',
                    data: {id : id},
    
                    success: function(response){
                        switch(response.status){
                            case 'success':
                                var html = `<div class="alert alert-success">${response.message}</div>`;
                                modal.find('.response').html(html);

                                setTimeout(function(){
                                    window.location.reload();
                                }, 2000);
                            break;
                        }
                    }
                })
            });
            
        }

        app.init();
    })(Script);
  });