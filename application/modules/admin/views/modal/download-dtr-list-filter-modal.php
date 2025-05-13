<div class="modal fade" id="download-dtr-list-filter-modal" tabindex="-1" role="dialog" aria-labelledby="download-dtr-list-filter-modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white p-0 lh-0"><i class="fas fa-file-excel"></i> Download DTR List</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times text-white"></i>
        </button>
      </div>
      <form id="download-dtr-list-filter-form">
        <div class="modal-body">
          <div class="row input-con">
            <div class="col-md-7 offset-md-4 text-center bg-lblue text-white">
              <b>Filter</b>
            </div>

            </div><div class="row input-con">
              <div class="col-md-3 offset-md-1"><label>From:* </label></div>
              <div class="col-md-7 p-0">
                <input class="form-control" type="date" name="start-date" max="2099-12-31">
              </div>
            </div>

            <div class="row input-con">
              <div class="col-md-3 offset-md-1"><label>To:* </label></div>
              <div class="col-md-7 p-0">
                <input class="form-control" type="date" name="end-date" max="2099-12-31">
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-sblue w-100">Download</button>
        </div>
      </form>
    </div>
  </div>
</div>