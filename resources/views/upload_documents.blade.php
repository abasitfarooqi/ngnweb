<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>Admin Dashboard | Neguinhomotors</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Neguinhomotors Admin Dashboard - Control and manage all aspects of Neguinhomotors services including motorbike rentals, customer relations, and rental agreements.">
    <meta name="author" content="Shariq">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('/assets/images/white-bg-ico.ico') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> <!-- App css -->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/custom-css.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style_admin.css') }}" rel="stylesheet" type="text/css" />
    <!-- icons -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- pdf viewer -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.0.379/pdf_viewer.min.css">
</head>

<body>
    <!-- Begin page -->
    <input id="customer_id" type="hidden" value="{{ $customer->id }}">
    <input id="motorbike_id" type="hidden" value="{{ $motorbike->id }}">
    <input id="booking_id" type="hidden" value="{{ $booking->id }}">

    <div class="container">
        <div>
            <h5>DOCUMENTS CHECKLIST</h5>
            <p>Upload all possible documents as available now. <br>Take a clear picture
                of each document or upload from device</p>
            <div id="document-section">
                <form id="documentUploadForm" action="" method="POST" enctype="multipart/form-data">
                    @csrf <!-- CSRF token for security -->
                    <div class="row">
                        <!-- SOMETHING REMVD :) -->
                    </div>
                </form>
                <h5>MOTORBIKES DOCUMENTS</h5>
                <form id="documentUploadFormMotorbike" action="" method="POST" enctype="multipart/form-data">
                    @csrf <!-- CSRF token for security -->
                    <div class="row">
                        <!-- SOMETHING REMVD :) -->
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            var baseUrl = "{{ url('/') }}";
            var customer_id = $('#customer_id').val();
            var motorbike_id = $('#motorbike_id').val();
            var booking_id = $('#booking_id').val();
            // 2.2.1 - Customer Section > Customer Selected (First Time) > Booking Not Exists > Customer Pending Document Fetch >>> //
            $.ajax({
                url: '/customers/documents/left',
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
                                '<span class="badge bg-success inforce-padding">Verified</span><br>'
                            );
                            newDocUpload.find('input[type="file"]').remove();
                            newDocUpload.append(fileLink);
                        } else {
                            if (fileName != null) {
                                let fileLink = $(
                                    '<span class="badge bg-danger inforce-padding">Verifying in Process</span><br>' +
                                    fileName + '</a>');
                                newDocUpload.find('input[type="file"]').remove();
                                newDocUpload.append(fileLink);
                            }
                        }

                        newCol.append(newDocUpload);
                        $('#documentUploadForm .row').append(newCol);

                        let fileUploadCount = $('#documentUploadForm input[type="file"]')
                            .length;

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
                url: '/customers/documents/motorbikeleft',
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
                            '#documentUploadFormMotorbike input[type="file"]').length;

                        if (fileUploadCount === 0) {
                            $('#btnDocsCompleted').show();
                        } else {
                            $('#btnDocsCompleted').show();
                        }

                    });
                },
            });
            // 2.2.2 - END <<< //

            // 4.1 - Upload Documents >>> //
            $(document).on('change', 'input[type="file"]', function(e) {

                // 4.1.1 - Upload Documents > Upload File >>> ///////
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

                var uploadUrl = '/customers/' + customer_id + '/documents/upload';
                $.ajax({
                    url: uploadUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        alert('Document uploaded successfully');
                        // refresh but need to resolve motorbikes docs
                        // location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert('Document upload failed');
                    }
                });

            });
            // 4.1 - END <<< //
        });
    </script>
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/waypoints/lib/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery.counterup/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery-knob/jquery.knob.min.js') }}"></script>
    <script src="{{ asset('assets/libs/morris.js06/morris.min.js') }}"></script>
    <script src="{{ asset('assets/libs/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/dashboard.init.js') }}"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    <script src="{{ asset('assets/js/sign-pad.min.js') }}"></script>

</body>

</html>
