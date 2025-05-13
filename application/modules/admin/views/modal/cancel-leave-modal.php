<div class="modal fade" id="cancel-leave-modal" tabindex="-1" role="dialog" aria-labelledby="cancel-leave-modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white p-0 lh-0"><i class="fas fa-times-circle"></i> Cancel Leave</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times text-white"></i>
        </button>
      </div>

      <div class="modal-body">
        <div class="row question-row text-center">
            <h4>
                Are you sure you want to cancel <span class="employee-name"></span>'s <span class="leave-type"></span> leave on <span class="leave-date"></span>?
            </h4>
        </div>
        <div class="response"></div>
      </div>

      <form id="cancel-leave-form">
        <div class="modal-footer">
          <div class="row w-100">
            <input type="hidden" name="leave-id">
            <div class="col-md-4 col-sm-6 offset-md-2">
              <button type="submit" class="btn btn-sblue w-100">Yes</button>
            </div>

            <div class="col-md-4 col-sm-6">
              <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-sblue w-100">No</button>
            </div>
          </div>
        </div>
      </form>

    </div>
  </div>
</div>