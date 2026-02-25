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

{{-- Classify Labels

Allocate Class

Connect with Renting

Connect Customer / Keeper

Connect with Installment

Connect PCN

Connect Recovery

Connect Claim

Connect Repair --}}

{{--
@if (is_array($current_keeper) && isset($current_keeper[0]))
@foreach ($current_keeper as $keeper)
    <p>Motorbike ID: {{ $keeper['MOTORBIKE_ID'] }}</p>
    <p>Vehicle Class: {{ $keeper['VEH_CLASS'] }}</p>
    <p>User: {{ $keeper['USER'] ?? 'Unknown User' }}</p>
@endforeach
@elseif(is_array($current_keeper) && isset($current_keeper['MOTORBIKE_ID']))
<p>Motorbike ID: {{ $current_keeper['MOTORBIKE_ID'] }}</p>
<p>Vehicle Class: {{ $current_keeper['VEH_CLASS'] }}</p>
<p>User: {{ $current_keeper['USER'] ?? 'Unknown User' }}</p>
@else
<p>No keeper information available.</p>
@endif --}}

<div class="container-fluid" style="width:99%">

    <div class="info-box">
        @if ($current_keeper instanceof \Illuminate\Support\Collection && $current_keeper->isNotEmpty())

            @php $keepers = $current_keeper->toArray(); @endphp

            <div class="info-box-content">
                <i class="fa fa-motorcycle"></i>
                @foreach ($keepers as $keeper)
                    <span class="info-box-text">Current Keeper is </span>
                    <span class="info-box-number">{{ $keeper->USER ?? 'Unknown User' }}</span>
                    <span class="info-box-text"> under </span>
                    <span class="info-box-number">{{ $keeper->VEH_CLASS }}</span>
                    <span class="info-box-text"> database. </span>
                @endforeach
            </div>
        @elseif (is_array($current_keeper) && count($current_keeper) > 0)
            @php $keepers = array_values($current_keeper); @endphp
            <i class="fa fa-motorcycle"></i>
            <div class="info-box-content">
                @foreach ($keepers as $keeper)
                    <span class="info-box-text">Current Keeper is </span>
                    <span class="info-box-number">{{ $current_keeper['USER'] ?? 'Unknown User' }}</span>
                    <span class="info-box-text"> under </span>
                    <span class="info-box-number">{{ $current_keeper['VEH_CLASS'] }}</span>
                    <span class="info-box-text"> database. </span>
                @endforeach
            </div>
        @elseif (is_array($current_keeper) && isset($current_keeper['MOTORBIKE_ID']))
            <span class="info-box-icon bg-aqua"><i class="fa fa-motorcycle"></i></span>
            @if ($current_keeper['VEH_CLASS'] == 'CLAIM')
                <div class="info-box-content">
                    <span class="info-box-text">The Vehicle is </span>
                    <span class="info-box-number">{{ $current_keeper['USER'] ?? 'Unknown User' }}</span>
                    <span class="info-box-text"> shown under </span>
                    <span class="info-box-number">{{ $current_keeper['VEH_CLASS'] }}</span>
                    <span class="info-box-text"> database. The Keeper {{ $current_keeper['USER'] ?? 'Unknown User' }}
                        shows.</span>
                </div>
            @elseif($current_keeper['VEH_CLASS'] == 'REPAIR')
                <div class="info-box-content">
                    <span class="info-box-text">Found </span>
                    <span class="info-box-number">{{ $current_keeper['USER'] ?? 'Unknown User' }}</span>
                    <span class="info-box-text"> under </span>
                    <span class="info-box-number">{{ $current_keeper['VEH_CLASS'] }}</span>
                    <span class="info-box-text"> database. </span>
                </div>
            @endif
        @else
            <span class="info-box-text">No keeper information available.</span>
        @endif
    </div>

    <span style="font-size:7px">Veh. Compliance id: {{ $id }}</span>
    @if ($pcn_debt)
        <span style="font-size:7px">Motorbike ID: {{ $pcn_debt->MOTORBIKE_ID }}</span>
        <div class="row">
            <div class="col-md-2 col-lg-2">
                <div class="card text-bg-dark mb-1">
                    <div class="card-body">
                        <h5 class="card-title">PCN's Dues</h5>
                        <table class="table" style="font-size:13px">
                            <tr>
                                <td>Total:</td>
                                <td>{{ $pcn_debt->TOTAL_PCN ?? 0 }}</td>
                            </tr>
                            </tr>
                            <tr>
                                <td>Total Closed:</td>
                                <td>{{ $pcn_debt->TOTAL_CLOSED ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td>Total Amount (Reduced):</td>
                                <td>{{ $pcn_debt->TOTAL_REDUCED ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td>Total Amount (Full):</td>
                                <td>{{ $pcn_debt->FULL_AMOUNT ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td>No. of PCN Paid by NGN:</td>
                                <td>{{ $pcn_debt->TOTAL_NGN_PAID ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td>Total Admin Fee (25/PCN):</td>
                                <td>{{ $pcn_debt->ADMIN_FEE ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td>Paid by NGN:</td>
                                <td>{{ $pcn_debt->PAID_BY_NGN ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td>Customer Paid:</td>
                                <td>{{ $pcn_debt->TOTAL_PAID ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td>PCN Balance:</td>
                                <td>{{ ($pcn_debt->TOTAL_REDUCED ?? 0) - ($pcn_debt->TOTAL_PAID ?? 0) }}</td>
                            </tr>
                            <tr>
                                <td style="font-size:10px">Total Balance (PCN Balance + Admin Fee):</td>
                                <td>{{ ($pcn_debt->TOTAL_REDUCED ?? 0) - ($pcn_debt->TOTAL_PAID ?? 0) + $pcn_debt->ADMIN_FEE }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-8 col-lg-8">
                <div class="card text-bg-light mb-3">
                    <div class="card-header">PCN HISTORY</div>
                    <div class="card-body">
                        {{-- <h5 class="card-title">Light card title</h5> --}}
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>PCN Number</th>
                                    <th>Date of Contravention</th>
                                    <th>Time of Contravention</th>
                                    <th>Case Closed</th>
                                    <th>FULL Amount</th>
                                    <th>Reduced Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pcn_list as $pcn)
                                    <tr>
                                        <td>{{ $pcn->PCN_NUMBER }}</td>
                                        <td>{{ $pcn->DOC }}</td>
                                        <td>{{ $pcn->TOC }}</td>
                                        @if ($pcn->IS_CLOSED == 0)
                                            <td>No</td>
                                        @else
                                            <td>Yes</td>
                                        @endif
                                        <td>{{ $pcn->FULL_AMOUNT }}</td>

                                        <td>{{ $pcn->TOTAL_REDUCED }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

</div>
@else
<div class="alert alert-info">
    No PCN's Found... √
</div>
@endif


<div class="clearfix"></div>
