<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>Upload documents | Neguinhomotors</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Upload your rental documents securely.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" href="{{ asset('/assets/images/white-bg-ico.ico') }}">
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <link rel="stylesheet" href="{{ ngn_asset('agreement-signing-public.css') }}">
    @include('livewire.agreements.partials.signing-theme-boot')
</head>

<body class="agreement-signing-page">
    <script>
        (function () {
            var stored = localStorage.getItem('ngn-theme');
            if (stored === 'dark' || stored === 'light') {
                document.body.setAttribute('data-theme', stored);
            }
        })();
    </script>
    <input id="customer_id" type="hidden" value="{{ $customer->id }}">
    <input id="motorbike_id" type="hidden" value="{{ $motorbike?->id ?? '' }}">
    <input id="booking_id" type="hidden" value="{{ $booking->id }}">

    <div class="container py-4">
        <div class="upload-doc-topbar">
            <div>
                <img
                    src="{{ asset(config('agreement.brand.web_logo_light', 'img/ngn-motor-logo-fit-small-ngn.png')) }}"
                    alt="Neguinho Motors"
                    class="upload-doc-logo-light"
                >
                <img
                    src="{{ asset(config('agreement.brand.web_logo_dark', 'img/ngn-motor-logo-fit-small-ngn.png')) }}"
                    alt="Neguinho Motors"
                    class="upload-doc-logo-dark"
                >
            </div>
            <button type="button" id="agreement-theme-toggle" class="agreement-theme-toggle" aria-label="Toggle light and dark mode" title="Light or dark mode">
                <svg class="icon-moon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                <svg class="icon-sun" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </button>
        </div>

        <div class="upload-doc-intro mb-3">
            <h1>Documents checklist</h1>
            <p>Upload all possible documents as available now. Take a clear picture of each document or upload from your device.</p>
        </div>

        <div id="document-reupload-banner" class="upload-doc-alert upload-doc-alert-danger upload-doc-banner-reupload" style="display:none" role="alert"></div>
        <div id="document-load-status" class="upload-doc-alert upload-doc-alert-info" role="status">Loading documents…</div>

        <div id="document-section">
            <div class="agreement-signing-panel upload-doc-panel">
                <h2 class="upload-doc-section-title">Your documents</h2>
                <form id="documentUploadForm" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                    </div>
                </form>
            </div>

            <div class="agreement-signing-panel upload-doc-panel">
                <h2 class="upload-doc-section-title">Motorbike documents</h2>
                <form id="documentUploadFormMotorbike" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var customer_id = $('#customer_id').val();
            var motorbike_id = $('#motorbike_id').val();
            var booking_id = $('#booking_id').val();
            var pendingLoads = 0;
            var loadedDocuments = [];

            function markLoadDone() {
                pendingLoads--;
                if (pendingLoads <= 0) {
                    $('#document-load-status').hide();
                }
            }

            function showLoadError(message) {
                $('#document-load-status')
                    .removeClass('upload-doc-alert-info')
                    .addClass('upload-doc-alert-danger')
                    .text(message || 'Could not load documents. Please refresh and try again.');
            }

            function showReuploadBanner(documents) {
                var rejected = documents.filter(function(doc) {
                    return doc.status === 'rejected';
                });

                if (rejected.length === 0) {
                    $('#document-reupload-banner').hide();
                    return;
                }

                var names = rejected.map(function(doc) { return doc.name; }).join(', ');
                $('#document-reupload-banner')
                    .html('<strong>Action needed:</strong> our team asked you to upload a new copy of '
                        + (rejected.length === 1 ? '<strong>' + rejected[0].name + '</strong>' : rejected.length + ' document(s)')
                        + (rejected.length > 1 ? ' (' + names + ')' : '')
                        + '. Use the file fields marked <strong>Re-upload requested</strong> below.')
                    .show();
            }

            function renderDocumentField(document, idPrefix) {
                var documentTypeId = document.id;
                var code = document.code;
                var name = document.name;
                var isRequired = !!document.is_required;
                var fileName = document.file_name;
                var status = document.status || (document.is_verified ? 'approved' : (fileName ? 'pending_review' : 'missing'));
                var validUntil = document.valid_until || '';

                var panelClass = (status === 'rejected' || status === 'expired') ? ' upload-doc-reupload-panel' : '';
                var newDocUpload = $('<div class="document-upload' + panelClass + '" data-document-type="' + code + '">' +
                    '<label for="' + idPrefix + documentTypeId + '" class="form-label">' + name +
                    (isRequired ? ' <span class="text-danger">*</span>' : '') +
                    '</label>' +
                    '<input class="form-control" type="file" name="documents[' + code + ']" id="' + idPrefix + documentTypeId + '" ' +
                        (isRequired ? 'required' : '') + ' data-document-type-code="' + code + '">' +
                    '<label class="form-label mt-2 mb-1" for="' + idPrefix + documentTypeId + '_expiry">Valid until (optional)</label>' +
                    '<input class="form-control form-control-sm upload-doc-expiry" type="date" id="' + idPrefix + documentTypeId + '_expiry" ' +
                        'data-document-type-code="' + code + '" value="' + validUntil + '">' +
                    '<div class="form-text">Optional expiry helps us ask again only when this document runs out.</div>' +
                    '</div>');

                if (status === 'approved') {
                    newDocUpload.find('input[type="file"]').remove();
                    newDocUpload.find('input.upload-doc-expiry').remove();
                    newDocUpload.find('label.mt-2').remove();
                    newDocUpload.find('.form-text').remove();
                    newDocUpload.append('<span class="upload-doc-badge upload-doc-badge-success">Approved</span>');
                    if (validUntil) {
                        newDocUpload.append('<div class="upload-doc-filename">Valid until: ' + validUntil + '</div>');
                    }
                } else if (status === 'expired') {
                    newDocUpload.prepend(
                        '<span class="upload-doc-badge upload-doc-badge-reupload">Expired — re-upload</span>' +
                        '<p class="upload-doc-reupload-text">This document has expired. Please upload a new copy' +
                        (validUntil ? ' (was valid until ' + validUntil + ')' : '') + '.</p>'
                    );
                    if (fileName) {
                        newDocUpload.append('<div class="upload-doc-filename upload-doc-filename-muted">Previous file: ' + fileName + '</div>');
                    }
                } else if (status === 'rejected') {
                    newDocUpload.prepend(
                        '<span class="upload-doc-badge upload-doc-badge-reupload">Re-upload requested</span>' +
                        '<p class="upload-doc-reupload-text">Please upload a new, clear copy of this document.</p>'
                    );
                    if (document.rejection_reason) {
                        newDocUpload.append('<div class="upload-doc-filename"><strong>Reason:</strong> ' + document.rejection_reason + '</div>');
                    }
                    if (fileName) {
                        newDocUpload.append('<div class="upload-doc-filename upload-doc-filename-muted">Previous file: ' + fileName + '</div>');
                    }
                } else if (status === 'pending_review' && fileName) {
                    newDocUpload.find('input[type="file"]').remove();
                    newDocUpload.find('input.upload-doc-expiry').remove();
                    newDocUpload.find('label.mt-2').remove();
                    newDocUpload.find('.form-text').remove();
                    newDocUpload.append(
                        '<span class="upload-doc-badge upload-doc-badge-pending">Awaiting review</span>' +
                        '<div class="upload-doc-filename">' + fileName + '</div>'
                    );
                }

                return $('<div class="col-md-6"></div>').append(newDocUpload);
            }

            pendingLoads++;
            $.ajax({
                url: '/customers/documents/left',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    customer_id: customer_id
                },
                success: function(response) {
                    $('#documentUploadForm .row').empty();

                    if (!Array.isArray(response) || response.length === 0) {
                        $('#documentUploadForm .row').append(
                            '<div class="col-12"><p class="text-muted mb-0">No customer documents required.</p></div>'
                        );
                        markLoadDone();
                        return;
                    }

                    response.forEach(function(document) {
                        $('#documentUploadForm .row').append(renderDocumentField(document, 'document_'));
                    });

                    loadedDocuments = response;
                    showReuploadBanner(loadedDocuments);
                    markLoadDone();
                },
                error: function(xhr) {
                    showLoadError((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Could not load customer documents.');
                }
            });

            pendingLoads++;
            $.ajax({
                url: '/customers/documents/motorbikeleft',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    motorbike_id: motorbike_id,
                    customer_id: customer_id,
                    booking_id: booking_id
                },
                success: function(response) {
                    $('#documentUploadFormMotorbike .row').empty();

                    if (!Array.isArray(response) || response.length === 0) {
                        $('#documentUploadFormMotorbike .row').append(
                            '<div class="col-12"><p class="text-muted mb-0">No motorbike documents required.</p></div>'
                        );
                        markLoadDone();
                        return;
                    }

                    response.forEach(function(document) {
                        $('#documentUploadFormMotorbike .row').append(renderDocumentField(document, 'mb_document_'));
                    });

                    loadedDocuments = loadedDocuments.concat(response);
                    showReuploadBanner(loadedDocuments);
                    markLoadDone();
                },
                error: function(xhr) {
                    showLoadError((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Could not load motorbike documents.');
                }
            });

            $(document).on('change', 'input[type="file"]', function() {
                var fileInput = $(this);
                var documentTypeCode = fileInput.data('document-type-code');
                var file = fileInput.get(0).files[0];
                var expiryInput = fileInput.closest('.document-upload').find('input.upload-doc-expiry');

                if (!file) {
                    return;
                }

                var formData = new FormData();
                formData.append('document', file);
                formData.append('documentTypeCode', documentTypeCode);
                formData.append('bookingID', booking_id);
                formData.append('motorbikeID', motorbike_id);
                if (expiryInput.length && expiryInput.val()) {
                    formData.append('valid_until', expiryInput.val());
                }
                formData.append('_token', $('input[name="_token"]').val());

                $.ajax({
                    url: '/customers/' + customer_id + '/documents/upload',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        alert('Document uploaded successfully');
                        location.reload();
                    },
                    error: function() {
                        alert('Document upload failed');
                    }
                });
            });
        });
    </script>
</body>

</html>
