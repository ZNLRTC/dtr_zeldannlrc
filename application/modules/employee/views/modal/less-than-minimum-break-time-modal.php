<div class="modal fade" id="less-than-minimum-break-time-modal" tabindex="-1" role="dialog" aria-labelledby="cancel-ongoing-dtr-modal" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title text-white p-0 lh-0">Opps!</h5>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <i class="fas fa-times text-white"></i>
            </button>
        </div>

        <div class="modal-body d-flex flex-column align-items-center">
            <img src="<?= base_url() ?>assets/img/sleep-time-cute.gif" alt="Sleep-time">
            <div class="text-left">
                <p class="pt-3">Minimum allowed break time:<br><span class="text-danger"><b>30 minutes.</b></span></p>
                <p class="mb-0 pb-0">Time remaining:<br><span class="text-success time-remaining"></span></p>
            </div>
            
        </div>
    
        <div class="modal-footer">
            <button type="button" data-bs-dismiss="modal" class="btn btn-danger">Close</button>
        </div>
    
    </div>
  </div>
</div>