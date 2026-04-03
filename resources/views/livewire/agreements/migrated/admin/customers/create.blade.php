@extends('layouts.admin')

@section('content')
    <div class="content-page">
        <!-- Content -->
        <div class="content">
            <div class="container-fluid">
                <!-- Form Row -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title">Create Customer</h4>
                                <form id="newCustomerForm">
                                    @csrf
                                    <div class="row" id="input-row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="first_name">First Name</label>
                                                <input type="text" class="form-control" id="first_name" name="first_name"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="last_name">Last Name</label>
                                                <input type="text" class="form-control" id="last_name" name="last_name"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="dob">Date of Birth</label>
                                                <input type="date" class="form-control" id="dob" name="dob"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="email">Email</label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    required>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="address">Address</label>
                                                <input type="text" class="form-control" id="address" name="address"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="postcode">Postcode</label>
                                                <input type="text" class="form-control" id="postcode" name="postcode"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="city">City</label>
                                                <input type="text" class="form-control" id="city" name="city"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="nationality">Nationality</label>
                                                <input type="text" class="form-control" id="nationality"
                                                    name="nationality" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="phone">Phone</label>
                                                <input type="tel" class="form-control" id="phone" name="phone"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="whatsapp">Whatsapp
                                                    <span style="font-size:11px">(Note: if Phone & Whatsapp on Same Number
                                                        Just Copied here.)</span>
                                                </label>
                                                <input type="tel" class="form-control" id="whatsapp" name="whatsapp">
                                            </div>
                                            <div class="mb-3">
                                                <label for="emergency_contact">Emergency Contact <span
                                                        style="font-size:11px">(If customer not provide, type - or 0 to
                                                        avoid)</span></label>
                                                <input type="tel" class="form-control" id="emergency_contact"
                                                    name="emergency_contact" required>
                                            </div>

                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-success" id="btnSave">
                                        Save
                                    </button>
                                    <div id="errorMessagesRegNo" style="color: red;"></div>
                                </form>

                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-12">

                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Customer List</h4>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" style="font-size:11px">
                                    <thead>
                                        <tr>
                                            <th>id</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Phone</th>
                                            <th>WHATSAPP</th>
                                            <th>EMERGENCY CONTACT</th>
                                            <th>email</th>
                                            <th>DOB</th>
                                            <th>Address</th>
                                            <th>Postcode</th>
                                            <th>City</th>
                                            <th>Nationality</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($customers as $customer)
                                            <tr>
                                                <td>{{ $customer->id }}</td>
                                                <td>{{ $customer->first_name }}</td>
                                                <td>{{ $customer->last_name }}</td>
                                                <td>{{ $customer->phone }}</td>
                                                <td>{{ $customer->whatsapp }}</td>
                                                <td>{{ $customer->emergency_contact }}</td>
                                                <td>{{ $customer->email }}</td>
                                                <td>{{ $customer->dob ? $customer->dob->format('Y-m-d') : 'n/a' }}</td>
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
                    </div>
                </div>
                <!-- Result Row -->
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            var selected_id = null;

            document.getElementById('btnSave').disabled = true;

            function checkFields() {
                const fields = [
                    document.getElementById('first_name').value,
                    document.getElementById('last_name').value,
                    document.getElementById('email').value,
                    document.getElementById('phone').value,
                    document.getElementById('dob').value,
                    document.getElementById('emergency_contact').value,
                    document.getElementById('whatsapp').value,
                    document.getElementById('address').value,
                    document.getElementById('postcode').value,
                    document.getElementById('city').value,
                    document.getElementById('nationality').value,
                ];

                const allFilled = fields.every(field => field !== '');

                document.getElementById('btnSave').disabled = !allFilled;
            }

            ['first_name', 'last_name', 'email', 'phone', 'emergency_contact', 'whatsapp', 'dob', 'address',
                'postcode', 'city',
                'nationality'
            ]
            .forEach(id => {
                document.getElementById(id).addEventListener('input', checkFields);
            });

            checkFields();

            document.getElementById('btnSave').addEventListener('click', function() {

                const firstName = document.getElementById('first_name').value;
                const lastName = document.getElementById('last_name').value;
                const email = document.getElementById('email').value;
                const phone = document.getElementById('phone').value;
                const whatsapp = document.getElementById('whatsapp').value;
                const emergencyContact = document.getElementById('emergency_contact').value;
                const dob = document.getElementById('dob').value;
                const address = document.getElementById('address').value;
                const postcode = document.getElementById('postcode').value;
                const city = document.getElementById('city').value;
                const nationality = document.getElementById('nationality').value;

                const data = {
                    id: selected_id !== null ? selected_id : null,
                    first_name: firstName,
                    last_name: lastName,
                    dob: dob,
                    email: email,
                    whatsapp: whatsapp,
                    emergency_contact: emergencyContact,
                    address: address,
                    postcode: postcode,
                    phone: phone,
                    city: city,
                    nationality: nationality,
                    _token: document.querySelector('input[name="_token"]').value
                };

                fetch('/admin/renting/customers', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data),
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        }
                        throw new Error('Missing Data or Already Exists: ' + response.status);
                    })
                    .then(data => {
                        console.log('Success:', data);
                        alert('Customer created successfully');
                        window.location.href = '/admin/renting/customers/create';
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                        document.getElementById('errorMessagesRegNo').innerText = 'Error: ' + error
                            .message;
                    });
            });

            $('table tbody tr').on('click', function() {

                selected_id = $(this).find('td:first').text();
                var first_name = $(this).find('td:nth-child(2)').text();
                var last_name = $(this).find('td:nth-child(3)').text();
                var phone = $(this).find('td:nth-child(4)').text();
                var whatsapp = $(this).find('td:nth-child(5)').text();
                var emergency_contact = $(this).find('td:nth-child(6)').text();
                var email = $(this).find('td:nth-child(7)').text();
                var dob = $(this).find('td:nth-child(8)').text();
                var address = $(this).find('td:nth-child(9)').text();
                var postcode = $(this).find('td:nth-child(10)').text();
                var city = $(this).find('td:nth-child(11)').text();
                var nationality = $(this).find('td:nth-child(12)').text();

                $('#first_name').val(first_name);
                $('#last_name').val(last_name);
                $('#phone').val(phone);
                $('#whatsapp').val(whatsapp);
                $('#emergency_contact').val(emergency_contact);
                $('#email').val(email);
                $('#dob').val(dob);
                $('#address').val(address);
                $('#postcode').val(postcode);
                $('#city').val(city);
                $('#nationality').val(nationality);

                document.getElementById('btnSave').disabled = false;
            });



        });
    </script>
@endsection
