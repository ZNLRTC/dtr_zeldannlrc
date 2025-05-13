$(function () {
    var Script = {};
    (function (app) {
        var ww = window.innerWidth;
        let leavesTable;
  
        app.init = function() {
          app.bindings();
        }
  
        app.bindings = function() {
          app.initializeActiveDtrList();
          app.initializeObserverActiveDtrList();
          app.initializeEmployeeDtrTable();
          app.viewEmployeeDtr();
          app.viewLeaves();
        }

        app.initializeActiveDtrList = function(){
            dtrTable = $('#active-dtr-table').DataTable({
                bLengthChange: false, 
                searching: true,
                info: false,
                iDisplayLength: 50,
                order: [],
                columnDefs: [
                    {
                        targets: [0,1,2,3],
                        orderable: false
                    }
                ]
            });

            $("#active-dtr-table-search").keyup(function(){
                dtrTable.search($(this).val()).draw();
            });
        }

        app.initializeObserverActiveDtrList = function(){
          dtrObserverTable = $('#observer-active-dtr-table').DataTable({
            //scrollY: "70vh",
            //scrollCollapse: true,
            //autoWidth: true,
            //scrollX: true,
            fixedHeader: true,
            bLengthChange: false, 
            searching: true,
            info: false,
            iDisplayLength: -1,
            paging: false,
            columnDefs: [
              {
                  targets: [1,2,3,4,5,6],
                  orderable: false
              }
            ]
          });

          $("#observer-active-dtr-table-search").keyup(function(){
              dtrObserverTable.search($(this).val()).draw();
          });
        }

        app.initializeEmployeeDtrTable = function(){
            var emp_dtr_table = $('#employee-dtr-list').DataTable({
              bLengthChange: false, 
              searching: true,
              info: false,
              iDisplayLength: -1,
              paging: false,
              order: [0, 'desc'],
              columnDefs: [
                {
                  targets: [1,2,3,4,5],
                  orderable: false
                }
              ],
    
            });
    
            $('#active-dtr-table-search').keyup(function(){
              emp_dtr_table.search($(this).val()).draw();
            })
          }

        app.viewEmployeeDtr = function(){
            $(document).on('click', '.view-dtr-btn', function(e){
                e.preventDefault();

                var userId = $(this).parent().parent().data('empId');
                var month = $(this).parent().parent().data('month');
                var year = $(this).parent().parent().data('year');
                window.location.href = base_url + 'employee/view_emp?i=' + userId + '&m=' + month + '&y=' + year;
            });
        }

        app.viewLeaves = function(){
            $(document).on('click', '.view-leaves-btn', function(e){
                e.preventDefault();
                var userId = $(this).parent().parent().data('empId');
                window.location.href = base_url + 'employee/employee_leaves?id=' + userId;
            });
        }
  
        app.init();
    })(Script);
  });