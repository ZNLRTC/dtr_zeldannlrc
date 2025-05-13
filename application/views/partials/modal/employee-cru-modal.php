<div class="modal fade" id="employee-cru-modal" tabindex="-1" role="dialog" aria-labelledby="employee-cru-modal" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white p-0 lh-0"></h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times text-white"></i>
        </button>
      </div>

      <form enctype="multipart/form-data">
        <div class="modal-body">
          <div class="row">
            <div class="row d-flex justify-content-center">
              <div class="col-md-10 offset-md-1 preview">
                <div class="preview-con" title="Click to upload a profile">
                  <img src="" alt="Upload Profile">
                </div>
              </div>
            </div>

            <div class="user-info-column <?= ($user_type == 'admin' || $user_type == 'secretary' || $user_type == 'supervisor' || $user_type == 'IT Administrator') ? 'col-md-6' : '' ?>">
              <h5 class="mb-0 pb-0 border-bottom offset-md-1 col-md-10">User Info:</h5>
              <input type="file" class="d-none" id="upload-profile" name="profile">
              <input type="text" class="d-none" name="filename">
              <input type="hidden" name="user-id">
              <div class="user-profile-response"></div>
              <div class="row input-con mt-3">
                <div class="col-md-3 offset-md-1"><label>Name: </label></div>
                <div class="col-md-7">
                  <input class="form-control" type="text" name="name">
                </div>
              </div>

              <div class="row input-con">
                <div class="col-md-3 offset-md-1"><label>Email: </label></div>
                <div class="col-md-7">
                  <input class="form-control" type="text" name="email">
                </div>
              </div>

              <div class="row input-con">
                <div class="col-md-3 offset-md-1"><label>Mobile #: </label></div>
                <div class="col-md-7">
                  <input class="form-control" type="text" name="mobile-number">
                </div>
              </div>

              <div class="row input-con">
                <div class="col-md-3 offset-md-1"><label>Gender: </label></div>
                <div class="col-md-7">
                  <select class="form-select" name="gender">
                    <option value="" disabled>--select gender--</option>  
                    <option value="Male">Male</option>  
                    <option value="Female">Female</option>  
                  </select>
                </div>
              </div>

              <div class="row input-con">
                <div class="col-md-3 offset-md-1"><label>Date of Birth: </label></div>
                <div class="col-md-7">
                  <input type="text" name="date-of-birth" class="date-format form-control" maxlength="10" placeholder="mm/dd/yyyy">
                </div>
              </div>

              <?php if($user_type == "admin" || $user_type == "qc-admin"): ?>
                <div class="row input-con role-row">
                  <div class="col-md-3 offset-md-1"><label>Role: </label></div>
                  <div class="col-md-7">
                    <select class="form-select" name="role">
                      <option value="" disabled>--select role--</option>
                      <?php
                        $role = $this->users_type_model->get_all();
                        foreach($role as $role):
                          echo "<option value='{$role['id']}'>".ucfirst($role['user_type'])."</option>";
                        endforeach;
                      ?>
                    </select>
                  </div>
                </div>


                <div class="row input-con">
                  <div class="col-md-3 offset-md-1"><label>Branch: </label></div>
                  <div class="col-md-7">
                    <select class="form-select" name="branch">
                      <option value="Baguio City">Baguio City</option>  
                      <option value="Quezon City">Quezon City</option>  
                    </select>
                  </div>
                </div>

                <div class="row input-con salary-grade-row d-none">
                  <div class="col-md-3 offset-md-1"><label>Salary Grade: </label></div>
                  <div class="col-md-7">
                    <select class="form-select" name="salary-grade">
                      <option value="" disabled>--select salary grade--</option>
                      <?php
                        $salary_grade = $this->salary_grade_model->get_all();
                        foreach($salary_grade as $salary_grade): ?>
                          <option value="<?= $salary_grade['id'] ?>" <?= $salary_grade['id'] == '3' ? 'selected' : '' ?>> <?= $salary_grade['grade_number'] .'-'. $salary_grade['hourly_rate'] ?>/hour </option>
                        <?php endforeach ?>
                    </select>
                  </div>
                </div>

              <?php endif; ?>

              <div class="row input-con username-row">
                <div class="col-md-3 offset-md-1"><label>Username: </label></div>
                <div class="col-md-7">
                  <input class="form-control" type="text" name="username">
                </div>
              </div>

              <div class="row input-con password-row">
                <div class="col-md-3 offset-md-1"><label>Password: </label></div>
                <div class="col-md-7">
                  <input class="form-control" type="password" name="password">
                </div>
              </div>
              
              <!-- button -->
              <div class="row input-con emp-cru-update-profile-container d-none">
                <div class="col-md-7 offset-md-4 text-right">
                  <button type="submit" class="btn btn-sblue emp-cru-update-profile-btn">Save Profile</button>
                </div>
              </div>
            </div>

            <?php if($user_type == 'admin' || $user_type == 'Secretary' || $user_type == 'supervisor' || $user_type == 'IT Administrator'): ?>
              <div class="col-md-6 schedule-info-column">
                <h5 class="mb-0 pb-0 border-bottom col-md-11">Fixed Schedule:</h5>
                <div class="user-fixed-schedule-response"></div>
                <div class="row input-con mt-3">
                  <div class="col-md-2 text-right"><label>Monday: </label></div>
                  <div class="col-md-6 d-flex align-items-center">
                    <input class="form-control w-50 me-1" type="time" name="schedule-monday-in" required> - <input class="form-control w-50 ms-1" type="time" name="schedule-monday-out">
                  </div>
                  <div class="col-md-3 d-flex align-items-center justify-content-between">
                    <div class="form-check mb-0">
                      <input class="form-check-input" type="checkbox" value="WFH" name="monday-workbase" id="monday-workbase-wfh">
                      <label for="monday-workbase-wfh" class="pointer form-check-label">WFH</label> 
                    </div>
                    <div class="form-check mb-0">
                      <input class="form-check-input" type="checkbox" value="Office" name="monday-workbase" id="monday-workbase-office">
                      <label for="monday-workbase-office" class="pointer form-check-label">Office</label> 
                    </div>
                  </div>
                </div>

                <div class="row input-con mt-3">
                  <div class="col-md-2 text-right"><label>Tuesday: </label></div>
                  <div class="col-md-6 d-flex align-items-center">
                    <input class="form-control w-50 me-1" type="time" name="schedule-tuesday-in"> - <input class="form-control w-50 ms-1" type="time" name="schedule-tuesday-out">
                  </div>
                  <div class="col-md-3 d-flex align-items-center justify-content-between">
                    <div class="form-check mb-0">
                      <input class="form-check-input" type="checkbox" value="WFH" name="tuesday-workbase" id="tuesday-workbase-wfh">
                      <label for="tuesday-workbase-wfh" class="pointer form-check-label">WFH</label> 
                    </div>
                    <div class="form-check mb-0">
                      <input class="form-check-input" type="checkbox" value="Office" name="tuesday-workbase" id="tuesday-workbase-office">
                      <label for="tuesday-workbase-office" class="pointer form-check-label">Office</label> 
                    </div>
                  </div>
                </div>

                <div class="row input-con mt-3">
                  <div class="col-md-2 text-right"><label>Wednesday: </label></div>
                  <div class="col-md-6 d-flex align-items-center">
                    <input class="form-control w-50 me-1" type="time" name="schedule-wednesday-in"> - <input class="form-control w-50 ms-1" type="time" name="schedule-wednesday-out">
                  </div>
                  <div class="col-md-3 d-flex align-items-center justify-content-between">
                    <div class="form-check mb-0">
                      <input class="form-check-input" type="checkbox" value="WFH" name="wednesday-workbase" id="wednesday-workbase-wfh">
                      <label for="wednesday-workbase-wfh" class="pointer form-check-label">WFH</label> 
                    </div>
                    <div class="form-check mb-0">
                      <input class="form-check-input" type="checkbox" value="Office" name="wednesday-workbase" id="wednesday-workbase-office">
                      <label for="wednesday-workbase-office" class="pointer form-check-label">Office</label> 
                    </div>
                  </div>
                </div>

                <div class="row input-con mt-3">
                  <div class="col-md-2 text-right"><label>Thursday: </label></div>
                  <div class="col-md-6 d-flex align-items-center">
                    <input class="form-control w-50 me-1" type="time" name="schedule-thursday-in"> - <input class="form-control w-50 ms-1" type="time" name="schedule-thursday-out">
                  </div>
                  <div class="col-md-3 d-flex align-items-center justify-content-between">
                    <div class="form-check mb-0">
                      <input class="form-check-input" type="checkbox" value="WFH" name="thursday-workbase" id="thursday-workbase-wfh">
                      <label for="thursday-workbase-wfh" class="pointer form-check-label">WFH</label> 
                    </div>
                    <div class="form-check mb-0">
                      <input class="form-check-input" type="checkbox" value="Office" name="thursday-workbase" id="thursday-workbase-office">
                      <label for="thursday-workbase-office" class="pointer form-check-label">Office</label> 
                    </div>
                  </div>
                </div>

                <div class="row input-con mt-3">
                  <div class="col-md-2 text-right"><label>Friday: </label></div>
                  <div class="col-md-6 d-flex align-items-center">
                    <input class="form-control w-50 me-1" type="time" name="schedule-friday-in"> - <input class="form-control w-50 ms-1" type="time" name="schedule-friday-out">
                  </div>
                  <div class="col-md-3 d-flex align-items-center justify-content-between">
                    <div class="form-check mb-0">
                      <input class="form-check-input" type="checkbox" value="WFH" name="friday-workbase" id="friday-workbase-wfh">
                      <label for="friday-workbase-wfh" class="pointer form-check-label">WFH</label> 
                    </div>
                    <div class="form-check mb-0">
                      <input class="form-check-input" type="checkbox" value="Office" name="friday-workbase" id="friday-workbase-office">
                      <label for="friday-workbase-office" class="pointer form-check-label">Office</label> 
                    </div>
                  </div>
                </div>
                
                <!-- button -->
                <div class="row input-con emp-cru-update-schedule-container d-none">
                  <div class="col-md-7 offset-md-4 text-right">
                    <button class="btn btn-sblue emp-cru-update-schedule-btn">Save Schedule</button>
                  </div>
                </div>
                
                <div class="temporary-schedule-main-con">
                  <h5 class="mb-0 pb-0 border-bottom col-md-11">Temporary Schedule <small>(Optional)</small>:</h5>
                  <div class="user-temporary-schedule-response"></div>
                  <div class="row input-con mt-3">
                    <div class="col-md-8 offset-md-2 d-flex">
                      <div class="form-check mb-0 me-3">
                        <input class="form-check-input" type="checkbox" value="WFH" name="temp-workbase" id="temp-workbase-wfh">
                        <label for="temp-workbase-wfh" class="pointer form-check-label">WFH</label> 
                      </div>
                      <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" value="Office" name="temp-workbase" id="temp-workbase-office">
                        <label for="temp-workbase-office" class="pointer form-check-label">Office</label> 
                      </div>
                    </div>
                  </div>

                  <div class="row input-con mt-3">
                    <div class="col-md-2 text-right"><label>Date: </label></div>
                    <div class="col-md-9 d-flex align-items-center">
                      <input class="form-control me-1" type="date" name="temp-schedule-date-from"> - <input class="form-control ms-1" type="date" name="temp-schedule-date-to">
                    </div>
                  </div>
                  
                  <div class="row input-con mt-3">
                    <div class="col-md-2 text-right"><label>Time: </label></div>
                    <div class="col-md-9 d-flex align-items-center">
                      <input class="form-control w-50 me-1" type="time" name="temp-schedule-in"> - <input class="form-control w-50 ms-1" type="time" name="temp-schedule-out">
                    </div>
                  </div>
                  
                  <!-- button -->
                  <div class="row input-con emp-cru-save-temp-schedule-container d-none">
                    <div class="col-md-7 offset-md-4 text-right">
                      <button type="submit" class="btn btn-sblue emp-cru-save-temp-schedule-btn">Save Schedule</button>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-11">
                    <table id="temporary-schedule-table" class="w-100 table-striped d-none">
                      <thead>
                        <tr>
                          <th>Date</th>
                          <th>Time In</th>
                          <th>Time Out</th>
                          <th>Area</th>
                          <th class="text-center">Action</th>
                        </tr>
                      </thead>

                      <tbody>
                        
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            <?php endif ?>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-sblue emp-cru-main-submit-btn">Submit</button>
        </div>

      </form>
    </div>
  </div>
</div>