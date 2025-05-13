<div class="modal fade" id="dtr-cru-modal" tabindex="-1" role="dialog" aria-labelledby="dtr-cru-modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white p-0 lh-0"></h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times text-white"></i>
        </button>
      </div>
      <form id="create-dtr-form">
        <div class="modal-body">
          <div class="row input-con">
            <div class="col-md-3 offset-md-1"><label>Date:* </label></div>
            <div class="col-md-7">
              <input class="form-control" type="date" name="date" max="2099-12-31" placeholder="mm/dd/yyyy">
            </div>
          </div>
          <div class="response"></div>
          <input type="hidden" name="user-id">
          <input type="hidden" name="per-hour">
          <div class="row input-con">
            <div class="col-md-3 offset-md-1 d-flex align-self-start"><label>Work Base:* </label></div>
            <div class="col-md-7 d-flex justify-content-between">

              <div class="form-check me-3">
                <input class="form-check-input py-0" type="radio" value="Office" id="work-base-office" name="work-base" checked>
                <label class="form-check-label pointer d-flex align-items-center" for="work-base-office">Office</label>
              </div>

              <div class="form-check me-3">
                <input class="form-check-input py-0" type="radio" value="WFH" id="work-base-home" name="work-base">
                <label class="form-check-label pointer" for="work-base-home">WFH</label>
              </div>

              <div class="form-check">
                <input class="form-check-input py-0" type="radio" value="WFH/Office" id="work-base-both" name="work-base">
                <label class="form-check-label pointer" for="work-base-both">WFH/Office</label>
              </div>

            </div>
          </div>

          <div class="row input-con">
            <div class="col-md-3 offset-md-1"><label>Time-in:* </label></div>
            <div class="col-md-7">
              <div class="d-flex justify-content-center">
                <input class="form-control" type="time" min="00:00" max="24:00" name="time-in">
              </div>
            </div>
          </div>

          <div class="row input-con break-row d-none">
            <div class="col-md-3 offset-md-1"><label>Break: </label></div>
            <div class="col-md-7">
              <div class="cs-form break-div d-flex align-items-center">
                <input class="form-control w-50" type="time" min="00:00" max="24:00" name="break-in">
                &nbsp;&nbsp;-&nbsp;&nbsp;
                <input class="form-control w-50" type="time" min="00:00" max="24:00" name="break-out">
              </div>
            </div>
          </div>

          <div class="row input-con eod-con mb-0 d-none">
            <div class="col-md-3 offset-md-1 d-flex align-self-start"><label>End of day:* </label></div>
            <div class="col-md-7 cs-form">
              <textarea class="form-control" name="end-of-day" rows="5"></textarea>
            </div>
          </div>

          <div class="row input-con time-out-row d-none mt-3">
            <div class="col-md-3 offset-md-1"><label>Time-out:* </label></div>
            <div class="col-md-7">
              <div class="d-flex justify-content-center">
               <input class="form-control" type="time" min="00:00" max="24:00" name="time-out">
              </div>
            </div>
          </div>

          <!-- <div class="row input-con">
            <div class="col-md-3 offset-md-1 d-flex align-self-start"><label>Work Base:* </label></div>
            <div class="col-md-7">
              <select name="work-base" class="form-select">
                <option value="">---Select---</option>
                <option value="Office">Office</option>
                <option value="WFH">WFH</option>
                <option value="WFH/Office">WFH/Office</option>
              </select>
            </div>
          </div> -->

          <div class="row input-con ot-cb-row d-none">
            <div class="col-md-5 offset-md-6">
              <div class="w-100 check-con d-flex justify-content-end align-items-center mt-2">
                <input type="checkbox" class="w-auto" value="overtime" name="cb-overtime" id="cb-overtime">
                <label for="cb-overtime">&nbsp;Overtime</label>
              </div>
            </div>
          </div>

          <div class="row input-con overtime-row d-none">
            <div class="col-md-3 offset-md-1"><label>Time:* </label></div>
            <div class="col-md-7">
              <div class="d-flex align-items-center ot-div cs-form">
                <input class="form-control" type="time" min="00:00" max="24:00" name="ot-in">
                &nbsp;&nbsp;-&nbsp;&nbsp;
                <input class="form-control" type="time" min="00:00" max="24:00" name="ot-out">
              </div>
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