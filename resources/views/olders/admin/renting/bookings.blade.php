@extends('layouts.admin')

@section('content')
    <style>
        .middleleft {
            vertical-align: middle;
            text-align: left;
        }

        .closing-center-middle {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .border-now {
            border: 0.4px solid #ec5555;
            padding: 6px;
            margin: 6px;
            font-size: 13px;
            font-weight: 700;
        }

        .document-upload {
            display: flex;
            flex-direction: column;
            justify-content: start;
            align-items: left;
            height: 90px;
            padding: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            border-radius: 1px;
            background-color: #f9f9f9;
            margin-bottom: 4px;
        }

        .document-upload .form-label {
            /* margin-bottom: 0px; */
            text-align: left;
            word-wrap: break-word;
        }

        .badge {
            display: block;
            margin-bottom: 5px;
        }

        .form-check {
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .form-check-input {
            margin-top: 0;
            margin-right: 0px;
            width: 20px;
            height: 20px !important;
        }

        .form-check-label {
            text-align: left;
        }

        .table-font {
            font-size: 12px;
        }

        /* Invoice Accordion Styles */
        .invoice-row-clickable {
            transition: background-color 0.2s ease;
        }

        .invoice-row-clickable:hover {
            background-color: #f0f0f0 !important;
        }

        .invoice-accordion-row {
            background-color: #f8f9fa;
        }

        .invoice-accordion-content {
            padding: 15px;
        }

        .invoice-accordion-content h5 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .invoice-accordion-content h6 {
            color: #555;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .invoice-accordion-content .table-sm {
            font-size: 13px;
        }

        .invoice-accordion-content .table-sm td {
            padding: 8px;
        }
    </style>

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
    {{-- MODAL - Confirm PAYMENT of Additional charges --}}
    <!-- Modal Amount Confirmation -->
    <div id="modal-amount-confirm" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center">CONFIRM PAYMENT RECEIVED</h4>
                </div>
                <div class="modal-body text-center">
                    <p>Have you received the Amount?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="btn-confirm-additional-amount">YES</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">NO</button>
                </div>
            </div>
        </div>
    </div>
    {{-- MODAL - Confirm PAYMENT of Additional charges --}}
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
                    <button type="button" class="btn btn-success waves-effect waves-light" id="btn-confirm-pay-selection">
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
    <!-- Modal Document Confirm -->
    <div id="modal-document-confirm" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center">CONFIRM DOCUMENTS</h4>
                </div>
                <div class="container" style="padding:4px">
                    <p>Have all the necessary documents been thoroughly reviewed?</p>
                    <p>Have all documents been cross-referenced with our customer's details to ensure accuracy?</p>
                    <p>Can you confirm that the insurance policy and certificates are valid and cover the relevant period?
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success waves-effect waves-light" id="btn-confirm-documents">
                        YES
                    </button>
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal"
                        id="btn-cancel-document">
                        NO
                    </button>
                </div>
            </div>
        </div>
    </div><!-- /. Modal Document Confirm -->
    <!-- Modal Document Request Link -->
    <div id="modal-document-request-link" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center">DOCUMENT REQUEST LINK</h4>
                </div>
                <div class="container" style="padding:4px">
                    <p>MAKE SURE EMAIL IN DATABASE IS CORRECT OR RIGHT ONE.</p>
                    <p>JUST IN CASE IF EMAIL IS NOT CORRECT UPDATE IT IN CUSTOMER SECTION BEFORE RE-REQUEST LINK.</p>
                    <p>PRESS YES IF YOU WANT TO SEND LINK TO CUSTOMER.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success waves-effect waves-light" id="btn-request-documents">
                        YES
                    </button>
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal"
                        id="btn-cancel-document">
                        NO
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- /. Modal Document Request Link -->
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <h3>BOOKING MANAGEMENT</h3>
                <div class="card">
                    <div class="card-body">
                        <div class="dropdown float-end">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- Dynamic dropdown items could be added here based on requirements -->
                            </div>
                        </div>
                        <p class="text-muted font-14 mb-3">
                            All Bookings
                        </p>
                        <div style="display: flex; flex-direction: column; align-items: flex-start;">
                            <div class="col-md-6" style="display: flex; align-items: center; margin: 6px;">
                                <div class="stateReadyColor-square"></div>
                                <span style="font-size: 12px; font-weight: 700;">ALL COMPLIANCE OBLIGATIONS COMPLIED WITH &
                                    GOOD TO GO</span>
                            </div>
                            <div class="col-md-6" style="display: flex; align-items: center; margin: 6px;">
                                <div class="stateStopColor-square"></div>
                                <span style="font-size: 12px; font-weight: 700;">STOP AS COMPLIANCE ARE NOT YET
                                    FINISHED</span>
                            </div>
                            <div class="col-md-6" style="display: flex; align-items: center; margin: 6px;">
                                <div class="stateGoColor-square"></div>
                                <span style="font-size: 12px; font-weight: 700;">ISSUED TO CUSTOMER</span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <div class="search-box">
                                <label for="regFilterInput">Search by REG No:</label>
                                <input type="text" id="regFilterInput" placeholder="Enter REG No."
                                    class="form-control">
                            </div>
                            <table class="table mb-0 table-font">
                                <thead>
                                    <tr class="tr-booking-list">

                                        <th></th>
                                        <th>ITEM_ID</th>
                                        <th>B. ID</th>
                                        <th>STATUS</th>
                                        <th>REG</th>
                                        <th>CUSTOMER NAME</th>
                                        <th>PHONE</th>
                                        <th>EMAIL</th>
                                        <th>BOOKING DATE</th>
                                        <th>WEEKLY RENT</th>
                                        <th>END DATE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- COLOR CODING CAME FROM HERE --}}
                                    @foreach ($bookingDetails as $booking)
                                        @php
                                            $stateClass = '';
                                            $stateClass_square = '';
                                            $today = new DateTime();
                                            $dueDate = new DateTime($booking->NEXT_DUE_DATE);
                                            $interval = $today->diff($dueDate);
                                            $daysUntilDue = $interval->days;
                                            $isDueInFourDays = $interval->invert == 0 && $daysUntilDue <= 3;
                                            $isDueInThreeDays = $interval->invert == 0 && $daysUntilDue <= 3;

                                            $isDueInSevenDays =
                                                $interval->invert == 0 && $daysUntilDue > 4 && $daysUntilDue <= 7;
                                            $isDueInTwoDays = $interval->invert == 0 && $daysUntilDue <= 1;
                                            $rowClass = '';

                                            if ($isDueInTwoDays) {
                                                //$rowClass = 'bg-light text-dark';
                                                // $rowClass = 'bg-danger text-white';
                                            } elseif ($isDueInFourDays) {
                                                //  $rowClass = 'bg-light text-dark';
                                            } elseif ($isDueInThreeDays) {
                                                //  $rowClass = 'bg-info text-dark';
                                            } elseif ($isDueInSevenDays) {
                                                // $rowClass = 'text-black';
                                            } else {
                                                $rowClass = $loop->iteration % 2 == 0 ? 'table-active' : '';
                                            }

                                            if (
                                                $booking->RBSTATE == 'Awaiting Documents & Payment' ||
                                                $booking->RBSTATE == 'Awaiting Documents' ||
                                                $booking->RBSTATE == 'Awaiting Payment'
                                            ) {
                                                $stateClass = 'stateStopColor';
                                                $stateClass_square = 'stateStopColor-square';
                                            } elseif ($booking->RBSTATE == 'Completed') {
                                                $stateClass = 'stateReadyColor';
                                                $stateClass_square = 'stateReadyColor-square';
                                            } elseif ($booking->RBSTATE == 'Completed & Issued') {
                                                $stateClass = 'stateGoColor';
                                                $stateClass_square = 'stateGoColor-square';
                                            }
                                        @endphp
                                        <tr class="{{ $rowClass }} booking-row">
                                            <td class="middleleft">
                                                <div class="{{ $stateClass_square }}" style="padding: 16px; margin: 2px;">
                                                </div>
                                            </td>
                                            {{-- 'RBI.id as BOOKING_ITEM_ID', --}}
                                            <td class="middleleft">{{ $booking->BOOKING_ITEM_ID }}</td>
                                            <td class="middleleft">{{ $booking->BOOKING_ID }}</td>
                                            <td class="middleleft"><span
                                                    class="{{ $stateClass }} ">{{ $booking->RBSTATE }}</span></td>
                                            <td class="middleleft"><span class="regplate">{{ $booking->REG_NO }}</span>
                                            </td>
                                            <td class="middleleft">{{ $booking->FIRST_NAME }} {{ $booking->LAST_NAME }}
                                            </td>
                                            <td class="middleleft">{{ $booking->PHONE }}</td>
                                            <td class="middleleft">{{ $booking->EMAIL }}</td>
                                            <td class="middleleft">{{ $booking->BOOKING_Date }}</td>
                                            <td class="middleleft">{{ $booking->WEEKLY_RENT }}</td>
                                            <td class="middleleft">{{ $booking->END_DATE ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- SECOND SECTION Where the Details of Each bookings -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-4">BOOKING VIEW</h4>
                        <ul class="nav nav-pills navtab-bg nav-justified">
                            {{-- Document Tab Button --}}
                            <li class="nav-item">
                                <a href="#DOCUMENTS_TAB" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                    DOCUMENTS
                                </a>
                            </li>
                            {{-- Payment Tab Button --}}
                            <li class="nav-item">
                                <a href="#PAYMENT_TAB" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                    PAYMENTS
                                </a>
                            </li>
                            {{-- Issuance Tab Button --}}
                            <li class="nav-item">
                                <a href="#ISSUANCE_TAB" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                    ISSUANCE
                                </a>
                            </li>
                            {{-- Other Charges Button --}}
                            <li class="nav-item">
                                <a href="#OTHER_TAB" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                    CHARGES
                                </a>
                            </li>
                            {{-- Closing / Cancel tab Button --}}
                            <li class="nav-item">
                                <a href="#ENDBOOKING_TAB" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                    CLOSING
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <!-- DOCUMENT TAB -->
                            <div class="tab-pane" id="DOCUMENTS_TAB">
                                <p>DOCUMENT TAB CONTENT</p>
                                <div class="row">
                                </div>
                                <button class="btn-soft-success mt-3" name="btnDocumentsComplete"
                                    style="height: 65px; width: 145px;" id="btnDocumentsComplete">Documents
                                    Completed</button>
                                <button class="btn-soft-success mt-3" name="btnResendDocLink"
                                    style="height: 65px; width: 145px;" id="btnResendDocLink">Resend Document
                                    Link</button>

                            </div>
                            <!-- DOCUMENT TAB -->
                            <!-- PAYMENT TAB -->
                            <div class="tab-pane" id="PAYMENT_TAB">
                                <p id="payment-heading">PAYMENTS</p>
                                <div id="payment-invoices">

                                </div>
                                <div class="payment-section">
                                    <div class="table-responsive">
                                        {{-- <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="width:70%">
                                                        <h3> WEEKLY RENTAL AMOUNT </h3>
                                                    </td>
                                                    <td>
                                                        <h3>£<span id="weekly">0</span></h3>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <h3> DEPOSIT </h3>
                                                    </td>
                                                    <td>
                                                        <h3>£<span id="deposit">0</span></h3>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <h2> TOTAL PAYABLE </h2>
                                                    </td>
                                                    <td>
                                                        <h2>£<span id="total">0</span></h2>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <h3> PAID </h3>
                                                    </td>
                                                    <td>
                                                        <h3>£<span id="paidamt">0</span></h3>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <h3> BALANCE </h3>
                                                    </td>
                                                    <td>
                                                        <h3>£<span id="balance">0</span></h3>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table> --}}
                                    </div>
                                    <button class="btn-soft-success mt-3" name="btnReceiveAmount"
                                        id="btnReceiveAmount">Received
                                        Amount</button>
                                </div>
                            </div>
                            <!-- PAYMENT TAB -->
                            <!-- ISSUANCE TAB -->
                            <div class="tab-pane" id="ISSUANCE_TAB">
                                <div class="container">
                                </div>
                                <div id="issuance-tab">
                                    <div id="motorbike-issuance">
                                    </div>
                                </div>
                            </div>
                            <!-- ISSUANCE TAB -->
                            <!-- OTHER Charges TAB -->
                            <div class="tab-pane" id="OTHER_TAB">
                                <div class="container">
                                </div>
                                <div id="other-tab">
                                    <div id="other-tab">
                                        <h4>ADDITIONAL CHARGES</h4>
                                        <p><i>Any Additional amount on top of rent (i.e.,
                                                Damages). note: PCN must be
                                                updates on /ngn-admin/</i></p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="booking-id">BOOKING ID:</label>
                                                <span id="booking-id"></span>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="booking-iid-reg">REG. NO:</label>
                                                <span id="booking-iid-reg"></span>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="textbox">Description of Additional
                                                    Charges:</label>
                                                <input type="text" id="other-item-desc" class="form-control"
                                                    placeholder="Enter text">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="numeric-textbox">Enter
                                                    Amount:</label>
                                                <input type="number" id="other-item-amount" class="form-control"
                                                    placeholder="Enter Amount">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button id="btn-other-item" class="btn btn-success">Add</button>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button id='getOtherCharges' class="btn btn-info">LOCATE PENDING
                                                    CHARGES</button>
                                            </div>
                                        </div>
                                        <br>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12" id='load-other-item-data'>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- OTHER Charges TAB -->
                            <!-- CLOSING TAB -->
                            <div class="tab-pane" id="ENDBOOKING_TAB">
                                <h4>CLOSING CONTRACT</h4>
                                <div id="booking-closing-tab">
                                    <div id="motorbike-closing">
                                        <div class="container">
                                            {{-- STEP ONE - NOTICE PERIOD --}}
                                            <div class="row">
                                                <div class="col-md-1"
                                                    style="background-color: rgb(86, 123, 84); text-align: center;">
                                                    <label for="notice-period"
                                                        style="color: white; font-weight: bold; font-size: 18px; display: flex; align-items: center; justify-content: center; height: 100%;">1</label>
                                                </div>
                                                <div class="col-md-1">
                                                    <label for="notice-period" class="closing-center-middle">Notice
                                                        Period:</label>
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="text" id="notice-details" class="form-control"
                                                        placeholder="Enter Any Details">
                                                </div>
                                                <div class="col-md-1">
                                                    <input type="checkbox" id="notice-checkbox"
                                                        class="closing-center-middle">
                                                </div>
                                                <div class="col-md-1">
                                                    <button id="check-button" class="btn btn-primary"
                                                        disabled>CHECK</button>
                                                </div>
                                            </div>
                                            <div class="text-center mt-1"
                                                style="font-size: 10px; padding: 1px; margin: 1px; text-align: left; font-style: italic;">
                                                Notice Period Example: <span style="background-color: yellow">Received
                                                    call
                                                    from customer as user informed that the
                                                    12-June-2024 Motorbike will be
                                                    handed over to workshop</span>.
                                            </div>
                                            {{-- END / STEP ONE - NOTICE PERIOD --}}
                                            <br>
                                            {{-- STEP TWO - COLLECT MOTORBIKE --}}
                                            <div class="row">
                                                <div class="col-md-1"
                                                    style="background-color: rgb(86, 123, 84); text-align: center;">
                                                    <label for="collect-motorbike"
                                                        style="color: white; font-weight: bold; font-size: 18px; display: flex; align-items: center; justify-content: center; height: 100%;">2</label>
                                                </div>
                                                <div class="col-md-1">
                                                    <label for="collect-motorbike" class="closing-center-middle">Collect
                                                        Motorbike:</label>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" id="collect-details" class="form-control"
                                                        placeholder="Enter Any Details">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="date" id="collect-date" class="form-control"
                                                        placeholder="Select Date">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="time" id="collect-time" class="form-control"
                                                        placeholder="Select Time">
                                                </div>
                                                <div class="col-md-1">
                                                    <input type="checkbox" id="collect-checkbox"
                                                        class="closing-center-middle">
                                                </div>
                                                <div class="col-md-1">
                                                    <button id="collect-button" class="btn btn-primary"
                                                        disabled>CHECK</button>
                                                </div>
                                            </div>
                                            <div class="text-center mt-1"
                                                style="font-size: 10px; padding: 1px; margin: 1px; text-align: left; font-style: italic;">
                                                Notice Period Example: <span style="background-color: yellow">Received
                                                    call
                                                    from customer as user informed that the
                                                    12-June-2024 Motorbike will be
                                                    handed over to workshop</span>.
                                            </div>
                                            {{-- END / STEP TWO -  COLLECT MOTORBIKE --}}
                                            <br>
                                            {{-- STEP THREE - DAMAGES/ADDITIONAL COST --}}
                                            <div class="row">
                                                <div class="col-md-1"
                                                    style="background-color: rgb(86, 123, 84); text-align: center;">
                                                    <label for="damages-additional-cost"
                                                        style="color: white; font-weight: bold; font-size: 18px; display: flex; align-items: center; justify-content: center; height: 100%;">3</label>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="damages-additional-cost">Additional
                                                        Cost:</label>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="damages-total" id="damages-total-label"
                                                        style="color: rgb(4, 4, 4); font-weight: bold; font-size: 18px; display: flex; align-items: center; justify-content: center; height: 100%;">TOTAL:
                                                        £210</label>
                                                    <input type="hidden" id="damages-total" value="0">
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="damages-received" id="damages-received-label"
                                                        style="color: rgb(4, 4, 4); font-weight: bold; font-size: 18px; display: flex; align-items: center; justify-content: center; height: 100%;">RECEIVED:
                                                        £210</label>
                                                    <input type="hidden" id="damages-received" value="0">
                                                </div>
                                                <div class="col-md-1">
                                                    <input type="checkbox" id="damages-checkbox"
                                                        class="closing-center-middle">
                                                </div>
                                                <div class="col-md-1">
                                                    <button id="damages-button" class="btn btn-primary"
                                                        disabled>CHECK</button>
                                                </div>
                                            </div>
                                            <div class="text-center mt-1"
                                                style="font-size: 10px; padding: 1px; margin: 1px; text-align: left; font-style: italic;">
                                                Note: all the additional charges is total came
                                                from the option <span style="background-color: yellow">CHARGES</span>
                                                Tab and it must be paid
                                                on the same tab.
                                            </div>
                                            {{-- STEP THREE - DAMAGES/ADDITIONAL COST --}}
                                            <br>
                                            {{-- STEP FOUR - PCN PENDINGS --}}
                                            <div class="row">
                                                <div class="col-md-1"
                                                    style="background-color: rgb(86, 123, 84); text-align: center;">
                                                    <label for="pcn-pendings"
                                                        style="color: white; font-weight: bold; font-size: 18px; display: flex; align-items: center; justify-content: center; height: 100%;">4</label>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="pcn-pendings">PCN:</label>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="pcn-total" id="pcn-total-label"
                                                        style="color: rgb(4, 4, 4); font-weight: bold; font-size: 18px; display: flex; align-items: center; justify-content: center; height: 100%;">TOTAL:
                                                        £0</label>
                                                    <input type="hidden" id="pcn-total" value="0">
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="pcn-received" id="pcn-received-label"
                                                        style="color: rgb(4, 4, 4); font-weight: bold; font-size: 18px; display: flex; align-items: center; justify-content: center; height: 100%;">RECEIVED:
                                                        £0</label>
                                                    <input type="hidden" id="pcn-received" value="0">
                                                </div>
                                                <div class="col-md-1">
                                                    <input type="checkbox" id="pcn-checkbox"
                                                        class="closing-center-middle">
                                                </div>
                                                <div class="col-md-1">
                                                    <button id="pcn-button" class="btn btn-primary"
                                                        disabled>CHECK</button>
                                                </div>
                                            </div>
                                            <div class="text-center mt-1"
                                                style="font-size: 10px; padding: 1px; margin: 1px; text-align: left; font-style: italic;">
                                                Note: If any PCN is missing that will never
                                                reflect there. All PCN must be
                                                clear and add from <span
                                                    style="background-color: yellow">/ngn-admin/pcn-case/
                                                    (search for
                                                    registration number)</span> then you would
                                                be complete Step 4.
                                            </div>
                                            {{-- END / STEP FOUR - PCN PENDINGS --}}
                                            <br>
                                            {{-- STEP FIVE - PENDING RENT --}}
                                            <div class="row">
                                                <div class="col-md-1"
                                                    style="background-color: rgb(86, 123, 84); text-align: center;">
                                                    <label for="pending-rent"
                                                        style="color: white; font-weight: bold; font-size: 18px; display: flex; align-items: center; justify-content: center; height: 100%;">5</label>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="pending-rent">PENDING
                                                        RENT:</label>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="pending-total"
                                                        style="color: rgb(4, 4, 4); font-weight: bold; font-size: 18px; display: flex; align-items: center; justify-content: center; height: 100%;">PENDING
                                                        TOTAL: £0</label>
                                                    <input type="hidden" id="pending-total" value="0">
                                                </div>
                                                <div class="col-md-1">
                                                    <input type="checkbox" id="pending-checkbox"
                                                        class="closing-center-middle">
                                                </div>
                                                <div class="col-md-1">
                                                    <button id="pending-button" class="btn btn-primary"
                                                        disabled>CHECK</button>
                                                </div>
                                            </div>
                                            <div class="text-center mt-1"
                                                style="font-size: 10px; padding: 1px; margin: 1px; text-align: left; font-style: italic;">
                                                Note: Any due pending rent must be clear on
                                                <span style="background-color: yellow">PAYMENT
                                                    TAB</span>.
                                            </div>
                                            {{-- END / STEP FIVE - PENDING RENT --}}
                                            <br>
                                            {{-- STEP SIX - DEPOSIT RETURN --}}
                                            <div class="row">
                                                <div class="col-md-1"
                                                    style="background-color: rgb(86, 123, 84); text-align: center;">
                                                    <label for="deposit-return"
                                                        style="color: white; font-weight: bold; font-size: 18px; display: flex; align-items: center; justify-content: center; height: 100%;">6</label>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="deposit-return">DEPOSIT
                                                        RETURN:</label>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="total-deposit" id="total-deposit"
                                                        style="color: rgb(4, 4, 4); font-weight: bold; font-size: 18px; display: flex; align-items: center; justify-content: center; height: 100%;">TOTAL
                                                        DEPOSIT: £300</label>
                                                </div>
                                                <div class="col-md-1">
                                                    <input type="checkbox" id="deposit-checkbox"
                                                        class="closing-center-middle">
                                                </div>
                                                <div class="col-md-1">
                                                    <button id="deposit-button" class="btn btn-primary"
                                                        disabled>RETURN</button>
                                                </div>
                                            </div>
                                            <div class="text-center mt-1"
                                                style="font-size: 10px; padding: 1px; margin: 1px; text-align: left; font-style: italic;">
                                                Note: Deposit could be returned at least <span
                                                    style="background-color: yellow">15
                                                    Days</span> after motorbike handed
                                                over.
                                            </div>
                                            {{-- END / STEP SIX - DEPOSIT RETURN --}}

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- CLOSING TAB -->
                        </div>
                    </div>
                </div>
            </div> <!-- container -->
        </div> <!-- content -->
    </div>
    <script>
        $(document).ready(function() {
            var baseUrl = "{{ url('/') }}";
            var bookingId = null;
            var booking_item_id = null;
            var due_amount = null;
            var customer_id = null;
            var motorbike_id = null;
            var input = null; // UNUSED REMOVAL REQUESTED
            var booking_id = null;
            var invoice_id = null;
            // var status = null; // UNUSED REMOVAL REQUESTED
            var state = null;
            var is_posted = false;
            var start_date = null;

            var regno, make, model, motorbike_id, customer_id, fullname, phone, email, transaction_id;

            var isVehicleSelected = false;
            var isCustomerSelected = false;
            var isPaymentSelected = false;
            var isDocumentsSelected = false;
            var isAgreementSelected = false;
            var isCompleteSelected = false;
            // var genQR = false; // UNUSED REMOVAL REQUESTED
            var updated_total = 0;

            // VRM SEARCH BAR
            document.getElementById("regFilterInput").addEventListener("keyup", function() {
                // Get the input value
                var filterValue = this.value.toUpperCase();

                // Get the table rows
                var table = document.querySelector(".table-font tbody");
                var rows = table.querySelectorAll("tr");

                // Loop through all rows and filter
                rows.forEach(function(row) {
                    var regCell = row.querySelector(".regplate");
                    if (regCell) {
                        var regNo = regCell.textContent || regCell.innerText;
                        if (regNo.toUpperCase().indexOf(filterValue) > -1) {
                            row.style.display = "";
                        } else {
                            row.style.display = "none";
                        }
                    }
                });
            });
            // VRM SEARCH BAR

            // 1.0 - The Table Row Selected of Booking Item >>> //
            $('.booking-row').click(function() {

                booking_item_id = $(this).find('td:nth-child(2)').text();
                bookingId = $(this).find('td:nth-child(3)').text();
                selected_state = $(this).find('td:nth-child(4)').text();
                regno = $(this).find('td:nth-child(5)').text();
                due_amount = $(this).find('td:nth-child(9)').text();
                booking_id = bookingId;

                $('#booking-id').text(bookingId);
                $('#booking-iid-reg').text(regno);

                $('#load-other-item-data').html('');

                $('.booking-row').removeClass('selected-row');
                $(this).addClass('selected-row');

                // 1.0.1 - The Table Row Selected of Booking Item > AJAX Fetch Booking Details >>> //
                //  LOAD CUSTOMER DETAILS
                $.ajax({
                    url: '/admin/renting/bookings/' + bookingId + '/customer',
                    type: 'GET',
                    beforeSend: function() {
                        $('#modal-wait').modal('show');
                        setTimeout(function() {
                            $('#modal-wait').modal('hide');
                        }, 2000);
                    },
                    complete: function() {
                        $('#modal-wait').modal('hide');
                    },
                    success: function(response) {
                        customer_id = response.customer_id;
                        motorbike_id = response.motorbike_id;
                        console.log('fetchDocumentList -> Customer ID:', customer_id);
                        console.log('fetchDocumentList -> Motorbike ID:', motorbike_id);

                        fetchDocumentList(customer_id);

                        console.log('1.0.1 - ', motorbike_id);

                        if (motorbike_id) {
                            // payment related details are fetched here
                            fetchOutstanding(customer_id, bookingId, motorbike_id);
                            fetchInvoices(bookingId);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(
                            `1.0.1 -> Error: Select Customer Again or refresh it. ${error}`);
                        $('#info-message').text(`${error}`);
                        $('#modal-info').modal('show');
                    }
                });

                // Price Fetch on PayNow App
                input = '<input type="hidden" name="booking_id" value="' + bookingId +
                    '"><label for="due_amount">Enter Received Amount</label>' +
                    '<input type="text" class="form-control" id="due_amount" name="due_amount" value="' +
                    due_amount + '">';


                if (selected_state === 'Completed') {

                    template =
                        '<div class="mb-3">' +
                        '   <label for="issuance_note">Person who issued</label>' +
                        '   <input type="text" class="form-control" id="issuance_note" name="issued_by">' +
                        '</div>' +
                        '<div class="mb-3">' +
                        '   <label for="current-mileage">Current Mileage</label>' +
                        '   <input type="number" class="form-control" id="current-mileage" name="current_mileage" step="1">' +
                        '</div>' +
                        '<button class="btn btn-primary mb-2" id="toggle-video-maintenance-section">Click if you want to Upload Video or Add Maintenance Log</button>' +
                        '<div class="row mb-3" id="video-upload-or-maintenance-log-section" style="display:none;">' +
                        '  <div class="col-md-12" id="video-upload-section">' +
                        '    <label for="video_file">Upload video file</label>' +
                        '    <div class="d-flex align-items-center">' +
                        '      <input type="file" id="video_file" name="video_file" accept="video/*" class="form-control me-2" style="max-width:70%;" />' +
                        '      <button class="btn btn-primary" id="btn-upload-video" type="button">Upload Video</button>' +
                        '    </div>' +
                        '    <div id="uploaded-video-link" class="mt-2"></div>' +
                        '  </div>' +
                        '  <div class="col-md-12">' +
                        '    <div id="maintenance-log-section">' +
                        '      <div class="p-1" style="font-weight:bold;">Maintenance / Repair Logs</div>' +
                        '      <div>' +
                        '        <form id="maintenance-log-form">' +
                        '          <div id="maintenance-log-rows">' +
                        '            <div class="row maintenance-log-row mb-2">' +
                        
                        '                <div class="col-md-3">' +
                        '                  <input type="text" name="description[]" class="form-control mb-1" placeholder="Description" required />' +
                        '                </div>' +
                        '                <div class="col-md-2">' +
                        '                  <input type="number" name="cost[]" class="form-control mb-1" placeholder="Cost (£)" min="0" step="0.01" required />' +
                        '                </div>' +
                        '                <div class="col-md-3">' +
                        '                  <input type="datetime-local" name="serviced_at[]" class="form-control mb-1" required />' +
                        '                </div>' +
                        '                <div class="col-md-3">' +
                        '                  <input type="text" name="note[]" class="form-control mb-1" placeholder="Notes (optional)" />' +
                        '                </div>' +
                        '                <div class="col-md-1 d-flex align-items-center">' +
                        '                  <button type="button" class="btn btn-success btn-add-log-row me-1" title="Add another"><strong>+</strong></button>' +
                        '                  <button type="button" class="btn btn-danger btn-remove-log-row" title="Remove" style="display:none;"><strong>-</strong></button>' +
                        '                </div>' +
                        
                        '            </div>' +
                        '          </div>' +
                        '          <button type="submit" class="btn btn-primary ">Save Maintenance Logs</button>' +
                        '        </form>' +
                        '      </div>' +
                        '    </div>' +
                        '  </div>' +
                        '</div>' +
                        '<div class="form-check mb-3">' +
                        '   <input class="form-check-input" type="checkbox" id="is-video-recorded" name="is_video_recorded">' +
                        '   <label class="form-check-label" for="is-video-recorded">I have recorded video.</label>' +
                        '</div>' +
                        '<div class="form-check mb-3">' +
                        '   <input class="form-check-input" type="checkbox" id="accessories-checked" name="accessories_checked">' +
                        '   <label class="form-check-label" for="accessories-checked">I have checked the accessories</label>' +
                        '</div>' +
                        '<div class="form-check mb-3">' +
                        '   <input class="form-check-input" type="radio" id="catford" name="issue_from" value="Catford">' +
                        '   <label class="form-check-label" for="catford">Catford</label>' +
                        '</div>' +
                        '<div class="form-check mb-3">' +
                        '   <input class="form-check-input" type="radio" id="tooting" name="issue_from" value="Tooting">' +
                        '   <label class="form-check-label" for="tooting">Tooting</label>' +
                        '</div>' +
                        '<div class="form-check mb-3">' +
                        '   <input class="form-check-input" type="radio" id="sutton" name="issue_from" value="Sutton">' +
                        '   <label class="form-check-label" for="sutton">Sutton</label>' +
                        '</div>' +
                        '<button class="btn btn-success mt-3" id="btn-management-issue">ISSUE NOW</button>' +
                        '<button class="btn btn-secondary mt-3 mb-2" id="toggle-maintenance-summary">Hide Maintenance & Summary</button>' +
                        '<div id="maintenance-log-list-section-and-summary-container" style="display:none;"><div class="card mb-3 mt-3" id="maintenance-log-list-section">' +
                        '  <div class="card-header p-1"><strong>Existing Maintenance Logs</strong></div>' +
                        '  <div class="card-body p-0">' +
                        '    <div class="table-responsive">' +
                        '      <table class="table table-bordered table-sm table-bordered table-striped table-hover mb-0" id="maintenance-log-list-table">' +
                        '        <thead><tr><th>Description</th><th>Cost (£)</th><th>Notes</th><th>Date/Time</th><th>Actions</th></tr></thead>' +
                        '        <tbody><!-- Maintenance logs will be loaded here by JS --></tbody>' +
                        '      </table>' +
                        '    </div>' +
                        '  </div>' +
                        '</div>' +
                        '<h4>Uploaded Videos</h4>' +
                        '   <div id="all-uploaded-videos" class="mt-2"></div>' +
                        '</div>';

                    $('#issuance-tab #motorbike-issuance').html(template);

                } else if (selected_state === 'Completed & Issued') {

                    console.log('fetchDocumentList -> it is completed and issued');

                    template =
                        '<div class="mb-3">' +
                        '   <label for="issuance_note">Any Notes/Remarks, Person name who inspect</label>' +
                        '   <input type="text" class="form-control" id="issuance_note" name="issued_by">' +
                        '</div>' +
                        '<div class="mb-3">' +
                        '   <label for="current-mileage">Current Mileage</label>' +
                        '   <input type="number" class="form-control" id="current-mileage" name="current_mileage" step="1">' +
                        '</div>' +
                        '<button class="btn btn-primary mb-2" id="toggle-video-maintenance-section">Click if you want to Upload Video or Add Maintenance Log</button>' +
                        '<div class="row mb-3" id="video-upload-or-maintenance-log-section" style="display:none;">' +
                        '  <div class="col-md-12" id="video-upload-section">' +
                        '    <label for="video_file">Upload video file</label>' +
                        '    <div class="d-flex align-items-center">' +
                        '      <input type="file" id="video_file" name="video_file" accept="video/*" class="form-control me-2" style="max-width:70%;" />' +
                        '      <button class="btn btn-primary" id="btn-upload-video" type="button">Upload Video</button>' +
                        '    </div>' +
                        '    <div id="uploaded-video-link" class="mt-2"></div>' +
                        '  </div>' +
                        '  <div class="col-md-12">' +
                        '    <div id="maintenance-log-section">' +
                        '      <div class="p-1" style="font-weight:bold;">Maintenance / Repair Logs</div>' +
                        '      <div>' +
                        '        <form id="maintenance-log-form">' +
                        '          <div id="maintenance-log-rows">' +
                        '            <div class="row maintenance-log-row mb-2">' +
                        
                        '                <div class="col-md-3">' +
                        '                  <input type="text" name="description[]" class="form-control mb-1" placeholder="Description" required />' +
                        '                </div>' +
                        '                <div class="col-md-2">' +
                        '                  <input type="number" name="cost[]" class="form-control mb-1" placeholder="Cost (£)" min="0" step="0.01" required />' +
                        '                </div>' +
                        '                <div class="col-md-3">' +
                        '                  <input type="datetime-local" name="serviced_at[]" class="form-control mb-1" required />' +
                        '                </div>' +
                        '                <div class="col-md-3">' +
                        '                  <input type="text" name="note[]" class="form-control mb-1" placeholder="Notes (optional)" />' +
                        '                </div>' +
                        '                <div class="col-md-1 d-flex align-items-center">' +
                        '                  <button type="button" class="btn btn-success btn-add-log-row me-1" title="Add another"><strong>+</strong></button>' +
                        '                  <button type="button" class="btn btn-danger btn-remove-log-row" title="Remove" style="display:none;"><strong>-</strong></button>' +
                        '                </div>' +
                        
                        '            </div>' +
                        '          </div>' +
                        '          <button type="submit" class="btn btn-primary ">Save Maintenance Logs</button>' +
                        '        </form>' +
                        '      </div>' +
                        '    </div>' +
                        '  </div>' +
                        '</div>' +
                        '<div class="form-check mb-3">' +
                        '   <input class="form-check-input" type="checkbox" id="is-video-recorded" name="is_video_recorded" checked>' +
                        '   <label class="form-check-label" for="is-video-recorded">Video: Already recorded.</label>' +
                        '</div>' +
                        '<div class="form-check mb-3">' +
                        '   <input class="form-check-input" type="checkbox" id="is_insured" name="is_insured">' +
                        '   <label class="form-check-label" for="is_insured">I have checked the insurance on askmid.  <a href="https://enquiry.navigate.mib.org.uk/checkyourvehicle" target="_blank">Click here to check</a> </label>' +
                        '</div>' +
                        '<div class="form-check mb-3">' +
                        '   <input class="form-check-input" type="checkbox" id="accessories-checked" name="accessories_checked" chedked>' +
                        '   <label class="form-check-label" for="accessories-checked">I have checked the accessories</label>' +
                        '</div>' +
                        '<div class="form-check mb-3">' +
                        '   <input class="form-check-input" type="radio" id="catford" name="issue_from" value="Catford">' +
                        '   <label class="form-check-label" for="catford">Catford</label>' +
                        '</div>' +
                        '<div class="form-check mb-3">' +
                        '   <input class="form-check-input" type="radio" id="tooting" name="issue_from" value="Tooting">' +
                        '   <label class="form-check-label" for="tooting">Tooting</label>' +
                        '</div>' +
                        '<div class="form-check mb-3">' +
                        '   <input class="form-check-input" type="radio" id="sutton" name="issue_from" value="Sutton">' +
                        '   <label class="form-check-label" for="sutton">Sutton</label>' +
                        '</div>' +
                        '<button class="btn btn-success mt-3" id="btn-management-reissue">INSPECT & ISSUE</button><br><br>' +
                        '<button class="btn btn-secondary mt-3 mb-2" id="toggle-maintenance-summary">Hide Maintenance & Summary</button>' +
                        '<div id="maintenance-log-list-section-and-summary-container" style="display:none;"><div class="card mb-3 mt-3" id="maintenance-log-list-section">' +
                        '  <div class="card-header p-1"><strong>Existing Maintenance Logs</strong></div>' +
                        '  <div class="card-body p-0">' +
                        '    <div class="table-responsive">' +
                        '      <table class="table table-bordered table-sm table-bordered table-striped table-hover mb-0" id="maintenance-log-list-table">' +
                        '        <thead><tr><th>Description</th><th>Cost (£)</th><th>Notes</th><th>Date/Time</th><th>Actions</th></tr></thead>' +
                        '        <tbody><!-- Maintenance logs will be loaded here by JS --></tbody>' +
                        '      </table>' +
                        '    </div>' +
                        '  </div>' +
                        '</div>' +
                        '<h4>Uploaded Videos</h4>' +
                        '   <div id="all-uploaded-videos" class="mt-2"></div>' +
                        '</div>';

                    $('#issuance-tab #motorbike-issuance').html(template);

                }

                // LOAD CLOSING STATUS AND IF CLOSED OR NOT LOAT PENDING REND // PCN // DAMAGES ///
                // BOOKING CLOSING PROCEDURE //
                $.ajax({
                    url: '/admin/renting/booking/' + bookingId + '/closing-status',
                    type: 'GET',
                    success: function(response) {
                        console.log('Closing Status:', response);

                        if (response.data === 'no data found') {
                            console.log('No data found for closing status.');
                            return;
                        }

                        $('#notice-details').val(response.notice_details);
                        $('#notice-checkbox').prop('checked', response.notice_checked);

                        $('#collect-details').val(response.collect_details);
                        $('#collect-date').val(response.collect_date);
                        $('#collect-time').val(response.collect_time);
                        $('#collect-checkbox').prop('checked', response.collect_checked);

                        $('#damages-checkbox').prop('checked', response.damages_checked);
                        $('#pcn-checkbox').prop('checked', response.pcn_checked);
                        $('#pending-checkbox').prop('checked', response.pending_checked);
                        $('#deposit-checkbox').prop('checked', response.deposit_checked);
                    },
                    error: function(xhr, status, error) {
                        console.log(`1.0.1 -> Error: ${error}`);
                        $('#info-message').text(`${error}`);
                        $('#modal-info').modal('show');
                    }
                });

                // 7.3 - DAMAGES/ADDITIONAL COST
                $.ajax({
                    url: '/admin/renting/booking/' + bookingId + '/additional-costs',
                    type: 'GET',
                    success: function(response) {
                        console.log('Additional Costs:', response);

                        $('#damages-total-label').text('TOTAL: £' + response.total_amount);
                        $('#damages-received-label').text('RECEIVED: £' + response.paid_amount);

                        $('#damages-total').val(response.total_amount);
                        $('#damages-received').val(response.paid_amount);

                    },
                    error: function(xhr, status, error) {
                        console.log(`1.0.1 -> Error: ${error}`);
                        $('#info-message').text(`${error}`);
                        $('#modal-info').modal('show');
                    }
                });

                // 7.4 - PCN PENDINGS
                $.ajax({
                    url: '/admin/renting/booking/' + booking_item_id + '/pcn-pendings',
                    type: 'GET',
                    success: function(response) {
                        console.log('PCN Pendings:', response);

                        $('#pcn-total-label').text('TOTAL: £' + response.pcn_pending);
                        $('#pcn-received-label').text('RECEIVED: £' + response.paid_amount);

                        $('#pcn-total').val(response.pcn_pending);
                        $('#pcn-received').val(response.paid_amount);

                    },
                    error: function(xhr, status, error) {
                        $('#pcn-total-label').text('TOTAL: £' + 0.00);
                        $('#pcn-received-label').text('RECEIVED: £' + 0.00);

                        $('#pcn-total').val(0.00);
                        $('#pcn-received').val(0.00);
                    }
                });

                ////////////////////////////////
                // 7.6 - DAMAGES/ADDITIONAL COST
                $.ajax({
                    url: '/admin/renting/booking/' + bookingId + '/deposit',
                    type: 'GET',
                    success: function(response) {
                        console.log('Deposit Amount:', response);
                        // Update the UI with the deposit amount
                        $('#total-deposit').text('TOTAL DEPOSIT: £' + response.deposit);
                    },
                    error: function(xhr, status, error) {
                        console.log(`Error: ${error}`);
                        $('#info-message').text(`${status}`);
                        $('#modal-info').modal('show');
                    }
                });
                ////////////////////////////////

                // In .booking-row click handler, after bookingId is set and template is rendered: Video Renting Service Video is being load
                fetchAllBookingVideos();
                loadMaintenanceLogs();
                loadBookingSummary();
            });

            //////////////////////////////
            // 7.0 - CLOSING TABLE - START

            // 7.1 - NOTICE PERIOD
            $('#notice-checkbox').on('change', function() {
                var isChecked = $(this).is(':checked');
                $('#check-button').prop('disabled', !isChecked);
            });

            // 7.1.1 - NOTICE PERIOD AJAX POST
            $('#check-button').on('click', function() {

                var noticeDetails = $('#notice-details').val();
                var isChecked = $('#notice-checkbox').is(':checked');
                $.ajax({
                    url: '/admin/renting/notice-period',
                    type: 'POST',
                    data: {
                        noticeDetails: noticeDetails,
                        isChecked: isChecked,
                        booking_id: bookingId
                    },
                    success: function(response) {
                        console.log(response);
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.log(`7.1.1 -> Error: ${error}`);
                        $('#info-message').text(`${error}`);
                        $('#modal-info').modal('show');
                    }
                });

            });

            // 7.2 - COLLECT MOTORBIKE
            $('#collect-checkbox').on('change', function() {
                var isChecked = $(this).is(':checked');
                $('#collect-button').prop('disabled', !isChecked);
            });

            // 7.2.1 - COLLECT MOTORBIKE AJAX POST
            $('#collect-button').on('click', function() {

                var collectDetails = $('#collect-details').val();
                var collectDate = $('#collect-date').val();
                var collectTime = $('#collect-time').val();
                var isChecked = $('#collect-checkbox').is(':checked');

                $.ajax({
                    url: '/admin/renting/collect-motorbike',
                    method: 'POST',
                    data: {
                        booking_id: bookingId,
                        booking_item_id: booking_item_id,
                        collectDetails: collectDetails,
                        collectDate: collectDate,
                        collectTime: collectTime,
                        isChecked: isChecked
                    },
                    success: function(response) {
                        console.log(response);
                        location.reload();

                    },
                    error: function(xhr, status, error) {
                        console.log(`7.2.1 -> Error: ${error}`);
                        $('#info-message').text(`${error}`);
                        $('#modal-info').modal('show');
                    }
                });
            });

            // 7.3 - DAMAGES/ADDITIONAL COST
            $('#damages-checkbox').on('change', function() {
                var isChecked = $(this).is(':checked');
                var totalAmount = parseFloat($('#damages-total').val());
                var receivedAmount = parseFloat($('#damages-received').val());

                if (totalAmount === receivedAmount) {
                    $('#damages-button').prop('disabled', !(isChecked && totalAmount === receivedAmount));
                } else {
                    $('#damages-button').prop('disabled', true);
                }
            });

            // 7.3.1 - DAMAGES/ADDITIONAL COST AJAX POST
            $('#damages-button').on('click', function() {

                var isChecked = $('#damages-checkbox').is(':checked');
                var totalAmount = parseFloat($('#damages-total').val());
                var receivedAmount = parseFloat($('#damages-received').val());

                console.log('Total Amount:', totalAmount);
                console.log('Received Amount:', receivedAmount);

                if (totalAmount === receivedAmount) {
                    $.ajax({
                        url: '/admin/renting/damages-cost',
                        type: 'POST',
                        data: {
                            booking_id: bookingId,
                            isChecked: isChecked
                        },
                        success: function(response) {
                            console.log(response);
                            location.reload();

                        },
                        error: function(xhr, status, error) {
                            console.log(`7.3.1 -> Error: ${error}`);
                            $('#info-message').text(`${error}`);
                            $('#modal-info').modal('show');
                        }
                    });
                } else {
                    console.log('Total amount and received amount must be equal before proceeding.');
                }
            });

            // 7.4 - PCN PENDINGS
            $('#pcn-checkbox').on('change', function() {
                var isChecked = $(this).is(':checked');
                var totalAmount = parseFloat($('#pcn-total').val());
                var receivedAmount = parseFloat($('#pcn-received').val());

                // Enable button only if the checkbox is checked and amounts are equal
                $('#pcn-button').prop('disabled', !(isChecked && totalAmount === receivedAmount));
            });

            // 7.4.1 - PCN PENDINGS AJAX POST
            $('#pcn-button').on('click', function() {

                var isChecked = $('#pcn-checkbox').is(':checked');
                var totalAmount = parseFloat($('#pcn-total').val());
                var receivedAmount = parseFloat($('#pcn-received').val());

                if (totalAmount === receivedAmount) {
                    $.ajax({
                        url: '/admin/renting/pcn-pendings',
                        type: 'POST',
                        data: {
                            booking_id: bookingId,
                            isChecked: isChecked
                        },
                        success: function(response) {
                            console.log(response);
                            location.reload();

                        },
                        error: function(xhr, status, error) {
                            console.log(`7.4.1 -> Error: ${error}`);
                            $('#info-message').text(`${error}`);
                            $('#modal-info').modal('show');
                        }
                    });
                } else {
                    console.log('Total amount and received amount must be equal before proceeding.');
                }
            });


            // 7.5 - PENDING RENT
            $('#pending-checkbox').on('change', function() {
                var isChecked = $(this).is(':checked');
                var pendingTotal = parseFloat($('#pending-total').val());

                $('#pending-button').prop('disabled', !(isChecked && pendingTotal === 0));
            });

            // 7.5.1 - PENDING RENT AJAX POST
            $('#pending-button').on('click', function() {

                var isChecked = $('#pending-checkbox').is(':checked');

                var pendingTotal = parseFloat($('#pending-total').val());
                if (pendingTotal > 0) {
                    console.log('Pending rent must be zero before proceeding.');
                    $('#info-message').text('Pending rent must be zero before proceeding.');
                    $('#modal-info').modal('show');
                    return;
                }

                $.ajax({
                    url: '/admin/renting/pending-rent',
                    type: 'POST',
                    data: {
                        booking_id: bookingId,
                        isChecked: isChecked
                    },
                    success: function(response) {
                        console.log(response);
                        location.reload();

                    },
                    error: function(xhr, status, error) {
                        console.log(`7.5.1 -> Error: ${error}`);
                        $('#info-message').text(`${error}`);
                        $('#modal-info').modal('show');
                    }
                });
            });


            // 7.6 - DEPOSIT RETURN
            $('#deposit-checkbox').on('change', function() {
                var isChecked = $(this).is(':checked');
                $('#deposit-button').prop('disabled', !isChecked);
            });

            // 7.6.1 - DEPOSIT RETURN AJAX POST
            $('#deposit-button').on('click', function() {

                var isChecked = $('#deposit-checkbox').is(':checked');

                $.ajax({
                    url: '/admin/renting/deposit-return',
                    type: 'POST',
                    data: {
                        booking_id: bookingId,
                        isChecked: isChecked
                    },
                    success: function(response) {
                        console.log(response);
                        location.reload();

                    },
                    error: function(xhr, status, error) {
                        console.log(`7.6.1 -> Error: ${error}`);
                        $('#info-message').text(`${error}`);
                        $('#modal-info').modal('show');
                    }
                });
            });


            // 7 - CLOSING TABLE - END
            //////////////////////////

            // 5.0 - PAY ADDITIONAL AMOUNT
            $('#btn-other-item').click(function() {
                var other_item_desc = $('#other-item-desc').val();
                var other_item_amount = $('#other-item-amount').val();

                if (!other_item_desc || !other_item_amount) {
                    alert('Please fill in the description and amount');
                    return;
                }

                $.ajax({
                    url: '/admin/renting/bookings/' + bookingId + '/other-charges',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        booking_id: bookingId,
                        description: other_item_desc,
                        amount: other_item_amount
                    },
                    success: function(response) {
                        console.log(response);
                        alert('Additional Charges Added');
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.log(`5.0 -> Error: ${error}`);
                        $('#info-message').text(`${error}`);
                        $('#modal-info').modal('show');
                    }
                });
            });

            // 5.1 - LOAD ADDITIONAL AMOUNT
            $('#getOtherCharges').click(function() {
                var otherChargesHtml = '';

                $.ajax({
                    url: '/admin/renting/bookings/' + bookingId + '/other-charges',
                    method: "GET",
                    success: function(response) {
                        var otherCharges = response.other_charges;
                        console.log(response.otherCharges);
                        otherChargesHtml = '';
                        otherChargesHtml += '<div class="row">';
                        otherChargesHtml +=
                            '<div class="col-md-3">DESCRIPTION</div>';
                        otherChargesHtml += '<div class="col-md-3">AMOUNT </div>';
                        otherChargesHtml +=
                            '<div class="col-md-2">PAID ALREADY ?</div>';
                        otherChargesHtml += '<div class="col-md-4">ACTION</div>';
                        otherChargesHtml += '</div>';
                        otherCharges.forEach(function(charge) {
                            otherChargesHtml +=
                                '<div class="row border-now">';
                            otherChargesHtml += '<div class="col-md-3">' +
                                charge
                                .description + '</div>';
                            otherChargesHtml += '<div class="col-md-3">' +
                                charge
                                .amount + '</div>';
                            otherChargesHtml += '<div class="col-md-2">' +
                                charge
                                .is_paid + '</div>';

                            if (charge.is_paid === 'No') {
                                otherChargesHtml +=
                                    '<div class="col-md-4"><button class="btn btn-danger payOtherCharges" data-id="' +
                                    charge.id +
                                    '">Mark Paid</button></div>';
                            } else {
                                otherChargesHtml +=
                                    '<div class="col-md-4"><button class="btn btn-success payOtherCharges" data-id="' +
                                    charge.id +
                                    '" disabled>Paid</button></div>';
                            }

                            otherChargesHtml += '</div>';
                        });
                        $('#load-other-item-data').html(otherChargesHtml);
                    },
                    error: function(xhr, status, error) {
                        console.log(`5.1 -> Error: ${error}`);
                        $('#info-message').text(`${error}`);
                        $('#modal-info').modal('show');
                    }
                });
            });

            $(document).on('click', '.payOtherCharges', function() {
                // Store chargeId in data attribute of the confirm button
                $('#btn-confirm-additional-amount').data('id', $(this).data('id'));
                // Show the modal
                $('#modal-amount-confirm').modal('show');
            });

            // Handle the YES button in the amount confirmation modal
            $('#btn-confirm-additional-amount').click(function() {
                var chargeId = $(this).data('id');
                $.ajax({
                    url: '/admin/renting/bookings/other-charges/pay',
                    method: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        charges_id: chargeId
                    },
                    success: function(response) {
                        console.log(response);
                        alert('Payment Updated');
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.log(`Error: ${error}`);
                        $('#info-message').text(`${error}`);
                        $('#modal-info').modal('show');
                    }
                });
                // Hide the confirmation modal after action
                $('#modal-amount-confirm').modal('hide');
            });



            // 1.0.3 - Motorbike Issuance (prerequesite: milate, person who issued, video recorded, accessories checked, issue from)
            $('#issuance-tab').on('click', '#btn-management-issue', function() {
                var noteIssuance = $('#issuance_note').val();
                var currentMileage = $('#current-mileage').val();
                var isVideoRecorded = $('#is-video-recorded').is(':checked');
                var accessoriesChecked = $('#accessories-checked').is(':checked');
                var is_insured = $('#is_insured').is(':checked');
                var issuanceBranch = $('input[name="issue_from"]:checked').parent().text()
                    .trim();

                if (!noteIssuance || !currentMileage || !issuanceBranch || !regno || !
                    bookingId || !
                    isVideoRecorded || !accessoriesChecked) {
                    alert(
                        'Please check all followings: \n\n1. Issued By\n2. Current Mileage\n3. Issue From'
                    );
                    return;
                }

                // 1.0.3.1 - Motorbike Section | Issue Motorbike >>> //
                // POST TO SERVER - ISSUE MOTORBIKE (must be done after Document Verification, Payment, and Agreement)
                alert(noteIssuance);

                $.ajax({
                    url: '/admin/renting/bookings/' + bookingId + '/issue',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        reg_no: regno,
                        booking_id: bookingId,
                        booking_item_id: booking_item_id,
                        current_mileage: currentMileage,
                        is_video_recorded: isVideoRecorded,
                        notes: noteIssuance,
                        accessories_checked: accessoriesChecked,
                        is_insured: is_insured,
                        issuance_branch: issuanceBranch
                    },
                    success: function(response) {
                        console.log(response);
                        alert('Booking Issued');
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.log(`1.0.3.1 -> Error: ${error}`);
                        $('#issue-message').text(`${error}`);
                        $('#modal-issue').modal('show');
                    }
                });
            });

            // 1.0.4 - Motorbike weekly note taking, and re-issuance
            $('#issuance-tab').on('click', '#btn-management-reissue', function() {
                var noteIssuance = $('#issuance_note').val();
                var currentMileage = $('#current-mileage').val();
                var isVideoRecorded = $('#is-video-recorded').is(':checked');
                var accessoriesChecked = $('#accessories-checked').is(':checked');
                var issuanceBranch = $('input[name="issue_from"]:checked').parent().text()
                    .trim();

                if (!noteIssuance || !currentMileage || !issuanceBranch || !regno || !
                    bookingId || !
                    isVideoRecorded || !accessoriesChecked) {
                    alert(
                        'Please check all followings: \n\n1. Issued By\n2. Current Mileage\n3. Issue From'
                    );
                    return;
                }

                // 1.0.4 - Motorbike Section | Issue Motorbike >>> //
                // POST TO SERVER - ISSUE MOTORBIKE (must be done after Document Verification, Payment, and Agreement)
                $.ajax({
                    url: '/admin/renting/bookings/' + bookingId + '/reissue',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        reg_no: regno,
                        booking_id: bookingId,
                        booking_item_id: booking_item_id,
                        current_mileage: currentMileage,
                        notes: noteIssuance,
                        is_video_recorded: isVideoRecorded,
                        accessories_checked: accessoriesChecked,
                        issuance_branch: issuanceBranch
                    },
                    success: function(response) {
                        console.log(response);
                        alert('Booking Issued');
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.log(`1.0.4.2 -> Error: ${error}`);
                        $('#issue-message').text(`${error}`);
                        $('#modal-issue').modal('show');
                    }
                });
            });

            // 2.0 - Pay now clicked - no witness found for that button - subject to remove
            $('#btn-paid').click(function() {
                // 2.0.1 - Log button click
                console.log('btn-paid -> Paid Button Clicked', bookingId);
                if (bookingId == '' || bookingId == undefined) {
                    // 2.0.2 - Check if bookingId is selected
                    alert('Please select a booking');
                    return;
                }
                var due_amount = $('#due_amount').val();
                var booking_id = $('input[name="booking_id"]').val();

                $.ajax({
                    url: '/admin/renting/bookings/' + bookingId + '/invoice/create',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        booking_id: bookingId,
                        due_amount: due_amount
                    },
                    success: function(response) {
                        // 2.0.3 - Handle server response for the payment
                        if (response.status == 'success') {
                            // 2.0.4 - Success case: Payment confirmed
                            alert('Booking Paid');
                            location.reload();
                        } else {
                            // 2.0.5 - Failure case: Payment not successful
                            console.log('btn-paid -> ', response);
                            alert('Booking Not Paid');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(`2.0 -> Error: ${error}`);
                        $('#info-message').text(`${error}`);
                        $('#modal-info').modal('show');
                    }
                });
            });

            // 3.0 - Payment Section >>> //
            $('#btnReceiveAmount').click(function() {

                // 3.0.1 - Payment Section > Fetch Available Payment Methods >>> ////
                $.get("{{ '/admin/payment-methods' }}", function(data) {

                    // 3.0.1.1 - Payment Section > Fetch Available Payment Methods > Generate Template for P.Method Selection >>> ////
                    $('#paymentdropdown').empty();
                    $('#paymentdropdown').append(
                        '<option selected>Payment Method</option>');
                    console.log(data);
                    $.each(data, function(index, paymentMethod) {
                        $('#paymentdropdown').append(
                            '<option value="' +
                            paymentMethod.id + '">' +
                            paymentMethod.title + '</option>'
                        );
                    });
                });

                // 3.0.2 - Payment Section > Render TextBox for Amount Input >>> ////
                $('#paymentdropdown').change(function() {
                    if ($(this).val() != '') {
                        if ($('#paymentvalue').length === 0) {
                            $(this).after(`
                                        <div class="mb-3">
                                        <label for="paymentvalue">Enter Received Amount</label>
                                        <input type="text" class="form-control" name="txtbpaymentvalue" id="paymentvalue" placeholder="Enter Amount">
                                        <br>
                                        £<span id='rem-payment-payable'>${updated_total}</span> is the total amount payable
                                        </div>
                                    `);

                            // 3.0.2.1 - Payment Section > Render TextBox for Amount Input > Sanitization Input >>> ////
                            $('#paymentvalue').keypress(function(e) {
                                var charCode = (e.which) ? e.which : e
                                    .keyCode;
                                if (charCode != 46 && charCode > 31 &&
                                    (charCode < 48 || charCode > 57))
                                    return false;
                                // prevent multiple decimal points
                                if (charCode == 46 && $(this).val().indexOf(
                                        '.') != -1)
                                    return false;
                                // prevent negative values
                                if (charCode == 45)
                                    return false;
                                return true;
                            });
                        }
                    }
                });

                $('#modal-paynow').modal('show');
                $('#modal-paynow #btn-confirm-pay-selection').prop('disabled', false);

            });
            // 3.0 - END <<< //

            // 3.2 - Payment Section > Confirm Amount >>> // -- Receipt of Payment
            $('#modal-paynow #btn-confirm-pay-selection').click(function() {

                var payment_method = $('#paymentdropdown').val();
                var payment_value = $('#paymentvalue').val();

                // 3.1.1 - Payment Section > Confirm Amount > Check for P.Method >>> (RentingController::updateBooking) //
                if (payment_method && payment_method !== "" && payment_method !==
                    "Payment Method") {

                    if (payment_value || payment_value > 0) {

                        //disable button btn-confirm-pay-selection
                        $('#modal-paynow #btn-confirm-pay-selection').prop('disabled',
                            true);

                        isPaymentSelected = true;

                        $('#modal-paynow').modal('hide');

                        // 3.1.1.1 - Payment Section > Confirm Amount > Check for P.Method . AJAX >>> (RentingController::updateBooking) //
                        var csrf_token = $('meta[name="csrf-token"]').attr('content');
                        $.ajax({
                            url: '/admin/renting/bookings/update',
                            type: 'POST',
                            data: {
                                _token: csrf_token,
                                booking_id: booking_id,
                                payment_method_id: payment_method,
                                amount: payment_value
                            },
                            beforeSend: function() {
                                $('#modal-wait').modal('show');
                            },
                            complete: function() {
                                setTimeout(function() {
                                    $('#modal-wait').modal('hide');
                                }, 500);
                            },
                            success: function(response) {
                                setTimeout(function() {

                                    $('#modal-wait').modal('hide');
                                }, 500);

                                console.log('success - updateBooking', response);

                                transaction_id = response.transaction_id;
                                state = response.state;
                                is_posted = response.is_posted;

                                $('#deposit').text(response.deposit.toFixed(2));
                                $('#weekly').text(response.weekly.toFixed(2));
                                $('#total').text(response.total.toFixed(2));
                                $('#paidamt').text(response.paid.toFixed(2));
                                $('#balance').text(response.balance.toFixed(2));

                                updated_total = response.balance;

                                $('#rem-payment-payable').text(updated_total
                                    .toFixed(2));
                                if (response.balance > 0) {
                                    alert('Payment received. Remaining balance: £' +
                                        response
                                        .balance.toFixed(2));

                                    location.reload();
                                } else {
                                    console.log(
                                        'at else 1599 - url: /admin/renting/bookings/update'
                                    );

                                    location.reload();
                                }
                            },
                            error: function(xhr, status, error) {
                                if ($('#modal-paynow').modal('show')) {
                                    $('#modal-paynow').modal('hide');
                                }

                                if (xhr.status === 422) {
                                    $('#error-message').text(
                                        `Error: ${xhr.responseText}`);
                                    $('#modal-error').modal('show');
                                } else {
                                    $('#error-message').text(
                                        `Error: ${xhr.responseText}\n${error}`
                                    );
                                    $('#modal-error').modal('show');
                                }
                            }
                        });
                        // 3.1.1.1 - END <<< //

                    } else {
                        alert('Please enter the amount received');
                    }

                } else {
                    alert('Please select a payment method');
                }
            });
            // 3.2 - END <<< //

            // Modal Show
            $('#btnDocumentsComplete').click(function() {

                if (selected_state == 'Awaiting Documents & Payment' || selected_state ==
                    'Awaiting Documents') {
                    $('#modal-document-confirm').modal('show');
                } else {
                    alert('Documents already completed');
                    return;
                }
            });

            // MODAL - Resend Document Link
            $('#btnResendDocLink').click(function() {

                $('#modal-document-request-link').modal('show');

            });

            // x.2.1 - button Resend Document Link
            $('#btn-request-documents').click(function() {

                console.log(customer_id, bookingId);

                $.ajax({
                    url: `/generate-docs-upload-link-access/${customer_id}?booking_id=${bookingId}`,
                    type: 'GET',
                    beforeSend: function() {
                        $('#modal-wait').modal(
                            'show');
                    },
                    complete: function() {
                        setTimeout(function() {
                            $('#modal-wait').modal(
                                'hide'
                            );
                        }, 500);
                    },
                    success: function(response) {
                        setTimeout(function() {
                            $('#modal-wait').modal(
                                'hide');
                        }, 500);
                        console.log("Response received:", response);

                        if (response.uploadLink) {
                            // Assuming response contains a URL to upload documents
                            console.log("Upload Link:", response.uploadLink);
                            // Update the UI with the link or enable a button with the link
                            $('#upload-link').attr('href', response.uploadLink)
                                .text(
                                    "Upload Documents");
                            $('#upload-link').show();
                            $('#info-message').html('<a href="' + response
                                .uploadLink +
                                '">Upload Documents</a>');
                            // $('#modal-info').modal('show');
                        } else {
                            console.error("No upload link received in response");
                            $('#info-message').text(
                                "Error: No upload link available");
                            $('#modal-info').modal('show');
                        }

                    },
                    error: function(xhr, status, error) {
                        console.error("Error generating upload link: ", xhr
                            .responseText);
                        $('#error-message').text(
                            `Error: ${xhr.statusText} (${xhr.status})`);
                        $('#modal-error').modal(
                            'show'); // Show an error modal with more info
                    }
                });

            });

            // X.2 - click docs confirm
            $('#btn-confirm-documents').click(function() {

                $('#modal-document-confirm').modal('hide');
                $.ajax({
                    url: '/admin/renting/bookings/doc-confirm',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        booking_id: bookingId
                    },
                    beforeSend: function() {
                        // X.2.1 - Show modal wait before request
                        $('#modal-wait').modal('show');
                    },
                    complete: function() {
                        // X.2.2 - Hide modal wait after request
                        setTimeout(function() {
                            $('#modal-wait').modal('hide');
                        }, 500);
                    },
                    success: function(response) {
                        // X.2.3 - Hide modal wait on success
                        setTimeout(function() {
                            $('#modal-wait').modal('hide');
                        }, 500);
                        if (response.status == 'success') {
                            // X.2.4 - Handle success response
                            alert('Documents Confirmed');
                            location.reload();
                        } else {
                            // X.2.5 - Log response on other statuses
                            console.log('btn-confirm-documents -> ', response);
                        }
                    },
                    error: function(xhr, status, error) {
                        // X.2.6 - Handle error in AJAX call
                        console.log('X.2.6 -> btn-confirm-documents -> Error: ',
                            error);
                        alert('Error in Document Confirmation');
                    }
                });
            });

            // fetch invoices when click on booking
            function fetchInvoices() {
                $.ajax({
                    url: '/admin/renting/bookings/' + bookingId + '/invoices',
                    type: 'GET',
                    beforeSend: function() {
                        $('#modal-wait').modal('show');
                    },
                    complete: function() {
                        $('#modal-wait').modal('hide');
                    },
                    success: function(response) {
                        console.log('fetchInvoices -> ', response);
                        if (response.success === false) {
                            // $('#payment-heading').addClass('bg-info');
                            $('#payment-heading').text(response.message.toUpperCase());
                            return;
                        } else {
                            $('#payment-heading').text('PAYMENT SECTION\nPAYMENT HISTORY'
                                .toUpperCase());
                            $('#payment-invoices').empty();
                            $('#payment-invoices').append(
                                `<div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>INVOICE ID</th>
                                                <th>TRAN. NO</th>
                                                <th>INVOICE DATE</th>
                                                <th>INVOICE AMOUNT</th>
                                                <th>PAID AMOUNT</th>
                                                <th>PAID DATE</th>
                                                <th>INVOICE STATE</th>
                                                <th>DEPOSIT</th>
                                                <th>RECEIVED BY</th>
                                                <th>POSTING TIME</th>
                                                <th>ACTION</th>
                                            </tr>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        ${response.invoices.map(invoice => `
                                                                    <tr class="invoice-row ${invoice.IS_PAID == 0 ? 'invoice-row-clickable' : ''}" 
                                                                        ${invoice.IS_PAID == 0 ? `data-invoice-id="${invoice.INVOICE_ID}"` : ''}
                                                                        style="cursor: ${invoice.IS_PAID == 0 ? 'pointer' : 'default'};">
                                                                    <td style="font-size:12px;color:#333333 ;background-color: ${invoice.PAID_DATE ? 'transparent' : '#ffcccc'}">${invoice.INVOICE_ID || 'Awaiting Payment'}</td>
                                                                    <td style="color:#333333 ;background-color: ${invoice.PAID_DATE ? 'transparent' : '#ffcccc'}">${invoice.TRANSACTION_NO || 'Awaiting Payment'}</td>
                                                                    <td style="font-size:13px;font-weight:bold;color:#333333 ;background-color: ${invoice.PAID_DATE ? 'transparent' : '#ffcccc'}">${invoice.INVOICE_DATE || 'Awaiting Payment'}</td>
                                                                    <td style="font-size:12px;color:#333333 ;background-color: ${invoice.PAID_DATE ? 'transparent' : '#ffcccc'}">${invoice.INVOICE_AMOUNT || 'Awaiting Payment'}</td>
                                                                    <td style="font-size:12px;color:#333333 ;background-color: ${invoice.PAID_DATE ? 'transparent' : '#ffcccc'}">${invoice.PAID_AMOUNT || 'Awaiting Payment'}</td>
                                                                    <td style="font-size:12px;color:#333333 ;background-color: ${invoice.PAID_DATE ? 'transparent' : '#ffcccc'}">${invoice.PAID_DATE || 'Awaiting Payment'}</td>
                                                                    <td style="font-size:12px;color:#333333 ;background-color: ${invoice.PAID_DATE ? 'transparent' : '#ffcccc'}">${invoice.INV_STATE || 'Awaiting Payment'}</td>
                                                                    <td style="font-size:12px;color:#333333 ;background-color: ${invoice.PAID_DATE ? 'transparent' : '#ffcccc'}">${invoice.DEPOSIT || 'Awaiting Payment'}</td>
                                                                    <td style="font-size:12px;color:#333333 ;background-color: ${invoice.PAID_DATE ? 'transparent' : '#ffcccc'}">${invoice.FIRST_NAME || 'Awaiting Payment'}</td>
                                                                    <td style="font-size:12px;color:#333333 ;background-color: ${invoice.PAID_DATE ? 'transparent' : '#ffcccc'}">${invoice.TRANSACTION_DATETIME || 'Awaiting Payment'}</td>
                                                                    <td>
                                                                    ${invoice.INV_STATE === 'Completed' ? '<button class="btn btn-success" disabled>Paid</button>' : '<button class="btn btn-danger btn-pay-line-invoice" data-invoice-id="' + invoice.INVOICE_ID + '">UnPaid</button>'}
                                                                    </td>
                                                                    </tr>
                                                                    ${invoice.IS_PAID == 0 ? `
                                                                    <tr class="invoice-accordion-row" id="accordion-row-${invoice.INVOICE_ID}" style="display:none;">
                                                                        <td colspan="11" style="background-color: #f8f9fa; padding: 20px;">
                                                                            <div id="accordion-content-${invoice.INVOICE_ID}" class="invoice-accordion-content">
                                                                                <div class="text-center">
                                                                                    <i class="fa fa-spinner fa-spin"></i> Loading invoice details...
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    ` : ''}
                                                                    `).join('')}
                                        </tbody>
                                    </table>
                                </div>`
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(`fetchInvoices -> Error: ${error}`);
                        $('#info-message').text(`${error}`);
                        $('#modal-info').modal('show');
                    }
                });
            }

            // Invoice Accordion Functions - Event delegation for dynamically created rows
            $(document).on('click', '.invoice-row-clickable', function(e) {
                // Don't trigger if clicking on the payment button
                if ($(e.target).closest('.btn-pay-line-invoice').length > 0) {
                    return;
                }
                
                var invoiceId = $(this).data('invoice-id');
                if (!invoiceId) {
                    console.error('Invoice ID not found');
                    return;
                }
                
                toggleInvoiceAccordion(invoiceId);
            });

            // Event delegation for WhatsApp reminder button - backup handler
            $(document).on('click', '.btn-send-whatsapp', function(e) {
                // Only handle if direct handler didn't work
                if (e.isImmediatePropagationStopped()) {
                    return;
                }
                
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Delegated click handler triggered!');
                console.log('Event target:', e.target);
                console.log('Button element:', this);
                
                var $button = $(this);
                var invoiceId = $button.data('invoice-id');
                var whatsappUrl = $button.data('whatsapp-url') || '';
                
                console.log('Delegated handler - Invoice ID:', invoiceId);
                console.log('Delegated handler - WhatsApp URL:', whatsappUrl);
                
                if (!invoiceId) {
                    console.error('Invoice ID not found in delegated handler');
                    alert('Invoice ID not found');
                    return false;
                }
                
                console.log('Calling sendInvoiceWhatsapp from delegated handler');
                sendInvoiceWhatsapp(invoiceId, whatsappUrl);
                
                return false;
            });

            function toggleInvoiceAccordion(invoiceId) {
                var $accordionRow = $('#accordion-row-' + invoiceId);
                var $accordionContent = $('#accordion-content-' + invoiceId);
                
                if ($accordionRow.length === 0) {
                    console.error('Accordion row not found for invoice ID:', invoiceId);
                    return;
                }
                
                if ($accordionRow.is(':visible')) {
                    // Collapse accordion
                    $accordionRow.slideUp();
                } else {
                    // Expand accordion
                    $accordionRow.slideDown();
                    
                    // Fetch invoice details if not already loaded
                    if ($accordionContent.find('.invoice-details-loaded').length === 0) {
                        fetchInvoiceDetails(invoiceId);
                    }
                }
            }

            function fetchInvoiceDetails(invoiceId) {
                var $accordionContent = $('#accordion-content-' + invoiceId);
                
                $.ajax({
                    url: '/admin/renting/bookings/invoices/' + invoiceId + '/details',
                    type: 'GET',
                    beforeSend: function() {
                        $accordionContent.html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading invoice details...</div>');
                    },
                    success: function(response) {
                        if (response.success) {
                            var invoice = response.invoice;
                            var invoiceDate = invoice.invoice_date ? new Date(invoice.invoice_date).toISOString().split('T')[0] : '';
                            var formattedDate = invoice.invoice_date ? new Date(invoice.invoice_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : 'N/A';
                            var lastReminderDate = invoice.whatsapp_last_reminder_sent_at ? new Date(invoice.whatsapp_last_reminder_sent_at).toLocaleString('en-GB') : 'N/A';
                            
                            console.log('Generating invoice details HTML for invoiceId:', invoiceId);
                            console.log('Invoice data:', invoice);
                            console.log('WhatsApp URL from invoice:', invoice.whatsapp_url);
                            
                            var whatsappUrlEscaped = (invoice.whatsapp_url || '').replace(/"/g, '&quot;');
                            console.log('Escaped WhatsApp URL:', whatsappUrlEscaped);
                            
                            var html = `
                                <div class="invoice-details-loaded">
                                    <h5 class="mb-3">Invoice Details & Reminder Management</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Customer Information</h6>
                                            <table class="table table-sm table-bordered">
                                                <tr><td><strong>Customer Name:</strong></td><td>${invoice.customer_name || 'N/A'}</td></tr>
                                                <tr><td><strong>Phone:</strong></td><td>${invoice.customer_phone || 'N/A'}</td></tr>
                                                <tr><td><strong>WhatsApp:</strong></td><td>${invoice.customer_whatsapp || invoice.customer_phone || 'N/A'}</td></tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Motorbike Information</h6>
                                            <table class="table table-sm table-bordered">
                                                <tr><td><strong>Registration:</strong></td><td>${invoice.motorbike_reg_no || 'N/A'}</td></tr>
                                                <tr><td><strong>Weekly Rent:</strong></td><td>£${parseFloat(invoice.weekly_rent || 0).toFixed(2)}</td></tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <h6>Invoice Details</h6>
                                            <table class="table table-sm table-bordered">
                                                <tr><td><strong>Invoice Date:</strong></td><td>
                                                    <form id="invoice-date-form-${invoiceId}" class="d-inline" onclick="event.stopPropagation();">
                                                        <input type="date" 
                                                               name="invoice_date" 
                                                               value="${invoiceDate}" 
                                                               class="form-control form-control-sm d-inline-block" 
                                                               style="width: 160px;"
                                                               onclick="event.stopPropagation();"
                                                               onchange="updateInvoiceDateAjax(${invoiceId}, this.value)">
                                                        <span class="ml-2 small text-muted">(${formattedDate})</span>
                                                    </form>
                                                </td></tr>
                                                <tr><td><strong>Amount:</strong></td><td>£${parseFloat(invoice.amount || 0).toFixed(2)}</td></tr>
                                                <tr><td><strong>Status:</strong></td><td>${invoice.is_paid ? '<span class="badge badge-success">Paid</span>' : '<span class="badge badge-danger">Unpaid</span>'}</td></tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>WhatsApp Reminder</h6>
                                            <table class="table table-sm table-bordered">
                                                <tr><td><strong>Reminder Sent:</strong></td><td>${invoice.is_whatsapp_sent ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-warning">No</span>'}</td></tr>
                                                <tr><td><strong>Last Reminder:</strong></td><td>${lastReminderDate}</td></tr>
                                            </table>
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-success btn-sm btn-send-whatsapp" data-invoice-id="${invoiceId}" data-whatsapp-url="${whatsappUrlEscaped}" onclick="event.stopPropagation();">
                                                    <i class="fab fa-whatsapp"></i> Send WhatsApp Reminder
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            console.log('HTML generated, inserting into accordion');
                            $accordionContent.html(html);
                            
                            // Attach click handler directly to the button after HTML is inserted
                            var $button = $accordionContent.find('.btn-send-whatsapp');
                            console.log('Button found after insert:', $button.length > 0);
                            if ($button.length > 0) {
                                console.log('Button data-invoice-id:', $button.data('invoice-id'));
                                console.log('Button data-whatsapp-url:', $button.data('whatsapp-url'));
                                
                                // Remove any existing handlers and attach new one
                                $button.off('click').on('click', function(e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    e.stopImmediatePropagation();
                                    
                                    console.log('Direct click handler triggered!');
                                    var btnInvoiceId = $(this).data('invoice-id');
                                    var btnWhatsappUrl = $(this).data('whatsapp-url') || '';
                                    
                                    console.log('Direct handler - Invoice ID:', btnInvoiceId);
                                    console.log('Direct handler - WhatsApp URL:', btnWhatsappUrl);
                                    
                                    if (!btnInvoiceId) {
                                        console.error('Invoice ID not found in direct handler');
                                        alert('Invoice ID not found');
                                        return false;
                                    }
                                    
                                    sendInvoiceWhatsapp(btnInvoiceId, btnWhatsappUrl);
                                    return false;
                                });
                                
                                console.log('Click handler attached directly to button');
                            } else {
                                console.error('Button not found after HTML insertion!');
                            }
                        } else {
                            $accordionContent.html('<div class="alert alert-danger">Failed to load invoice details.</div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('fetchInvoiceDetails -> Error:', error);
                        $accordionContent.html('<div class="alert alert-danger">Error loading invoice details: ' + error + '</div>');
                    }
                });
            }

            function sendInvoiceWhatsapp(invoiceId, whatsappUrl) {
                console.log('sendInvoiceWhatsapp function called');
                console.log('Parameters - invoiceId:', invoiceId, 'whatsappUrl:', whatsappUrl);
                
                var csrfToken = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val() || '{{ csrf_token() }}';
                console.log('CSRF Token found:', csrfToken ? 'Yes' : 'No');
                
                if (!csrfToken) {
                    console.error('CSRF token not found');
                    alert('CSRF token not found. Please refresh the page.');
                    return;
                }
                
                var ajaxUrl = '/admin/renting/bookings/invoices/' + invoiceId + '/send-whatsapp';
                console.log('AJAX URL:', ajaxUrl);
                console.log('AJAX Type: POST');
                console.log('AJAX Data:', { _token: csrfToken });
                
                $.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    data: {
                        _token: csrfToken
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    beforeSend: function() {
                        console.log('AJAX beforeSend - disabling button');
                        // Disable button to prevent double clicks
                        $('.btn-send-whatsapp[data-invoice-id="' + invoiceId + '"]').prop('disabled', true);
                    },
                    success: function(response) {
                        console.log('AJAX Success - Response:', response);
                        if (response.success) {
                            console.log('Response indicates success, refreshing invoice details');
                            // Refresh invoice details to update the display
                            fetchInvoiceDetails(invoiceId);
                            
                            // Open WhatsApp in new tab
                            if (whatsappUrl && whatsappUrl !== 'undefined' && whatsappUrl !== '') {
                                console.log('Opening WhatsApp URL:', whatsappUrl);
                                window.open(whatsappUrl, '_blank');
                            } else {
                                console.log('No WhatsApp URL provided');
                            }
                            
                            // Show success message
                            alert('WhatsApp reminder marked as sent.' + (whatsappUrl && whatsappUrl !== 'undefined' && whatsappUrl !== '' ? ' WhatsApp window opened.' : ''));
                        } else {
                            console.error('Response indicates failure');
                            alert('Failed to mark reminder as sent.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error occurred');
                        console.error('Status:', status);
                        console.error('Error:', error);
                        console.error('XHR Status:', xhr.status);
                        console.error('XHR Response:', xhr.responseText);
                        console.error('XHR Response JSON:', xhr.responseJSON);
                        
                        if (xhr.status === 419) {
                            alert('Session expired. Please refresh the page and try again.');
                        } else if (xhr.status === 404) {
                            alert('Invoice not found. Please refresh the page.');
                        } else {
                            alert('Error sending WhatsApp reminder: ' + (xhr.responseJSON?.message || error));
                        }
                    },
                    complete: function() {
                        console.log('AJAX complete - re-enabling button');
                        // Re-enable button
                        $('.btn-send-whatsapp[data-invoice-id="' + invoiceId + '"]').prop('disabled', false);
                    }
                });
            }

            function updateInvoiceDateAjax(invoiceId, newDate) {
                $.ajax({
                    url: '/admin/renting/bookings/invoices/' + invoiceId + '/update-date',
                    type: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    data: JSON.stringify({
                        invoice_date: newDate
                    }),
                    success: function(response) {
                        if (response.success) {
                            // Refresh invoice details to update WhatsApp message with new date
                            fetchInvoiceDetails(invoiceId);
                            
                            // Show success message
                            alert('Invoice date updated successfully.');
                        } else {
                            alert('Failed to update invoice date.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('updateInvoiceDateAjax -> Error:', error);
                        if (xhr.status === 419) {
                            alert('Session expired. Please refresh the page and try again.');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            alert('Error: ' + xhr.responseJSON.message);
                        } else {
                            alert('Error updating invoice date: ' + error);
                        }
                    }
                });
            }

            $(document).on('click', '.btn-pay-line-invoice', function(e) {
                e.stopPropagation();

                // 3.0.1 - Payment Section > Fetch Available Payment Methods >>> ////
                $.get("{{ '/admin/payment-methods' }}", function(data) {
                    // 3.0.1.1 - Payment Section > Fetch Available Payment Methods > Generate Template for P.Method Selection >>> ////
                    $('#paymentdropdown').empty();
                    $('#paymentdropdown').append(
                        '<option selected>Payment Method</option>');
                    console.log(data);
                    $.each(data, function(index, paymentMethod) {
                        $('#paymentdropdown').append(
                            '<option value="' +
                            paymentMethod.id + '">' +
                            paymentMethod.title + '</option>'
                        );
                    });
                });

                // 3.0.2 - Payment Section > Render TextBox for Amount Input >>> ////
                $('#paymentdropdown').change(function() {
                    if ($(this).val() != '') {
                        if ($('#paymentvalue').length === 0) {
                            $(this).after(`
                    <div class="mb-3">
                    <label for="paymentvalue">Enter Received Amount</label>
                    <input type="text" class="form-control" name="txtbpaymentvalue" id="paymentvalue" placeholder="Enter Amount">
                    <br>
                    £<span id='rem-payment-payable'>${updated_total}</span> is the total amount payable
                    </div>
                `);

                            // 3.0.2.1 - Payment Section > Render TextBox for Amount Input > Sanitization Input >>> ////
                            $('#paymentvalue').keypress(function(e) {
                                var charCode = (e.which) ? e.which : e
                                    .keyCode;
                                if (charCode != 46 && charCode > 31 &&
                                    (charCode < 48 || charCode > 57))
                                    return false;
                                // prevent multiple decimal points
                                if (charCode == 46 && $(this).val().indexOf(
                                        '.') != -1)
                                    return false;
                                // prevent negative values
                                if (charCode == 45)
                                    return false;
                                return true;
                            });
                        }
                    }
                });

                $('#modal-paynow').modal('show');
                $('#modal-paynow #btn-confirm-pay-selection').prop('disabled', false);

            });

            // 1.0 - Motorbike Section | Payment Receivable >>> //
            function fetchOutstanding(customer_id, booking_id, motorbike_id, callback) {

                console.log('fetchOutstanding - Motorbike ID:', motorbike_id);
                console.log('fetchOutstanding - Reg No ', regno);

                console.log("fetchOutstanding - Fetching Motorbike Pricing");

                // 1.0.1 - Motorbike Section | Fetch Motorbike Pricing >>> //
                // LOADING PAYMENT SECTION - IT IS STRAINGE THAT THIS IS POST REQUEST
                $.ajax({
                    url: '/admin/renting/bookings/motorbike-pricing',
                    type: 'POST',
                    data: {
                        motorbike_id: motorbike_id,
                        reg_no: regno,
                        booking_id: booking_id,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {

                        console.log('fetchOutstanding - Motorbike Pricing:', response);

                        if (response.success === false) {

                            $('.payment-section').hide();
                            // $('#payment-heading').addClass('bg-info');
                            $('#payment-heading').text(response.message
                                .toUpperCase());
                            return;

                        } else {
                            // Failure was not an option :)
                            if (response.repayment === true) {

                                console.log('fetchOutstanding -> repayment -> Pricing:',
                                    response);

                                $('.payment-section').show();
                                $('#payment-heading').text(
                                    'PAYMENT SECTION\nPAYMENT HISTORY'
                                    .toUpperCase());

                                console.log('fetchOutstanding -> reissue -> Pricing:',
                                    response);


                                var update_date = new Date(response.update_date);
                                var comparison_date = new Date(update_date);

                                var today = new Date();
                                var yesterday = new Date(today);
                                yesterday.setDate(today.getDate() - 1);
                                var dayBeforeYesterday = new Date(today);
                                dayBeforeYesterday.setDate(today.getDate() - 2);

                                today.setHours(0, 0, 0, 0);
                                yesterday.setHours(0, 0, 0, 0);
                                dayBeforeYesterday.setHours(0, 0, 0, 0);

                                var lastUpdate;
                                if (comparison_date.setHours(0, 0, 0, 0) === today
                                    .getTime()) {
                                    lastUpdate = "today";
                                } else if (comparison_date.setHours(0, 0, 0, 0) ===
                                    yesterday
                                    .getTime()) {
                                    lastUpdate = "yesterday";
                                } else {
                                    var options = {
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        second: '2-digit'
                                    };
                                    lastUpdate = update_date.toLocaleString(undefined,
                                        options);
                                }

                                var minimum_deposit = 0;
                                var weekly_price = Number(response.pricing);
                                var total = minimum_deposit + weekly_price;
                                var already_paid = Number(response.totalPaid);
                                total = total - already_paid;

                                updated_total = response.pricing - already_paid;

                                console.log('fetchOutstanding -', total, already_paid,
                                    minimum_deposit,
                                    weekly_price, lastUpdate.toString());

                                if (
                                    typeof minimum_deposit !== 'undefined' && isFinite(
                                        minimum_deposit) &&
                                    typeof weekly_price !== 'undefined' && isFinite(
                                        weekly_price) &&
                                    typeof lastUpdate !== 'undefined' && lastUpdate !==
                                    null &&
                                    typeof total !== 'undefined' && isFinite(total)
                                )

                                {

                                    // OLD PAY - COMMENT

                                    $('.payment-section #deposit').html(
                                        minimum_deposit);
                                    $('.payment-section #weekly').html(weekly_price);
                                    $('.payment-section #last-update').html(lastUpdate);
                                    $('.payment-section #total').html('<span>' + total +
                                        '</span>');
                                    $('.payment-section #paidamt').html('<span>' +
                                        already_paid + '</span>');
                                    $('.payment-section #balance').html('<span>' +
                                        total + ' </span>');
                                }

                            } else {

                                console.log('fetchOutstanding -> firsttime -> Pricing:',
                                    response);

                                // FIRST TIME - NEW INVOICE AREA - FIRST TIME
                                $('.payment-section').show();
                                $('#payment-heading').text(
                                    'PAYMENT SECTION\nPAYMENT HISTORY'
                                    .toUpperCase());
                                console.log('fetchOutstanding -> firsttime -> Pricing:',
                                    response);
                                var update_date = new Date(response.update_date);
                                var comparison_date = new Date(update_date);

                                var today = new Date();
                                var yesterday = new Date(today);
                                yesterday.setDate(today.getDate() - 1);
                                var dayBeforeYesterday = new Date(today);
                                dayBeforeYesterday.setDate(today.getDate() - 2);

                                today.setHours(0, 0, 0, 0);
                                yesterday.setHours(0, 0, 0, 0);
                                dayBeforeYesterday.setHours(0, 0, 0, 0);

                                var lastUpdate;
                                if (comparison_date.setHours(0, 0, 0, 0) === today
                                    .getTime()) {
                                    lastUpdate = "today";
                                } else if (comparison_date.setHours(0, 0, 0, 0) ===
                                    yesterday
                                    .getTime()) {
                                    lastUpdate = "yesterday";
                                } else {
                                    var options = {
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        second: '2-digit'
                                    };
                                    lastUpdate = update_date.toLocaleString(undefined,
                                        options);
                                }

                                var minimum_deposit = Number(response.pricing
                                    .minimum_deposit);
                                var weekly_price = Number(response.pricing
                                    .weekly_price);
                                var total = minimum_deposit + weekly_price;
                                var already_paid = Number(response.totalPaid);
                                total = total - already_paid;
                                updated_total = total - already_paid;

                                console.log('fetchOutstanding -', total, already_paid,
                                    minimum_deposit,
                                    weekly_price, lastUpdate.toString());

                                if (minimum_deposit && weekly_price) {

                                    // OLD PAY - COMMENT
                                    // $('.payment-section #deposit').html(
                                    //     minimum_deposit);
                                    // $('.payment-section #weekly').html(weekly_price);
                                    // $('.payment-section #last-update').html(lastUpdate);
                                    // $('.payment-section #total').html('<span>' + total +
                                    //     '</span>');
                                    // $('.payment-section #paidamt').html('<span>' +
                                    //     already_paid + '</span>');
                                    // $('.payment-section #balance').html('<span>' +
                                    //     total + '</span>');

                                }
                            }

                        }

                    },
                    error: function(xhr, status, error) {
                        console.log(
                            `1.0 -> fetchOutstanding - Error: ${error}\nStatus: ${status}`
                        );
                        $('#issue-message').text(`${error}`);
                        $('#modal-issue').modal('show');
                    }
                });
                console.log('fetchOutstanding - Motorbike ID:', motorbike_id);

            }

            // 2.0 - Document Section >>> //
            function fetchDocumentList(customer_id, callback) {

                $.ajax({
                    url: '/admin/customers/documents/list',
                    type: 'POST',
                    datatype: 'json',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        customer_id: customer_id,
                        booking_id: bookingId
                    },
                    success: function(response) {
                        console.log('fetchDocumentList -> Success:', response);
                        var documentsTab = $('#DOCUMENTS_TAB .row');
                        documentsTab.empty();

                        if (typeof callback === "function") callback();

                        response.customer_docs.forEach(function(document) {
                            var documentHtml = createDocumentHtml(document);
                            documentsTab.append(documentHtml);
                        });

                        response.motorbike_docs.forEach(function(document) {
                            var documentHtml = createDocumentHtml(document);
                            documentsTab.append(documentHtml);
                        });

                        response.customer_agreement.forEach(function(document) {
                            var documentHtml = createDocumentHtml(document);
                            documentsTab.append(documentHtml);
                        });

                        $('.nav-pills a[href="#DOCUMENTS_TAB"]').tab('show');

                    },
                    error: function(xhr, status, error) {
                        console.log('2.0 -> fetchDocumentList -> Error:', error);
                        $('#error-message').text(`Error: ${error}`);
                        $('#modal-error').modal('show');
                    }
                });

                // TO BE CHANGE // to be modify as add warning message before checked
                $('#DOCUMENTS_TAB .row').off('change', '.form-check-input').on('change',
                    '.form-check-input',
                    function() {
                        var documentTypeId = $(this).data('document-type-id');
                        console.log('fetchDocumentList - Checkbox for Document Type ID changed:',
                            documentTypeId);

                        // MODAL CONFIRMATION verify each



                        // Simplified the check status log
                        console.log(
                            'fetchDocumentList - Document Type ID ' + documentTypeId + ' is ' +
                            ($(
                                this).is(
                                ':checked') ? 'checked' : 'unchecked') + '.'
                        );

                        if ($(this).is(':checked')) {
                            var documentType = $(this).closest('.document-upload').data(
                                'document-type');
                            if (documentType === 'rental_agreement') {
                                // Call a different function for rental agreements
                                handleRentalAgreement(documentTypeId, customer_id);
                            } else {
                                verifyDocument(documentTypeId, customer_id);
                            }
                        }
                    });
            }

            function createDocumentHtml(document) {

                var documentTypeId = document.id;
                var code = document.code;
                var name = document.name;
                var isRequired = document.is_required;
                var isVerified = document.is_verified;
                var fileName = document.file_name || 'No File Uploaded';
                var filePath = document.file_path;
                var documentCol = $('<div class="col-md-4"></div>');

                var documentUpload = $(
                    '<div class="mb-3 document-upload" data-document-type="' + code +
                    '" style="height:140px"></div>');

                var label = $('<label class="form-label">' + name + '</label>').attr('for',
                    'document_' +
                    documentTypeId);

                documentUpload.append(label);

                if (isVerified) {
                    // var fileLink = $('<a target="_blank">Verified: ' + fileName + '</a>').attr(
                    var fileLink = $(
                        '<a style="margin:2px; padding:2px; font-size:14px; color:green; border: 1px solid green; border-radius: 5px; width: 120px" target="_blank"><span style="padding 6px; margin: 6px">Verified: FILE √</span></a>'
                    ).attr(
                        'href', baseUrl +
                        '/storage/' + filePath);

                    documentUpload.append(fileLink);
                } else {
                    var input = $('<input class="form-control" type="file">')
                        .attr({
                            name: 'documents[' + code + ']',
                            id: 'document_' + documentTypeId,
                            required: isRequired,
                            'data-document-type-code': code
                        });

                    var verifyCheckbox = $(
                            '<input class="form-check-input" type="checkbox" style="padding: 12px !important; height: 12px !important">'
                        )
                        .attr({
                            id: 'missing_document_' + documentTypeId,
                            'data-document-type-code': code,
                            'data-document-type-id': documentTypeId
                        });

                    var verifyLabel = $(
                        '<label style="margin:2px; padding:2px; font-size:10px; color:red" class="form-check-label">Marked Check to Verify Document / Cheque marcado para verificar o documento</label>'
                    ).attr(
                        'for',
                        'missing_document_' + documentTypeId);

                    var fileLink = $(
                        '<a  style="margin:2px; padding:2px; font-size:14px; color:red; border: 1px solid RED; border-radius: 5px; width: 120px"  target="_blank"><span style="padding 6px; margin: 6px">CLICK FILE</span></a>'
                    ).attr('href',
                        baseUrl + '/storage/' +
                        filePath);

                    var badge = $('<br><span class="badge bg-info mt-12">NOT FOUND</span>');

                    if (!isVerified && fileName !== 'No File Uploaded') {
                        var deldocBtn = $(
                            '<button  style="margin:2px;background:red ; padding:2px; font-size:14px; color:white; border: 1px solid white; border-radius: 5px; width: 80px"  class="btn btn-primary">DELETE</button>'
                        ).attr('id', 'btn-doc-del-' + documentTypeId);

                        documentUpload.append(verifyCheckbox).append(verifyLabel);
                        documentUpload.append(fileLink).append(deldocBtn);
                    } else {

                        var badge = $('<br><span class="badge bg-info mt-12">NOT FOUND</span>');



                        documentUpload.append(input).append(badge);
                    }

                    // documentUpload.append(fileLink); //.append(badge);
                    // documentUpload.append(badge).append(badge);
                }

                documentCol.append(documentUpload);
                return documentCol;
            }

            // delete rejected document
            $(document).on('click', '[id^="btn-doc-del-"]', function() {
                var documentTypeId = $(this).attr('id').split('-').pop();
                console.log('Delete button clicked for document type ID:', documentTypeId);
                $.ajax({
                    url: '/admin/customer/delete-document',
                    method: 'POST',
                    data: {
                        documentTypeId: documentTypeId,
                        customerId: customer_id,
                        bookingId: bookingId,
                    },
                    success: function(response) {
                        window.location.reload();
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                    }
                });
            })

            function verifyDocument(documentTypeId, customer_id) {


                $.ajax({
                    url: '/admin/customers/documents/' + documentTypeId + '/verify',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        document_id: documentTypeId
                    },
                    success: function(response) {
                        console.log('verifyDocument - Document Verified', response);
                        alert('Document Verified');
                        fetchDocumentList(customer_id);
                    },
                    error: function(xhr, status, error) {
                        console.error('verifyDocument - Document Verification Error:',
                            error);
                        alert('Document Not Verified');
                    }
                });
            }

            function handleRentalAgreement(documentTypeId, customer_id) {
                $.ajax({
                    url: '/admin/customers/documents/' + documentTypeId +
                        '/verifyAgreement',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        document_id: documentTypeId,
                        customer_id: customer_id,
                        booking_id: bookingId
                    },
                    success: function(response) {
                        console.log('Document Verified', response);
                        alert('Document Verified');
                        fetchDocumentList(customer_id);
                    },
                    error: function(xhr, status, error) {
                        console.error('Document Verification Error:', error);
                        alert('Document Not Verified');
                    }
                });
            }

            // 4.1 - Upload Documents >>> //
            $(document).on('change', 'input[type="file"]', function(e) {
                // Prevent document upload handler from running for video upload
                if ($(this).attr('id') === 'video_file') {
                    return;
                }
                // 4.1.1 - Upload Documents > Upload File >>> ///////
                if (customer_id !== null && customer_id !== undefined) {
                    var fileInput = $(this);
                    var documentTypeCode = fileInput.data('document-type-code');
                    var file = fileInput.get(0).files[0];

                    if (!file) {
                        console.log('No file selected or cancel was pressed');
                        return;
                    }

                    var formData = new FormData();
                    formData.append('document', file);
                    formData.append('documentTypeCode', documentTypeCode);
                    formData.append('bookingID', booking_id);
                    formData.append('motorbikeID', motorbike_id);
                    formData.append('_token', $('input[name="_token"]').val());

                    var uploadUrl = '/admin/renting/customers/' + customer_id +
                        '/documents/upload';
                    $.ajax({
                        url: uploadUrl,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            $('#modal-wait').modal('show');
                        },
                        complete: function() {
                            setTimeout(function() {
                                $('#modal-wait').modal('hide');
                            }, 500);
                        },
                        success: function(response) {
                            setTimeout(function() {
                                $('#modal-wait').modal('hide');
                            }, 500);
                            $('#success-message').text('Your File Uploaded.');
                            $('#modal-success').modal('show');
                            console.log(response);

                            // In upload success callback, after upload is successful:
                            fetchAllBookingVideos();
                            loadMaintenanceLogs();
                            loadBookingSummary();
                        },
                        error: function(xhr, status, error) {
                            $('#error-message').text(
                                `Error, Upload failed: \n ${error}`);
                            $('#modal-error').modal('show');
                        }
                    });
                }
                // 4.1.1 - END <<< ///////
                else {
                    e.preventDefault();
                    $('#modal-issue #error-message').text('Please select a customer first');
                    $('#modal-issue').modal('show');
                }

            });
            // 4.1 - END <<< //

            // Add JS for upload
            $(document).on('click', '#btn-upload-video', function() {
                if (!bookingId) {
                    alert('Booking ID not found');
                    return;
                }
                let fileInput = $('#video_file')[0];
                if (!fileInput.files || fileInput.files.length === 0) {
                    alert('Please select a video file.');
                    return;
                }
                let formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('video_file', fileInput.files[0]);
                $.ajax({
                    url: '/admin/renting/bookings/' + bookingId + '/video/upload',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.video && response.video.video_path) {
                            let publicPath = response.video.video_path.replace('public/', '/storage/');
                            $('#uploaded-video-link').html('<a href="' + publicPath + '" target="_blank">View Uploaded Video</a>');
                        } else {
                            $('#uploaded-video-link').html('<span class="text-danger">Upload succeeded but no video path returned.</span>');
                        }
                        // Clear the file input after successful upload
                        $('#video_file').val('');
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        $('#uploaded-video-link').html('<span class="text-danger">There was an error uploading the video.</span>');
                    }
                });
            });

            // Add JS for always showing all uploaded videos
            function fetchAllBookingVideos() {
                if (!bookingId) return;
                $.ajax({
                    url: '/admin/renting/bookings/' + bookingId + '/videos',
                    method: 'GET',
                    success: function(videos) {
                        let html = '';
                        if (videos.length === 0) {
                            html = '<span>No videos uploaded for this booking.</span>';
                        } else {
                            videos.forEach(function(video, idx) {
                                let publicPath = video.video_path.replace('public/', '/storage/');
                                let fileName = publicPath.split('/').pop();
                                html += '<div><a href="' + publicPath + '" target="_blank">' + fileName + '</a></div>';
                            });
                        }
                        $('#all-uploaded-videos').html(html);
                    },
                    error: function(xhr, status, error) {
                        $('#all-uploaded-videos').html('<span class="text-danger">Could not load videos.</span>');
                    }
                });
            }

            // Call this on booking row click and after upload
            // After booking row click (inside .booking-row click handler):
            fetchAllBookingVideos();
            // After successful upload (inside upload success):
            fetchAllBookingVideos();
            loadMaintenanceLogs();
            loadBookingSummary();

            $(document).on("click", ".btn-add-log-row", function() {
              var $row = $(this).closest(".maintenance-log-row");
              var $clone = $row.clone();
              $clone.find("input").val("");
              $clone.find(".btn-remove-log-row").show();
              $("#maintenance-log-rows").append($clone);
            });
            $(document).on("click", ".btn-remove-log-row", function() {
              $(this).closest(".maintenance-log-row").remove();
            });

            function loadMaintenanceLogs() {
                if (!bookingId) return;
                $.ajax({
                    url: '/admin/renting/bookings/' + bookingId + '/maintenance-logs',
                    type: 'GET',
                    success: function(response) {
                        var logs = response.logs || [];
                        var html = '';
                        var total = 0;
                        if (logs.length === 0) {
                            html = '<tr><td colspan="4" class="text-center">No maintenance logs found.</td></tr>';
                        } else {
                            logs.forEach(function(log) {
                                total += parseFloat(log.cost || 0);
                                html += '<tr>' +
                                    '<td>' + (log.description || '') + '</td>' +
                                    '<td>' + (log.cost ? '£' + parseFloat(log.cost).toFixed(2) : '') + '</td>' +
                                    '<td>' + (log.note || '') + '</td>' +
                                    '<td>' + (log.serviced_at ? new Date(log.serviced_at).toLocaleString() : '') + '</td>' +
                                    '<td><button class="btn btn-danger btn-sm btn-delete-maintenance-log" data-log-id="' + log.id + '">Delete</button></td>' +
                                    '</tr>';
                            });
                        }
                        $('#maintenance-log-list-table tbody').html(html);
                        $('#maintenance-log-total-cost').html('<strong>Total Cost: £' + total.toFixed(2) + '</strong>');
                    },
                    error: function(xhr, status, error) {
                        $('#maintenance-log-list-table tbody').html('<tr><td colspan="4" class="text-danger">Could not load logs.</td></tr>');
                        $('#maintenance-log-total-cost').html('');
                    }
                });
            }

            $(document).off('submit', '#maintenance-log-form');
            $(document).on('submit', '#maintenance-log-form', function(e) {
                e.preventDefault();
                if (!bookingId || !motorbike_id) {
                    alert('Booking or motorbike not selected.');
                    return;
                }
                var rows = $('#maintenance-log-rows .maintenance-log-row');
                var logs = [];
                var hasError = false;
                rows.each(function() {
                    var desc = $(this).find('input[name="description[]"]').val();
                    var cost = $(this).find('input[name="cost[]"]').val();
                    var serviced_at = $(this).find('input[name="serviced_at[]"]').val();
                    var note = $(this).find('input[name="note[]"]').val();
                    if (!desc || !cost || !serviced_at) {
                        hasError = true;
                        return false;
                    }
                    logs.push({
                        description: desc,
                        cost: cost,
                        serviced_at: serviced_at,
                        note: note
                    });
                });
                if (hasError || logs.length === 0) {
                    alert('Please fill all required fields.');
                    return;
                }

                // Submit each log (could be batched, but let's do one by one for now)
                var requests = logs.map(function(log) {
                    return $.ajax({
                        url: '/admin/renting/bookings/' + bookingId + '/maintenance-logs',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            motorbike_id: motorbike_id,
                            cost: log.cost,
                            serviced_at: log.serviced_at,
                            description: log.description,
                            note: log.note
                        }
                    });
                });

                $.when.apply($, requests).done(function() {
                    alert('Maintenance log(s) saved!');
                    loadMaintenanceLogs();
                    // Reset form to one empty row
                    $('#maintenance-log-rows').html($('#maintenance-log-rows .maintenance-log-row').first().clone());
                    $('#maintenance-log-rows .maintenance-log-row input').val('');
                    $('#maintenance-log-rows .btn-remove-log-row').hide();
                }).fail(function() {
                    alert('Error saving maintenance log(s).');
                });
            });

            function loadBookingSummary() {
                if (!bookingId) return;
                $.ajax({
                    url: '/admin/renting/bookings/' + bookingId + '/summary',
                    type: 'GET',
                    success: function(data) {
                        let html = '<div class="alert alert-info mt-2">';
                        html += '<strong>Booking Summary</strong><br>';
                        html += 'Booking ID: <b>' + (data.booking_id || '-') + '</b><br>';
                        html += 'Reg Number: <b>' + (data.reg_no || '-') + '</b><br>';
                        html += 'Start Date: <b>' + (data.start_date || '-') + '</b><br>';
                        html += 'End Date: <b>' + (data.end_date || '-') + '</b><br>';
                        html += 'Weeks Passed: <b>' + (data.weeks || 0) + '</b><br>';
                        html += 'Paid Invoices: <b>' + (data.paid_invoice_count || 0) + '</b><br>';
                        html += 'Total Rental Income: <b>£' + (data.total_income ? parseFloat(data.total_income).toFixed(2) : '0.00') + '</b><br>';
                        html += 'Total Maintenance Cost: <b>£' + (data.total_cost ? parseFloat(data.total_cost).toFixed(2) : '0.00') + '</b><br>';
                        html += 'Net Profit: <b>£' + (data.net_profit ? parseFloat(data.net_profit).toFixed(2) : '0.00') + '</b><br>';
                        html += 'Current Weekly Rent: <b>£' + (data.current_weekly_rent ? parseFloat(data.current_weekly_rent).toFixed(2) : '0.00') + '</b><br>';
                        // html += 'Weeks at Current Price: <b>' + (data.total_weeks_at_current_price || 0) + '</b><br>';
                        // html += 'Total at Current Price: <b>£' + (data.total_at_current_price ? parseFloat(data.total_at_current_price).toFixed(2) : '0.00') + '</b><br>';
                        if (data.price_periods && data.price_periods.length > 1) {
                            html += 'Total at All Prices: <b>£' + (data.total_at_all_prices ? parseFloat(data.total_at_all_prices).toFixed(2) : '0.00') + '</b><br>';
                            html += '<hr><b>Price Periods:</b><br>';
                            html += '<table class="table table-sm table-bordered table-striped table-hover mb-0"><thead><tr><th>Start</th><th>End</th><th>Weekly Price</th><th>Weeks</th><th>Total</th></tr></thead><tbody>';
                            data.price_periods.forEach(function(period) {
                                html += '<tr>' +
                                    '<td>' + period.start_date + '</td>' +
                                    '<td>' + period.end_date + '</td>' +
                                    '<td>£' + parseFloat(period.weekly_price).toFixed(2) + '</td>' +
                                    '<td>' + period.weeks + '</td>' +
                                    '<td>£' + parseFloat(period.total).toFixed(2) + '</td>' +
                                    '</tr>';
                            });
                            html += '</tbody></table>';
                        }
                        html += '</div>';
                        $('#booking-summary').html(html);
                    },
                    error: function() {
                        $('#booking-summary').html('<div class="alert alert-danger mt-2">Could not load booking summary.</div>');
                    }
                });
            }

            // After loadMaintenanceLogs() is called (in .booking-row click and after upload):
            loadMaintenanceLogs();
            loadBookingSummary();

            
            $(document).on('click', '.btn-delete-maintenance-log', function() {
                var logId = $(this).data('log-id');
                if (!logId) return;
                if (!confirm('Are you sure you want to delete this maintenance log?')) return;
                $.ajax({
                    url: '/admin/renting/bookings/maintenance-logs/' + logId,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            loadMaintenanceLogs();
                            loadBookingSummary(); // <-- Add this line to reload summary
                        } else {
                            alert(response.message || 'Failed to delete log.');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 419) {
                            alert('Session expired. Please refresh and try again.');
                        } else {
                            alert('Failed to delete log.');
                        }
                    }
                });
            });

            // Add JS for toggle button:
            $(document).on('click', '#toggle-maintenance-summary', function() {
                var $container = $('#maintenance-log-list-section-and-summary-container');
                $container.toggle();
                if ($container.is(':visible')) {
                    $(this).text('Hide Maintenance & Summary');
                } else {
                    $(this).text('Show Maintenance & Summary');
                }
            });
            
            // Add JS for toggle button:
            $(document).on('click', '#toggle-video-maintenance-section', function() {
                var $section = $('#video-upload-or-maintenance-log-section');
                $section.toggle();
                var $btn = $(this);
                if ($section.is(':visible')) {
                    $btn.text('Hide Video Upload / Maintenance Log');
                } else {
                    $btn.text('Show Video Upload / Maintenance Log');
                }
            });

        });
    </script>
@endsection
