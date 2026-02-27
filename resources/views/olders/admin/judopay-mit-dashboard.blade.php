@extends(backpack_view('blank'))

@php
  $breadcrumbs = [
    'Admin' => backpack_url('dashboard'),
    'Recurring Payments' => route('page.judopay.index'),
    'MIT Dashboard' => false,
  ];
@endphp

@section('content')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none" bp-section="page-header">
        <h5 class="text-capitalize mb-0 ml-2" bp-section="page-heading">MIT Payment Dashboard</h5>
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

    @can('can-run-mit')
        <section class="content container-fluid animated fadeIn" bp-section="content">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Main Content Row -->
            <div class="row">
                <!-- Left Column - Subscription Selection -->
                <div class="col-md-4">
                    <x-ui.panel title="Select Subscription" icon="fa fa-list">
                        @if($subscriptions->count() > 0)
                            <div class="list-group">
                                @foreach($subscriptions as $subscription)
                                    <div class="list-group-item" style="background: rgba(52, 73, 94, 0.6); border: 1px solid rgba(255,255,255,0.1); margin-bottom: 8px; border-radius: 6px;">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1" style="color: #ecf0f1; font-size: 0.8rem;">
                                                <strong>{{ $subscription->judopayOnboarding->onboardable->first_name }} {{ $subscription->judopayOnboarding->onboardable->last_name }}</strong>
                                            </h6>
                                            <small style="color: #3498db;">#{{ $subscription->id }}</small>
                                        </div>
                                        <p class="mb-1" style="color: #bdc3c7; font-size: 0.7rem;">
                                            <strong>Reference:</strong> {{ $subscription->consumer_reference }}<br>
                                            <strong>Amount:</strong> £{{ $subscription->amount }} {{ ucfirst($subscription->billing_frequency) }}<br>
                                            <strong>MIT Payments:</strong> {{ $subscription->mitPaymentSessions->count() }}
                                        </p>
                                        <div class="mt-2">
                                            <a href="{{ route('page.judopay.mit-dashboard', ['selected_subscription' => $subscription->id]) }}"
                                               class="btn btn-sm btn-primary" style="font-size: 0.7rem;">
                                                <i class="fa fa-credit-card"></i> Manage MIT
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert" style="background: rgba(52, 73, 94, 0.6); border: 1px solid rgba(52, 152, 219, 0.3); color: #ecf0f1;">
                                <i class="fa fa-info-circle" style="color: #3498db;"></i> No MIT-eligible subscriptions found.
                            </div>
                        @endif
                    </x-ui.panel>
                </div>

                <!-- Right Column - Direct MIT Payment Interface -->
                <div class="col-md-8">
                    <x-ui.panel title="Fire MIT Payment" icon="fa fa-bolt" variant="warning">
                        @if(request('selected_subscription'))
                            @php
                                $selectedSubscription = $subscriptions->find(request('selected_subscription'));
                            @endphp

                            @if($selectedSubscription)
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <h6 style="font-size: 0.9rem; color: #3498db; font-weight: 600;">
                                            <i class="fa fa-info-circle" style="color: #3498db;"></i> Subscription Details
                                        </h6>
                                        <div class="border rounded p-3" style="background-color: rgba(52, 73, 94, 0.6); border-color: rgba(255,255,255,0.1) !important;">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                                        <strong style="color: #ecf0f1;">Customer:</strong><br>
                                                        <span style="color: #3498db; font-weight: 600;">{{ $selectedSubscription->judopayOnboarding->onboardable->first_name }} {{ $selectedSubscription->judopayOnboarding->onboardable->last_name }}</span>
                                                    </p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                                        <strong style="color: #ecf0f1;">Reference:</strong><br>
                                                        <span style="color: #3498db;">{{ $selectedSubscription->consumer_reference }}</span>
                                                    </p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                                        <strong style="color: #ecf0f1;">Amount:</strong><br>
                                                        <span style="color: #27ae60; font-weight: 600;">£{{ $selectedSubscription->amount }}</span>
                                                    </p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p class="mb-1" style="font-size: 0.75rem; color: #ecf0f1;">
                                                        <strong style="color: #ecf0f1;">Status:</strong><br>
                                                        <span class="badge" style="background: linear-gradient(45deg, #27ae60, #2ecc71); color: white; font-size: 0.7rem; padding: 6px 10px; font-weight: 600;">Active</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if(empty($selectedSubscription->card_token))
                                    <div class="alert" style="background: rgba(231, 76, 60, 0.2); border: 1px solid rgba(231, 76, 60, 0.4); color: #ffebee; margin-bottom: 15px;">
                                        <i class="fa fa-exclamation-triangle" style="color: #e74c3c;"></i> No card token available. Customer must complete CIT setup first.
                                    </div>
                                @elseif($selectedSubscription->status !== 'active')
                                    <div class="alert" style="background: rgba(231, 76, 60, 0.2); border: 1px solid rgba(231, 76, 60, 0.4); color: #ffebee; margin-bottom: 15px;">
                                        <i class="fa fa-exclamation-triangle" style="color: #e74c3c;"></i> Subscription must be active to fire MIT payment.
                                    </div>
                                @else
                                    <div class="text-center p-4">
                                        <div class="mb-3">
                                            <i class="fa fa-bolt" style="font-size: 2.5rem; color: #e74c3c; margin-bottom: 1rem;"></i>
                                            <h5 style="color: #ecf0f1; font-weight: 600;">Ready for Direct MIT Payment</h5>
                                            <p style="color: #bdc3c7; font-size: 0.9rem;">Fire MIT payment immediately for £{{ $selectedSubscription->amount }}.</p>
                                        </div>

                                        @can('can-fire-mit')
                                            <form method="POST" action="{{ route('page.judopay.fire-direct-mit') }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to fire MIT payment for £{{ $selectedSubscription->amount }}?');">
                                                @csrf
                                                <input type="hidden" name="subscription_id" value="{{ $selectedSubscription->id }}">
                                                <button type="submit" class="btn btn-danger btn-lg" style="font-size: 0.9rem; font-weight: 600;">
                                                    <i class="fa fa-bolt"></i> Fire MIT Payment
                                                </button>
                                            </form>
                                        @endcan

                                        <div class="mt-3">
                                            <small class="text-muted" style="font-size: 0.75rem; color: #95a5a6;">
                                                <i class="fa fa-info-circle"></i> This will immediately charge the customer's card for the subscription amount.
                                            </small>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @else
                            <div class="text-center p-4">
                                <i class="fa fa-hand-pointer" style="font-size: 3rem; color: #95a5a6; margin-bottom: 1rem;"></i>
                                <h5 style="color: #7f8c8d;">Select a subscription to fire MIT payment</h5>
                                <p style="color: #95a5a6; font-size: 0.9rem;">Choose a subscription from the left panel to view details and fire an immediate MIT payment.</p>
                            </div>
                        @endif
                    </x-ui.panel>
                </div>
            </div>

            @can('can-view-mit-history')
            <!-- MIT Payment History -->
            <div class="row mt-3">
                <div class="col-md-12">
                    <x-ui.panel title="Recent MIT Payment History" icon="fa fa-history" variant="warning">
                        @if($recentMitPayments->count() > 0)
                            <!-- Filter Section -->
                            <div class="mb-3" style="background: rgba(52, 73, 94, 0.5); padding: 12px; border: 1px solid rgba(243, 156, 18, 0.3);">
                                <div class="row">
                                    <div class="col-md-2">
                                        <input type="text" id="filterMitDashboardVrm" class="form-control form-control-sm" placeholder="VRM..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(243, 156, 18, 0.3);">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" id="filterMitDashboardCustomer" class="form-control form-control-sm" placeholder="Customer..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(243, 156, 18, 0.3);">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" id="filterMitDashboardReference" class="form-control form-control-sm" placeholder="Consumer Reference..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(243, 156, 18, 0.3);">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" id="filterMitDashboardAmount" class="form-control form-control-sm" placeholder="Amount..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(243, 156, 18, 0.3);">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" id="filterMitDashboardStatus" class="form-control form-control-sm" placeholder="Status..." style="font-size: 0.7rem; background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(243, 156, 18, 0.3);">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <small style="color: #bdc3c7; font-size: 0.65rem;"><i class="fa fa-filter"></i> Real-time filtering - Type to filter table rows</small>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm" id="mitDashboardTable" style="font-size: 0.75rem; background: transparent; color: #ecf0f1;">
                                    <thead>
                                        <tr style="background: rgba(52, 73, 94, 0.6); border-bottom: 2px solid #f39c12;">
                                            <th style="color: #f39c12; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-car"></i> VRM
                                            </th>
                                            <th style="color: #f39c12; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-user"></i> Customer
                                            </th>
                                            <th style="color: #f39c12; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-link"></i> Consumer Reference
                                            </th>
                                            <th style="color: #f39c12; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-pound-sign"></i> Amount
                                            </th>
                                            <th style="color: #f39c12; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-info-circle"></i> Status
                                            </th>
                                            <th style="color: #f39c12; font-weight: 600; border: none; padding: 12px 8px;">
                                                <i class="fa fa-calendar-plus"></i> Queue Created
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentMitPayments as $mitSession)
                                            @php
                                                $vrm = null;
                                                $subscribable = $mitSession->subscription->subscribable;
                                                if ($subscribable) {
                                                    if ($mitSession->subscription->subscribable_type === 'App\Models\RentingBooking') {
                                                        $item = $subscribable->rentingBookingItems->whereNull('end_date')->first()
                                                            ?? $subscribable->rentingBookingItems->sortByDesc('id')->first();
                                                        $vrm = $item->motorbike->reg_no ?? null;
                                                    } elseif ($mitSession->subscription->subscribable_type === 'App\Models\FinanceApplication') {
                                                        $item = $subscribable->application_items->sortByDesc('id')->first();
                                                        $vrm = $item->motorbike->reg_no ?? null;
                                                    }
                                                }
                                            @endphp

                                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); transition: all 0.3s ease; cursor: pointer;"
                                                onclick="window.location.href='{{ route('page.judopay.subscribe', $mitSession->subscription->id) }}'"
                                                onmouseover="this.style.background='rgba(243, 156, 18, 0.1)'"
                                                onmouseout="this.style.background='transparent'">
                                                <td style="border: none; padding: 12px 8px;">
                                                    @if($vrm)
                                                        <span class="badge" style="background: linear-gradient(45deg, #f39c12, #e67e22); color: white; font-weight: 600; font-size: 0.7rem; padding: 6px 10px;">
                                                            {{ $vrm }}
                                                        </span>
                                                    @else
                                                        <span style="color: #95a5a6; font-size: 0.7rem;">-</span>
                                                    @endif
                                                </td>
                                                <td style="border: none; padding: 12px 8px;">
                                                    <div style="font-size: 0.7rem;">
                                                        <strong style="color: #ecf0f1;">{{ $mitSession->subscription->judopayOnboarding->onboardable->first_name }} {{ $mitSession->subscription->judopayOnboarding->onboardable->last_name }}</strong>
                                                    </div>
                                                </td>
                                                <td style="border: none; padding: 12px 8px;">
                                                    <code
                                                        style="font-size: 0.7rem; background: rgba(239, 240, 242, 0.8); color: #e74c3c; padding: 4px 8px;">
                                                        {{ $mitSession->judopay_payment_reference }}
                                                    </code>
                                                </td>
                                                <td style="border: none; padding: 12px 8px; font-weight: 600; color: #27ae60;">
                                                    £{{ $mitSession->amount }}
                                                </td>
                                                <td style="border: none; padding: 12px 8px;">
                                                    @if($mitSession->status === 'success' && $mitSession->failure_reason === null)
                                                        <span class="badge" style="background: linear-gradient(45deg, #27ae60, #2ecc71); color: white; font-weight: 600; padding: 6px 12px;">
                                                            <i class="fa fa-check"></i> Success
                                                        </span>
                                                    @elseif($mitSession->status === 'declined')
                                                        <span class="badge" style="background: linear-gradient(45deg, #e74c3c, #c0392b); color: white; font-weight: 600; padding: 6px 12px;">
                                                            <i class="fa fa-times"></i> Declined
                                                        </span>
                                                    @elseif($mitSession->status === 'error')
                                                        <span class="badge" style="background: linear-gradient(45deg, #e74c3c, #c0392b); color: white; font-weight: 600; padding: 6px 12px;">
                                                            <i class="fa fa-exclamation-circle"></i> Error
                                                        </span>
                                                    @elseif($mitSession->status === 'created' && $mitSession->failure_reason === null)
                                                        <span class="badge" style="background: linear-gradient(45deg, #8b8f62, #d7f110); color: rgb(0, 0, 0); font-weight: 600; padding: 6px 12px;">
                                                            <i class="fa fa-clock"></i> Queued
                                                        </span>
                                                    @elseif($mitSession->status === 'cancelled')
                                                        <span class="badge" style="background: linear-gradient(45deg, #707c88, #2b75be); color: rgb(255, 255, 255); font-weight: 600; padding: 6px 12px;">
                                                            <i class="fa fa-ban"></i> Cancelled
                                                        </span>
                                                    @else
                                                        <span class="badge" style="background: linear-gradient(45deg, #676b6e, #88237e); color: white; font-weight: 600; padding: 6px 12px;">
                                                            DECLINED/NOT SUCCESS (REPORT IT)
                                                        </span>
                                                    @endif
                                                </td>
                                                <td style="border: none; padding: 12px 8px; color: #bdc3c7;">
                                                    {{ $mitSession->created_at->format('d/m/Y H:i') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            @if($recentMitPayments->hasPages())
                                <div class="mt-3" style="background: rgba(52, 73, 94, 0.5); padding: 12px; border: 1px solid rgba(243, 156, 18, 0.3);">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <div style="color: #bdc3c7; font-size: 0.75rem;">
                                            Showing {{ $recentMitPayments->firstItem() }} to {{ $recentMitPayments->lastItem() }} of {{ $recentMitPayments->total() }} results
                                        </div>
                                        <nav>
                                            <ul class="pagination pagination-sm mb-0" style="margin: 0;">
                                                {{-- Previous Page Link --}}
                                                @if ($recentMitPayments->onFirstPage())
                                                    <li class="page-item disabled">
                                                        <span class="page-link" style="background: rgba(44, 62, 80, 0.8); color: #7f8c8d; border: 1px solid rgba(243, 156, 18, 0.3); padding: 6px 12px; font-size: 0.75rem;">
                                                            <i class="fa fa-chevron-left"></i> Previous
                                                        </span>
                                                    </li>
                                                @else
                                                    <li class="page-item">
                                                        <a class="page-link" href="{{ $recentMitPayments->previousPageUrl() }}" style="background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(243, 156, 18, 0.3); padding: 6px 12px; font-size: 0.75rem; text-decoration: none; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(243, 156, 18, 0.3)'; this.style.borderColor='#f39c12';" onmouseout="this.style.background='rgba(44, 62, 80, 0.8)'; this.style.borderColor='rgba(243, 156, 18, 0.3)';">
                                                            <i class="fa fa-chevron-left"></i> Previous
                                                        </a>
                                                    </li>
                                                @endif

                                                {{-- Pagination Elements --}}
                                                @php
                                                    $currentPage = $recentMitPayments->currentPage();
                                                    $lastPage = $recentMitPayments->lastPage();
                                                    $startPage = max(1, $currentPage - 2);
                                                    $endPage = min($lastPage, $currentPage + 2);
                                                @endphp

                                                {{-- First page --}}
                                                @if ($startPage > 1)
                                                    <li class="page-item">
                                                        <a class="page-link" href="{{ $recentMitPayments->url(1) }}" style="background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(243, 156, 18, 0.3); padding: 6px 12px; font-size: 0.75rem; text-decoration: none; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(243, 156, 18, 0.3)'; this.style.borderColor='#f39c12';" onmouseout="this.style.background='rgba(44, 62, 80, 0.8)'; this.style.borderColor='rgba(243, 156, 18, 0.3)';">
                                                            1
                                                        </a>
                                                    </li>
                                                    @if ($startPage > 2)
                                                        <li class="page-item disabled">
                                                            <span class="page-link" style="background: rgba(44, 62, 80, 0.8); color: #7f8c8d; border: 1px solid rgba(243, 156, 18, 0.3); padding: 6px 12px; font-size: 0.75rem;">
                                                                ...
                                                            </span>
                                                        </li>
                                                    @endif
                                                @endif

                                                {{-- Page range --}}
                                                @for ($page = $startPage; $page <= $endPage; $page++)
                                                    @if ($page == $currentPage)
                                                        <li class="page-item active">
                                                            <span class="page-link" style="background: linear-gradient(45deg, #f39c12, #e67e22); color: white; border: 1px solid #f39c12; padding: 6px 12px; font-size: 0.75rem; font-weight: 600;">
                                                                {{ $page }}
                                                            </span>
                                                        </li>
                                                    @else
                                                        <li class="page-item">
                                                            <a class="page-link" href="{{ $recentMitPayments->url($page) }}" style="background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(243, 156, 18, 0.3); padding: 6px 12px; font-size: 0.75rem; text-decoration: none; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(243, 156, 18, 0.3)'; this.style.borderColor='#f39c12';" onmouseout="this.style.background='rgba(44, 62, 80, 0.8)'; this.style.borderColor='rgba(243, 156, 18, 0.3)';">
                                                                {{ $page }}
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endfor

                                                {{-- Last page --}}
                                                @if ($endPage < $lastPage)
                                                    @if ($endPage < $lastPage - 1)
                                                        <li class="page-item disabled">
                                                            <span class="page-link" style="background: rgba(44, 62, 80, 0.8); color: #7f8c8d; border: 1px solid rgba(243, 156, 18, 0.3); padding: 6px 12px; font-size: 0.75rem;">
                                                                ...
                                                            </span>
                                                        </li>
                                                    @endif
                                                    <li class="page-item">
                                                        <a class="page-link" href="{{ $recentMitPayments->url($lastPage) }}" style="background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(243, 156, 18, 0.3); padding: 6px 12px; font-size: 0.75rem; text-decoration: none; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(243, 156, 18, 0.3)'; this.style.borderColor='#f39c12';" onmouseout="this.style.background='rgba(44, 62, 80, 0.8)'; this.style.borderColor='rgba(243, 156, 18, 0.3)';">
                                                            {{ $lastPage }}
                                                        </a>
                                                    </li>
                                                @endif

                                                {{-- Next Page Link --}}
                                                @if ($recentMitPayments->hasMorePages())
                                                    <li class="page-item">
                                                        <a class="page-link" href="{{ $recentMitPayments->nextPageUrl() }}" style="background: rgba(44, 62, 80, 0.8); color: #ecf0f1; border: 1px solid rgba(243, 156, 18, 0.3); padding: 6px 12px; font-size: 0.75rem; text-decoration: none; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(243, 156, 18, 0.3)'; this.style.borderColor='#f39c12';" onmouseout="this.style.background='rgba(44, 62, 80, 0.8)'; this.style.borderColor='rgba(243, 156, 18, 0.3)';">
                                                            Next <i class="fa fa-chevron-right"></i>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li class="page-item disabled">
                                                        <span class="page-link" style="background: rgba(44, 62, 80, 0.8); color: #7f8c8d; border: 1px solid rgba(243, 156, 18, 0.3); padding: 6px 12px; font-size: 0.75rem;">
                                                            Next <i class="fa fa-chevron-right"></i>
                                                        </span>
                                                    </li>
                                                @endif
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="alert" style="background: rgba(52, 73, 94, 0.6); border: 1px solid rgba(243, 156, 18, 0.3); color: #ecf0f1;">
                                <i class="fa fa-info-circle" style="color: #f39c12;"></i> No MIT payments found.
                            </div>
                        @endif
                    </x-ui.panel>
                </div>
            </div>
            @endcan
        </section>
    @endcan

@endsection

@section('after_scripts')
    <script>
        // Real-time table filtering for MIT Dashboard
        document.addEventListener('DOMContentLoaded', function() {
            const mitDashboardTable = document.getElementById('mitDashboardTable');
            if (mitDashboardTable) {
                const filters = {
                    vrm: document.getElementById('filterMitDashboardVrm'),
                    customer: document.getElementById('filterMitDashboardCustomer'),
                    reference: document.getElementById('filterMitDashboardReference'),
                    amount: document.getElementById('filterMitDashboardAmount'),
                    status: document.getElementById('filterMitDashboardStatus')
                };

                function filterMitDashboardTable() {
                    const rows = mitDashboardTable.querySelectorAll('tbody tr');
                    const filterValues = {
                        vrm: filters.vrm.value.toLowerCase(),
                        customer: filters.customer.value.toLowerCase(),
                        reference: filters.reference.value.toLowerCase(),
                        amount: filters.amount.value.toLowerCase(),
                        status: filters.status.value.toLowerCase()
                    };

                    rows.forEach(row => {
                        const rowText = row.textContent.toLowerCase();
                        const matches =
                            (!filterValues.vrm || rowText.includes(filterValues.vrm)) &&
                            (!filterValues.customer || rowText.includes(filterValues.customer)) &&
                            (!filterValues.reference || rowText.includes(filterValues.reference)) &&
                            (!filterValues.amount || rowText.includes(filterValues.amount)) &&
                            (!filterValues.status || rowText.includes(filterValues.status));
                        row.style.display = matches ? '' : 'none';
                    });
                }

                Object.values(filters).forEach(input => {
                    if (input) input.addEventListener('input', filterMitDashboardTable);
                });
            }
        });
    </script>
@endsection
