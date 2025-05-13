<div class="modal fade" id="add-leave-modal" tabindex="-1" role="dialog" aria-labelledby="add-leave-modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white p-0 lh-0"><i class="fa-solid fa-plus"></i> Add Leave</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times text-white"></i>
        </button>
      </div>

      <form id="add-leave-form">
        <div class="modal-body">
            <div class="response"></div>
            <div class="row input-con ">
                <div class="col-md-3 ">
                    <label>Type:* </label>
                </div>
                <div class="col-md-9">
                    <select class="form-select" name="leave-type" class="w-100 d-block">
                        <option value="0" disabled selected>---Select Leave Type---</option>
                        <option class="lt-vacation" value="vacation">Vacation Leave</option>
                        <option class="lt-sick" value="sick">Sick Leave</option>
                        <option class="lt-bday" value="birthday">Birthday Leave</option>
                        <option class="lt-special" value="special">Special Leave</option>
                        <option class="lt-maternity" value="maternity">Maternity Leave</option>
                        <option class="lt-paternity" value="paternity">Paternity Leave</option>
                        <option class="lt-bereavement" value="bereavement">Bereavement Leave</option>
                    </select>
                </div>
            </div>
          
            <div class="row input-con">
                <div class="col-md-3 ">
                    <label>Employee:* </label>
                </div>
                <div class="col-md-9 leave-employee-name">
                    <select class="form-select" name="employee-id" class="w-100">
                        <option value="0" selected disabled>---Select Employee---</option>
                        <?php foreach($employees as $emp): ?>
                            <option class="ei-<?= $emp['id'] ?>" value="<?= $emp['id'] ?>"><?= $emp['name'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
          

          <div class="row input-con ">
            <div class="col-md-3 ">
                <label>Dates:* </label>
            </div>
            <div class="col-md-9">
                <div class="d-flex">
                    <div class="form-group">
                        <label>From:</label>
                        <input class="form-control" type="date" name="leave-from">
                    </div>
                    <div class="form-group">
                        <label>To:</label>
                        <input class="form-control" type="date" name="leave-to">
                    </div>
                </div>
                <div class="mt-3 d-flex align-items-center">
                  <div class="me-3 d-flex align-items-center">
                    <input type="radio" id="whole-day-radio" value="1" name="whole-day-radio" checked>
                    <label class="form-label pointer mb-0 ms-1" for="whole-day-radio">Whole Day</label>
                  </div>

                  <div class="d-flex align-items-center">
                    <input type="radio" id="half-day-radio" value="0.5" name="whole-day-radio">
                    <label class="form-label pointer mb-0 ms-1" for="half-day-radio">Half Day</label>
                  </div>
                  
                </div>
            </div>
          </div>

          <div class="row input-con">
            <div class="col-md-3 ">
                <label>Reason/Details:* </label>
            </div>
            <div class="col-md-9">
              <textarea class="form-control" name="reason" rows="4" placeholder="Your Reason Here"></textarea>
            </div>
          </div>

          <div class="row input-con">
            <div class="col-md-3 ">
                <label>Remarks: </label>
            </div>
            <div class="col-md-9">
              <textarea class="form-control" name="remarks" placeholder="Your Remarks Here"></textarea>
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