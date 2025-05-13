<div class="modal fade" id="archive-user-modal" tabindex="-1" role="dialog" aria-labelledby="archive-user-modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white p-0 lh-0"><i class="fas fa-archive"></i> Archive</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times text-white"></i>
        </button>
      </div>
      <div class="modal-body">

        <div class="row question-row text-center">
          <h4>
            Are you sure you want to reactivate?<br>
            <b class="t-maroon"></b>?
          </h4>
        </div>

      </div>
      <form id="archive-user-form">
        <div class="modal-footer">
          <div class="row w-100">
            <input type="hidden" name="user-id">
            <input type="hidden" name="action">
            <div class="col-md-4 offset-md-2">
              <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-sblue w-100">No</button>
            </div>
            <div class="col-md-4">
              <button type="submit" class="btn btn-sblue w-100">Yes</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>