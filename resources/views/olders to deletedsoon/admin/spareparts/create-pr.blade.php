@extends('layouts.admin')

@section('content')
    {{-- MODAL GO THERE >> --}}
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
    {{-- MODAL GO THERE << --}}
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <h4>PURCHASE REQUEST</h4>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                {{-- Brand OEM --}}
                                <div class="form-group">
                                    <label for="brand_name">Make</label>
                                    <select class="form-control" name="brand_name" id="brand_name">
                                        <option value="">Select Make</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <br>
                                {{-- Year --}}
                                <div class="form-group">
                                    <label for="bike_year">Year</label>
                                    <input type="text" class="form-control" name="bike_year" id="bike_year"
                                        placeholder="Enter Year">
                                </div>
                                <br>
                                {{-- Color --}}
                                <div class="form-group">
                                    <label for="bike_color">Colour Code</label>
                                    <input type="text" class="form-control" name="bike_color" id="bike_color"
                                        placeholder="Enter Colour Code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                {{-- Model --}}
                                <div class="form-group">
                                    <label for="bike_model">Models</label>
                                    <select class="form-control" name="bike_model" id="bike_model">
                                        <option value="">Select Model</option>
                                    </select>
                                </div>
                                <br>
                                {{-- Chesis no --}}
                                <div class="form-group">
                                    <label for="chassis_no">Chassis No</label>
                                    <input type="text" class="form-control" name="chassis_no" id="chassis_no"
                                        placeholder="Enter Chassis No">
                                </div>
                                <br>
                                {{-- Reg No --}}
                                <div class="form-group">
                                    <label for="reg_no">Reg No</label>
                                    <input type="text" class="form-control" name="reg_no" id="reg_no"
                                        placeholder="Enter Reg No">
                                </div>
                            </div>
                        </div>
                        <hr>
                        {{-- Part No & Place --}}
                        <fieldset>
                            <legend>Part Number & Place</legend>
                            <div class="row">
                                <div class="col-md-3">
                                    {{-- Part No --}}
                                    <div class="form-group">
                                        <label for="part_number">Part Number</label>
                                        <input type="text" class="form-control" name="part_number" id="part_number"
                                            placeholder="Enter Part Number">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    {{-- Part Position --}}
                                    <div class="form-group">
                                        <label for="part_position">Place</label>
                                        <select class="form-control" name="part_position" id="part_position">
                                            <option value="">Select Part Place</option>
                                            <option value="Front Left">Front Left</option>
                                            <option value="Front">Front</option>
                                            <option value="Front Right">Front Right</option>
                                            <option value="Middle Left">Middle Left</option>
                                            <option value="Middle">Middle</option>
                                            <option value="Middle Right">Middle Right</option>
                                            <option value="Lower Left">Lower Left</option>
                                            <option value="Lower">Lower</option>
                                            <option value="Lower Right">Lower Right</option>
                                            <option value="Rear Left">Rear Left</option>
                                            <option value="Rear">Rear</option>
                                            <option value="Rear Right">Rear Right</option>
                                            <option value="Rear Right">Under</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    {{-- Quantity --}}
                                    <div class="row">
                                        <div class="form-group">
                                            <label for="quantity">Quantity</label>
                                            <input type="number" class="form-control" name="quantity" id="quantity"
                                                placeholder="Enter Quantity">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    {{-- Add Line Item --}}
                                    <button class="btn btn-warning" id="addLineItem">Add Line Item</button>
                                    <br>
                                </div>
                            </div>
                            <div class="row" id="line-items">
                                {{--  Line Items Goes There  --}}
                                <div class="col">
                                    <table id="itemsTable" class="table table-bordered"
                                        style="margin: 20px auto; border-collapse: collapse;">
                                        <thead>
                                            <tr style="border-bottom: 0.4px solid silver;">
                                                <th style="border: none; text-align: left;">Quantity</th>
                                                <th style="border: none; text-align: left;">Part Position</th>
                                                <th style="border: none; text-align: left;">Part Number</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </fieldset>
                        <hr>
                        {{-- Link  --}}
                        <div class="row">
                            <div class="form-group ">
                                <label for="link">Link</label>
                                <input type="text" class="form-control" name="link" id="link"
                                    placeholder="Enter Link">
                            </div>
                        </div>
                        <br>
                        {{-- Link Two
                        <div class="row">
                            <div class="form-group">
                                <label for="link_two">Link Two</label>
                                <input type="text" class="form-control" name="link_two" id="link_two"
                                    placeholder="Enter Link Two">
                            </div>
                        </div>
                        <br> --}}

                        {{-- Picture --}}
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="row">
                                <div class="mb-3 document-upload" data-document-type="code">
                                    <label for="document_id" class="form-label">Picture of Part</label>
                                    <input class="form-control" type="file" name="documents[code]" id="document_id"
                                        required data-document-type-code="code">
                                </div>
                            </div>
                            <br>
                            {{-- Submit --}}
                            <div class="row">
                                <div class="form-group text-center">
                                    <button type="button" class="btn btn-primary" id="submit">Add to
                                        List</button>
                                </div>
                            </div>
                        </form>
                        <br>
                        {{-- PURCHASE ITEMS --}}
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-bordered" id="sparepart_id" style="font-size: 11px;">
                                    <thead>
                                        <tr>
                                            <th>EMPLOYEE ID</th>
                                            <th>MAKE</th>
                                            <th>MODEL</th>
                                            <th>COLOUR CODE</th>
                                            <th>YEAR</th>
                                            <th>CHASSIS No</th>
                                            <th>REG. NO</th>
                                            <th>PART NUMBER</th>
                                            <th>PLACE</th>
                                            <th>QUANTITY</th>
                                            <th>LINK</th>
                                            <th>IMAGE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br>
                        {{-- PURCHASE ITEMS --}}
                        <div class="row">
                            <div class="form-group text-center">
                                <button type="button" class="btn btn-success" id="submitpost">Send to Supplier</button>
                            </div>
                        </div>
                    </div>
                </div> <!-- content -->
            </div>

            <script>
                $(document).ready(function() {

                    var lineItems = [];

                    $('#addLineItem').click(function() {

                        var quantity = $('#quantity').val();
                        var part_position = $('#part_position').val();
                        var part_number = $('#part_number').val();


                        if (!quantity || !part_position || !part_number) {
                            alert('Please provide all information.');
                            return;
                        }

                        lineItems.push({
                            quantity: quantity,
                            part_position: part_position,
                            part_number: part_number
                        });

                        var newRow = $('<tr>');
                        newRow.append('<td>' + quantity + '</td>');
                        newRow.append('<td>' + part_position + '</td>');
                        newRow.append('<td>' + part_number + '</td>');

                        $('#itemsTable tbody').append(newRow);

                        $('#quantity').val('');
                        $('#part_position').val('');
                        $('#part_number').val('');
                    });

                    var baseUrl = "{{ url('/') }}";
                    var prno = null;

                    $('#brand_name, #bike_model, #part_position').select2({
                        placeholder: "Select an option",
                        allowClear: true
                    });

                    $('#brand_name').change(handleBrandChange);
                    $('#bike_model').change(function() {
                        modelId = $(this).val();
                    });


                    function fetchModelsForBrand(brandId, dropdown) {
                        $.ajax({
                            url: '/admin/spareparts/get-bike-models/' + brandId,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                $.each(data, function(key, value) {
                                    dropdown.append('<option value="' + key + '">' + value +
                                        '</option>');
                                });
                            }
                        });
                    }


                    $.ajax({
                        url: '/admin/spareparts/view-pr',
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            console.log('Unposted PR Items:', data);
                            console.log(data);
                            prno = data[0].pr_number;
                            $('#sparepart_id > tbody').empty();
                            $.each(data, function(index, item) {
                                updateTableWithNewItem(item);
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching data:', xhr.responseText);
                        }
                    });



                    $('#submit').click(function(e) {
                        e.preventDefault();
                        if (validateFormData()) {
                            if (prno === null) {
                                console.log('Creating PRNO...');
                                $.ajax({
                                    url: '/admin/spareparts/create-pr',
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {
                                        _token: $('meta[name="csrf-token"]').attr('content')
                                    },
                                    success: function(data) {
                                        prno = data.prno;
                                        console.log('PRNO Created: ', prno);
                                        handleAddToList(prno);
                                    }
                                });
                            } else {
                                handleAddToList(prno);
                            }
                        }
                    });

                    $('#submitpost').click(function(e) {
                        e.preventDefault();
                        if (prno === null) {
                            showModalInfo('Please add items to the list before sending to supplier.');
                            return;
                        } else {
                            console.log("Sending PRNO:", prno);
                            $.ajax({
                                url: '/admin/spareparts/send-to-supplier',
                                type: 'POST',
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                    prno: prno
                                },
                                success: function(response) {
                                    console.log('Server response:', response);
                                    location.reload();
                                    if (response.success) {

                                        prno = null;
                                        $('#sparepart_id > tbody').empty();
                                        alert(
                                            'Items sent to supplier successfully. Refresh pagfegit as'
                                        );
                                    } else {
                                        console.log('Failed to send items to supplier.');
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error("Error response:", xhr.responseText);
                                    console.error("Error details:", xhr.responseJSON);
                                }
                            });
                        }
                    });


                    function handleBrandChange() {
                        var brandId = $(this).val();
                        var modelsDropdown = $('#bike_model');
                        modelsDropdown.empty().append('<option selected="selected" value="">Select Model</option>');
                        if (brandId) {
                            fetchModelsForBrand(brandId, modelsDropdown);
                        }
                    }

                    function handleAddToList(prno) {
                        console.log('Adding to list...', prno);

                        lineItems.forEach(function(item) {

                            var formData = new FormData();
                            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                            formData.append('pr_id', prno);
                            formData.append('brand_name_id', $('#brand_name').val());
                            formData.append('bike_model_id', $('#bike_model').val());
                            formData.append('color', $('#bike_color').val());
                            formData.append('year', $('#bike_year').val());
                            formData.append('chassis_no', $('#chassis_no').val());
                            formData.append('reg_no', $('#reg_no').val());
                            formData.append('part_number', item.part_number);
                            formData.append('part_position', item.part_position);
                            formData.append('quantity', item.quantity);
                            formData.append('link_one', $('#link_one').val());
                            formData.append('link_two', $('#link_two').val());

                            if ($('#document_id')[0].files.length > 0) {
                                formData.append('image', $('#document_id')[0].files[0]);
                            }

                            console.log('Form Data:', formData);
                            $.ajax({
                                url: '/admin/spareparts/add-pr-item',
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    console.log('Server response:', response);
                                    if (response.success) {
                                        console.log(response);
                                        // alert('Item and file uploaded successfully');
                                        response.item.brand_name = response.brand_name;
                                        response.item.bike_model = response.bike_model;
                                        updateTableWithNewItem(response.item);
                                        resetFormFields();
                                    } else {
                                        alert('Failed to upload item and file');
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error(xhr.responseText);
                                    console.error(xhr.responseJSON);
                                }
                            });
                        });


                    }

                    function updateTableWithNewItem(item) {
                        var table = $('#sparepart_id > tbody');
                        var newRow = `<tr style='padding:'>
                                        <td>${item.employee_id}</td>
                                        <td>${item.brand_name}</td>
                                        <td>${item.bike_model}</td>
                                        <td>${item.color}</td>
                                        <td>${item.year}</td>
                                        <td>${item.chassis_no}</td>
                                        <td>${item.reg_no}</td>
                                        <td>${item.part_number}</td>
                                        <td>${item.part_position}</td>
                                        <td>${item.quantity}</td>
                                        <td>${item.link_one || ''}</td>
                                        <td><a href=${item.image}>image</a></td>
                                    </tr>`;
                        table.append(newRow);
                    }


                    function resetFormFields() {
                        $('#brand_name').val('').trigger('change');
                        $('#bike_model').val('').trigger('change');
                        $('#bike_color, #bike_year, #chassis_no, #reg_no, #part_number, #quantity, #link_one, #link_two')
                            .val('');
                        $('#part_position').val('').trigger('change');
                        $('#document_id').val('');
                        $('#itemsTable tbody').empty();
                        lineItems = [];
                    }

                    function showModalInfo(message) {
                        $('#info-message').text(message);
                        $('#modal-info').modal('show');
                    }

                    function validateFormData() {
                        if (!$('#brand_name').val() || !$('#bike_model').val() || !$('#bike_color').val() ||
                            !$('#bike_year').val() || !$('#chassis_no').val() || !$('#reg_no').val() || lineItems.length ===
                            0) {

                            showModalInfo('Please complete all fields before adding to the list.');
                            return false;
                        }
                        return true;
                    }

                });
            </script>
        @endsection
