<div class="modal fade" id="overtime-request-modal" tabindex="-1" role="dialog" aria-labelledby="cancel-ongoing-dtr-modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white p-0 lh-0"><i class="fa-solid fa-business-time"></i> Overtime Request</i></h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times text-white"></i>
        </button>
      </div>
    
      <form id="overtime-request-form">
        <input type="hidden" name="request-id">
        <div class="modal-body">
            <div class="response"></div>
            <input type="hidden" name="dtr-id">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group d-flex align-items-center mb-3">
                        <input type="checkbox" name="holiday-ot" id="holiday-ot-checkbox" class="form-check mb-0 me-2">
                        <label class="form-label mb-0 pointer" for="holiday-ot-checkbox">Check this if Holiday Overtime</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Time-in</label>
                        <input type="time" class="form-control" name="ot-in">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Time-out</label>
                        <input type="time" class="form-control" name="ot-out">
                    </div>
                </div>

                <div class="invalid-ot-response mt-2"></div>

                <div class="col-md-12 mt-3">
                    <div class="form-group">
                        <label class="form-label">Task</label>
                        <textarea class="form-control" name="ot-reason" placeholder="Enter Overtime details here" rows="4"></textarea>
                    </div>
                </div>
            </div>
            
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-success">Submit</button>
            <button type="button" data-bs-dismiss="modal" class="btn btn-danger">Cancel</button>
        </div>
      </form>
    
    </div>
  </div>
</div>