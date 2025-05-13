<div class="modal fade" id="eod-ot-report-modal" tabindex="-1" role="dialog" aria-labelledby="eod-report-modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white p-0 lh-0"><i class="fa-solid fa-message"></i> End Of Day Report</i></h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times text-white"></i>
        </button>
      </div>

      <div class="modal-body">
        <div class="response"></div>
        <input type="hidden" name="ot-id">
        <div class="form-group">
            <label class="form-label">Taks taken:</label>
            <textarea class="form-control" name="ot-report" rows="6" placeholder="Haan gamin manen nga tuldok lang!"></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-success ot-time-out-btn-save">Time-out</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>