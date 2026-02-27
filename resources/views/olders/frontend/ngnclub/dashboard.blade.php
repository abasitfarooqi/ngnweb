@extends('olders.frontend.main_master_noheadfoot')

@section('title', 'Member Dashboard - NGN Club | NGN - Motorcycle Rentals, Repairs in UK')
@section('meta_keywords')
    <meta name="keywords"
        content="NGN Club, motorcycle rentals, motorcycle repairs, motorcycle MOT, loyalty program, motorbike rewards">
@endsection

@section('meta_description')
    <meta name="description" content="Welcome to your NGN Club member dashboard. Check your rewards and purchases.">
@endsection

@section('content')
    <style>
        body {
         font-size: 19px;
        }
        button.resendbtn::before {
            content: "";
            width: 0%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            background: transparent !important;
            z-index: -1;
            transition: all .3s ease 0s;
        }

        /* UK VRM Styling */
        .uk-vrm-container {
            background: #FED71B;
            border: 3px solid #000;
            border-radius: 5px;
            padding: 0;
            display: inline-block;
            width: 100%;
            position: relative;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background-image: linear-gradient(45deg, #FED71B 25%, #FFE03D 25%, #FFE03D 50%, #FED71B 50%, #FED71B 75%, #FFE03D 75%, #FFE03D 100%);
            background-size: 4px 4px;
        }

        .uk-vrm-input {
            width: 90%;
            height: 25px;
            padding: 8px 12px 8px 35px;
            font-family: 'UKNumberPlate', 'CharlesWright', Arial, sans-serif;
            font-size: 22px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            border: none !important;
            outline: none !important;
            background: transparent !important;
            color: #000;
            text-align: center;
            -webkit-text-fill-color: #000;
            box-shadow: none !important;
            margin: 0;
            appearance: none;
            -webkit-appearance: none;
        }

        .uk-vrm-input::placeholder {
            color: rgba(0, 0, 0, 0.2);
            font-weight: bold;
            letter-spacing: 2px;
            opacity: 0.5;
        }

        /* GB Band Styling */
        .uk-vrm-container::before {
            content: "GB";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 30px;
            background: #0055a4;
            border-top-left-radius: 3px;
            border-bottom-left-radius: 3px;
            color: white;
            font-size: 12px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            letter-spacing: 0;
            border-right: 2px solid #003876;
            z-index: 1;
        }

        /* Remove any white background on autofill */
        .uk-vrm-input:-webkit-autofill,
        .uk-vrm-input:-webkit-autofill:hover,
        .uk-vrm-input:-webkit-autofill:focus,
        .uk-vrm-input:-webkit-autofill:active {
            transition: background-color 5000s ease-in-out 0s;
            -webkit-box-shadow: 0 0 0 30px #FED71B inset !important;
            -webkit-text-fill-color: #000 !important;
            border: none !important;
            outline: none !important;
        }

        /* Add embossed effect */
        .uk-vrm-input {
            text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.2);
        }

        /* Remove inner shadow */
        .uk-vrm-container::after {
            display: none;
        }

        /* Remove focus outline */
        .uk-vrm-input:focus {
            outline: none !important;
            box-shadow: none !important;
            border: none !important;
        }
    </style>

    <div class="page-title parallax parallax1 pagehero-header" style="background-position: 50% 160px;">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="pagehero-title-heading xt">
                        <h1 class="title" style="font-size: 25px;">NGN Club Member Dashboard</h1>
                    </div><!-- /.pagehero-title-heading xt -->
                </div><!-- /.col-md-12 -->
            </div><!-- /.row -->
        </div><!-- /.container -->
    </div>

    <div class="container">
        <br>

        <div class="">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <style>
                /* Media query for larger screens */
                
            </style>

            <div class="welcome-box" style="border-radius:10px; padding:20px; border:4px solid #4CAF50;">
                <div class="text-center">
                    <h5 class="welcome-message">Hey! {{ $clubMember->full_name }}!</h5>
                </div>
                <div class="row ">
                    <div class="col-md-6 col-sm-6 col-xs-12 savings-box">
                        <div class="figure-box savings-amount-text" style="color: #4CAF50;">
                            <p class="p-0 m-0 savings-label"><strong>Your Total Savings</strong></p>
                            <h2 class="savings-amount"><strong>£{{ number_format($total_reward, 2) }}</strong></h2>
                            <p class="p-0 m-0 credits-message">The more you save the more you take</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12 credits-box">
                        <div class="figure-box credits-amount-text" style="color: #FF9800;">
                            <p class="p-0 m-0 credits-label"><strong>Credits Redeemed</strong></p>
                            <h2 class="credits-amount"><strong>£{{ number_format($total_redeemed, 2) }}</strong></h2>
                            <p class="p-0 m-0 credits-message">Very wise</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12 available-credits-box">
                        <div class="figure-box available-credits-text" style="color: #4CAF50;">    
                            <p class="p-0 m-0 available-credits-label"><strong>Available Credits</strong></p>
                            <h2 class="available-credits-amount"><strong>£{{ number_format($total_not_redeemed, 2) }}</strong></h2>
                            <p class="p-0 m-0 ahead-message">Ahead of the game</p>
                        </div>
                    </div>
                </div>

            <div class="clearfix"></div>
        </div>

        @if ($qualified_referal)
            <div class="alert alert-success referral-box" style="padding:10px; margin-top:10px;">
                You are qualified for a referral reward of £5 on each successful referral (T&C Apply).
                <br>
                Você está qualificado para uma recompensa de indicação de £5 / Cada Indicação com Sucesso (Termos e
                Condições se Aplicam).
                <br>
                <button onclick="window.location.href='{{ route('ngnclub.referral', ['id' => $clubMember->id]) }}'"
                    class="btn ngn-btn" style="margin-top:10px;">Refer Now</button>
            </div>
        @endif

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs mt-4" id="dashboardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="sell-tab" data-bs-toggle="tab" href="#sell" role="tab" aria-controls="sell"
                    aria-selected="false">
                    <span class="badge bg-danger"
                        style="font-size: 8px; padding: 5px;margin-bottom:0px; border-radius:9px;padding-top:-3px !important;padding-left:-3px !important;">New</span>
                    Sell your Motorbike
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="purchases-tab" data-bs-toggle="tab" href="#purchases" role="tab"
                    aria-controls="purchases" aria-selected="true">Purchases</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="spendings-tab" data-bs-toggle="tab" href="#spendings" role="tab"
                    aria-controls="spendings" aria-selected="false">Spendings</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="transactions-tab" data-bs-toggle="tab" href="#transactions" role="tab"
                    aria-controls="transactions" aria-selected="false">Transactions</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="referrals-tab" data-bs-toggle="tab" href="#referrals" role="tab"
                    aria-controls="referrals" aria-selected="false">Referrals</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab"
                    aria-controls="profile" aria-selected="false">Profile</a>
            </li>

        </ul>

        <!-- Tabs Content -->
        <div class="tab-content" id="dashboardTabsContent">
            <!-- Purchases Tab -->
            <div class="tab-pane fade show active" id="purchases" role="tabpanel" aria-labelledby="purchases-tab">
                <div id="purchaseTable" style="">
                    <h2 class="mt-3 mb-1" style="color:#c31924;padding-left:10px;border-left:3px solid #c31924">Your
                        Purchases</h2>
                    @if ($purchases->isEmpty())
                        <p>You have no purchases yet.</p>
                    @else
                        <div class="new-design-mb">
                            @foreach ($purchases as $purchase)
                                <div class="mt- mb-2">
                                    <h4 class="m-0 pl-2 pb-1" style="font-weight: 500;">
                                        {{ \Carbon\Carbon::parse($purchase->date)->format('jS F Y, h:i A') }}</h4>

                                    <div class="purchase-box">
                                        <div class="purchase-box-inner d-flex justify-content-between align-items-center">
                                            <div class="purchase-box-left">
                                                <p class="m-0 purchase-invoice-label">
                                                    <strong>POS INVOICE:</strong>
                                                </p>
                                                <h3 class="m-0 purchase-invoice">{{ $purchase->pos_invoice }}</h3>
                                            </div>
                                            <div class="purchase-box-right text-end">
                                                <p class="m-0 credit-label">
                                                    <strong>Credit</strong>
                                                </p>
                                                <h3 class="m-0 credit-amount">
                                                    £{{ number_format($purchase->discount, 2) }}</h3>
                                                <p class="m-0 redemption-info" style="color: {{ $purchase->is_redeemed ? 'green' : 'red' }};">
                                                    @if ($purchase->discount == $purchase->redeem_amount)
                                                        {{ number_format($purchase->redeem_amount, 2) }} Redeemed
                                                    @elseif ($purchase->redeem_amount > 0)
                                                        {{ number_format($purchase->redeem_amount, 2) }} Redeemed - £{{ number_format($purchase->discount - $purchase->redeem_amount, 2) }} left from this purchase
                                                    @else
                                                        £0.00
                                                    @endif
                                                </p>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <style>
                                        
                                    </style>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>


                <!-- --- -->

                
            </div>

            <!-- Spendings Tab -->
            <div class="tab-pane fade" id="spendings" role="tabpanel" aria-labelledby="spendings-tab">
                <div id="spendingTable" style="">
                    <h2 class="mt-3 mb-1" style="color:#c31924;padding-left:10px;border-left:3px solid #c31924">Your
                        Spending Record</h2>
                    <br>
                    <p class="mb-2"><strong>Total spending: £{{ number_format($spendings->sum('total'), 2) }}</strong></p>
                    @if ($spendings->isEmpty())
                        <p>You have no spending records yet.</p>
                    @else
                        <div class="new-design-mb">
                            @foreach ($spendings as $spending)
                                <div class="mt- mb-2">
                                    <h4 class="m-0 pl-2 pb-1" style="font-weight: 500;">
                                        {{ \Carbon\Carbon::parse($spending->date)->format('jS F Y, h:i A') }}</h4>

                                    <div class="purchase-box">
                                        <div class="purchase-box-inner d-flex justify-content-between align-items-center">
                                            <div class="purchase-box-left">
                                                <p class="m-0 purchase-invoice-label">
                                                    <strong>POS INVOICE:</strong>
                                                </p>
                                                <h3 class="m-0 purchase-invoice">{{ $spending->pos_invoice }}</h3>
                                            </div>
                                            <div class="purchase-box-right text-end">
                                                <p class="m-0 credit-label">
                                                    <strong>Total Amount</strong>
                                                </p>
                                                <h3 class="m-0 credit-amount">
                                                    £{{ number_format($spending->total, 2) }}</h3>
                                                @if ($spending->branch_id)
                                                    <p class="m-0 redemption-info" style="color: #666;">
                                                        Branch: {{ $spending->branch_id }}
                                                    </p>
                                                @endif
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Transactions Tab -->
            <div class="tab-pane fade" id="transactions" role="tabpanel" aria-labelledby="transactions-tab">
                <h2 class="mt-3 mb-1" style="color:#c31924;padding-left:10px;border-left:3px solid #c31924">All Transactions</h2>

                {{-- Transactions tab specific styling --}}
                <style>
                    .transactions-table thead th {
                        font-size: 12px;
                        font-weight: 600;
                        letter-spacing: 0.04em;
                        text-transform: uppercase;
                        background-color: #f5f5f5;
                        border-bottom: 2px solid #c31924;
                    }

                    .transactions-table tbody td {
                        font-size: 13px;
                        vertical-align: middle;
                    }

                    .transactions-table tbody td strong {
                        font-weight: 600;
                    }

                    .transactions-table tbody td small {
                        font-size: 11px;
                        color: #6c757d;
                    }

                    .transactions-table tbody tr:nth-child(even) {
                        background-color: #fafafa;
                    }

                    .transactions-table tbody tr:hover {
                        background-color: #f1f3f5;
                    }

                    .transactions-table td.text-end {
                        font-variant-numeric: tabular-nums;
                    }
                </style>

                @if (empty($transactions) || $transactions->isEmpty())
                    <p>You have no transactions yet.</p>
                @else
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-striped table-sm transactions-table mb-0">
                            <thead>
                                <tr>
                                    <th>Inv#</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-end">Discount</th>
                                    <th class="text-end">Redeemed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $tx)
                                    <tr>
                                        <td>
                                            <strong>{{ $tx->pos_invoice }}</strong><br>
                                            <small>{{ $tx->date }}</small>
                                        </td>
                                        <td class="text-end">£{{ number_format($tx->amount, 2) }}</td>
                                        <td class="text-end">£{{ number_format($tx->discount, 2) }}</td>
                                        <td class="text-end">£{{ number_format($tx->redeemed, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Referrals Tab -->
            <div class="tab-pane fade" id="referrals" role="tabpanel" aria-labelledby="referrals-tab">
                <h2 class="mt-3 mb-1" style="color:#c31924;padding-left:10px;border-left:3px solid #c31924">Your Referrals
                </h2>
                @if ($referrals->isEmpty())
                    <p>You have no referrals yet.</p>
                @else
                    <div class="referrals-list mt-3">
                        @foreach ($referrals as $referral)
                            <div class="referral-item mb-3"
                                style="border:1px solid #ccc; border-radius:10px; padding:15px;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Full Name:</strong> {{ $referral->referred_full_name }}</p>
                                        <p><strong>Phone Number:</strong> {{ $referral->referred_phone }}</p>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        @if (!$referral->validated)
                                            <span class="badge badge-warning">Pending</span>
                                        @else
                                            <span class="badge badge-success">Accepted</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Profile Tab -->
            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <h2 class="mt-3 mb-1" style="color:#c31924;padding-left:10px;border-left:3px solid #c31924">Your Profile
                </h2>

                @if (session('profile_success'))
                    <div class="alert alert-success">
                        {{ session('profile_success') }}
                    </div>
                @endif

                @if ($errors->has('profile.*'))
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->get('profile.*') as $error)
                                <li>{{ $error[0] }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('ngnclub.profile.update') }}" method="POST" class="mt-4">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="full_name"
                                        value="{{ $clubMember->full_name }}" readonly>
                                    <small class="text-muted">Contact our store to modify if not correct.</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email"
                                        value="{{ $clubMember->email }}" readonly>
                                    <small class="text-muted">Contact our store to modify if not correct.</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone"
                                        value="{{ $clubMember->phone }}" readonly>

                                </div>
                            </div>
                            <div class="card mt-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Vehicle Details (Optional)</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="make" class="form-label">Make</label>
                                            <input type="text" class="form-control" id="make"
                                                name="profile[make]" value="{{ $clubMember->make }}" maxlength="50"
                                                style="text-transform: uppercase" pattern="[A-Za-z0-9/\s-]*"
                                                title="Only letters, numbers, forward slash, and hyphens allowed">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="model" class="form-label">Model</label>
                                            <input type="text" class="form-control" id="model"
                                                name="profile[model]" value="{{ $clubMember->model }}" maxlength="50"
                                                style="text-transform: uppercase" pattern="[A-Za-z0-9/ -]*">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="year" class="form-label">Year</label>
                                            <input type="text" class="form-control" id="year"
                                                name="profile[year]" value="{{ $clubMember->year }}" pattern="[0-9]*"
                                                inputmode="numeric" maxlength="4"
                                                title="Please enter a valid year between 1960 and 2025">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <label for="vrm" class="form-label"
                                                style="display: flex; justify-content: center;">Vehicle Registration
                                                Number</label>
                                            <div style="display: flex; justify-content: center;">
                                                <div class="uk-vrm-container" style="max-width: 280px; height: 50px;">
                                                    <input type="text" name="profile[vrm]" id="vrm"
                                                        class="uk-vrm-input" value="{{ $clubMember->vrm }}"
                                                        maxlength="12"
                                                        style="height: 100%; width: 100%; text-transform: uppercase !important; background: transparent !important; border: none !important; outline: none !important; box-shadow: none !important; margin: 0; appearance: none; -webkit-appearance: none; padding: 2px 10px 2px 35px !important;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <button type="submit" class="btn ngn-btn">Update Profile</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Sell Tab -->
            <div class="tab-pane fade" id="sell" role="tabpanel" aria-labelledby="sell-tab">
                <h2 class="mt-3 mb-1" style="color:#c31924;padding-left:10px;border-left:3px solid #c31924">Sell Your Bike
                </h2>

                <div class="card mt-4">
                    <div class="card-body">

                        <form id="sellBikeForm" method="POST" action="{{ route('vehicle.estimate') }}">
                            @csrf
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="vrm" class="form-label"
                                        style="display: flex; justify-content: center;">Vehicle Registration Number</label>
                                    <div style="display: flex; justify-content: center;">
                                        <div class="uk-vrm-container" style="max-width: 280px; height: 50px;">
                                            <input type="text" name="vrm" id="vrm" class="uk-vrm-input"
                                                required maxlength="12"
                                                style="height: 100%; width: 100%; text-transform: uppercase !important; 
                                                   background: transparent !important; border: none !important; outline: none !important; 
                                                   box-shadow: none !important; margin: 0; appearance: none; -webkit-appearance: none; 
                                                   padding: 2px 10px 2px 35px !important;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="model" class="form-label">Model</label>
                                    <input type="text" class="form-control text-uppercase" id="model"
                                        name="model" required style="text-transform: uppercase"
                                        pattern="[A-Za-z0-9/ -]*">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="make" class="form-label">Make</label>
                                    <input type="text" class="form-control text-uppercase" id="make-sell"
                                        name="make" required style="text-transform: uppercase"
                                        pattern="[A-Za-z0-9/ -]*">
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="vehicle_year" class="form-label">Year</label>
                                    <select class="form-select" id="vehicle_year" name="vehicle_year" required>
                                        <option value="">Select Year</option>
                                        @for ($year = 2025; $year >= 2014; $year--)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="engine_size" class="form-label">Engine (CC)</label>
                                    <input type="number" class="form-control" id="engine_size" name="engine_size"
                                        min="0" step="1" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="mileage" class="form-label">Mileage</label>
                                    <input type="number" class="form-control" id="mileage" name="mileage"
                                        pattern="[0-9.]*" min="0" step="1" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="base_price" class="form-label">New Vehicle Price (£)</label>
                                    <input type="number" class="form-control" id="base_price" name="base_price"
                                        min="0" step="1" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Condition Rating (1-10): <span id="conditionValue"
                                            class="text-primary fw-bold">5</span></label>
                                    <div class="d-flex justify-content-center align-items-center gap-3">
                                        <div class="condition-rating-container" style="width: 80%;">
                                            <input type="range" class="form-range" id="condition" name="condition"
                                                min="1" max="10" step="1" value="5">
                                            <div class="d-flex justify-content-between px-2">
                                                <small class="text-muted">Poor (1)</small>
                                                <small class="text-muted">Excellent (10)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn ngn-btn">Get Estimate</button>
                            </div>
                        </form>
                        <!-- Estimate Result Section -->
                        <div id="estimateResult" class="mt-4" style="display: none;">
                            <hr>
                            <div class="text-center">
                                <h4 class="mb-4">Estimated Value</h4>
                                <div class="h2 mb-3" style="color: #c31924;">
                                    £<span id="estimatedValue">0</span>
                                </div>
                                <p class="text-muted">
                                    Based on your vehicle's condition and specifications
                                </p>
                                <input type="hidden" id="estimateRecordId" value="">
                                <div class="feedback-buttons mt-3">
                                    <p class="mb-2">How do you feel about this estimate?</p>
                                    <div class="d-flex justify-content-center gap-4">
                                        <button type="button" class="btn btn-outline-success feedback-btn"
                                            data-value="1">
                                            <i class="fa fa-thumbs-up fa-2x" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger feedback-btn"
                                            data-value="0">
                                            <i class="fa fa-thumbs-down fa-2x" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Logout Button -->
        <form action="{{ route('ngnclub.logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn ngn-btn text-center">Logout</button>
        </form>

        <br>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Persistent active tab: restore from localStorage on refresh
            var tabStorageKey = 'ngnclub_dashboard_tab';
            var savedTab = localStorage.getItem(tabStorageKey);
            var validHrefs = ['#sell', '#purchases', '#spendings', '#transactions', '#referrals', '#profile'];
            if (savedTab && validHrefs.indexOf(savedTab) !== -1) {
                var tabTrigger = document.querySelector('#dashboardTabs a[href="' + savedTab + '"]');
                if (tabTrigger && typeof bootstrap !== 'undefined') {
                    var tab = new bootstrap.Tab(tabTrigger);
                    tab.show();
                }
            }
            document.querySelectorAll('#dashboardTabs a[data-bs-toggle="tab"]').forEach(function(link) {
                link.addEventListener('shown.bs.tab', function(e) {
                    var href = e.target.getAttribute('href');
                    if (href && validHrefs.indexOf(href) !== -1) {
                        localStorage.setItem(tabStorageKey, href);
                    }
                });
            });

            const conditionInput = document.getElementById('condition');
            const conditionValue = document.getElementById('conditionValue');

            conditionInput.addEventListener('input', function() {
                conditionValue.textContent = this.value;
            });

            // Form submission handling
            const sellBikeForm = document.getElementById('sellBikeForm');
            const estimateResult = document.getElementById('estimateResult');
            const estimatedValue = document.getElementById('estimatedValue');
            const estimateRecordId = document.getElementById('estimateRecordId');
            const feedbackButtons = document.querySelectorAll('.feedback-btn');

            // Debug log to check if the script is running
            console.log('Script initialized');

            // Get VRM input from the sell bike form specifically
            const vrmInputs = document.querySelectorAll('input[name="vrm"]');
            console.log('Found VRM inputs:', vrmInputs.length);

            // Get the VRM input specifically from the sell bike form
            const vrmInput = sellBikeForm ? sellBikeForm.querySelector('input[name="vrm"]') : null;
            console.log('Sell form VRM Input element:', vrmInput);

            // Add multiple event listeners to catch the blur event
            if (vrmInput) {
                console.log('Adding event listeners to VRM input');

                // Using standard blur
                vrmInput.addEventListener('blur', handleVrmCheck);

                // Using focusout (similar to blur but bubbles)
                vrmInput.addEventListener('focusout', handleVrmCheck);

                // Using change event as backup
                vrmInput.addEventListener('change', handleVrmCheck);

                // Add input event for immediate feedback
                vrmInput.addEventListener('input', () => {
                    console.log('Input event on VRM:', vrmInput.value);
                });
            } else {
                console.error('Sell form VRM input element not found!');
            }

            function handleVrmCheck(event) {
                console.log('VRM event triggered:', event.type);
                console.log('VRM current value:', this.value);

                const registrationNumber = this.value.trim();
                if (!registrationNumber) return;

                // Show loading state
                const makeInput = document.getElementById('make-sell');
                const modelInput = document.getElementById('model');
                const yearSelect = document.getElementById('vehicle_year');
                const engineSizeInput = document.getElementById('engine_size');

                [makeInput, modelInput, yearSelect, engineSizeInput].forEach(input => {
                    input.disabled = true;
                });

                fetch(`/vrm/check-vehicle?registration_number=${encodeURIComponent(registrationNumber)}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data) {
                            const vehicleData = data.data;

                            // Populate form fields
                            if (vehicleData.make) {
                                makeInput.value = vehicleData.make.toUpperCase();
                            }
                            if (vehicleData.model) {
                                modelInput.value = vehicleData.model.toUpperCase();
                            }
                            if (vehicleData.yearOfManufacture) {
                                yearSelect.value = vehicleData.yearOfManufacture;
                            }
                            if (vehicleData.engineCapacity) {
                                engineSizeInput.value = vehicleData.engineCapacity;
                            }
                        } else {
                            console.error('Vehicle check failed:', data.message || 'Unknown error');
                            alert('Could not fetch vehicle details. Please enter them manually.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(
                            'An error occurred while checking the vehicle. Please enter details manually.'
                        );
                    })
                    .finally(() => {
                        // Re-enable inputs
                        [makeInput, modelInput, yearSelect, engineSizeInput].forEach(input => {
                            input.disabled = false;
                        });
                    });
            }

            sellBikeForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                // Show loading state in the estimate section
                if (estimateResult.style.display === 'none') {
                    estimateResult.style.display = 'block';
                }
                estimatedValue.textContent = 'Calculating...';

                fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const value = parseFloat(data.calculated_value);
                            if (!isNaN(value)) {
                                // Animate the value change
                                const currentValue = parseFloat(estimatedValue.textContent.replace(
                                    /[^0-9.-]+/g, "")) || 0;
                                animateValue(currentValue, value, 500); // 500ms duration

                                estimateRecordId.value = data.record_id; // Store the record ID

                                // Reset feedback buttons
                                feedbackButtons.forEach(btn => {
                                    btn.classList.remove('active');
                                    btn.disabled = false;
                                });

                                // Smooth scroll to the result if not in view
                                const resultRect = estimateResult.getBoundingClientRect();
                                if (resultRect.top < 0 || resultRect.bottom > window.innerHeight) {
                                    estimateResult.scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'center'
                                    });
                                }
                            } else {
                                throw new Error('Invalid calculated value received');
                            }
                        } else {
                            throw new Error(data.message || 'Estimation failed');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        estimatedValue.textContent = 'Error';
                        alert('An error occurred while processing your request: ' + error.message);
                    });
            });

            // Add animation function for smooth value transitions
            function animateValue(start, end, duration) {
                const startTime = performance.now();

                function update(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);

                    // Easing function for smooth animation
                    const easeOutQuad = progress * (2 - progress);
                    const current = start + (end - start) * easeOutQuad;

                    estimatedValue.textContent = current.toLocaleString('en-GB', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    if (progress < 1) {
                        requestAnimationFrame(update);
                    }
                }

                requestAnimationFrame(update);
            }

            // Add input event listeners to form fields for real-time validation
            const numericInputs = sellBikeForm.querySelectorAll('input[type="number"]');
            numericInputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value < 0) this.value = 0;
                });
            });

            // Handle feedback buttons
            feedbackButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const recordId = estimateRecordId.value;
                    if (!recordId) {
                        console.error('No estimate record ID found');
                        return;
                    }

                    const likeValue = this.dataset.value;

                    // Disable all feedback buttons
                    feedbackButtons.forEach(b => b.disabled = true);

                    fetch('/vehicle/estimate/feedback', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                record_id: recordId,
                                like: likeValue
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Visual feedback
                                feedbackButtons.forEach(b => {
                                    b.classList.remove('active');
                                    b.disabled = true;
                                });
                                this.classList.add('active');
                            } else {
                                throw new Error(data.message || 'Failed to save feedback');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to save feedback: ' + error.message);
                            // Re-enable buttons on error
                            feedbackButtons.forEach(b => b.disabled = false);
                        });
                });
            });
        });
    </script>

@endsection
