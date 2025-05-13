<div class="modal fade" id="leave-status-modal" tabindex="-1" role="dialog" aria-labelledby="leave-status-modal" aria-hidden="true">

  <div class="modal-dialog" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title text-white p-0 lh-0"><i class="fas fa-edit"></i> Leave Status</h5>

        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">

          <i class="fas fa-times text-white"></i>

        </button>

      </div>

      <form id="leave-status-form">

        <div class="modal-body">

          

          <div class="row input-con">

            <div class="col-md-3 offset-md-1"><label>Status:* </label></div>

            <div class="col-md-7">

              <select class="form-select" name="status">

                <option value="pending">Pending</option>

                <option value="approved">Approve</option>

                <option value="denied">Deny</option>

              </select>

            </div>

          </div>



          <div class="row input-con statement-row d-none">

            <div class="col-md-3 offset-md-1"><label>Statement:* </label></div>

            <div class="col-md-7">

              <textarea class="w-100 form-control" rows="3" name="reason-denied" placeholder="statement as to why leave is denied"></textarea>

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