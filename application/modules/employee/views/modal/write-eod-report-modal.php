<div class="modal fade" id="write-eod-report-modal" tabindex="-1" role="dialog" aria-labelledby="write-eod-report-modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white p-0 lh-0"><i class="fa-solid fa-message"></i> End Of Day Report</i></h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times text-white"></i>
        </button>
      </div>

      <div class="modal-body">
        <form id="eod-report-text-area">
          <div class="cs-form mt-4">
            <input name="action" type="hidden">
            <input name="user_id" type="hidden">
            <input name="dtr_id" type="hidden">
            <textarea name="eod-report-text" class="form-control " placeholder="End of day report!" rows="6"></textarea>
          </div>

          <h5 class="p-0 mt-4 d-none">Overtime:</h5>
          <div class="row d-none">
            <div class="cs-form col-md-6 col-sm-6 d-flex align-items-center">
              <label class="me-2 fs-6">From:</label>
              <input type="time" class="form-control" name="dtr-ot-from">
            </div>
            <div class="cs-form col-md-6 col-sm-6 d-flex align-items-center">
              <label class="me-2 fs-6">To:</label>
              <input type="time" class="form-control" name="dtr-ot-to">
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button class="btn btn-success dtr-time-out-btn">Time-Out</button>
        <button class="btn btn-sblue" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>