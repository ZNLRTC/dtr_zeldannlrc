<div class="modal fade" id="add-undertime-modal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white p-0 lh-0"><i class="fas fa-plus"></i> Add Undertime</i></h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times text-white"></i>
        </button>
      </div>
    
      <form id="add-undertime-form">
        <div class="modal-body row">
          <div class="response"></div>

          <div class="form-group mb-3">
            <label class="form-label">Employee</label>
            <select class="form-select" name="employee">
              <option disabled selected>--Select Employee--</option>
              <?php foreach ($employees as $e): ?>
                <option value="<?= $e['id'] ?>"><?= $e['name'] ?></option>
              <?php endforeach ?>
            </select>
          </div>

          <div class="form-group mb-3">
            <label class="form-label">Date:</label>
            <input class="form-control" type="date" name="date">
          </div>

          <div class="col-md-6 form-group mb-3">
            <label class="form-label">Schedule Time-in:</label>
            <input class="form-control" type="time" name="sched-time-in">
          </div>

          <div class="col-md-6 form-group mb-3">
            <label class="form-label">Schedule Time-out:</label>
            <input class="form-control" type="time" name="sched-time-out">
          </div>

          <div class="col-md-6 form-group mb-3">
            <label class="form-label">Time-in:</label>
            <input class="form-control" type="time" name="time-in">
          </div>

          <div class="col-md-6 form-group mb-3">
            <label class="form-label">Break-in:</label>
            <input  class="form-control"type="time" name="break-in">
          </div>

          <div class="col-md-6 form-group mb-3">
            <label class="form-label">Break-out:</label>
            <input class="form-control" type="time" name="break-out">
          </div>

          <div class="col-md-6 form-group mb-3">
            <label class="form-label">Time-out:</label>
            <input class="form-control" type="time" name="time-out">
          </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-success">Add</button>
            <button type="button" data-bs-dismiss="modal" class="btn btn-danger">Cancel</button>
        </div>
      </form>
    
    </div>
  </div>
</div>