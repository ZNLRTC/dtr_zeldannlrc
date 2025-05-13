<div class="modal fade" id="request-ot-cru-modal" tabindex="-1" role="dialog" aria-labelledby="request-ot-cru-modal" aria-hidden="true">

  <div class="modal-dialog" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title text-white p-0 lh-0 d-flex align-items-center"></h5>

        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">

          <i class="fas fa-times text-white"></i>

        </button>

      </div>

      <form>

        <div class="modal-body">



          <div class="row input-con ot-type-con">

            <div class="col-md-2 offset-md-1"><label>Overtime:* </label></div>

            <div class="col-md-8">

              <select class="form-select" name="ot-type">

                <option value="Regular Overtime">Regular Overtime</option>

                <option value="Document Checking">Document Checking</option>

              </select>

            </div>

          </div>



          <div class="row input-con date-con">

            <div class="col-md-2 offset-md-1"><label>Date:* </label></div>

            <div class="col-md-8">

              <input type="text" name="date" class="date-format form-control" maxlength="10" placeholder="mm/dd/yyyy">

            </div>

          </div>



          <div class="row input-con time-con pr-20 d-none">

            <div class="col-md-2 offset-md-1"><label>Time:* </label></div>

            <div class="col-md-8 time-in-out-col">

              <div class="d-flex align-items-center break-div cs-form">

                <input class="form-control" type="time" min="00:00" max="24:00" name="start">

                &nbsp;&nbsp;-&nbsp;&nbsp;

                <input class="form-control" type="time" min="00:00" max="24:00" name="end">

              </div>

            </div>

          </div>

          

          <div class="row input-con task-con">

            <div class="col-md-2 offset-md-1"><label>Task:* </label></div>

            <div class="col-md-8">

              <textarea class="w-100" rows="5" name="task" placeholder="Separate tasks by next line"></textarea>

            </div>

          </div>



        </div>

        <div class="modal-footer">

          <button type="submit" class="btn btn-sblue w-100">Submit</button>

        </div>

      </form>

    </div>

  </div>

</div>