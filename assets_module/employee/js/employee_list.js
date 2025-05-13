$(function () {
    var Script = {};
    (function (app) {
        var ww = window.innerWidth;
  
        app.init = function() {
          app.bindings();
        }
  
        app.bindings = function() {
        app.initializeEmployeeLeaveListTable();
          app.viewEmployeeDtr();
          app.viewLeaves();
        }

        app.initializeEmployeeLeaveListTable = function(){
            employeeLeaveListTable = $('#employee-leave-list').DataTable({
              bLengthChange: false, 
              searching: true,
              info: false,
              iDisplayLength: -1,
              paging: false,
              order: [],
            });
    
            $('#employee-leave-list-search, #employee-leave-list-search-mobile').keyup(function(){
              employeeLeaveListTable.search($(this).val()).draw();
            })
          }

        app.viewEmployeeDtr = function(){
            $(document).on('click', '.view-dtr-btn', function(e){
                e.preventDefault();

                var userId = $(this).closest('[data-emp-id]').data('empId');
                var month = $(this).closest('[data-month]').data('month');
                var year = $(this).closest('[data-year]').data('year');
                window.location.href = base_url + 'employee/view_emp?i=' + userId + '&m=' + month + '&y=' + year;
            });
        }

        app.viewLeaves = function(){
            $(document).on('click', '.view-leaves-btn', function(e){
                e.preventDefault();
                var userId = $(this).closest('[data-emp-id]').data('empId');
                window.location.href = base_url + 'employee/employee_leaves?id=' + userId;
            });
        }
  
        app.init();
    })(Script);
  });