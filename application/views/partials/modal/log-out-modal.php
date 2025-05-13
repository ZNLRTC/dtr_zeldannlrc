<div class="modal fade" id="log-out-modal" tabindex="-1" role="dialog" aria-labelledby="log-out-modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white p-0 lh-0"><i class="fas fa-power-off"></i></h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times text-white"></i>
        </button>
      </div>

      <div class="modal-body">
        <div class="row question-row text-center">
          <h4>
            Are you sure you want to log-out?
          </h4>
        </div>
      </div>

      <div class="modal-footer">
        <div class="row w-100">
          <div class="col-md-4 offset-md-2 col-sm-6">
            <a href="<?= base_url("logout") ?>" class="btn btn-sblue w-100">Yes</a>
          </div>
          <div class="col-md-4 col-sm-6">
            <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-sblue w-100">No</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>