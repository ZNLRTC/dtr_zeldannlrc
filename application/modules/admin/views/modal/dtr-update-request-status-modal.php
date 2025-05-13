<div class="modal fade" id="dtr-update-request-status-modal" tabindex="-1" role="dialog" aria-labelledby="leave-status-modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white p-0 lh-0"><i class="fas fa-edit"></i> Request Approval</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times text-white"></i>
        </button>
      </div>
      <form id="dtr-update-request-status-form">
        <div class="modal-body">

          <div class="row input-con">
            <div class="col-md-3 offset-md-1"><label>Message: </label></div>
            <div class="col-md-7 message-col"></div>
          </div>

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
              <textarea class="w-100 form-control" rows="3" name="reason-denied" placeholder="Reason for denied request"></textarea>
              <i class="t-12px t-red lh-12px">Note: Denied requests can no longer be undone, unless request is made again</i>
            </div>
          </div>

          <div class="d-none approved-contents">
            <div class="row input-con">
              <div class="col-md-3 offset-md-1">
                <label class="form-label">Workbase:</label>
              </div>
              <div class="col-md-7 row">
                <div class="col-md-6">
                  <small>Time-in</small>
                  <div class="d-flex">
                    <div class="form-check me-2">
                      <input class="form-check-input" type="radio" name="time-in-workbase" id="ti-wb-WFH" value="WFH">
                      <label class="form-check-label pointer" for="ti-wb-WFH">WFH</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="time-in-workbase" id="ti-wb-Office" value="Office">
                      <label class="form-check-label pointer" for="ti-wb-Office">Office</label>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <small>Break-out</small>
                  <div class="d-flex">
                    <div class="form-check me-2">
                      <input class="form-check-input" type="radio" name="break-out-workbase" id="bo-wb-WFH" value="WFH">
                      <label class="form-check-label pointer" for="bo-wb-WFH">WFH</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="break-out-workbase" id="bo-wb-Office" value="Office">
                      <label class="form-check-label pointer" for="bo-wb-Office">Office</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row input-con">
              <div class="col-md-3 offset-md-1"><label class="form-label">Time-in:</label></div>
              <div class="col-md-7"><input class="form-control" type="time" name="time-in" required></div>
            </div>

            <div class="row input-con">
              <div class="col-md-3 offset-md-1"><label class="form-label">Break-in:</label></div>
              <div class="col-md-7"><input class="form-control" type="time" name="break-in"></div>
            </div>

            <div class="row input-con">
              <div class="col-md-3 offset-md-1"><label class="form-label">Break-out:</label></div>
              <div class="col-md-7"><input class="form-control" type="time" name="break-out"></div>
            </div>

            <div class="row input-con">
              <div class="col-md-3 offset-md-1"><label class="form-label">Time-out:</label></div>
              <div class="col-md-7"><input class="form-control" type="time" name="time-out"></div>
            </div>

            <div class="row input-con">
              <div class="col-md-3 offset-md-1"><label class="form-label">Overtime-in:</label></div>
              <div class="col-md-7"><input class="form-control" type="time" name="overtime-in"></div>
            </div>

            <div class="row input-con">
              <div class="col-md-3 offset-md-1"><label class="form-label">Overtime-out:</label></div>
              <div class="col-md-7"><input class="form-control" type="time" name="overtime-out"></div>
            </div>

            <div class="row input-con">
              <div class="col-md-3 offset-md-1"><label class="form-label">End of Day Report:</label></div>
              <div class="col-md-7"><textarea class="form-control" name="eod-report" rows="4"></textarea></div>
            </div>
          </div>
          
        <div class="modal-footer">
          <button type="submit" class="btn btn-sblue w-100">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>