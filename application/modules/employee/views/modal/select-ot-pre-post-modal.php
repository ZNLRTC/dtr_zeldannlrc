<div class="modal fade" id="select-ot-pre-post-modal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white p-0 lh-0">Select Overtime shift</i></h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times text-white"></i>
        </button>
      </div>
    
      
        <div class="modal-body">
          <form id="select-ot-pre-post-form" class="row">
            <div class="response"></div>
            <input type="hidden" name="user-id">
            <input type="hidden" name="dtr-id">
            <input type="hidden" name="action">
            <input type="hidden" name="workbase">

            <div class="col-md-4 mb-3 ">
              <button class="btn btn-success ot-shift-btn w-100" type="submit" value="pre">Pre-shift Overtime</button>
            </div>

            <div class="col-md-4 mb-3">
              <button class="btn btn-primary ot-shift-btn w-100 <?= $holiday ? '' : 'disabled' ?>" type="submit" value="holiday">Holiday</button>
            </div>

            <div class="col-md-4 mb-3">
              <button class="btn btn-warning ot-shift-btn w-100" type="submit" value="post">Post-shift Overtime</button>
            </div>
          </form>
        </div>
    </div>
  </div>
</div>