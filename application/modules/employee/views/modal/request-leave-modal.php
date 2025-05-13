<div class="modal fade" id="request-leave-modal" tabindex="-1" role="dialog" aria-labelledby="request-leave-modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white p-0 lh-0 d-flex align-items-center"><i class="fas fa-sign-out"></i>&nbsp;Request Leave</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times text-white"></i>
        </button>
      </div>

      <form id = "request-leave-form">
        <div class="response"></div>
        <div class="modal-body">
          <div class="row input-con">
            <div class="col-md-3"><label>Leave Type: </label></div>
            <div class="col-md-9">
              <select name="leave-type" class="form-select">
                <option selected disabled>--Select Leave Type--</option>
                <option class="type-birthday" value="birthday">Birthday Leave</option>
                <option class="type-sick" value="sick">Sick Leave</option>
                <option class="type-vacation" value="vacation">Vacation Leave</option>
                <option class="type-special" value="special">Special Leave</option>
              </select>
            </div>
          </div>

          <div class="row input-con">
            <div class="col-md-3"><label>Date: </label></div>
            <div class="col-md-9">
              <div class="d-flex">
                <div class="col-md-6 form-group">
                  <label>From:</label>
                  <input type="date" name="leave-from" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                  <label>To:</label>
                  <input type="date" name="leave-to" class="form-control">
                </div>
              </div>
            </div>
          </div>

          <div class="row input-con">
            <div class="col-md-3"><label>Reason: </label></div>
            <div class="col-md-9">
              <textarea type="text" class="form-control" name="leave-reason" placeholder="Enter you reason for leave here!" rows="4"></textarea>
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