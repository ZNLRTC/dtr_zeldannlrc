<div class="modal fade" id="decline-leave-request-modal" tabindex="-1" role="dialog" aria-labelledby="cancel-ongoing-dtr-modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white p-0 lh-0"><i class="fas fa-times-circle"></i> Decline Request</i></h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times text-white"></i>
        </button>
      </div>
    
      <form id="decline-leave-request-form">
        <input type="hidden" name="request-id">
        <div class="modal-body">
            <div class="response"></div>
            <div class="form-group">
                <h6 class="form-label">Paki enter jy reason no apay nga declined piman request da</h6>
                <textarea class="form-control" name="reason-declined" placeholder="Enter reason here!" rows="5" required></textarea>
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