    <!-- WAITING SPINNER -->
    <div id="modal-wait" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-body">
                <div id="loader">
                    <div class="spinner-grow text-primary m-2" role="status"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- WAITING SPINNER -->
    <!-- SIGNATURE PAD -->
    <div id="modal-signaturepad" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content modal-filled bg-success">
                <div class="modal-body">
                    <form action="/admin/renting/bookings/create-new-agreement" method="POST">
                        @csrf
                        <div class="text-center">
                            <i class="dripicons-checkmark h1 text-white"></i>
                            <h4 class="mt-2 text-white">Sign Here!</h4>
                            <p class="mt-3 text-white" id="success-message"></p>
                            <div id="signature-pad-booking-id"></div>
                            <div style="text-align: center" id="sigpad">
                                <x-creagia-signature-pad />
                            </div>
                            <button type="button" class="btn btn-light my-2" id="signature-pad-cancel"
                                data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- SIGNATURE PAD -->
    <!-- SUCCESS ALERT -->
    <div id="modal-success" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content modal-filled bg-success">
                <div class="modal-body">
                    <div class="text-center">
                        <i class="dripicons-checkmark h1 text-white"></i>
                        <h4 class="mt-2 text-white">Success!</h4>
                        <p class="mt-3 text-white" id="success-message"></p>
                        <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- SUCCESS ALERT END -->
    <!-- CRITICAL ISSUES, ERRORs,  -->
    <div id="modal-error" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content modal-filled bg-danger">
                <div class="modal-body">
                    <div class="text-center">
                        <i class="dripicons-cross h1 text-white"></i>
                        <h4 class="mt-2 text-white">Error Occurred!</h4>
                        <p class="mt-3 text-white" id="error-message"></p>
                        <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- CRITICAL ISSUES, ERRORs,  -->
    <!-- ISSUES ALERTT RELATED MODAL -->
    <div id="modal-issue" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content modal-filled bg-info">
                <div class="modal-body">
                    <div class="text-center">
                        <i class="dripicons-warning h1 text-white"></i>
                        <h4 class="mt-2 text-white">Information...!</h4>
                        <p class="mt-3 text-white" id="issue-message"></p>
                        <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ISSUES ALERT RELATED MODAL -->
    <!-- INFO ALERTT RELATED MODAL -->
    <div id="modal-info" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content modal-filled bg-info">
                <div class="modal-body">
                    <div class="text-center">
                        <i class="dripicons-warning h1 text-white"></i>
                        <h4 class="mt-2 text-white">Information...!</h4>
                        <p class="mt-3 text-white" id="info-message"></p>
                        <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- INFO ALERT RELATED MODAL -->
    <!-- Success Alert Modal -->
    <div id="modal-finished" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content modal-filled bg-success">
                <div class="modal-body">
                    <div class="text-center">
                        <i class="dripicons-checkmark h1 text-white"></i>
                        <h4 class="mt-2 text-white">All Set!</h4>
                        <p class="mt-3 text-black">Booking Id:
                            <span id="finish-message-booking-id"></span>
                        </p>
                        <p class="mt-3 text-black">Invoice Number:
                            <span id="finish-message-tran-id"></span>
                        </p>
                        <p>DO NOT ISSUE MOTORBIKE UNLESS PAYMENT AND ALL DOCUMENTS RECEIVED & VERIFIED</p>
                        <p>Further this Booking could be managed in "Booking Management"</p>
                        <p>We Have Sent a link of Upload Document request to Customer via Email.</p>
                        <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal"
                            id="btnFinished">Done...!</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.modal success -->
    <!-- Modal Vehicle -->
    <div id="modal-motorbike" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center">YOU HAVE SELECTED</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding-top: 10px !important;">
                    <div class="col">

                    </div>
                    <p class="text-center mb-3" style="padding-top: 20px; font-weight: bold;">Select this vehicle?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success waves-effect waves-light"
                        id="btn-confirm-vehicle-selection">
                        YES
                    </button>
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal"
                        id="btn-cancel-vehicle-selection">
                        NO
                    </button>

                </div>
            </div>
        </div>
    </div>
    <!-- /.modal Vehicle -->
    <!-- Modal Customer -->
    <div id="modal-customer" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center">YOU HAVE SELECTED</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding-top: 10px !important;">
                    <div class="col">

                    </div>
                    <p class="text-center mb-3" style="padding-top: 20px; font-weight: bold;">Select this customer?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success waves-effect waves-light"
                        id="btn-confirm-customer-selection">
                        YES
                    </button>
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal"
                        id="btn-cancel-customer-selection">
                        NO
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- /.modal Modal Customer -->
    <!-- Modal Payment -->
    <div id="modal-paynow" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center">PAYNOW</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding-top: 10px !important;">
                    <div class="col">
                    </div>
                    <p class="text-center" style="padding-top: 20px; font-weight: bold;">Select Payment Method</p>

                    <select class="form-select mb-3" id="paymentdropdown">
                        <option selected>Payment Method</option>
                        <option value="1">Cash</option>
                        <option value="2">Card</option>
                    </select>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success waves-effect waves-light"
                        id="btn-confirm-pay-selection">
                        YES
                    </button>
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal"
                        id="btn-cancel-pay-selection">
                        NO
                    </button>
                </div>
            </div>
        </div>
    </div><!-- /.modal Modal PAYMENT -->
    <!-- BODY -->
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <!-- <h4 class="header-title mb-3">NEW BOOKING</h4> -->
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="nav flex-column nav-pills nav-pills-tab" id="v-pills-tab"
                                            role="tablist" aria-orientation="vertical">
                                            <a class="nav-link active show mb-1" id="tab-motorbike" data-bs-toggle="pill"
                                                href="#v-pills-BIKESELECTION" role="tab"
                                                style="font-size: 0.70rem !important;"
                                                aria-controls="v-pills-BIKESELECTION" aria-selected="true">
                                                MOTORBIKE</a>
                                            <a class="nav-link mb-1" id="tab-customer" data-bs-toggle="pill"
                                                href="#v-pills-CUSTOMERSELECTION" role="tab"
                                                style="font-size: 0.70rem !important;"
                                                aria-controls="v-pills-CUSTOMERSELECTION" aria-selected="false">
                                                CUSTOMER</a>
                                            <a class="nav-link mb-1" id="tab-agreement" data-bs-toggle="pill"
                                                href="#v-pills-AGREEMENT" role="tab"
                                                style="font-size: 0.70rem !important;" aria-controls="v-pills-AGREEMENT"
                                                aria-selected="false">
                                                AGREEMENT</a>
                                            <a class="nav-link mb-1" id="tab-payment" data-bs-toggle="pill"
                                                href="#v-pills-PAYMENT" role="tab"
                                                style="font-size: 0.70rem !important;" aria-controls="v-pills-PAYMENT"
                                                aria-selected="false">
                                                PAYMENT</a>
                                            <a class="nav-link mb-1" id="tab-documents" data-bs-toggle="pill"
                                                href="#v-pills-DOCS" role="tab"
                                                style="font-size: 0.70rem !important;" aria-controls="v-pills-DOCS"
                                                aria-selected="false">
                                                DOCUMENTS CHECKLIST</a>
                                            <!-- <a class="nav-link mb-1" id="tab-complete" data-bs-toggle="pill"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            href="#v-pills-DONE" role="tab" style="font-size: 0.70rem !important;"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            aria-controls="v-pills-DONE" aria-selected="false">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            DONE</a> -->
                                        </div>
                                    </div> <!-- end col-->
                                    <div class="col-sm-10">
                                        <div class="tab-content pt-0">
                                            <!-- MOTORBIKE TAB -->
                                            <div class="tab-pane fade active show" id="v-pills-BIKESELECTION"
                                                role="tabpanel" aria-labelledby="tab-motorbike">
                                                <!-- <p class="text-center">AVAILABLE MOTORBIKES</p> -->
                                                <div class="table-responsive">
                                                    <table class="table table-hover mb-0 inforce-font-size">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>REG</th>
                                                                <th>MAKE</th>
                                                                <th>MODEL</th>
                                                                <th>ENGINE CC</th>
                                                                <th>YEAR</th>
                                                                <th>MOT</th>
                                                                <th>ROAD TAX</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($motorbikes as $motorbike)
                                                                <tr class="motorbike-row">
                                                                    <td>{{ $motorbike->MOTORBIKE_ID }}</td>
                                                                    <td><span
                                                                            class="regplate">{{ $motorbike->REG_NO }}</span>
                                                                    </td>
                                                                    <td>{{ $motorbike->MAKE }}</td>
                                                                    <td>{{ $motorbike->MODEL }}</td>
                                                                    <td>{{ $motorbike->ENGINE }}</td>
                                                                    <td>{{ $motorbike->YEAR }}</td>
                                                                    <!-- Availability data is not fetched in the controller -->
                                                                    <td>
                                                                        <span
                                                                            class="badge {{ $motorbike->MOT_STATUS == 'Not valid' ? 'bg-danger' : 'bg-success inforce-padding' }}">
                                                                            {{ $motorbike->MOT_STATUS }}
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            class="badge {{ $motorbike->ROAD_TAX_STATUS_FLAG == 'Taxed' ? 'bg-success inforce-padding' : 'bg-danger' }}">
                                                                            {{ $motorbike->ROAD_TAX_STATUS }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <!-- CUSTOMER TAB -->
                                            <div class="tab-pane fade" id="v-pills-CUSTOMERSELECTION" role="tabpanel"
                                                aria-labelledby="tab-customer">
                                                <!-- <p class="text-center">CUSTOMERS</p> -->
                                                <div class="table-responsive">
                                                    <table class="table table-hover mb-0 inforce-font-size">
                                                        <thead>
                                                            <tr>
                                                                <th>id</th>
                                                                <th>FIRST NAME</th>
                                                                <th>LAST NAME</th>
                                                                <th>PHONE</th>
                                                                <th>EMAIL</th>
                                                                <th>DATE OF<br>BIRTH</th>
                                                                <th>ADDRESS</th>
                                                                <th>POSTCODE</th>
                                                                <th>CITY</th>
                                                                <th>NATIONALITY</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($customers as $customer)
                                                                <tr class="customer-row">
                                                                    <td>{{ $customer->id }}</td>
                                                                    <td>{{ $customer->first_name }}</td>
                                                                    <td>{{ $customer->last_name }}</td>
                                                                    <td>{{ $customer->phone }}</td>
                                                                    <td>{{ $customer->email }}</td>
                                                                    <td>{{ $customer->dob ? $customer->dob->format('Y-m-d') : '01-01-2024' }}
                                                                    </td>
                                                                    <td>{{ $customer->address }}</td>
                                                                    <td>{{ $customer->postcode }}</td>
                                                                    <td>{{ $customer->city }}</td>
                                                                    <td>{{ $customer->nationality }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <!-- PAYMENT TAB CONTENT -->
                                            <div class="tab-pane mt-3 fade" id="v-pills-PAYMENT" role="tabpanel"
                                                aria-labelledby="tab-payment">
                                                <h5>PAYMENT REVIEW</h5>
                                                <div class="payment-section">
                                                    <figcaption class="blockquote-footer">
                                                        last vehicle price updated on: <cite id="last-update"
                                                            title="updated at">updated
                                                            at</cite>
                                                    </figcaption>
                                                    <div class="table-responsive">
                                                        <table class="table table-striped mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th></th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td style="width:70%">
                                                                        <h3>WEEKLY RENTAL AMOUNT </h3>
                                                                    </td>
                                                                    <td>
                                                                        <h3>£<span id="weekly">0</span></h3>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <h3>DEPOSIT </h3>
                                                                    </td>
                                                                    <td>
                                                                        <h3>£<span id="deposit">0</span></h3>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <h2>TOTAL PAYABLE </h2>
                                                                    </td>
                                                                    <td>
                                                                        <h2>£<span id="total">0</span></h2>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <h3>PAID </h3>
                                                                    </td>
                                                                    <td>
                                                                        <h3>£<span id="paidamt">0</span></h3>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <h3>BALANCE </h3>
                                                                    </td>
                                                                    <td>
                                                                        <h3>£<span id="balance">0</span></h3>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <button class="btn-soft-success mt-3" name="btnReceiveAmount"
                                                        id="btnReceiveAmount">Received Amount</button>
                                                    <button class="btn-soft-info mt-3" name="btnPayLater"
                                                        id="btnPayLater">Pay Later</button>
                                                </div>
                                            </div>
                                            <!-- DOCUMENT CHECKLIST -->
                                            <div class="tab-pane fade" id="v-pills-DOCS" role="tabpanel"
                                                aria-labelledby="tab-documents">
                                                <h5>DOCUMENTS CHECKLIST</h5>
                                                <p>Upload all possible documents as available now. <br>Take a clear picture
                                                    of each document or upload from device</p>
                                                <div id="document-section">
                                                    <form id="documentUploadForm" action="" method="POST"
                                                        enctype="multipart/form-data">
                                                        @csrf <!-- CSRF token for security -->
                                                        <div class="row">
                                                            <!-- SOMETHING REMVD :) -->
                                                        </div>
                                                    </form>
                                                    <h5>MOTORBIKES DOCUMENTS</h5>
                                                    <form id="documentUploadFormMotorbike" action="" method="POST"
                                                        enctype="multipart/form-data">
                                                        @csrf <!-- CSRF token for security -->
                                                        <div class="row">
                                                            <!-- SOMETHING REMVD :) -->
                                                        </div>
                                                    </form>
                                                    <button class="btn-soft-success mt-3" name="btnDocsCompleted"
                                                        id="btnDocsCompleted">Send Document Request to Customer &
                                                        Finish</button>
                                                </div>
                                            </div>
                                            <!-- Agreement -->
                                            <div class="tab-pane fade" id="v-pills-AGREEMENT" role="tabpanel"
                                                aria-labelledby="tab-agreement">
                                                <h4>SEND AGREEMENT</h4>

                                                <div class="form-check d-inline-block">
                                                    <input type="checkbox" class="form-check-input" id="chkaccept">
                                                    <label class="form-check-label" for="customCheck3">I have
                                                        Checked Essential Documents</label>
                                                </div>
                                                <div class="mb-3">

                                                </div>
                                                <div id="qr-area"></div>
                                                <button class="btn-soft-info mt-1" name="btnGenQR" id="btnGenQR">Send
                                                    The
                                                    Agreement for Review & Sign</button>
                                                <button class="btn-soft-success mt-1 " name="btnNextPayTab"
                                                    id="btnNextPayTab">Next</button>
                                            </div>
                                        </div>
                                        <!-- COMPLETION -->
                                        <!-- <div class="tab-pane fade" id="v-pills-DONE" role="tabpanel"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        aria-labelledby="tab-complete">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="text-center">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <h2 class="mt-0"><i class="mdi mdi-check-all"></i></h2>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <h3 class="mt-0">Thank you !</h3>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <p class="w-75 mb-2 mx-auto">neguinhomotors.co.uk</p>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="mb-3">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="form-check d-inline-block">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <input type="checkbox" class="form-check-input"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        id="customCheck3">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <label class="form-check-label" for="customCheck3">I agree
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        with
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        the Terms and Conditions</label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div> -->
                                    </div>
                                </div> <!-- end col-->
                            </div> <!-- end row-->
                            <hr>
                            <!-- Quick View of Selection for new Booking -->
                            <div class="col-md-12 selection-section">
                                <div class="bookingcard text-white"
                                    style="background-color: #fffcfc; border-color: #333;">
                                </div>
                                <div class="motorbikecard text-white"
                                    style="background-color: #fffcfc; border-color: #333;"></div>
                                <div class="paymentcard text-white"
                                    style="background-color: #fffcfc; border-color: #333;">
                                </div>
                                <div class="customercard text-white"
                                    style="background-color: #fffcfc; border-color: #333;">
                                </div>
                            </div>
                        </div>
                    </div> <!-- end card-->
                </div> <!-- end col -->
            </div>
        </div> <!-- container -->
    </div> <!-- content -->
    </div>
