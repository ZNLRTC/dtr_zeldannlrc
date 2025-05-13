<div class="modal fade" id="delete-undertime-modal" tabindex="-1" role="dialog" aria-labelledby="delete-undertim-modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white p-0 lh-0"><i class="fas fa-trash"></i> Delete undertime?</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times text-white"></i>
        </button>
      </div>
      <div class="modal-body">
        <div class="row question-row text-center">
          <div class="col-md-10 offset-md-1">
            <h4>
              Are you sure you want to delete this undertime record?
            </h4>
          </div>
        </div>

        <div class="response"></div>

      </div>
      <form id="delete-undertime-form">
        <div class="modal-footer">
          <input type="hidden" name="ut-id">
          <div class="row w-100">
            <div class="col-md-4 offset-md-2">
              <button type="submit" class="btn btn-sblue w-100">Yes</button>
            </div>
            <div class="col-md-4">
              <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-sblue w-100">No</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>