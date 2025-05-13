<div class="modal fade" id="leave-retraction-request-modal" tabindex="-1" role="dialog" aria-labelledby="cancel-ongoing-dtr-modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white p-0 lh-0"><i class="fas fa-check"></i> Approve Retraction Request</i></h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times text-white"></i>
        </button>
      </div>
    
      <form id="leave-retraction-request-form">
        <input type="hidden" name="request-id">
        <div class="modal-body">
            <div class="response"></div>
            <div class="form-group">
                <h6 class="form-label">Are you sure you want to approve <span class="emp-name"></span>'s request?</h6>
                <p class="mb-0"><b>Retraction request message:</b></p>
                <p class="reason-retracted"></p>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-success">Save</button>
            <button type="button" data-bs-dismiss="modal" class="btn btn-danger">Cancel</button>
        </div>
      </form>
    
    </div>
  </div>
</div>