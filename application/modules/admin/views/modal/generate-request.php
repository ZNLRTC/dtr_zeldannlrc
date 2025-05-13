<div class="modal fade" id="generate-request-modal" tabindex="-1" role="dialog" aria-labelledby="cancel-ongoing-dtr-modal" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white p-0 lh-0"><i class="fa-solid fa-file-pdf"></i> Generate PDF Request</i></h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fas fa-times text-white"></i>
        </button>
      </div>
    
        <div id="pdf-request-content">
            <div class="p-5">
                <div class="row align-items-center">
                    <div class="col-md-2"><img class="w-100" src="<?= base_url('assets/img/nlrc-logo.png') ?>"></div>
                    <div class="col-md-8 text-center">
                        <h6 class="mb-0 pb-0"><b>ZELDAN NORDIC LANGUAGE REVIEW TRAINING CENTER, OPC</b></h6>
                        <span>Rm 2F, Piao Yan Building, No. 50 Bonifacio Street, Baguio City 2600</span><br>
                        <span>Tel. No. (074) 420-9098 | Website: www.zeldannlrc.com</span>
                    </div>
                </div>
                <hr>
                <div class="req-title-container text-center mb-5">
                    <div class="req-leave-type"></div>
                </div>

                <div>
                    <p>
                        <span class="req-date"></span><br>
                        <i>Date</i>
                    </p>

                    <p>
                        <span><b>The Management</b></span><br>
                        <span>Zeldan Nordic Language Training Center OPC</span><br>
                        <span>Rm 2F Piao Yan Building</span><br>
                        <span>No. 50 Bonifacio Street</span><br>
                        <span>Baguio City 2600</span><br>
                    </p>
                </div>

                <div class="content">
                    <div class="row">
                        <div class="col-lg-12 mb-5">
                            <h6 class="mb-3 pb-0 border-bottom">Leave Details:</h6>
                            <table class="w-100 mb-3">
                                <thead class="text-left bg-lblue text-white">
                                    <th class="col-4">Name</th>
                                    <th class="col-2">Department</th>
                                    <th class="col-6">Date</th>
                                </thead>
                                <tbody class="bg-light">
                                    <tr class="text-left">
                                        <td class="req-name py-3 px-2"></td>
                                        <td class="req-department py-3 px-2"></td>
                                        <td class="req-leave-date py-3 px-2"></td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="req-reason"></div>
                        </div>
                        
                            <p class="truly-yours-p">Very truly yours, </p>
                        <div class="col-lg-4">
                            <div class="signature-container-employee"><img src="<?= base_url('assets/img/signatures/99.png') ?>"></div>
                            <div><span class="req-name border-top"></span></div>
                            <small><i>Employee</i></small>
                        </div>

                        <div class="col-lg-4">
                            <div class="signature-container-approver"><img src="<?= base_url('assets/img/signatures/65.png') ?>"></div>
                            <div><span class="req-conformed-by border-top"></span></div>
                            <small><i>Conformed By:</i></small>
                        </div>

                        <div class="col-lg-4">
                            <div class="signature-container"><img src="<?= base_url('assets/img/signatures/1.png') ?>"></div>
                            <div><span class="border-top"><b>Nestor A. Mestito</b></span></div>
                            <small><i>Approved By:</i></small>
                        </div>
                    </div>
                </div>
            </div>
            

        </div>



        
        <div class="modal-footer">
            <button type="submit" class="btn btn-success generate-pdf">Generate PDF</button>
            <button type="button" data-bs-dismiss="modal" class="btn btn-danger">Cancel</button>
        </div>
    
    </div>
  </div>
</div>