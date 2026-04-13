    <script>
        // 0.0 - Document Ready
        $(document).ready(function() {
            // 0.1 - Global Variables
            var baseUrl = "{{ url('/') }}";
            var invoice_begin = false;
            var booking_id = null;
            var invoice_id = null;
            var status = null;
            var state = null;
            var is_posted = false;
            var start_date = null;
            var regno, make, model, motorbike_id, customer_id, fullname, phone, email, transaction_id;
            // States of application processes
            var isVehicleSelected = false;
            var isCustomerSelected = false;
            var isPaymentSelected = false;
            var isDocumentsSelected = false;
            var isAgreementSelected = false;
            var isCompleteSelected = false;
            var genQR = false;
            var already_paid = 0;

            var no_price_bike = true;

            // Data to be used in the Payment Tab
            var weekly_price, updated_total;
            var total = 0;
            var minimum_deposit = 0;

            // 1.0 - Motorbike Selection >>> //
            $('tbody .motorbike-row').click(function() {

                if (isPaymentSelected === true) {
                    alert('Payment already made. Please proceed to the next step.');
                    return;
                }

                // invoice_begin = true; when the booking is started and it is not sign that the invoice is started. invoice start when customer select the vehicle
                invoice_begin = true;

                // Collect the Motorbike Data
                motorbike_id = $(this).find('td:nth-child(1)').text();
                regno = $(this).find('td:nth-child(2)').text();
                make = $(this).find('td:nth-child(3)').text();
                model = $(this).find('td:nth-child(4)').text();

                var isAvailable = false;

                // 1.0.1 - Motorbike Selection > Confirm Motorbike > Pricing > AJAX Request >>> //
                $.ajax({
                    url: '/admin/renting/motorbike-price-check',
                    type: 'GET',
                    data: {
                        motorbike_id: motorbike_id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $('#modal-wait').modal('show');
                        setTimeout(function() {
                            $('#modal-wait').modal('hide');
                        }, 500);
                    },
                    complete: function() {
                        setTimeout(function() {
                            $('#modal-wait').modal('hide');
                        }, 500);
                    },
                    success: function(response) {
                        if (response.pricing) {
                            // Handle the successful retrieval of pricing here
                            console.log('Pricing details:', response.pricing);
                            no_price_bike = false;
                        } else {
                            // Handle the case whre pricing is not found
                            alert(
                                'Price Not Found.\n\nKindly set the price first.'
                            );
                            no_price_bike = true;
                            $('#modal-motorbike').modal('hide');
                            return;
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#issue-message').text(`${error}`);
                        $('#modal-issue').modal('show');
                    }
                });

                // 1.0.1 - Motorbike Selection >>> //
                // Very Important - Check Motorbike Availability - It occurs in when page was loaded
                // long time ago and the availability status may have changed in actual database
                // $.ajax({
                //     url: '/admin/renting/bookings/motorbike-availability',
                //     type: 'GET',
                //     data: {
                //         motorbike_id: motorbike_id,
                //         _token: '{{ csrf_token() }}'
                //     },
                //     dataType: 'json',
                //     beforeSend: function () {
                //         $('#modal-wait').modal('show');
                //     },
                //     complete: function () {
                //         setTimeout(function () {
                //             $('#modal-wait').modal('hide');
                //         }, 500);
                //     },
                //     success: function (response) {
                //         setTimeout(function () {
                //             $('#modal-wait').modal('hide');
                //         }, 500);
                //         console.log('Availability:', response);
                //         if (response.status === 'Available') {
                //             isAvailable = true;
                //         } else {
                //             isAvailable = false;
                //         }
                //     },
                //     error: function (xhr, status, error) {
                //         $('#issue-message').text(`${error}`);
                //         $('#modal-issue').modal('show');
                //     }
                // });

                // if (!isAvailable) {
                //     alert('This motorbike is not available. Please select another motorbike. or Refresh page');
                //     return;
                // }
                // 1.0.1 - END <<< //

                // Select remain selected
                $('.motorbike-row').removeClass('selected-row');
                $(this).addClass('selected-row');

                // 1.0.2 - Motorbike Selection > Confirm Motorbike Modal View >>> /////
                $('#modal-motorbike .modal-body .col').html(`
                <div class="table-responsive">
                    <table class="table table-dark table-borderless mb-0">
                        <thead>
                            <tr>
                                <th class="inforce-font-size" >REG</th>
                                <th class="inforce-font-size" !important;" >MAKE</th>
                                <th class="inforce-font-size" !important;" >MODEL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="regplate">${regno}</span></td>
                                <td>${make}</td>
                                <td>${model}</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                    `);
                // 1.0.2 - END <<< //

                // Show the Modal
                $('#modal-motorbike').modal('show');

            });
            // 1.0 - END <<< //
            // 1.1 - Motorbike Selection > Confirm Motorbike >>> //
            $('#modal-motorbike #btn-confirm-vehicle-selection').click(function() {

                if (no_price_bike) {
                    alert('Price Not Found.\n\nKindly set the price first from Pricing option.');
                    return;
                }

                isVehicleSelected = true;
                if (isVehicleSelected) {
                    $('#tab-motorbike').html('MOTORBIKE <i class="mdi mdi-check-all"></i>');
                }

                // 1.1.1 - Motorbike Selection > Confirm Motorbike > Pricing >>> //
                if (motorbike_id) {
                    console.log('Motorbike ID:', motorbike_id);
                    console.log('Reg No ', regno);
                    // 1.1.1.1 - Motorbike Selection > Confirm Motorbike > Pricing > AJAX Request >>> //
                    $.ajax({
                        url: '/admin/renting/bookings/motorbike-pricing',
                        type: 'POST',
                        data: {
                            motorbike_id: motorbike_id,
                            reg_no: regno,
                            booking_id: booking_id,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
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
                            console.log('Pricing:', response);

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
                            if (comparison_date.setHours(0, 0, 0, 0) === today.getTime()) {
                                lastUpdate = "today";
                            } else if (comparison_date.setHours(0, 0, 0, 0) === yesterday
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
                                lastUpdate = update_date.toLocaleString(undefined, options);
                            }

                            minimum_deposit = Number(response.pricing.minimum_deposit);
                            weekly_price = Number(response.pricing.weekly_price);
                            total = minimum_deposit + weekly_price;
                            already_paid = Number(response.totalPaid);
                            total = total - already_paid;
                            updated_total = total;

                            console.log(total, already_paid, minimum_deposit, weekly_price,
                                lastUpdate);

                            if (minimum_deposit && weekly_price) {
                                $('.payment-section #deposit').html(minimum_deposit);
                                $('.payment-section #weekly').html(weekly_price);
                                $('.payment-section #last-update').html(lastUpdate);
                                $('.payment-section #total').html('<span>' + total + '</span>');
                                $('.payment-section #balance').html('<span>' + total +
                                    '</span>');
                            }

                        },
                        error: function(xhr, status, error) {
                            $('#issue-message').text(`${error}`);
                            $('#modal-issue').modal('show');
                        }
                    });
                    // 1.1.1.1 - END <<< //
                } else {
                    alert('Motorbike ID is not set. Please select a motorbike.');
                }
                // 1.1.1 - END <<< //

                var customer_tab = new bootstrap.Tab($('#tab-customer'));

                $('.selection-section').show();
                $('.selected-vehicle').show();

                customer_tab.show();

                // Data insert into Quickview
                $('.selection-section .motorbikecard').html(`
                <h6 class="text-center">Motorbike</h6>
                <div class="table-responsive">
                    <table class="table table-dark table-borderless mb-1">
                        <thead>
                            <tr>
                                <th class="inforce-font-size" >REG</th>
                                <th class="inforce-font-size" !important;" >MAKE</th>
                                <th class="inforce-font-size" !important;" >MODEL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="regplate">${regno}</span></td>
                                <td>${make}</td>
                                <td>${model}</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                    `);

                $('#modal-motorbike').modal('hide');
            });
            // 1.1 - END <<< //
            // 1.2 - MOTORVEHICLE Section > X Row Selected >>> //
            $('#btn-cancel-vehicle-selection').click(function() {
                $('#modal-motorbike').modal('hide');
                $('.motorbike-row').removeClass('selected-row');
            });
            // 1.2 - END <<< //
            // 2.0 - Customer Section >>> //
            $('#tab-customer').click(function(event) {
                if (isVehicleSelected === false) {
                    event.preventDefault();
                    alert('Select Vehicle First\n\nSelect MOTORBIKE tab to continue');
                }
            });
            // 2.0 - END <<< //
            // 2.1 - Customer Section > Customer Row Selected >>> //
            $('tbody .customer-row').click(function() {
                // 2.1.1 - Customer Section > Customer Row Selected > Invoice=False >>> //
                if (invoice_begin === false) {
                    alert('Please select a vehicle first');
                    return;
                }
                // 2.1.2 - Customer Section > Customer Row Selected > New Booking >>> //
                if (booking_id === null || isNaN(booking_id) && isPaymentSelected === false) {

                    customer_id = $(this).find('td:nth-child(1)').text();
                    fullname = $(this).find('td:nth-child(2)').text() + ' ' + $(this).find(
                        'td:nth-child(3)').text();
                    phone = $(this).find('td:nth-child(4)').text();
                    email = $(this).find('td:nth-child(5)').text();

                    // Select remain selected
                    $('.customer-row').removeClass('selected-row');
                    $(this).addClass('selected-row');

                    // 2.1.2.1 - Customer Section > Customer Row Selected > New Booking > Customer Modal >>> //
                    $('#modal-customer .modal-body .col').html(`
                        <div class="table-responsive">
                            <table class="table table-dark table-borderless mb-0">
                                <thead>
                                    <tr>
                                        <th class="inforce-font-size" >Full Name</th>
                                        <th class="inforce-font-size" !important;" >Phone</th>
                                        <th class="inforce-font-size" !important;" >Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>${fullname}</td>
                                        <td>${phone}</td>
                                        <td>${email}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        `);

                    // Show the Modal
                    $('#modal-customer').modal('show');
                }
                // 2.1.3 - Customer Section > Customer Row Selected > Booking Exist - Change Customer >>> //
                else if (booking_id !== null && !isNaN(booking_id) && isPaymentSelected === false &&
                    genQR === false && isAgreementSelected === false) {

                    customer_id = $(this).find('td:nth-child(1)').text();
                    fullname = $(this).find('td:nth-child(2)').text() + ' ' + $(this).find(
                        'td:nth-child(3)').text();
                    phone = $(this).find('td:nth-child(4)').text();
                    email = $(this).find('td:nth-child(5)').text();

                    // 2.1.3.1 - Customer Section > Customer Row Selected > Booking Exist > Modify Customer >>> //
                    $.ajax({
                        url: '/admin/renting/bookings/' + booking_id + '/update',
                        type: 'PUT',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            customer_id: $(this).find('td:nth-child(1)').text()
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
                            console.log(response);
                        },
                        error: function(xhr, status, error) {
                            $('#error-message').text(`Error: ${error}`);
                            $('#modal-error').modal('show');
                        }
                    });
                    // 2.1.3.1 - END <<< //
                    $('#issue-message').text(`Customer Updated for Booking ID: ${booking_id}`);
                    $('#modal-issue').modal('show');

                    $('.customer-row').removeClass('selected-row');
                    $(this).addClass('selected-row');

                    // 2.1.3.2 - Customer Section > Customer Row Selected > Booking Exist > Modify Customer > Update Pending Docs FETCH >>> //
                    $.ajax({
                        url: '/admin/customers/documents/left',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            customer_id: customer_id
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
                            $('#documentUploadForm .row').empty();
                            response.forEach(document => {
                                let documentTypeId = document.id;
                                let code = document.code;
                                let name = document.name;
                                let isRequired = document.is_required;
                                let isVerified = document.is_verified;
                                let fileName = document.file_name;
                                let filePath = document.file_path;

                                let newCol = $('<div class="col-md-6"></div>');
                                let newDocUpload = $(`
                                <div class="mb-3 document-upload" data-document-type="${code}">
                                    <label for="document_${documentTypeId}" class="form-label">${name}</label>
                                    <input class="form-control" type="file" name="documents[${code}]" id="document_${documentTypeId}" ${isRequired ? 'required' : ''} data-document-type-code="${code}">
                                </div>
                            `);
                                if (isVerified) {

                                    let fileLink = $(
                                        '<span class="badge bg-success inforce-padding">Verified</span><br><a href="' +
                                        baseUrl + '/storage/' + filePath +
                                        '" target="_blank">' + fileName + '</a>');
                                    newDocUpload.find('input[type="file"]').remove();
                                    newDocUpload.append(fileLink);
                                }

                                newCol.append(newDocUpload);
                                $('#documentUploadForm .row').append(newCol);

                                let fileUploadCount = $(
                                    '#documentUploadForm input[type="file"]').length;

                                if (fileUploadCount === 0) {
                                    $('#btnDocsCompleted').show();
                                } else {
                                    $('#btnDocsCompleted').show();
                                }

                            });
                        },
                        error: function(xhr, status, error) {
                            $('#error-message').text(`Error: ${error}`);
                            $('#modal-error').modal('show');
                        }
                    });
                    // 2.1.3.2 - END <<< //

                    // 2.1.3.3 - Customer Section > Customer Row Selected > Booking Exist > Modify Customr > Update Pending MOTORBIKE Docs >>> //
                    $.ajax({
                        url: '/admin/customers/documents/motorbikeleft',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            motorbike_id: motorbike_id
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
                            console.log(response);
                            $('#documentUploadFormMotorbike .row').empty();
                            response.forEach(document => {
                                let documentTypeId = document.id;
                                let code = document.code;
                                let name = document.name;
                                let isRequired = document.is_required;

                                let newCol = $('<div class="col-md-6"></div>');
                                let newDocUpload = $(`
                                <div class="mb-3 document-upload" data-document-type="${code}">
                                    <label for="document_${documentTypeId}" class="form-label">${name}</label>
                                    <input class="form-control" type="file" name="documents[${code}]" id="document_${documentTypeId}" ${isRequired ? 'required' : ''} data-document-type-code="${code}">
                                </div>
                            `);

                                newCol.append(newDocUpload);
                                $('#documentUploadFormMotorbike .row').append(newCol);

                                let fileUploadCount = $(
                                    '#documentUploadFormMotorbike input[type="file"]'
                                ).length;

                                if (fileUploadCount === 0) {
                                    $('#btnDocsCompleted').show();
                                } else {
                                    $('#btnDocsCompleted').show();
                                }

                            });
                        },
                    });
                    // 2.1.3.3 - END <<< //

                    // Chang1e Priority
                    // var payment_tab = new bootstrap.Tab($('#tab-payment'));
                    // payment_tab.show();
                    // New Priority
                    var agreement_tab = new bootstrap.Tab($('#tab-agreement'));
                    agreement_tab.show();


                    $('.selected-customer').show();

                    // 2.1.3.4 - Customer Section > Customer Row Selected > Booking Exist > Modify Customer > Update Pending Docs > Data update into Quickview >>> //
                    $('.selection-section .customercard').html(`
                    <h6 class="text-center">Customer</h6>
                    <div class="table-responsive">
                    <table class="table table-dark table-borderless mb-1">
                        <thead>
                            <tr>
                                <th class="inforce-font-size" >Full Name</th>
                                <th class="inforce-font-size" !important;" >Phone</th>
                                <th class="inforce-font-size" !important;" >Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>${fullname}</td>
                                <td>${phone}</td>
                                <td>${email}</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                `);
                    // 2.1.3.4 - END <<< //

                }
                // 2.1.4 - Customer Section > Customer Row Selected > Booking Exist > Customer Locked >>> //
                else {
                    $('#issue-message').text(
                        `The Booking ID:${booking_id} is locked for Customer: ${fullname}.\nIf you made mistake, please contact the Thiago or William.`
                    );
                    $('#modal-issue').modal('show');
                }
                // 2.1.4 - END <<< //
            });
            // 2.1 - END <<< //
            // 2.2 - Customer Section > Customer Selected (First Time) Modal btn >>> //
            $('#modal-customer #btn-confirm-customer-selection').click(function() {

                // 2.2.1 - Customer Section > Customer Selected (First Time) > Booking Not Exists > Customer Pending Document Fetch >>> //
                $.ajax({
                    url: '/admin/customers/documents/left',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        customer_id: customer_id
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
                        $('#documentUploadForm .row').empty();

                        response.forEach(document => {
                            let documentTypeId = document.id;
                            let code = document.code;
                            let name = document.name;
                            let isRequired = document.is_required;
                            let isVerified = document.is_verified;
                            let fileName = document.file_name;
                            let filePath = document.file_path;

                            let newCol = $('<div class="col-md-6"></div>');
                            let newDocUpload = $(`
                                <div class="mb-3 document-upload" data-document-type="${code}">
                                    <label for="document_${documentTypeId}" class="form-label">${name}</label>
                                    <input class="form-control" type="file" name="documents[${code}]" id="document_${documentTypeId}" ${isRequired ? 'required' : ''} data-document-type-code="${code}">
                                </div>
                            `);
                            if (isVerified) {

                                let fileLink = $(
                                    '<span class="badge bg-success inforce-padding">Verified</span><br><a href="' +
                                    baseUrl + '/storage/' + filePath +
                                    '" target="_blank">' + fileName + '</a>');
                                newDocUpload.find('input[type="file"]').remove();
                                newDocUpload.append(fileLink);
                            }

                            newCol.append(newDocUpload);
                            $('#documentUploadForm .row').append(newCol);

                            let fileUploadCount = $(
                                '#documentUploadForm input[type="file"]').length;

                            if (fileUploadCount === 0) {
                                $('#btnDocsCompleted').show();
                            } else {
                                $('#btnDocsCompleted').show();
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        $('#issue-message').text(`${error}`);
                        $('#modal-issue').modal('show');
                    }
                });
                // 2.2.1 - END <<< //
                // 2.2.2 - Customer Section > Customer Row Selected > Booking NOT Exist > Update Pending MOTORBIKE Docs >>> //
                $.ajax({
                    url: '/admin/customers/documents/motorbikeleft',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        motorbike_id: motorbike_id
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
                        console.log(response);
                        $('#documentUploadFormMotorbike .row').empty();
                        response.forEach(document => {
                            let documentTypeId = document.id;
                            let code = document.code;
                            let name = document.name;
                            let isRequired = document.is_required;

                            let newCol = $('<div class="col-md-6"></div>');
                            let newDocUpload = $(`
                            <div class="mb-3 document-upload" data-document-type="${code}">
                                <label for="document_${documentTypeId}" class="form-label">${name}</label>
                                <input class="form-control" type="file" name="documents[${code}]" id="document_${documentTypeId}" ${isRequired ? 'required' : ''} data-document-type-code="${code}">
                            </div>
                        `);

                            newCol.append(newDocUpload);
                            $('#documentUploadFormMotorbike .row').append(newCol);

                            let fileUploadCount = $(
                                    '#documentUploadFormMotorbike input[type="file"]')
                                .length;

                            if (fileUploadCount === 0) {
                                $('#btnDocsCompleted').show();
                            } else {
                                $('#btnDocsCompleted').show();
                            }

                        });
                    },
                });
                // 2.2.2 - END <<< //

                isCustomerSelected = true;

                if (isCustomerSelected) {
                    $('#tab-customer').html('CUSTOMER <i class="mdi mdi-check-all"></i>');
                }

                // Chang1e Priority
                // var payment_tab = new bootstrap.Tab($('#tab-payment'));
                // payment_tab.show();
                // New Priority
                var agreement_tab = new bootstrap.Tab($('#tab-agreement'));
                agreement_tab.show();

                $('.selected-customer').show();

                // 2.2.1 - Customer Section > Customer Row Selected > Quick View >>> //
                $('.selection-section .customercard').html(`
                    <h6 class="text-center">Customer</h6>
                    <div class="table-responsive">
                    <table class="table table-dark table-borderless mb-1">
                        <thead>
                            <tr>
                                <th class="inforce-font-size" >Full Name</th>
                                <th class="inforce-font-size" !important;" >Phone</th>
                                <th class="inforce-font-size" !important;" >Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>${fullname}</td>
                                <td>${phone}</td>
                                <td>${email}</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                `);
                // 2.2.1 - END <<< //

                var data = {
                    customer_id: customer_id,
                    motorbike_id: motorbike_id,
                    amount: total,
                    deposit: minimum_deposit,
                    weekly: weekly_price,
                };

                // 2.2.2 - Customer Section > Customer Row Selected > New Booking > AJAX Request >>> //
                if (isVehicleSelected && isCustomerSelected) {

                    if (booking_id === null || isNaN(booking_id)) {

                        $.ajax({
                            url: '/admin/renting/bookings/create',
                            type: 'POST',
                            data: JSON.stringify(data),
                            contentType: 'application/json; charset=utf-8',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                                booking_id = response.booking_id;
                                status = response.state;
                                invoice_id = response.invoice_id;
                                is_posted = response.is_posted;

                                var date = new Date(response.start_date);
                                var day = ("0" + date.getDate()).slice(-2);
                                var month = ("0" + (date.getMonth() + 1)).slice(-2);
                                var year = date.getFullYear().toString().substr(-2);
                                start_date = day + "-" + month + "-" + year;

                                console.log(response);

                                // 2.2.2.1 - Customer Section > Customer Row Selected > New Booking > AJAX Request > Quick View >>> //
                                $('.selection-section .bookingcard').html(`
                                <h6 class="text-center">Booking</h6>
                                <div class="table-responsive">
                                    <table class="table table-dark table-borderless mb-1">
                                        <thead>
                                        <tr>
                                            <th class="inforce-font-size" >Booking ID</th>
                                            <th class="inforce-font-size" !important;" style="display: none;">TRANSACTION NO.</th>
                                            <th class="inforce-font-size" !important;" >START DATE</th>
                                            <th class="inforce-font-size" !important;" >BOOKING STATUS</th>
                                            <th class="inforce-font-size" !important;" >STARTED</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="booking_id">${booking_id}</td>
                                            <td id="transaction_id" style="display: none;"></td>
                                            <td id="start_date">${start_date}</td>
                                            <td id="bookingStatus">${status}</td>
                                            <td id="bookingStarted">${is_posted ? 'YES' : 'NO'}</td>
                                        </tr>
                                    </tbody>
                                </div>`);
                                // 2.2.2.1 - END <<< //

                            },
                            error: function(xhr, status, error) {
                                $('#issue-message').text(`${xhr.responseText}\n${error}`);
                                $('#modal-issue').modal('show');
                                console.error(xhr.responseText);
                            }
                        });
                    } else if (booking_id !== null && !isNaN(booking_id)) {
                        console.log('Booking ID:', booking_id);
                        $('#issue-message').text(
                            `Adding new Vehicle?\nBooking ID already exists: ${booking_id}`);
                        $('#modal-issue').modal('show');
                    }
                } else {
                    alert('Please select a vehicle and customer first');
                }

                $('#modal-customer').modal('hide');
                // 2.2.2 - END <<< //
            });
            // 2.2 - END <<< //
            // 2.3 - Customer Section > Customer Selected (First Time) Cancel No Impact | Second+ Time No Impact & remain Last one >>> //
            $('#btn-cancel-customer-selection').click(function() {
                $('#modal-customer').modal('hide');
                $('.customer-row').removeClass('selected-row');
                $(this).addClass('selected-row');
            });
            // 2 - END <<< //
            // 3.0 - Payment Section >>> //
            $('#btnReceiveAmount').click(function() {

                if (invoice_begin === false) {
                    return;
                }

                if (isVehicleSelected === false) {
                    $('#issue-message').text(`Please select a vehicle first`);
                    $('#modal-issue').modal('show');
                    return;
                }

                if (isCustomerSelected === false) {
                    $('#issue-message').text(`Please select a customer first`);
                    $('#modal-issue').modal('show');
                    return;
                }

                // 3.0.1 - Payment Section > Fetch Available Payment Methods >>> ////
                $.get("{{ '/admin/payment-methods' }}", function(data) {

                    // 3.0.1.1 - Payment Section > Fetch Available Payment Methods > Generate Template for P.Method Selection >>> ////
                    $('#paymentdropdown').empty();
                    $('#paymentdropdown').append('<option selected>Payment Method</option>');
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
                                var charCode = (e.which) ? e.which : e.keyCode;
                                if (charCode != 46 && charCode > 31 &&
                                    (charCode < 48 || charCode > 57))
                                    return false;
                                // prevent multiple decimal points
                                if (charCode == 46 && $(this).val().indexOf('.') != -1)
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
            // 3.1 - Payment Section > Pay Later >>> //
            $('#btnPayLater').click(function() {

                if (isVehicleSelected === false) {
                    $('#issue-message').text(`Please select a vehicle first`);
                    $('#modal-issue').modal('show');
                    return;
                }

                if (isCustomerSelected === false) {
                    $('#issue-message').text(`Please select a customer first`);
                    $('#modal-issue').modal('show');
                    return;
                }

                if (isAgreementSelected === false) {
                    $('#issue-message').text(`Please send the agreement for signature and review first`);
                    $('#modal-issue').modal('show');
                    return;
                }

                isPaymentSelected = true;

                $('#issue-message').text(
                    `Booking ID: ${booking_id} is not complete.\nPayment is pending.\nDocument verification is pending.\n\nAgreement signature is pending.`
                );
                $('#modal-issue').modal('show');

                $('#tab-payment').html('PAYMENT <i class="mdi mdi-check-all"></i>');
                var documents_tab = new bootstrap.Tab($('#tab-documents'));
                documents_tab.show();
                isPaymentSelected = true;
            });
            // 3.1 - END <<< //
            // 3.2 - Payment Section > Confirm Amount >>> //
            $('#modal-paynow #btn-confirm-pay-selection').click(function() {

                var payment_method = $('#paymentdropdown').val();
                var payment_value = $('#paymentvalue').val();

                // 3.1.1 - Payment Section > Confirm Amount > Check for P.Method >>> (RentingController::updateBooking) //
                if (payment_method && payment_method !== "" && payment_method !== "Payment Method") {

                    if (payment_value || payment_value > 0) {

                        //disable button btn-confirm-pay-selection
                        $('#modal-paynow #btn-confirm-pay-selection').prop('disabled', true);

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
                                console.log(response);

                                console.log(response);
                                transaction_id = response.transaction_id;
                                state = response.state;
                                is_posted = response.is_posted;

                                // Quickview update
                                $('.selection-section .bookingcard').empty().html(`
                                <h6 class="text-center">Booking</h6>
                                <div class="table-responsive">
                                    <table class="table table-dark table-borderless mb-1">
                                        <thead>
                                        <tr>
                                            <th class="inforce-font-size" >Booking ID</th>
                                            <th class="inforce-font-size" !important;" style="display: none;">TRANSACTION NO.</th>
                                            <th class="inforce-font-size" !important;" >START DATE</th>
                                            <th class="inforce-font-size" !important;" >BOOKING STATUS</th>
                                            <th class="inforce-font-size" !important;" >STARTED</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="booking_id">${booking_id}</td>
                                            <td id="transaction_id" style="display: none;"></td>
                                            <td id="start_date">${start_date}</td>
                                            <td id="bookingStatus">${state}</td>
                                            <td id="bookingStarted">${is_posted ? 'YES' : 'NO'}</td>
                                        </tr>
                                    </tbody>
                                </div>`);

                                $('#deposit').text(response.deposit.toFixed(2));
                                $('#weekly').text(response.weekly.toFixed(2));
                                $('#total').text(response.total.toFixed(2));
                                $('#paidamt').text(response.paid.toFixed(2));
                                $('#balance').text(response.balance.toFixed(2));
                                updated_total = response.balance;
                                $('#rem-payment-payable').text(updated_total.toFixed(2));
                                if (response.balance > 0) {
                                    alert('Payment received. Remaining balance: £' + response
                                        .balance.toFixed(2));
                                } else {
                                    // Quickview update
                                    $('.selection-section .bookingcard').empty().html(`
                                        <h6 class="text-center">Booking</h6>
                                        <div class="table-responsive">
                                            <table class="table table-dark table-borderless mb-1">
                                                <thead>
                                                <tr>
                                                    <th class="inforce-font-size" >Booking ID</th>
                                                    <th class="inforce-font-size" !important;" style="display: none;">TRANSACTION NO.</th>
                                                    <th class="inforce-font-size" !important;" >START DATE</th>
                                                    <th class="inforce-font-size" !important;" >BOOKING STATUS</th>
                                                    <th class="inforce-font-size" !important;" >STARTED</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td id="booking_id">${booking_id}</td>
                                                    <td id="transaction_id" style="display: none;"></td>
                                                    <td id="start_date">${start_date}</td>
                                                    <td id="bookingStatus">${state}</td>
                                                    <td id="bookingStarted">${is_posted ? 'YES' : 'NO'}</td>
                                                </tr>
                                            </tbody>
                                        </div>`);
                                    $('#modal-success h4').text('Complete Payment Received');
                                    $('#modal-success #success-message').text(
                                        `Payment fully received. Payment is now complete.\nTransaction ID: ${transaction_id}`
                                    );
                                    $('#modal-success').modal('show');
                                    $('#tab-payment').html(
                                        'PAYMENT <i class="mdi mdi-check-all"></i>');
                                    var documents_tab = new bootstrap.Tab($('#tab-documents'));
                                    documents_tab.show();
                                    isPaymentSelected = true;
                                    $('.selected-payment').show();
                                }
                            },
                            error: function(xhr, status, error) {
                                if ($('#modal-paynow').modal('show')) {
                                    $('#modal-paynow').modal('hide');
                                }

                                if (xhr.status === 422) {
                                    $('#error-message').text(`Error: ${xhr.responseText}`);
                                    $('#modal-error').modal('show');
                                } else {
                                    $('#error-message').text(
                                        `Error: ${xhr.responseText}\n${error}`);
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

            // 4.1 - Upload Documents >>> //
            $(document).on('change', 'input[type="file"]', function(e) {

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

                    var uploadUrl = '/admin/renting/customers/' + customer_id + '/documents/upload';
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
                        },
                        error: function(xhr, status, error) {
                            $('#error-message').text(`Error, Upload failed: \n ${error}`);
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

            // 4.2 - Upload Documents > Upload File >>> //
            $('#btnDocsCompleted').click(function() {
                if (customer_id !== null && customer_id !== undefined && isPaymentSelected === true) {
                    isDocumentsSelected = true;
                    if (isDocumentsSelected) {
                        // 4.2.3 - Upload Documents Link Generation >>> //
                        $.ajax({
                            url: `/generate-docs-upload-link-access/${customer_id}?booking_id=${booking_id}`,
                            type: 'GET',
                            beforeSend: function() {
                                $('#modal-wait').modal(
                                    'show'); // Show a loading modal before request
                            },
                            complete: function() {
                                setTimeout(function() {
                                    $('#modal-wait').modal(
                                        'hide'
                                    ); // Hide the modal after request completes
                                }, 500);
                            },
                            success: function(response) {
                                setTimeout(function() {
                                    $('#modal-wait').modal(
                                        'hide'); // Ensure modal is hidden on success
                                }, 500);
                                console.log("Response received:", response);

                                if (response.uploadLink) {
                                    // Assuming response contains a URL to upload documents
                                    console.log("Upload Link:", response.uploadLink);
                                    // Update the UI with the link or enable a button with the link
                                    $('#upload-link').attr('href', response.uploadLink).text(
                                        "Upload Documents");
                                    $('#upload-link').show();
                                    $('#info-message').html('<a href="' + response.uploadLink +
                                        '">Upload Documents</a>');
                                    // $('#modal-info').modal('show');
                                } else {
                                    console.error("No upload link received in response");
                                    $('#info-message').text("Error: No upload link available");
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
                        // 4.2.3 - END <<< //


                        $('#tab-documents').html('DOCUMENTS CHECKLIST <i class="mdi mdi-check-all"></i>');
                        // Change Priority
                        // var agreement_tab = new bootstrap.Tab($('#tab-agreement'));
                        // agreement_tab.show();
                        // NEW PRIORITY
                        if (isAgreementSelected) {
                            $('#tab-agreement').html('AGREEMENT <i class="mdi mdi-check-all"></i>');
                            // var complete_tab = new bootstrap.Tab($('#tab-complete'));
                            console.log(transaction_id);
                            console.log(booking_id);
                            $('#finish-message-booking-id').text(booking_id);
                            $('#finish-message-tran-id').text(invoice_id);

                            // Open the modal
                            $('#modal-finished').modal('show');
                        }
                        isDocumentsSelected = true;
                    }
                } else {
                    $('#error-message').text('Complete Your Payment First');
                    $('#modal-error').modal('show');
                }
            });
            // 4.2 - END <<< //
            // 4.3 - QR Code Generation >>> //
            $('#btnGenQR').click(function() {

                genQR = true;

                // $('#issue-message').text('QR code generation is not implemented yet\nPlease Send the link via email to the customer. Thank you.');
                // $('#modal-issue').modal('show');

                if (isVehicleSelected === false) {
                    $('#issue-message').text('Please select a vehicle first');
                    $('#modal-issue').modal('show');
                    return;
                }

                if (isCustomerSelected === false) {
                    $('#issue-message').text('Please select a customer first');
                    $('#modal-issue').modal('show');
                    return;
                }

                // Due to Priority Change - 24-03-2024 - Agreement First
                // if (isDocumentsSelected === false) {
                //     $('#issue-message').text('Please complete the documents checklist first');
                //     $('#modal-issue').modal('show');
                //     return;
                // }

                // 4.3.1 - QR Code Generation > FETCH QRCODE >>> ///////
                $.ajax({
                    url: `/generate-agreement-access/${customer_id}?booking_id=${booking_id}`,
                    type: 'GET',
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
                        console.log(response);
                        var img = `<img src="${response.qrImage}" alt="QR Code" />`;
                        $('#qr-area').html(img);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error generating QR code: ", error);
                        console.error("Error generating QR code: ", xhr);
                        $('#error-message').text(`Error: ${error}`);
                        $('#modal-error').modal('show');
                    }
                });
                // 4.3.1 - END <<< ///////

                // 4.3.2 - Update Record upon Rental Agreement Generation Signature >>> ///////
                $.ajax({
                    url: `/admin/renting/bookings/${booking_id}/startbooking`,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        booking_id: booking_id
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
                        console.log(response);
                        $('#info-message').html(
                            '<p>Rental Agreement Sent for Signature for booking: ' +
                            booking_id +
                            '.</p><p>Please inform the customer to check the email for the link</p><p>Or offer QR CODE to customer to scan.'
                        );
                        $('#modal-info').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error("Error starting booking: ", error);
                        $('#error-message').text(`Error: ${error}`);
                        $('#modal-error').modal('show');
                    }
                });
                // 4.3.2 - END <<< ///////

            });
            // 4.3 - END <<< //
            // 4.4 - Send Agreement >>> //
            $('#btnNextPayTab').click(function() {

                if (genQR === false) {
                    $('#issue-message').text('Kindly Click on "Generate QR & Send" First.');
                    $('#modal-issue').modal('show');
                    return;
                }

                if (isVehicleSelected === false) {
                    $('#issue-message').text('Please select a vehicle first');
                    $('#modal-issue').modal('show');
                    return;
                }

                if (isCustomerSelected === false) {
                    $('#issue-message').text('Please select a customer first');
                    $('#modal-issue').modal('show');
                    return;
                }

                if (isPaymentSelected === true) {
                    $('#issue-message').text('Payment already received. Please proceed to the next tab');
                    $('#modal-issue').modal('show');
                    return;
                }

                // if (isDocumentsSelected === true) {
                // $('#issue-message').text('Documents already Checked. Please proceed to the next tab');
                // $('#modal-issue').modal('show');
                // return;
                // }

                // due to priority change - 24-03-2024 - Agreement First
                // if (isDocumentsSelected === false) {
                //     alert('Please complete the documents checklist first');
                //     return;
                // }

                if ($('#chkaccept').is(':checked')) {

                    $('#signature-pad-booking-id').empty(); // Clear previous inputs if any
                    $('#signature-pad-booking-id').append(
                        `<input type="hidden" name="booking_id" value="${booking_id}">
                 <input type="hidden" name="customer_id" value="${customer_id}">`
                    );

                    // modal show modal-signaturepad
                    // $('#modal-signaturepad').modal('show');

                    isAgreementSelected = true;
                    if (isAgreementSelected) {
                        $('#tab-agreement').html('AGREEMENT <i class="mdi mdi-check-all"></i>');
                    }
                    // Change Priority - 24-03-2024 agreement first
                    // if (isAgreementSelected) {

                    //     $('#tab-agreement').html('AGREEMENT <i class="mdi mdi-check-all"></i>');

                    //     // var complete_tab = new bootstrap.Tab($('#tab-complete'));

                    //     console.log(transaction_id);
                    //     console.log(booking_id);
                    //     $('#finish-message-booking-id').text(booking_id);
                    //     $('#finish-message-tran-id').text(transaction_id);

                    //     // Open the modal
                    //     $('#modal-finished').modal('show');
                    // }

                    // New Priority
                    var payment_tab = new bootstrap.Tab($('#tab-payment'));
                    payment_tab.show();

                } else {
                    alert('Kindly Mark the Checkbox to proceed');
                }

            });
            // 4.4 - END <<< //

            // 4.5 - Finish Booking >>> //
            $('#btnFinished').click(function() {
                if (isAgreementSelected === true && isCompleteSelected === false && isPaymentSelected ===
                    true) {
                    isCompleteSelected = true;
                    if (isCompleteSelected) {
                        $('#tab-complete').html('DONE <i class="mdi mdi-check-all"></i>');
                    }
                    invoice_begin = false;
                    booking_id = null;
                    invoice_id = null;
                    status = null;
                    state = null;
                    is_posted = false;
                    start_date = null;
                    regno = null;
                    make = null;
                    model = null;
                    motorbike_id = null;
                    customer_id = null;
                    fullname = null;
                    genQR = false;
                    phone = null;
                    email = null;
                    transaction_id = null;
                    isVehicleSelected = false;
                    isCustomerSelected = false;
                    isPaymentSelected = false;
                    location.reload();
                }
            });
            // 4.5 - END <<< //
            // PREVENT PAGE RELOAD UNLESS IVNOICE IS SUBMITTED
            window.onbeforeunload = function() {
                if (invoice_begin) {
                    return "Data will be lost if you leave the page, are you sure?";
                }
            };

        });
    </script>
