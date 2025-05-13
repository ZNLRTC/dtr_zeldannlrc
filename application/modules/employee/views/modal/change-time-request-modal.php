<div class="modal fade" id="approve-leave-request-modal" tabindex="-1" role="dialog" aria-labelledby="cancel-ongoing-dtr-modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white p-0 lh-0"><i class="fa-solid fa-clock"></i> Change Time Request</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times text-white"></i>
        </button>
      </div>
    
      <form id="change-time-request-form">
        <div class="modal-body">
            <div class="response"></div>
            <div >
                <label class="form-label">
                    Enter change time detail <br>
                    <small class="text-warning">*Please make sure to add whether it it's WFH or Office based.</small>
            </label> 
                <textarea class="form-control" name="ctr-reason" rows="4" required></textarea>
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