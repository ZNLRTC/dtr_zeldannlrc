<div class="modal fade" id="update-password-modal" tabindex="-1" role="dialog" aria-labelledby="update-password-modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white p-0 lh-0"><i class="fas fa-lock"></i> Update Password</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times text-white"></i>
        </button>
      </div>
      <form id="update-password-form">
        <div class="modal-body">

          <div class="row input-con current-row">
            <div class="col-md-3 offset-md-1"><label>Current:* </label></div>
            <div class="col-md-7">
              <input type="password" name="current-password">
            </div>
          </div>

          <div class="row input-con">
            <div class="col-md-3 offset-md-1"><label>New:* </label></div>
            <div class="col-md-7">
              <input type="password" name="new-password">
            </div>
          </div>

          <div class="row input-con confirm-row">
            <div class="col-md-3 offset-md-1"><label>Confirm:* </label></div>
            <div class="col-md-7">
              <input type="password" name="confirm-password">
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