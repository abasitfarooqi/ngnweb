<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
    .info-box {
        padding: 4px !important;
        margin-bottom: 4px;
        font-size: 12px;

        background-color: #f9f9f9;
        border-left: 3px solid #3c8dbc;
    }

    .contracts-container {
        /* background-color: #333; */
        background-color: #E2F9FF;
        padding: 20px;
    }

    .contract-link {
        color: #505146;
        font-size: 13px;
        text-decoration: none !important;
        display: block;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .contract-link:hover {
        color: #000000;
        font-size: 15px !important;
        font-weight: bold;
        font-style: italic;
    }

    .fa-file {
        font-size: 24px;
        margin-right: 10px;
    }

    .contract-details {
        display: inline;
    }
</style>

<div class="container-fluid" style="width:99% ;">

    <div class="container-fluid"
        style="width:90% ;background-color: rgb(209, 55, 56); color: rgb(0, 0, 0); padding: 8px; margin-top: 20px; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);">

        <div class="m-t-10 m-b-10 p-l-10 p-r-10 p-t-10 p-b-10" bp-section="crud-details-row">
            <div class="row">
                <div class="col-md-12">

                    @if ($customer->created_at)
                        <span style="letter-spacing: 1.5; line-height: 1.2; font-size:11px; color:white !important"><i
                                style="padding-bottom:4px !important; margin-bottom:4px !important;">Registered
                                with us
                                on
                            </i>
                            <b>{{ $customer->created_at->format('Y-M-d H:i') }}</b></span>
                        <br>
                        <br>
                    @endif

                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box">
                                <strong>Address:</strong> {{ $customer->address }} {{ $customer->postcode }}
                            </div>
                            <div class="info-box">
                                @include('components.star_rating', ['rating' => $customer->rating])
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <strong>WhatsApp:</strong> {{ $customer->whatsapp }}
                            </div>
                            <div class="info-box">
                                <strong>Emergency Contact:</strong> {{ $customer->emergency_contact }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <strong>Nationality:</strong> {{ $customer->nationality }}
                            </div>
                            <div class="info-box">
                                <strong> Note: </strong> {{ $customer->reputation_note }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($customer_contracts && count($customer_contracts) > 0)
        <div class="container-fluid"
            style="width:90% ;background-color: rgb(209, 55, 56); color: rgb(255, 255, 255); padding: 8px; margin-top: 20px; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);">
            <span
                style="letter-spacing: 1.6; line-height: 1.4; font-weight: bold; font-size: 13px;padding-bottom:8px">DOWNLOAD
                CONTRACTS</span>
            <div class="contracts-container">
            @foreach ($customer_contracts as $cc)
                <div id="contract-{{ $cc->DOC_ID }}" class="d-flex align-items-center mb-2">
                    <a href="/storage/{{ $cc->FILE_PATH }}" target="_blank" class="contract-link flex-grow-1">
                        <i class="fa fa-file" aria-hidden="true"></i>
                        <div class="contract-details">
                            <span class="file-name">{{ $cc->FILE_NAME }}</span>
                            <span style="font-size:8px; padding-left:10px !important">
                                <br><b>CREATED AT: </b><span class="created-at">{{ $cc->CREATED_AT }}</span>
                                <b>CONTRACT ID: </b><span class="created-at">{{ $cc->DOC_ID }}</span>
                            </span>
                        </div>
                    </a>
                    <button 
                        class="btn btn-sm {{ $cc->SENT_PRIVATE ? 'btn-secondary' : 'btn-danger' }} ml-2"
                        onclick="if (!{{ $cc->SENT_PRIVATE ? 'true' : 'false' }}) deleteItem('{{ route('customer.contract.destroy', $cc->DOC_ID) }}', 'contract-{{ $cc->DOC_ID }}')"
                        {{ $cc->SENT_PRIVATE ? 'disabled' : '' }}>
                        {{ $cc->SENT_PRIVATE ? 'Already Moved' : 'Move to Private' }}
                    </button>
                </div>
            @endforeach

            </div>
        </div>
    @endif

    @if ($customer_agreements && count($customer_agreements) > 0)
        <div class="container-fluid"
            style="width:90% ;background-color: rgb(209, 55, 56); color: #fff; padding: 8px; margin-top: 20px; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);">
            <span style="letter-spacing: 1.6; line-height: 1.4; font-weight: bold; font-size: 13px;">DOWNLOAD AGREEMENT CONTRACTS</span>
            <div class="contracts-container">
                @foreach ($customer_agreements as $cc)
                    <div id="agreement-{{ $cc->CONTRACT_ID }}" class="d-flex align-items-center mb-2">
                        <a href="/storage/{{ $cc->FILE_PATH }}" target="_blank" class="contract-link flex-grow-1">
                            <i class="fa fa-file" aria-hidden="true"></i>
                            <div class="contract-details">
                                <span class="file-name">{{ $cc->FILE_NAME }}</span>
                                <span style="font-size:8px; padding-left:10px !important">
                                    <br><b>CREATED AT: </b>{{ $cc->CREATED_AT }}
                                    <b>CONTRACT ID: </b>{{ $cc->CONTRACT_ID }}
                                </span>
                            </div>
                        </a>
                        
                        <button 
                            class="btn btn-sm {{ $cc->SENT_PRIVATE ? 'btn-secondary' : 'btn-danger' }} ml-2"
                            onclick="if (!{{ $cc->SENT_PRIVATE ? 'true' : 'false' }}) deleteItem('{{ route('customer.agreement.delete', $cc->DOC_ID) }}', 'agreement-{{ $cc->DOC_ID }}')"
                            {{ $cc->SENT_PRIVATE ? 'disabled' : '' }}>
                            {{ $cc->SENT_PRIVATE ? 'Already Moved' : 'Move to Private' }}
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif


    @if ($customer_documents && count($customer_documents) > 0)
        <div class="container-fluid"
            style="width:90% ;background-color: rgb(209, 55, 56); color: rgb(255, 255, 255); padding: 8px; margin-top: 20px; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);">
            <span
                style="letter-spacing: 1.6; line-height: 1.4; font-weight: bold; font-size: 13px;padding-bottom:8px">DOWNLOAD
                DOCUMENTS</span>
            <div class="contracts-container">
            @foreach ($customer_documents as $cd)
                <div id="doc-{{ $cd->ID }}" class="d-flex align-items-center mb-2">
                    <a href="/storage/{{ $cd->FILE_PATH }}" target="_blank" class="contract-link flex-grow-1">
                        <i class="fa fa-file text-black" aria-hidden="true"></i>
                        <div class="contract-details">
                            <span class="file-name">{{ $cd->name }}</span>
                            <span style="font-size:8px; padding-left:10px !important">
                                <br><b>CREATED AT: </b><span class="created-at">{{ $cd->CREATED_AT }}</span>
                                <b>DOCUMENT ID: </b><span class="created-at">{{ $cd->ID }}</span>
                            </span>
                        </div>
                    </a>
                    <button 
                        class="btn btn-sm {{ $cd->SENT_PRIVATE ? 'btn-secondary' : 'btn-danger' }} ml-2"
                        onclick="if (!{{ $cd->SENT_PRIVATE ? 'true' : 'false' }}) deleteItem('{{ route('customer.document.delete', $cd->ID) }}', 'doc-{{ $cd->ID }}')"
                        {{ $cd->SENT_PRIVATE ? 'disabled' : '' }}>
                        {{ $cd->SENT_PRIVATE ? 'Already Moved' : 'Move to Private' }}
                    </button>
                </div>
            @endforeach

            </div>
        </div>
    @endif


    @if ($customer_bookings && count($customer_bookings) > 0)

        <div class="container-fluid"
            style="width:90% ;background-color: rgb(209, 55, 56); color: rgb(0, 0, 0); padding: 8px; margin-top: 20px; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);">
            <span
                style="letter-spacing: 1.6; line-height: 1.4; font-weight: bold; font-size: 13px; color:white !important">RENTING
                HISTORY</span>
        </div>

        <div class=" container-fluid" style="width:90% ;">
            <div class="">
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>BID</th>
                                <th>REG NO</th>
                                <th>START DATE</th>
                                <th>STATE</th>
                                <th>...</th>
                                <th>DEPOSIT</th>
                                <th>WEEKLY RENT</th>
                                <th>END DATE</th>
                                <th>WEEKS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customer_bookings as $booking)
                                <tr>
                                    <td>{{ $booking->BID }}</td>
                                    <td>{{ $booking->REG_NO }}</td>
                                    <td>{{ $booking->START_DATE }}</td>
                                    <td>{{ $booking->STATE }}</td>
                                    <td>
                                        @if ($booking->IS_POSTED == 1)
                                        @elseif ($booking->IS_POSTED == 0)
                                            Vehicle may never issued or booking premature cancelled.
                                        @endif
                                    </td>
                                    <td>{{ $booking->DEPOSIT }}</td>
                                    <td>{{ $booking->WEEKLY_RENT }}</td>
                                    <td>{{ $booking->END_DATE ? date('Y-m-d', strtotime($booking->END_DATE)) : 'N/A' }}
                                    </td>
                                    <td>{{ $booking->WEEKS }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @endif

    @if ($pcns_relevant_booking && $pcns_relevant_booking->count() > 0)

        <div class="container-fluid"
            style="width:90% ;background-color: rgb(209, 55, 56); color: rgb(0, 0, 0); padding: 8px; margin-top: 20px; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);">

            <span
                style="letter-spacing: 1.6; line-height: 1.4; font-weight: bold; font-size: 13px; color:white !important">PCN
                HISTORY FROM
                BOOKINGS</span>
        </div>
        <div class=" container-fluid" style="width:90% ;">
            <div class="">
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>PCN Number</th>
                                <th>Date of Contravention</th>
                                <th>Time of Contravention</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pcns_relevant_booking as $pcn)
                                <tr>
                                    <td>{{ $pcn->PCN_NO }}</td>
                                    <td>{{ $pcn->PCN_DATE }}</td>
                                    <td>{{ $pcn->PCN_TIME }}</td>
                                    <td>{{ $pcn->IS_CLOSED ? 'Closed' : 'Open' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if ($customer_contracts->isNotEmpty())
    <div class="container-fluid"
        style="width:90%; background-color: rgb(209, 55, 56); color:#fff; padding: 8px; margin-top: 20px; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);">
        <span style="letter-spacing: 1.6; line-height: 1.4; font-weight: bold; font-size: 13px;">CONTRACT DETAILS</span>
    </div>
    <div class="container-fluid" style="width:90%;">
        <div class="box-body table-responsive no-padding">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Contract ID</th>
                        <th>Contract Date</th>
                        <th>Logbook Sent</th>
                        <th>Reg No</th>
                        <!-- <th>Actions</th> -->
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customer_contracts as $contract)
                        @php
                            // Find matching finance_application to get extra details
                            $fa = $customer_contract->firstWhere('CONTRACT_ID', $contract->CONTRACT_ID);
                        @endphp
                        <tr id="contract-{{ $contract->DOC_ID }}">
                            <td>{{ $contract->CONTRACT_ID }}</td>
                            <td>{{ $fa && $fa->CONTRACT_DATE ? \Carbon\Carbon::parse($fa->CONTRACT_DATE)->format('Y-m-d') : 'N/A' }}</td>
                            <td>{{ $fa && $fa->LOGBOOK_SENT ? 'Yes' : 'No' }}</td>
                            <td>{{ $fa ? $fa->REG_NO : 'N/A' }}</td>
                            <!-- <td>
                                <button 
                                    class="btn btn-sm btn-danger"
                                    onclick="deleteItem('{{ route('customer.contract.destroy', $contract->DOC_ID) }}', 'contract-{{ $contract->DOC_ID }}')">
                                    Move to Private
                                </button>
                            </td> -->
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif



    @if ($pcns_relevant_contract && $pcns_relevant_contract->count() > 0)

        <div class="container-fluid"
            style="width:90% ;background-color: rgb(209, 55, 56); color: rgb(0, 0, 0); padding: 8px; margin-top: 20px; border: 1px solid #ccc; border-radius: 5px; box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);">
            <span
                style="letter-spacing: 1.6; line-height: 1.4; font-weight: bold; font-size: 13px; color:white !important">PCN
                HISTORY</span>
        </div>

        <div class=" container-fluid" style="width:90% ;">
            <div class="">
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>PCN Number</th>
                                <th>Date of Contravention</th>
                                <th>Time of Contravention</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pcns_relevant_contract as $pcn)
                                <tr>
                                    <td>{{ $pcn->PCN_NO }}</td>
                                    <td>{{ $pcn->PCN_DATE }}</td>
                                    <td>{{ $pcn->PCN_TIME }}</td>
                                    <td>{{ $pcn->REG_NO }}</td>
                                    <td>{{ $pcn->IS_CLOSED ? 'Closed' : 'Open' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

</div>
<div class="clearfix"></div>
<script>
function deleteItem(url, rowId) {
    if (!confirm('Are you sure you want to move this item?')) return;

    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById(rowId).remove();
            new Noty({ type: 'success', text: data.message }).show();
        } else {
            new Noty({ type: 'error', text: data.message || 'Failed to delete.' }).show();
        }
    })
    .catch(() => {
        new Noty({ type: 'error', text: 'Something went wrong.' }).show();
    });
}
</script>
