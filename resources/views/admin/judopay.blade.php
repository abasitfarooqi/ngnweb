@extends(backpack_view('blank'))

@php
  $breadcrumbs = [
    'Admin' => backpack_url('dashboard'),
    'Recurring Payments' => false,
  ];
@endphp

@section('content')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none" bp-section="page-header">
        <h5 class="text-capitalize mb-0 ml-2" bp-section="page-heading">Recurring Payment Management</h5>
        <div class="ml-auto d-flex align-items-center">
            <div class="btn-group" role="group" aria-label="Judopay navigation">
                <a href="{{ route('page.judopay.index') }}" class="btn btn-secondary btn-sm" style="font-size: 0.8rem; border-radius: 0;">
                    <i class="fa fa-home"></i> Judopay Home
                </a>
                <a href="{{ route('page.judopay.mit-dashboard') }}" class="btn btn-warning btn-sm" style="font-size: 0.8rem; border-radius: 0;">
                    <i class="fa fa-clock"></i> MIT Dashboard
                </a>
                <a href="{{ route('page.judopay.weekly-mit-queue') }}" class="btn btn-info btn-sm" style="font-size: 0.8rem; border-radius: 0;">
                    <i class="fa fa-calendar"></i> Weekly Schedule
                </a>
            </div>
        </div>
    </section>
    @can('can-run-cit')
        <!-- CUSTOMERS WITH ACTIVE SERVICES -->
        <section class="content container-fluid animated fadeIn" bp-section="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="card" style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                        <div class="card-header" style="background: rgba(52, 73, 94, 0.8); border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <h3 class="card-title mb-0" style="font-size: 1.1rem; color: #ffffff; font-weight: 600;">
                                <i class="fa fa-users" style="color: #3498db;"></i> Customers with Active Services
                            </h3>
                        </div>
                        <div class="card-body" style="background: rgba(44, 62, 80, 0.3);">
                            @if($customers->count() > 0)
                                <!-- Filter Section -->
                                <div class="mb-3" style="background: rgba(52, 73, 94, 0.5); padding: 12px; border: 1px solid rgba(52, 152, 219, 0.3);">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <input type="text" id="filterCustomer" class="form-control form-control-sm" placeholder="Filter by Customer..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(52, 152, 219, 0.3);">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" id="filterServiceType" class="form-control form-control-sm" placeholder="Filter by Service Type..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(52, 152, 219, 0.3);">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" id="filterVehicle" class="form-control form-control-sm" placeholder="Filter by Vehicle..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(52, 152, 219, 0.3);">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" id="filterStatus" class="form-control form-control-sm" placeholder="Filter by Status..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(52, 152, 219, 0.3);">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" id="filterAmount" class="form-control form-control-sm" placeholder="Filter by Amount..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(52, 152, 219, 0.3);">
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <small style="color: #bdc3c7; font-size: 0.65rem;"><i class="fa fa-filter"></i> Real-time filtering - Type to filter table rows</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm" id="customersTable" style="font-size: 0.75rem; background: transparent; color: #ecf0f1;">
                                        <thead>
                                            <tr style="background: rgba(52, 73, 94, 0.6); border-bottom: 2px solid #3498db;">
                                                <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                    <i class="fa fa-user"></i> Customer
                                                </th>
                                                <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                    <i class="fa fa-tag"></i> Service Type
                                                </th>
                                                <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                    <i class="fa fa-motorcycle"></i> Vehicle
                                                </th>
                                                <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                    <i class="fa fa-calendar-plus"></i> Start Date
                                                </th>
                                                <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                    <i class="fa fa-pound-sign"></i> Weekly Amount
                                                </th>
                                                <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                    <i class="fa fa-info-circle"></i> Status
                                                </th>
                                                <th style="color: #3498db; font-weight: 600; border: none; padding: 12px 8px;">
                                                    <i class="fa fa-cogs"></i> Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($customers as $onboarding)
                                                @php $customer = $onboarding->onboardable; @endphp
                                                
                                                @if($customer)
                                                {{-- Active Rentals --}}
                                                @foreach($customer->renting_bookings ?? [] as $rental)
                                                    @foreach($rental->rentingBookingItems as $item)
                                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); transition: all 0.3s ease;" 
                                                            onmouseover="this.style.background='rgba(52, 152, 219, 0.1)'" 
                                                            onmouseout="this.style.background='transparent'">
                                                            <td style="border: none; padding: 12px 8px;">
                                                                <div style="font-size: 0.75rem; color: #ecf0f1;">
                                                                    <strong style="color: #3498db;">{{ $customer->first_name ?? 'N/A' }} {{ $customer->last_name ?? 'N/A' }}</strong><br>
                                                                    <small style="color: #bdc3c7; font-size: 0.7rem;">{{ $customer->email ?? 'N/A' }}</small><br>
                                                                    <small style="color: #bdc3c7; font-size: 0.7rem;">{{ $customer->phone ?? 'N/A' }}</small>
                                                                </div>
                                                            </td>
                                                            <td style="border: none; padding: 12px 8px;">
                                                                <span class="badge" style="background: linear-gradient(45deg, #3498db, #2980b9); color: white; font-size: 0.7rem; padding: 6px 10px; font-weight: 600;">Rental</span><br>
                                                                <small style="color: #bdc3c7; font-size: 0.7rem;">ID: {{ $rental->id }}</small>
                                                            </td>
                                                            <td style="border: none; padding: 12px 8px;">
                                                                <div style="font-size: 0.75rem; color: #ecf0f1;">
                                                                    <strong style="color: #3498db;">{{ $item->motorbike->reg_no ?? 'N/A' }}</strong><br>
                                                                    <small style="color: #bdc3c7; font-size: 0.7rem;">{{ $item->motorbike->make ?? '' }} {{ $item->motorbike->model ?? '' }}</small>
                                                                </div>
                                                            </td>
                                                            <td style="border: none; padding: 12px 8px; color: #bdc3c7;">
                                                                {{ $item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('d/m/Y') : 'N/A' }}
                                                            </td>
                                                            <td style="border: none; padding: 12px 8px; font-weight: 600; color: #27ae60;">
                                                                £{{ number_format($item->weekly_rent, 2) }}
                                                            </td>
                                            @php
                                                $subscription = $onboarding->subscriptions->where('subscribable_id', $rental->id)->where('subscribable_type', 'App\\Models\\RentingBooking')->first();
                                                $subscriptionStatus = $subscription ? $subscription->status : 'no_subscription';
                                            @endphp
                                            <td style="border: none; padding: 12px 8px;">
                                                @if($subscriptionStatus === 'active')
                                                    <span class="badge" style="background: linear-gradient(45deg, #27ae60, #2ecc71); color: white; font-weight: 600; padding: 6px 12px;">
                                                        <i class="fa fa-check"></i> Active
                                                    </span>
                                                @elseif($subscriptionStatus === 'pending')
                                                    <span class="badge" style="background: linear-gradient(45deg, #f39c12, #e67e22); color: white; font-weight: 600; padding: 6px 12px;">
                                                        <i class="fa fa-clock"></i> Pending
                                                    </span>
                                                @elseif($subscriptionStatus === 'paused')
                                                    <span class="badge" style="background: linear-gradient(45deg, #3498db, #2980b9); color: white; font-weight: 600; padding: 6px 12px;">
                                                        <i class="fa fa-pause"></i> Paused
                                                    </span>
                                                @elseif($subscriptionStatus === 'completed')
                                                    <span class="badge" style="background: linear-gradient(45deg, #34495e, #2c3e50); color: white; font-weight: 600; padding: 6px 12px;">
                                                        <i class="fa fa-check-circle"></i> Completed
                                                    </span>
                                                @elseif($subscriptionStatus === 'cancelled')
                                                    <span class="badge" style="background: linear-gradient(45deg, #e74c3c, #c0392b); color: white; font-weight: 600; padding: 6px 12px;">
                                                        <i class="fa fa-times"></i> Cancelled
                                                    </span>
                                                @else
                                                    <span class="badge" style="background: linear-gradient(45deg, #95a5a6, #7f8c8d); color: white; font-weight: 600; padding: 6px 12px;">
                                                        <i class="fa fa-question"></i> No Subscription
                                                    </span>
                                                @endif
                                            </td>
                                            <td style="border: none; padding: 12px 8px;">
                                                @if($subscriptionStatus === 'pending')
                                                    <a href="{{ route('page.judopay.subscribe', $subscription->id) }}" 
                                                       class="btn btn-sm" 
                                                       style="background: linear-gradient(45deg, #3498db, #2980b9); color: white; border: none; font-size: 0.7rem; padding: 6px 12px; border-radius: 4px; font-weight: 600; text-decoration: none; transition: all 0.3s ease;"
                                                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(52, 152, 219, 0.4)'"
                                                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                        <i class="fa fa-cog"></i> Setup Payment
                                                    </a>
                                                @elseif($subscriptionStatus === 'active')
                                                    <a href="{{ route('page.judopay.subscribe', $subscription->id) }}" 
                                                       class="btn btn-sm" 
                                                       style="background: linear-gradient(45deg, #27ae60, #2ecc71); color: white; border: none; font-size: 0.7rem; padding: 6px 12px; border-radius: 4px; font-weight: 600; text-decoration: none; transition: all 0.3s ease;"
                                                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(39, 174, 96, 0.4)'"
                                                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                        <i class="fa fa-eye"></i> View Details
                                                    </a>
                                                @elseif($subscriptionStatus === 'paused')
                                                    <a href="{{ route('page.judopay.subscribe', $subscription->id) }}" 
                                                       class="btn btn-sm" 
                                                       style="background: linear-gradient(45deg, #3498db, #2980b9); color: white; border: none; font-size: 0.7rem; padding: 6px 12px; border-radius: 4px; font-weight: 600; text-decoration: none; transition: all 0.3s ease;"
                                                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(52, 152, 219, 0.4)'"
                                                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                        <i class="fa fa-pause"></i> Paused
                                                    </a>
                                                @elseif($subscriptionStatus === 'completed')
                                                    <a href="{{ route('page.judopay.subscribe', $subscription->id) }}" 
                                                       class="btn btn-sm" 
                                                       style="background: linear-gradient(45deg, #34495e, #2c3e50); color: white; border: none; font-size: 0.7rem; padding: 6px 12px; border-radius: 4px; font-weight: 600; text-decoration: none; transition: all 0.3s ease;"
                                                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(52, 73, 94, 0.4)'"
                                                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                        <i class="fa fa-check-circle"></i> Completed
                                                    </a>
                                                @elseif($subscriptionStatus === 'cancelled')
                                                    <a href="{{ route('page.judopay.subscribe', $subscription->id) }}" 
                                                       class="btn btn-sm" 
                                                       style="background: linear-gradient(45deg, #e74c3c, #c0392b); color: white; border: none; font-size: 0.7rem; padding: 6px 12px; border-radius: 4px; font-weight: 600; text-decoration: none; transition: all 0.3s ease;"
                                                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(231, 76, 60, 0.4)'"
                                                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                        <i class="fa fa-times"></i> Cancelled
                                                    </a>
                                                @else
                                                    <button class="btn btn-sm" disabled 
                                                            style="background: linear-gradient(45deg, #95a5a6, #7f8c8d); color: white; border: none; font-size: 0.7rem; padding: 6px 12px; border-radius: 4px; font-weight: 600;">
                                                        <i class="fa fa-ban"></i> No Subscription
                                                    </button>
                                                @endif
                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach

                                                {{-- Active Finance Applications --}}
                                                @foreach($customer->financeApplications ?? [] as $finance)
                                                    @foreach($finance->application_items as $item)
                                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); transition: all 0.3s ease;" 
                                                            onmouseover="this.style.background='rgba(52, 152, 219, 0.1)'" 
                                                            onmouseout="this.style.background='transparent'">
                                                            <td style="border: none; padding: 12px 8px;">
                                                                <div style="font-size: 0.75rem; color: #ecf0f1;">
                                                                    <strong style="color: #3498db;">{{ $customer->first_name ?? 'N/A' }} {{ $customer->last_name ?? 'N/A' }}</strong><br>
                                                                    <small style="color: #bdc3c7; font-size: 0.7rem;">{{ $customer->email ?? 'N/A' }}</small><br>
                                                                    <small style="color: #bdc3c7; font-size: 0.7rem;">{{ $customer->phone ?? 'N/A' }}</small>
                                                                </div>
                                                            </td>
                                                            <td style="border: none; padding: 12px 8px;">
                                                                <span class="badge" style="background: linear-gradient(45deg, #f39c12, #e67e22); color: white; font-size: 0.7rem; padding: 6px 10px; font-weight: 600;">Finance</span><br>
                                                                <small style="color: #bdc3c7; font-size: 0.7rem;">ID: {{ $finance->id }}</small>
                                                            </td>
                                                            <td style="border: none; padding: 12px 8px;">
                                                                <div style="font-size: 0.75rem; color: #ecf0f1;">
                                                                    <strong style="color: #3498db;">{{ $item->motorbike->reg_no ?? 'N/A' }}</strong><br>
                                                                    <small style="color: #bdc3c7; font-size: 0.7rem;">{{ $item->motorbike->make ?? '' }} {{ $item->motorbike->model ?? '' }}</small>
                                                                </div>
                                                            </td>
                                                            <td style="border: none; padding: 12px 8px; color: #bdc3c7;">
                                                                {{ $finance->contract_date ? \Carbon\Carbon::parse($finance->contract_date)->format('d/m/Y') : 'N/A' }}
                                                            </td>
                                                            <td style="border: none; padding: 12px 8px; font-weight: 600; color: #27ae60;">
                                                                £{{ number_format($finance->weekly_instalment, 2) }}
                                                            </td>
                                            @php
                                                $subscription = $onboarding->subscriptions->where('subscribable_id', $finance->id)->where('subscribable_type', 'App\\Models\\FinanceApplication')->first();
                                                $subscriptionStatus = $subscription ? $subscription->status : 'no_subscription';
                                            @endphp
                                            <td style="border: none; padding: 12px 8px;">
                                                @if($subscriptionStatus === 'active')
                                                    <span class="badge" style="background: linear-gradient(45deg, #27ae60, #2ecc71); color: white; font-weight: 600; padding: 6px 12px;">
                                                        <i class="fa fa-check"></i> Active
                                                    </span>
                                                @elseif($subscriptionStatus === 'pending')
                                                    <span class="badge" style="background: linear-gradient(45deg, #f39c12, #e67e22); color: white; font-weight: 600; padding: 6px 12px;">
                                                        <i class="fa fa-clock"></i> Pending
                                                    </span>
                                                @elseif($subscriptionStatus === 'paused')
                                                    <span class="badge" style="background: linear-gradient(45deg, #3498db, #2980b9); color: white; font-weight: 600; padding: 6px 12px;">
                                                        <i class="fa fa-pause"></i> Paused
                                                    </span>
                                                @elseif($subscriptionStatus === 'completed')
                                                    <span class="badge" style="background: linear-gradient(45deg, #34495e, #2c3e50); color: white; font-weight: 600; padding: 6px 12px;">
                                                        <i class="fa fa-check-circle"></i> Completed
                                                    </span>
                                                @elseif($subscriptionStatus === 'cancelled')
                                                    <span class="badge" style="background: linear-gradient(45deg, #e74c3c, #c0392b); color: white; font-weight: 600; padding: 6px 12px;">
                                                        <i class="fa fa-times"></i> Cancelled
                                                    </span>
                                                @else
                                                    <span class="badge" style="background: linear-gradient(45deg, #95a5a6, #7f8c8d); color: white; font-weight: 600; padding: 6px 12px;">
                                                        <i class="fa fa-question"></i> No Subscription
                                                    </span>
                                                @endif
                                            </td>
                                            <td style="border: none; padding: 12px 8px;">
                                                @if($subscriptionStatus === 'pending')
                                                    <a href="{{ route('page.judopay.subscribe', $subscription->id) }}" 
                                                       class="btn btn-sm" 
                                                       style="background: linear-gradient(45deg, #3498db, #2980b9); color: white; border: none; font-size: 0.7rem; padding: 6px 12px; border-radius: 4px; font-weight: 600; text-decoration: none; transition: all 0.3s ease;"
                                                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(52, 152, 219, 0.4)'"
                                                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                        <i class="fa fa-cog"></i> Setup Payment
                                                    </a>
                                                @elseif($subscriptionStatus === 'active')
                                                    <a href="{{ route('page.judopay.subscribe', $subscription->id) }}" 
                                                       class="btn btn-sm" 
                                                       style="background: linear-gradient(45deg, #27ae60, #2ecc71); color: white; border: none; font-size: 0.7rem; padding: 6px 12px; border-radius: 4px; font-weight: 600; text-decoration: none; transition: all 0.3s ease;"
                                                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(39, 174, 96, 0.4)'"
                                                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                        <i class="fa fa-eye"></i> View Details
                                                    </a>
                                                @elseif($subscriptionStatus === 'paused')
                                                    <a href="{{ route('page.judopay.subscribe', $subscription->id) }}" 
                                                       class="btn btn-sm" 
                                                       style="background: linear-gradient(45deg, #3498db, #2980b9); color: white; border: none; font-size: 0.7rem; padding: 6px 12px; border-radius: 4px; font-weight: 600; text-decoration: none; transition: all 0.3s ease;"
                                                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(52, 152, 219, 0.4)'"
                                                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                        <i class="fa fa-pause"></i> Paused
                                                    </a>
                                                @elseif($subscriptionStatus === 'completed')
                                                    <a href="{{ route('page.judopay.subscribe', $subscription->id) }}" 
                                                       class="btn btn-sm" 
                                                       style="background: linear-gradient(45deg, #34495e, #2c3e50); color: white; border: none; font-size: 0.7rem; padding: 6px 12px; border-radius: 4px; font-weight: 600; text-decoration: none; transition: all 0.3s ease;"
                                                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(52, 73, 94, 0.4)'"
                                                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                        <i class="fa fa-check-circle"></i> Completed
                                                    </a>
                                                @elseif($subscriptionStatus === 'cancelled')
                                                    <a href="{{ route('page.judopay.subscribe', $subscription->id) }}" 
                                                       class="btn btn-sm" 
                                                       style="background: linear-gradient(45deg, #e74c3c, #c0392b); color: white; border: none; font-size: 0.7rem; padding: 6px 12px; border-radius: 4px; font-weight: 600; text-decoration: none; transition: all 0.3s ease;"
                                                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(231, 76, 60, 0.4)'"
                                                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                        <i class="fa fa-times"></i> Cancelled
                                                    </a>
                                                @else
                                                    <button class="btn btn-sm" disabled 
                                                            style="background: linear-gradient(45deg, #95a5a6, #7f8c8d); color: white; border: none; font-size: 0.7rem; padding: 6px 12px; border-radius: 4px; font-weight: 600;">
                                                        <i class="fa fa-ban"></i> No Subscription
                                                    </button>
                                                @endif
                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert" style="background: rgba(52, 73, 94, 0.6); border: 1px solid rgba(52, 152, 219, 0.3); color: #ecf0f1;">
                                    <h4 style="color: #3498db; font-weight: 600;"><i class="fa fa-info-circle" style="color: #3498db;"></i> No Active Customers Found</h4>
                                    <p style="color: #bdc3c7;">There are currently no customers with active services available for Judopay onboarding.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endcan

    <script>
        function startOnboarding(bookingId, type = 'rental') {
            // Implement Judopay onboarding logic here
            alert('Starting onboarding for ' + type + ' ID: ' + bookingId);
        }

        function createOnboarding(bookingId, type = 'rental') {
            // Implement create onboarding logic here
            alert('Creating onboarding for ' + type + ' ID: ' + bookingId);
        }

        // Real-time table filtering
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.getElementById('customersTable');
            if (!table) return;

            const filterInputs = {
                customer: document.getElementById('filterCustomer'),
                serviceType: document.getElementById('filterServiceType'),
                vehicle: document.getElementById('filterVehicle'),
                status: document.getElementById('filterStatus'),
                amount: document.getElementById('filterAmount')
            };

            function filterTable() {
                const rows = table.querySelectorAll('tbody tr');
                const filters = {
                    customer: filterInputs.customer.value.toLowerCase(),
                    serviceType: filterInputs.serviceType.value.toLowerCase(),
                    vehicle: filterInputs.vehicle.value.toLowerCase(),
                    status: filterInputs.status.value.toLowerCase(),
                    amount: filterInputs.amount.value.toLowerCase()
                };

                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length < 6) return;

                    const customerText = (cells[0]?.textContent || '').toLowerCase();
                    const serviceTypeText = (cells[1]?.textContent || '').toLowerCase();
                    const vehicleText = (cells[2]?.textContent || '').toLowerCase();
                    const statusText = (cells[5]?.textContent || '').toLowerCase();
                    const amountText = (cells[4]?.textContent || '').toLowerCase();

                    const matches = 
                        (!filters.customer || customerText.includes(filters.customer)) &&
                        (!filters.serviceType || serviceTypeText.includes(filters.serviceType)) &&
                        (!filters.vehicle || vehicleText.includes(filters.vehicle)) &&
                        (!filters.status || statusText.includes(filters.status)) &&
                        (!filters.amount || amountText.includes(filters.amount));

                    row.style.display = matches ? '' : 'none';
                });
            }

            Object.values(filterInputs).forEach(input => {
                if (input) {
                    input.addEventListener('input', filterTable);
                }
            });
        });
    </script>
@endsection
