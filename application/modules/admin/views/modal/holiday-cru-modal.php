<div class="modal fade" id="holiday-cru-modal" tabindex="-1" role="dialog" aria-labelledby="holiday-cru-modal" aria-hidden="true">

  <div class="modal-dialog" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title text-white p-0 lh-0"><i class="fas fa-plus"></i> Custom Holiday</h5>

        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">

          <i class="fas fa-times text-white"></i>

        </button>

      </div>

      <form id="holiday-cru-form">

        <div class="modal-body">

          <div class="row input-con">

            <div class="col-md-3 offset-md-1"><label>Label:* </label></div>

            <div class="col-md-7">

              <input class="form-control" type="text" name="name">

            </div>

          </div>



          <div class="row input-con">

            <div class="col-md-3 offset-md-1"><label>Date:* </label></div>

            <div class="col-md-7">

              <input class="form-control" type="date" name="date">

            </div>

          </div>



          <div class="row input-con">

            <div class="col-md-3 offset-md-1"><label>Type:* </label></div>

            <div class="col-md-7">

              <select class="form-select" name="type">

                <option value="" disabled>--Select Type--</option>

                <option value="regular">Regular Holiday</option>

                <option value="special">Special Non-working Holiday</option>

                <option value="special-working">Special Working Holiday</option>

              </select>

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