@extends(backpack_view('blank'))

@php
  $breadcrumbs = [
    'Admin' => backpack_url('dashboard'),
    'Recurring Payments' => route('page.judopay.index'),
    'MIT Dashboard' => route('page.judopay.mit-dashboard'),
    'Weekly Schedule' => false,
  ];
@endphp

@section('content')
    <style>
        .card {
            border-radius: 0;
        }

        /* Checkbox styling */
        .queue-checkbox {
            position: absolute;
            top: 8px;
            right: 8px;
            z-index: 10;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid #2c3e50;
            cursor: pointer;
        }

        .queue-checkbox:checked {
            background: #27ae60;
            border-color: #27ae60;
        }

        .queue-checkbox:checked::after {
            content: '✓';
            color: white;
            font-weight: bold;
            font-size: 14px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .bulk-actions {
            background: rgba(44, 62, 80, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 12px 16px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .bulk-actions label {
            color: #ecf0f1;
            font-weight: 600;
            margin: 0;
            cursor: pointer;
        }

        .bulk-actions .btn {
            font-size: 0.9rem;
            font-weight: 600;
            padding: 8px 16px;
        }

        .selected-count {
            color: #3498db;
            font-weight: 700;
            font-size: 1rem;
        }

        /* List view styling */
        .mit-list-row .mit-item-card {
            display: flex;
            flex-direction: row;
            align-items: stretch;
        }

        .mit-list-row .mit-item-card .card-body {
            flex: 1;
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 20px;
        }

        .mit-list-row .mit-item-card .card-body > section {
            flex: 1;
            margin-bottom: 0 !important;
        }

        /* Summary card hover effect */
        .mit-summary-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .mit-summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }
    </style>
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none" bp-section="page-header">
        <h5 class="text-capitalize uppercase mb-0 ml-2" bp-section="page-heading" style="color: #000000; font-weight: 600; letter-spacing: 0.05em; font-size: 1.2rem; text-transform: uppercase;"   >WEEKLY SCHEDULE</h5>
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

    @can('see-weekly-queue')
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

            <!-- Week Navigation Header -->
            <div class="row">
                <div class="col-md-12">
                    <x-ui.panel title="Week Navigation | {{ now()->format('l, M d, Y') }}" icon="fa fa-calendar" variant="primary"
                               headerRight="Current {{ \Carbon\Carbon::now()->startOfWeek()->format('l, M d') }} - {{ \Carbon\Carbon::now()->endOfWeek()->format('l, M d, Y') }}"
                               headerRightUrl="{{ route('page.judopay.weekly-mit-queue') }}">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-left">
                                <a href="{{ route('page.judopay.weekly-mit-queue', ['week' => $previousWeek->format('Y-m-d')]) }}"
                                   class="btn btn-outline-warning btn-sm">
                                    <i class="fa fa-chevron-left"></i> Previous Week
                                </a>
                            </div>
                            <div class="col-md-6 text-center">
                                <h6 style="color: #3498db; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.05em; font-size: 1.0rem;">
                                    <i class="fa fa-calendar-alt" style="color: #f39c12; margin-right: 8px;"></i>
                                    {{ $currentWeekStart->format('l, M d, Y') }} - {{ $currentWeekEnd->format('l, M d, Y') }}
                                </h6>
                            </div>
                            <div class="col-md-3 text-right">
                                <a href="{{ route('page.judopay.weekly-mit-queue', ['week' => $nextWeek->format('Y-m-d')]) }}"
                                   class="btn btn-outline-warning btn-sm">
                                    Next Week <i class="fa fa-chevron-right"></i>
                                </a>
                            </div>
                        </div>





                           <!-- Weekly Summary Panel -->
                            @if($summary['expected'] > 0)
                            <br>
                            <div class="row">
                                <div class="col-md-12">

                                        <div class="row" style="margin: 0 !important;">
                                            <!-- Expected Card -->
                                            <div class="col-md-4">
                                                <div class="card mit-summary-card" data-mit-filter="expected" style="cursor: pointer; background: linear-gradient(135deg, #3498db, #2980b9); border: none; border-radius: 0; padding: 7px 0 15px 0; text-align: center; color: white;">
                                                    <div style="font-size: 2.5rem; margin-bottom: 10px;">
                                                        <i class="fa fa-calendar-check"></i>
                                                    </div>
                                                    <div style="font-size: 14px; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 10px; opacity: 0.9;">
                                                        Expected
                                                    </div>
                                                    <div style="display: flex; align-items: baseline; justify-content: center; gap: 8px;">
                                                        <span style="font-size: 20px; font-weight: 800; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                                            £{{ number_format($summary['expected'], 0) }}
                                                        </span>
                                                        <span style="font-size: 12px; font-weight: 600; text-shadow: 0 1px 2px rgba(0,0,0,0.3); opacity: 0.9;">
                                                            ({{ $summary['expectedItems']->count() }})
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Received Card -->
                                            <div class="col-md-4">
                                                <div class="card mit-summary-card" data-mit-filter="received" style="cursor: pointer; background: linear-gradient(135deg, #27ae60, #229954); border: none; border-radius: 0; padding: 7px 0 15px 0; text-align: center; color: white;">
                                                    <div style="font-size: 2.5rem; margin-bottom: 10px;">
                                                        <i class="fa fa-check-circle"></i>
                                                    </div>
                                                    <div style="font-size: 14px; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 10px; opacity: 0.9;">
                                                        Received
                                                    </div>
                                                    <div style="display: flex; align-items: baseline; justify-content: center; gap: 8px;">
                                                        <span style="font-size: 20px; font-weight: 800; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                                            £{{ number_format($summary['received'], 0) }}
                                                        </span>
                                                        <span style="font-size: 12px; font-weight: 600; text-shadow: 0 1px 2px rgba(0,0,0,0.3); opacity: 0.9;">
                                                            ({{ $summary['receivedItems']->count() }})
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Decline Card -->
                                            <div class="col-md-4">
                                                <div class="card mit-summary-card" data-mit-filter="decline" style="cursor: pointer; background: linear-gradient(135deg, #e74c3c, #c0392b); border: none; border-radius: 0; padding: 7px 0 15px 0; text-align: center; color: white;">
                                                    <div style="font-size: 2.5rem; margin-bottom: 10px;">
                                                        <i class="fa fa-times-circle"></i>
                                                    </div>
                                                    <div style="font-size: 14px; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 10px; opacity: 0.9;">
                                                        Decline or Cancelled
                                                    </div>
                                                    <div style="display: flex; align-items: baseline; justify-content: center; gap: 8px;">
                                                        <span style="font-size: 20px; font-weight: 800; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                                            £{{ number_format($summary['decline'], 0)}}
                                                        </span>
                                                        <span style="font-size: 12px; font-weight: 600; text-shadow: 0 1px 2px rgba(0,0,0,0.3); opacity: 0.9;">
                                                            ({{ $summary['declineItems']->count()}})
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                </div>
                            </div>
                            @endif
                            <!-- // END OF WEEKLY SUMMARY PANEL -->


                    </x-ui.panel>
                </div>
            </div>



            <!-- MIT Queue Items -->
            <div class="row">
                <div class="col-md-12">
                    <x-ui.panel title="MIT Queue Items" icon="fa fa-list" variant="warning">
                        @if($queueItems->count() > 0)
                            {{-- MIT Filters + View Toggle --}}
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" style="background: rgba(0,0,0,0.1); padding: 12px; border-radius: 4px;">
                                <div class="btn-group btn-group-sm" id="mit-status-filters" role="group" aria-label="MIT status filter">
                                    <button type="button" class="btn btn-outline-secondary active" data-mit-status="all">
                                        <i class="fa fa-list"></i> All
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" data-mit-status="expected">
                                        <i class="fa fa-calendar-check"></i> Expected
                                    </button>
                                    <button type="button" class="btn btn-outline-success" data-mit-status="received">
                                        <i class="fa fa-check-circle"></i> Received
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" data-mit-status="decline">
                                        <i class="fa fa-times-circle"></i> Decline / Cancel
                                    </button>
                                </div>

                                <div class="btn-group btn-group-sm" id="mit-view-toggle" role="group" aria-label="MIT view toggle">
                                    <button type="button" class="btn btn-outline-light active" data-mit-view="cards">
                                        <i class="fa fa-th-large"></i> Boxes
                                    </button>
                                    <button type="button" class="btn btn-outline-light" data-mit-view="list">
                                        <i class="fa fa-list"></i> List
                                    </button>
                                </div>
                            </div>

                            {{-- Search and Filter Row --}}
                            <div class="row mb-3">
                                <div class="col-md-3 mb-2">
                                    <input type="text" class="form-control form-control-sm" placeholder="Search name / VRM / contract / invoice / phone" id="mit-search-text" autocomplete="off">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <select id="mit-filter-type" class="form-control form-control-sm">
                                        <option value="all">All Types</option>
                                        <option value="finance">Finance</option>
                                        <option value="rental">Rental</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <select id="mit-filter-frequency" class="form-control form-control-sm">
                                        <option value="all">All Frequencies</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="custom">Custom</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <select id="mit-filter-due" class="form-control form-control-sm">
                                        <option value="all">All Due States</option>
                                        <option value="overdue">Overdue</option>
                                        <option value="today">Due Today</option>
                                        <option value="soon">Due Soon</option>
                                        <option value="future">Future</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <small class="text-muted" id="mit-filter-count">Showing all items</small>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('page.judopay.add-to-queue') }}" id="bulk-queue-form">
                                @csrf

                                @php
                                    $generatedItems = $queueItems->where('status', 'generated');
                                    $isPastWeek = $currentWeekStart->lt(\Carbon\Carbon::now()->startOfWeek());
                                @endphp

                                @if($generatedItems->count() > 0 && !$isPastWeek)
                                    <!-- Top Bulk Actions - only show if there are generated items and not past week -->
                                    <div class="bulk-actions">
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="checkbox" name="select_all_top" id="select_all_top" onchange="toggleAllCheckboxes(this)">
                                            <label for="select_all_top">Select All ({{ $generatedItems->count() }} available)</label>
                                        </div>

                                        <!-- Sort Buttons in Center -->
                                        <div class="d-flex align-items-center gap-2" style="flex-wrap: wrap;">
                                            <span style="color: #ecf0f1; font-weight: 600; font-size: 0.9rem; margin-right: 8px;">Sort by:</span>
                                            <a href="{{ route('page.judopay.weekly-mit-queue', array_filter(['week' => $currentWeekStart->format('Y-m-d'), 'sort' => 'decline'])) }}"
                                               class="btn btn-sm {{ isset($sortParam) && $sortParam === 'decline' ? 'btn-danger' : 'btn-outline-danger' }}"
                                               style="border-radius: 0; font-weight: 600;">
                                                <i class="fa fa-ban"></i> DECLINE
                                            </a>
                                            <a href="{{ route('page.judopay.weekly-mit-queue', array_filter(['week' => $currentWeekStart->format('Y-m-d'), 'sort' => 'success'])) }}"
                                               class="btn btn-sm {{ isset($sortParam) && $sortParam === 'success' ? 'btn-success' : 'btn-outline-success' }}"
                                               style="border-radius: 0; font-weight: 600;">
                                                <i class="fa fa-check-circle"></i> SUCCESS
                                            </a>
                                            @if(isset($sortParam))
                                                <a href="{{ route('page.judopay.weekly-mit-queue', ['week' => $currentWeekStart->format('Y-m-d')]) }}"
                                                   class="btn btn-sm btn-outline-secondary"
                                                   style="border-radius: 0; font-weight: 600;">
                                                    <i class="fa fa-list"></i> SHOW ALL
                                                </a>
                                            @endif
                                        </div>

                                        @can('add-weekly-queue')
                                        <button type="submit" class="btn btn-warning" onclick="return validateSelection()">
                                            <i class="fa fa-plus-circle"></i> Add Selected to Queue
                                        </button>
                                        @endcan
                                    </div>
                                @else
                                    <!-- Sort Buttons when no bulk actions -->
                                    <div class="mb-3 d-flex align-items-center gap-2" style="flex-wrap: wrap; justify-content: center;">
                                        <span style="color: #ecf0f1; font-weight: 600; font-size: 0.9rem; margin-right: 8px;">Sort by:</span>
                                        <a href="{{ route('page.judopay.weekly-mit-queue', array_filter(['week' => $currentWeekStart->format('Y-m-d'), 'sort' => 'decline'])) }}"
                                           class="btn btn-sm {{ isset($sortParam) && $sortParam === 'decline' ? 'btn-danger' : 'btn-outline-danger' }}"
                                           style="border-radius: 0; font-weight: 600;">
                                            <i class="fa fa-ban"></i> DECLINE
                                        </a>
                                        <a href="{{ route('page.judopay.weekly-mit-queue', array_filter(['week' => $currentWeekStart->format('Y-m-d'), 'sort' => 'success'])) }}"
                                           class="btn btn-sm {{ isset($sortParam) && $sortParam === 'success' ? 'btn-success' : 'btn-outline-success' }}"
                                           style="border-radius: 0; font-weight: 600;">
                                            <i class="fa fa-check-circle"></i> SUCCESS
                                        </a>
                                        @if(isset($sortParam))
                                            <a href="{{ route('page.judopay.weekly-mit-queue', ['week' => $currentWeekStart->format('Y-m-d')]) }}"
                                               class="btn btn-sm btn-outline-secondary"
                                               style="border-radius: 0; font-weight: 600;">
                                                <i class="fa fa-list"></i> SHOW ALL
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                <!-- Screen reader announcement -->
                                <div class="sr-only" role="status" aria-live="polite">
                                    Showing {{ $queueItems->count() }} payment {{ Str::plural('item', $queueItems->count()) }}
                                    for the week of {{ $currentWeekStart->format('F d') }} to {{ $currentWeekEnd->format('F d, Y') }}
                                </div>

                                <!-- Skip navigation for keyboard users -->
                                @if($queueItems->count() > 5)
                                    <nav aria-label="Queue navigation shortcuts" class="keyboard-nav mb-3">
                                        <a href="#end-of-queue" class="sr-only sr-only-focusable">Skip all {{ $queueItems->count() }} items</a>
                                    </nav>
                                @endif
                                <div class="row" id="mit-queue-items-container">
                                    @foreach($queueItems as $item)
                                        @php
                                            // Category from summary collections (matches top cards)
                                            $mitCategory = 'expected';
                                            if ($summary['receivedItems']->contains('id', $item->id)) {
                                                $mitCategory = 'received';
                                            } elseif ($summary['declineItems']->contains('id', $item->id)) {
                                                $mitCategory = 'decline';
                                            }

                                            // Type: finance vs rental - explicit detection
                                            $subscribableTypeFull = $item->subscribable->subscribable_type ?? '';
                                            if ($subscribableTypeFull === 'App\\Models\\RentingBooking') {
                                                $mitType = 'rental';
                                            } elseif ($subscribableTypeFull === 'App\\Models\\FinanceApplication') {
                                                $mitType = 'finance';
                                            } else {
                                                // Fallback: default to finance if unknown
                                                $mitType = 'finance';
                                            }
                                            
                                            // Billing details from subscribable (JudopaySubscription)
                                            $billingFrequency = $item->subscribable->billing_frequency ?? 'N/A';
                                            $billingDay = $item->subscribable->billing_day ?? 'N/A';
                                            $subscribableType = $item->subscribable->subscribable_type ?? 'N/A';

                                            // Core fields for search
                                            $regNo = $item->subscribable->subscribable_type === 'App\\Models\\RentingBooking'
                                                ? (optional(optional($item->subscribable->subscribable->rentingBookingItems->first())->motorbike)->reg_no ?? '')
                                                : (optional(optional($item->subscribable->subscribable->application_items->first())->motorbike)->reg_no ?? '');

                                            $customer = trim(
                                                (optional(optional($item->subscribable->judopayOnboarding)->onboardable)->first_name ?? '') . ' ' .
                                                (optional(optional($item->subscribable->judopayOnboarding)->onboardable)->last_name ?? '')
                                            );

                                            $phone = optional(optional($item->subscribable->judopayOnboarding)->onboardable)->phone ?? '';
                                            $contractId = $item->subscribable->subscribable_id ?? '';
                                            $invoiceNo = $item->invoice_number ?? '';

                                            // Due status bucket (reuse existing diff logic)
                                            $now = \Carbon\Carbon::now();
                                            $dueDate = \Carbon\Carbon::parse($item->mit_fire_date);
                                            $diffInMinutes = $now->diffInMinutes($dueDate, false);
                                            $diffInHours   = $now->diffInHours($dueDate, false);
                                            $diffInDays    = $now->diffInDays($dueDate, false);

                                            if ($diffInMinutes < 0) {
                                                $dueBucket = 'overdue';
                                            } elseif ($diffInMinutes < 60 || $diffInHours < 24) {
                                                $dueBucket = 'today';
                                            } elseif ($diffInDays <= 3) {
                                                $dueBucket = 'soon';
                                            } else {
                                                $dueBucket = 'future';
                                            }
                                        @endphp
                                        <div class="col-md-4 mit-item-wrapper">
                                            <article class="card mit-item-card" 
                                                     data-mit-category="{{ $mitCategory }}"
                                                     data-mit-type="{{ $mitType }}"
                                                     data-mit-frequency="{{ strtolower($billingFrequency) }}"
                                                     data-mit-name="{{ e($customer) }}"
                                                     data-mit-phone="{{ e($phone) }}"
                                                     data-mit-reg="{{ e($regNo) }}"
                                                     data-mit-contract="{{ e($contractId) }}"
                                                     data-mit-invoice="{{ e($invoiceNo) }}"
                                                     data-mit-due="{{ $dueBucket }}"
                                                     role="article" 
                                                     aria-labelledby="queue-item-{{ $item->id }}"
                                                @if($item->status === 'sent')
                                                    style="background: rgba(149, 165, 166, 0.3); border: 1px solid rgba(149, 165, 166, 0.5); opacity: 0.7; transition: all 0.3s ease;"
                                                    onmouseover="this.style.background='rgba(149, 165, 166, 0.5)'"
                                                    onmouseout="this.style.background='rgba(149, 165, 166, 0.3)'"
                                                @elseif($item->subscribable->subscribable_type === 'App\Models\RentingBooking')
                                                    style="background: rgba(52, 152, 219, 0.5); border: 1px solid rgba(52, 152, 219, 0.3); transition: all 0.3s ease;"
                                                    onmouseover="this.style.background='rgba(41, 128, 185, 0.8)'"
                                                    onmouseout="this.style.background='rgba(52, 152, 219, 0.5)'"
                                                @else
                                                    style="background: rgba(39, 174, 96, 0.5); border: 1px solid rgba(39, 174, 96, 0.3); transition: all 0.3s ease;"
                                                    onmouseover="this.style.background='rgba(46, 204, 113, 0.8)'"
                                                    onmouseout="this.style.background='rgba(39, 174, 96, 0.5)'"
                                                @endif>
                                            <!-- Checkbox for bulk selection - only show for generated items and not past week -->
                                            @if($item->status === 'generated' && !$isPastWeek)
                                                <input type="checkbox" name="ngn_mit_queue_ids[]" value="{{ $item->id }}"
                                                       class="queue-checkbox" id="queue-item-checkbox-{{ $item->id }}">
                                            @elseif($item->status === 'sent')
                                                <!-- QUEUED badge in top right corner -->
                                                <span class="badge" style="position: absolute; top: 8px; right: 8px; z-index: 10; background: linear-gradient(44deg, #27ae60, #2ecc71); color: white; font-weight: 800; padding: 6px 8px; font-size: 0.8rem;" aria-label="Status: Queued">
                                                    <i class="fa fa-paper-plane" aria-hidden="true"></i> QUEUED
                                                </span>
                                            @endif
                                                <div class="card-body p-1" id="queue-item-{{ $item->id }}" style="position: relative; z-index: 1;">
                                                    <!-- Skip to next item -->
                                                    @if($loop->iteration < $queueItems->count())
                                                        <a href="#queue-item-{{ $queueItems[$loop->iteration]->id }}" class="sr-only sr-only-focusable">
                                                            Skip to next queue item ({{ $loop->iteration + 1 }} of {{ $queueItems->count() }})
                                                        </a>
                                                    @else
                                                        <a href="#end-of-queue" class="sr-only sr-only-focusable">
                                                            Skip to end of queue
                                                        </a>
                                                    @endif
                                                    <!-- Row 1: ID, VRM, Customer Name and Phone -->
                                                    <section class="mb-1" aria-label="Vehicle and customer information">
                                                        <!-- NGN MIT Queue ID - small label first -->
                                                        <div style="margin-left: 8px; margin-bottom: 4px;">
                                                            <span style="color: rgba(255, 255, 255, 0.6); font-size: 0.6rem; font-weight: 600; background: rgba(0, 0, 0, 0.4); padding: 1px 4px; line-height: 1.2;" title="NGN MIT Queue ID">
                                                                #{{ $item->id }}
                                                            </span>
                                                            <span style="color: #ffa500; font-weight: 800; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.06em; text-shadow: 0 1px 2px rgba(0,0,0,0.8); margin-left: 2px;">
                                                                @if($item->subscribable->subscribable_type === 'App\Models\RentingBooking')
                                                                    {{ optional(optional($item->subscribable->subscribable->rentingBookingItems->first())->motorbike)->reg_no ?? 'N/A' }}
                                                                @else
                                                                    {{ optional(optional($item->subscribable->subscribable->application_items->first())->motorbike)->reg_no ?? 'N/A' }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                        <!-- Name and Phone below VRM, left-aligned -->
                                                        <div style="margin-left: 8px;">
                                                            <span style="color: #ecf0f1; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                                                {{ optional(optional($item->subscribable->judopayOnboarding)->onboardable)->first_name ?? 'N/A' }}
                                                                {{ optional(optional($item->subscribable->judopayOnboarding)->onboardable)->last_name ?? 'N/A' }}
                                                            </span>
                                                            <span style="color: #ecf0f1; font-weight: 700; font-size: 0.8rem; margin-left: 8px;">
                                                                <i class="fa fa-phone" style="color: #27ae60; margin-right: 8px;" aria-hidden="true"></i>
                                                                <span class="sr-only">Phone number: </span>{{ optional(optional($item->subscribable->judopayOnboarding)->onboardable)->phone ?? 'N/A' }}
                                                            </span>
                                                        </div>
                                                    </section>

                                                    <!-- Row 2: Contract ID, Invoice, Type and Billing Details -->
                                                    <section class="mb-1 text-center" aria-label="Contract and invoice information">
                                                        <div style="color: #bdc3c7; font-size: 0.85rem;">
                                                            <span style="color: #5dade2; font-weight: 600; font-size: 0.85rem;"><small style="font-size: 0.5rem; letter-spacing: 0.04em;">CONTRACT</small> {{ $item->subscribable->subscribable_id }}</span>
                                                            <span style="color: #ffffff; font-weight: 600; margin-left: 15px; font-size: 0.85rem;"><small style="font-size: 0.5rem; letter-spacing: 0.04em;">INVOICE</small> {{ $item->invoice_number }}</span>
                                                        </div>
                                                        <div style="margin-top: 4px; display: flex; justify-content: center; gap: 6px; align-items: center; flex-wrap: wrap;">
                                                            <!-- Type Badge -->
                                                            <span class="badge" style="background: {{ $mitType === 'rental' ? 'linear-gradient(44deg, #3498db, #2980b9)' : 'linear-gradient(44deg, #27ae60, #229954)' }}; color: white; font-weight: 700; padding: 2px 6px; font-size: 0.65rem; text-transform: uppercase;">
                                                                <i class="fa {{ $mitType === 'rental' ? 'fa-key' : 'fa-file-contract' }}" aria-hidden="true"></i> {{ $mitType === 'rental' ? 'RENTAL' : 'FINANCE' }}
                                                            </span>
                                                            <!-- Billing Frequency Badge - Clickable to toggle billing day -->
                                                            <span class="badge billing-frequency-toggle" 
                                                                  data-billing-day-target="billing-day-{{ $item->id }}"
                                                                  style="cursor: pointer; background: rgba(255, 255, 255, 0.15); color: #ecf0f1; font-weight: 600; padding: 2px 6px; font-size: 0.65rem; transition: background 0.2s ease;"
                                                                  onmouseover="this.style.background='rgba(255, 255, 255, 0.25)'"
                                                                  onmouseout="this.style.background='rgba(255, 255, 255, 0.15)'"
                                                                  title="Click to show/hide billing day">
                                                                <i class="fa fa-repeat" aria-hidden="true"></i> {{ strtoupper($billingFrequency) }}
                                                            </span>
                                                            <!-- Billing Day Badge -->
                                                            @if($billingDay !== 'N/A' && $billingDay !== null)
                                                                <span class="badge billing-day-badge" 
                                                                      id="billing-day-{{ $item->id }}"
                                                                      style="display: none; background: rgba(255, 255, 255, 0.15); color: #ecf0f1; font-weight: 600; padding: 2px 6px; font-size: 0.65rem;">
                                                                    <i class="fa fa-calendar-day" aria-hidden="true"></i> Day {{ $billingDay }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </section>

                                                    <!-- Row 3: £1.00 Weekly -->
                                                    <section class="mb-1 text-center" aria-label="Payment amount and frequency">
                                                        <div style="color: #ffffff; font-weight: 800; font-size: 1.4rem; text-transform: uppercase; letter-spacing: 0.02em; text-shadow: 0 2px 4px rgba(0,0,0,0.8);">
                                                            £{{ number_format($item->subscribable->amount, 0) }} <small style="font-size: 0.65rem; letter-spacing: 0.03em; text-transform: uppercase;">{{ ucfirst($item->subscribable->billing_frequency) }}</small>
                                                        </div>
                                                    </section>

                                                    <!-- Row 4: Due Date with Dynamic Countdown -->
                                                    <section class="mb-1 text-center" aria-label="Due date information">
                                                        <div style="color: #bdc3c7; font-size: 1.0rem;">
                                                            <span style="color: #ffffff; font-weight: 600; font-size: 0.9rem;"><small style="font-size: 0.55rem; letter-spacing: 0.04em;">DUE</small> {{ $item->mit_fire_date->format('D, M d, Y H:i') }}</span>
                                                            @if(!$item->cleared)
                                                                @php
                                                                    $now = \Carbon\Carbon::now();
                                                                    $dueDate = \Carbon\Carbon::parse($item->mit_fire_date);
                                                                    $diffInMinutes = $now->diffInMinutes($dueDate, false);
                                                                    $diffInHours = $now->diffInHours($dueDate, false);
                                                                    $diffInDays = $now->diffInDays($dueDate, false);

                                                                    if ($diffInMinutes < 0) {
                                                                        $dueText = 'OVERDUE BY ' . abs($diffInHours) . ' HOUR' . (abs($diffInHours) == 1 ? '' : 'S');
                                                                        $dueColor = '#e74c3c'; // Red for overdue
                                                                    } elseif ($diffInMinutes < 60) {
                                                                        $dueText = 'DUE IN ' . $diffInMinutes . ' MINUTE' . ($diffInMinutes == 1 ? '' : 'S');
                                                                        $dueColor = '#e74c3c'; // Red - imminent
                                                                    } elseif ($diffInHours < 24) {
                                                                        $dueText = 'DUE IN ' . $diffInHours . ' HOUR' . ($diffInHours == 1 ? '' : 'S');
                                                                        $dueColor = '#f39c12'; // Orange - today
                                                                    } elseif ($diffInDays == 1) {
                                                                        $dueText = 'DUE TOMORROW';
                                                                        $dueColor = '#3498db'; // Blue
                                                                    } else {
                                                                        $dueText = 'DUE IN ' . $diffInDays . ' DAY' . ($diffInDays == 1 ? '' : 'S');
                                                                        $dueColor = '#ffffff'; // White
                                                                    }
                                                                @endphp
                                                                <span style="color: {{ $dueColor }}; font-weight: 700; margin-left: 15px; font-size: 0.9rem; text-transform: uppercase;">
                                                                    {{ $dueText }}
                                                                </span>
                                                            @else
                                                                <span style="color: #27ae60; font-weight: 700; margin-left: 15px; font-size: 0.9rem; text-transform: uppercase;">
                                                                    <i class="fa fa-check-circle" aria-hidden="true"></i> PAID
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </section>

                                                    <!-- Row 5: Status Flags / Action Button -->
                                                    <section class="d-flex align-items-center justify-content-space-between" aria-label="Queue status flags" style="justify-content: space-between;">
                                                        <!-- VIEW Button on left -->
                                                        <a href="{{ route('page.judopay.subscribe', $item->subscribable->id) }}" class="btn btn-sm" style="background: linear-gradient(44deg, #3498db, #2980b9); color: white; font-weight: 800; padding: 6px 12px; border: none; cursor: pointer; border-radius: 0; text-decoration: none;" aria-label="View subscription">
                                                            <i class="fa fa-eye" aria-hidden="true"></i> VIEW
                                                        </a>

                                                        <!-- Status flags/actions on right -->
                                                        <div class="text-right">
                                                        @if($item->status === 'generated' && !$isPastWeek)
                                                            <form method="POST" action="{{ route('page.judopay.add-to-queue') }}" style="display: inline;">
                                                                @csrf
                                                                <input type="hidden" name="ngn_mit_queue_id" value="{{ $item->id }}">
                                                                <button type="submit" class="badge" style="background: linear-gradient(44deg, #fbb33f, #c98517); color: rgb(107, 106, 106); font-weight: 800; padding: 6px 8px; border: none; cursor: pointer;" aria-label="Add to queue">
                                                                    <i class="fa fa-clock" aria-hidden="true"></i> ADD TO QUEUE
                                                                </button>
                                                            </form>
                                                            @elseif($item->status === 'sent')
                                                                <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                                                                    <!-- MIT Attempt Flag - show all attempts for sent items -->
                                                                    @php
                                                                        $attemptColors = [
                                                                            'not attempt' => 'linear-gradient(44deg, #95a5a6, #7f8c8d)', // Grey for not attempted
                                                                            'first' => 'linear-gradient(44deg, #f39c12, #e67e22)', // Orange for first attempt
                                                                            'second' => 'linear-gradient(44deg, #e74c3c, #c0392b)', // Red for second attempt
                                                                            'manual' => 'linear-gradient(44deg, #8e44ad, #9b59b6)', // Purple for manual
                                                                        ];
                                                                        $attemptColor = $attemptColors[$item->mit_attempt] ?? 'linear-gradient(44deg, #95a5a6, #7f8c8d)';
                                                                    @endphp
                                                                    <div style="display: flex; flex-direction: row; align-items: center; gap: 6px;">
                                                                        @if($item->mit_attempt === 'manual')
                                                                            <span style="color: #bdc3c7; font-size: 0.9rem; font-weight: 500; line-height: 1.1; white-space: nowrap;">
                                                                                Declines more than one time for selected week
                                                                            </span>
                                                                        @endif
                                                                        <span class="badge" style="background: {{ $attemptColor }}; color: white; font-weight: 800; padding: 4px 6px; font-size: 0.75rem;" aria-label="MIT Attempt: {{ ucfirst($item->mit_attempt) }}">
                                                                            {{ strtoupper($item->mit_attempt) }}
                                                                        </span>
                                                                    </div>

                                                                    <!-- Cleared Status - only show if cleared -->
                                                                    @if($item->cleared)
                                                                        <span class="badge" style="background: linear-gradient(44deg, #27ae60, #2ecc71); color: white; font-weight: 800; padding: 4px 6px; font-size: 0.75rem;" aria-label="Cleared: {{ $item->cleared_at ? $item->cleared_at->format('M d, Y H:i') : 'Unknown' }}">
                                                                            <i class="fa fa-check" aria-hidden="true"></i> CLEARED
                                                                        </span>
                                                                    @endif

                                                                    <!-- Cleared Date - show in same row if cleared -->
                                                                    @if($item->cleared && $item->cleared_at)
                                                                        <span style="color: #27ae60; font-weight: 600; font-size: 0.8rem; white-space: nowrap;">
                                                                            <i class="fa fa-calendar-check" aria-hidden="true"></i> {{ $item->cleared_at->format('M d, H:i') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </section>
                                                </div>
                                            </article>
                                        </div>
                                    @endforeach
                                </div>
                            </form>
                        @else
                            <div class="text-center p-5">
                                <i class="fa fa-calendar-times" style="font-size: 3rem; color: #95a5a6; margin-bottom: 1rem;"></i>
                                <h5 style="color: #acb7b7; text-transform: uppercase; letter-spacing: 0.05em; font-size: 2.2rem;">No queue for this week</h5>
                                <a href="{{ route('page.judopay.weekly-mit-queue') }}" class="btn btn-outline-info btn-lg mt-4">
                                    <i class="fa fa-home"></i> View Current Week
                                </a>
                            </div>
                        @endif
                    </x-ui.panel>
                </div>
            </div>


            <!-- Live MIT Queue Items -->
            <div class="row">
                <div class="col-md-12">
                    <x-ui.panel title="Live MIT Queue" icon="fa fa-rocket" variant="danger">
                        @if($liveQueueItems->count() > 0)
                            {{-- Live MIT Filters --}}
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" style="background: rgba(0,0,0,0.1); padding: 12px; border-radius: 4px;">
                                <div class="row w-100">
                                    <div class="col-md-3 mb-2">
                                        <input type="text" class="form-control form-control-sm" placeholder="Search name / VRM / contract / invoice / phone" id="live-mit-search-text" autocomplete="off">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <select id="live-mit-filter-type" class="form-control form-control-sm">
                                            <option value="all">All Types</option>
                                            <option value="finance">Finance</option>
                                            <option value="rental">Rental</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <select id="live-mit-filter-frequency" class="form-control form-control-sm">
                                            <option value="all">All Frequencies</option>
                                            <option value="weekly">Weekly</option>
                                            <option value="monthly">Monthly</option>
                                            <option value="custom">Custom</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5 mb-2">
                                        <small class="text-muted" id="live-mit-filter-count">Showing all items</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="live-mit-queue-items-container">
                                @foreach($liveQueueItems as $liveItem)
                                    @php
                                        // Type: finance vs rental - explicit detection
                                        $liveSubscribableTypeFull = $liveItem->ngnMitQueue->subscribable->subscribable_type ?? '';
                                        if ($liveSubscribableTypeFull === 'App\\Models\\RentingBooking') {
                                            $liveType = 'rental';
                                        } elseif ($liveSubscribableTypeFull === 'App\\Models\\FinanceApplication') {
                                            $liveType = 'finance';
                                        } else {
                                            // Fallback: default to finance if unknown
                                            $liveType = 'finance';
                                        }
                                        
                                        // Billing details from subscribable (JudopaySubscription)
                                        $liveBillingFrequency = $liveItem->ngnMitQueue->subscribable->billing_frequency ?? 'N/A';
                                        $liveBillingDay = $liveItem->ngnMitQueue->subscribable->billing_day ?? 'N/A';

                                        $liveReg = $liveItem->ngnMitQueue->subscribable->subscribable_type === 'App\\Models\\RentingBooking'
                                            ? (optional(optional($liveItem->ngnMitQueue->subscribable->subscribable->rentingBookingItems->first())->motorbike)->reg_no ?? '')
                                            : (optional(optional($liveItem->ngnMitQueue->subscribable->subscribable->application_items->first())->motorbike)->reg_no ?? '');

                                        $liveCustomer = trim(
                                            (optional(optional($liveItem->ngnMitQueue->subscribable->judopayOnboarding)->onboardable)->first_name ?? '') . ' ' .
                                            (optional(optional($liveItem->ngnMitQueue->subscribable->judopayOnboarding)->onboardable)->last_name ?? '')
                                        );
                                        $livePhone    = optional(optional($liveItem->ngnMitQueue->subscribable->judopayOnboarding)->onboardable)->phone ?? '';
                                        $liveContract = $liveItem->ngnMitQueue->subscribable->subscribable_id ?? '';
                                        $liveInvoice  = $liveItem->ngnMitQueue->invoice_number ?? '';
                                    @endphp
                                    <div class="col-md-4 live-item-wrapper">
                                        <article class="card live-mit-item"
                                                 data-live-type="{{ $liveType }}"
                                                 data-live-frequency="{{ strtolower($liveBillingFrequency) }}"
                                                 data-live-name="{{ e($liveCustomer) }}"
                                                 data-live-phone="{{ e($livePhone) }}"
                                                 data-live-reg="{{ e($liveReg) }}"
                                                 data-live-contract="{{ e($liveContract) }}"
                                                 data-live-invoice="{{ e($liveInvoice) }}"
                                                 role="article" 
                                                 aria-labelledby="live-queue-item-{{ $liveItem->id }}"
                                            @if($liveItem->fired)
                                                style="background: rgba(149, 165, 166, 0.3); border: 1px solid rgba(149, 165, 166, 0.5); opacity: 0.7; transition: all 0.3s ease;"
                                                onmouseover="this.style.background='rgba(149, 165, 166, 0.5)'"
                                                onmouseout="this.style.background='rgba(149, 165, 166, 0.3)'"
                                            @else
                                                style="background: rgba(231, 76, 60, 0.5); border: 1px solid rgba(231, 76, 60, 0.3); transition: all 0.3s ease;"
                                                onmouseover="this.style.background='rgba(192, 57, 43, 0.8)'"
                                                onmouseout="this.style.background='rgba(231, 76, 60, 0.5)'"
                                            @endif>

                                            @if($liveItem->fired)
                                                <!-- FIRED badge in top right corner -->
                                                <span class="badge" style="position: absolute; top: 8px; right: 8px; z-index: 10; background: linear-gradient(44deg, #27ae60, #2ecc71); color: white; font-weight: 800; padding: 6px 8px; font-size: 0.8rem;" aria-label="Status: Fired">
                                                    <i class="fa fa-check-circle" aria-hidden="true"></i> FIRED
                                                </span>
                                            @else
                                                <!-- READY TO FIRE badge in top right corner -->
                                                <span class="badge" style="position: absolute; top: 8px; right: 8px; z-index: 10; background: linear-gradient(44deg, #e74c3c, #c0392b); color: white; font-weight: 800; padding: 6px 8px; font-size: 0.8rem;" aria-label="Status: Ready to fire">
                                                    <i class="fa fa-rocket" aria-hidden="true"></i> READY
                                                </span>
                                            @endif

                                            <div class="card-body p-1" id="live-queue-item-{{ $liveItem->id }}" style="position: relative; z-index: 1;">
                                                <!-- Row 1: IDs, VRM, Customer Name and Phone -->
                                                <section class="mb-1" aria-label="Vehicle and customer information">
                                                    <!-- IDs and VRM on top -->
                                                    <div style="margin-left: 8px; margin-bottom: 4px;">
                                                        <!-- Judopay MIT Queue ID - small label first -->
                                                        <span style="color: rgba(255, 255, 255, 0.6); font-size: 0.6rem; font-weight: 600; background: rgba(0, 0, 0, 0.4); padding: 1px 4px; line-height: 1.2;" title="Judopay MIT Queue ID">
                                                            J#{{ $liveItem->id }}
                                                        </span>
                                                        <!-- NGN MIT Queue ID - small label after Judopay ID -->
                                                        <span style="color: rgba(255, 255, 255, 0.6); font-size: 0.6rem; font-weight: 600; background: rgba(0, 0, 0, 0.4); padding: 1px 4px; margin-left: 4px; line-height: 1.2;" title="NGN MIT Queue ID">
                                                            N#{{ $liveItem->ngn_mit_queue_id }}
                                                        </span>
                                                        <span style="color: #ffa500; font-weight: 800; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.06em; text-shadow: 0 1px 2px rgba(0,0,0,0.8); margin-left: 2px;">
                                                            @if($liveItem->ngnMitQueue->subscribable->subscribable_type === 'App\Models\RentingBooking')
                                                                {{ optional(optional($liveItem->ngnMitQueue->subscribable->subscribable->rentingBookingItems->first())->motorbike)->reg_no ?? 'N/A' }}
                                                            @else
                                                                {{ optional(optional($liveItem->ngnMitQueue->subscribable->subscribable->application_items->first())->motorbike)->reg_no ?? 'N/A' }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <!-- Name and Phone below VRM, left-aligned -->
                                                    <div style="margin-left: 8px;">
                                                        <span style="color: #ecf0f1; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                                            {{ optional(optional($liveItem->ngnMitQueue->subscribable->judopayOnboarding)->onboardable)->first_name ?? 'N/A' }}
                                                            {{ optional(optional($liveItem->ngnMitQueue->subscribable->judopayOnboarding)->onboardable)->last_name ?? 'N/A' }}
                                                        </span>
                                                        <span style="color: #ecf0f1; font-weight: 700; font-size: 0.8rem; margin-left: 8px;">
                                                            <i class="fa fa-phone" style="color: #27ae60; margin-right: 8px;" aria-hidden="true"></i>
                                                            <span class="sr-only">Phone number: </span>{{ optional(optional($liveItem->ngnMitQueue->subscribable->judopayOnboarding)->onboardable)->phone ?? 'N/A' }}
                                                        </span>
                                                    </div>
                                                </section>

                                                <!-- Row 2: Contract ID, Invoice, Type and Billing Details -->
                                                <section class="mb-1 text-center" aria-label="Contract and invoice information">
                                                    <div style="color: #bdc3c7; font-size: 0.85rem;">
                                                        <span style="color: #5dade2; font-weight: 600; font-size: 0.85rem;"><small style="font-size: 0.5rem; letter-spacing: 0.04em;">CONTRACT</small> {{ $liveItem->ngnMitQueue->subscribable->subscribable_id }}</span>
                                                        <span style="color: #ffffff; font-weight: 600; margin-left: 15px; font-size: 0.85rem;"><small style="font-size: 0.5rem; letter-spacing: 0.04em;">INVOICE</small> {{ $liveItem->ngnMitQueue->invoice_number }}</span>
                                                    </div>
                                                    <div style="margin-top: 4px; display: flex; justify-content: center; gap: 6px; align-items: center; flex-wrap: wrap;">
                                                        <!-- Type Badge -->
                                                        <span class="badge" style="background: {{ $liveType === 'rental' ? 'linear-gradient(44deg, #3498db, #2980b9)' : 'linear-gradient(44deg, #27ae60, #229954)' }}; color: white; font-weight: 700; padding: 2px 6px; font-size: 0.65rem; text-transform: uppercase;">
                                                            <i class="fa {{ $liveType === 'rental' ? 'fa-key' : 'fa-file-contract' }}" aria-hidden="true"></i> {{ $liveType === 'rental' ? 'RENTAL' : 'FINANCE' }}
                                                        </span>
                                                        <!-- Billing Frequency Badge - Clickable to toggle billing day -->
                                                        <span class="badge billing-frequency-toggle" 
                                                              data-billing-day-target="live-billing-day-{{ $liveItem->id }}"
                                                              style="cursor: pointer; background: rgba(255, 255, 255, 0.15); color: #ecf0f1; font-weight: 600; padding: 2px 6px; font-size: 0.65rem; transition: background 0.2s ease;"
                                                              onmouseover="this.style.background='rgba(255, 255, 255, 0.25)'"
                                                              onmouseout="this.style.background='rgba(255, 255, 255, 0.15)'"
                                                              title="Click to show/hide billing day">
                                                            <i class="fa fa-repeat" aria-hidden="true"></i> {{ strtoupper($liveBillingFrequency) }}
                                                        </span>
                                                        <!-- Billing Day Badge -->
                                                        @if($liveBillingDay !== 'N/A' && $liveBillingDay !== null)
                                                            <span class="badge billing-day-badge" 
                                                                  id="live-billing-day-{{ $liveItem->id }}"
                                                                  style="display: none; background: rgba(255, 255, 255, 0.15); color: #ecf0f1; font-weight: 600; padding: 2px 6px; font-size: 0.65rem;">
                                                                <i class="fa fa-calendar-day" aria-hidden="true"></i> Day {{ $liveBillingDay }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </section>

                                                <!-- Row 3: £1.00 Weekly -->
                                                <section class="mb-1 text-center" aria-label="Payment amount and frequency">
                                                    <div style="color: #ffffff; font-weight: 800; font-size: 1.4rem; text-transform: uppercase; letter-spacing: 0.02em; text-shadow: 0 2px 4px rgba(0,0,0,0.8);">
                                                        £{{ number_format($liveItem->ngnMitQueue->subscribable->amount, 0) }} <small style="font-size: 0.65rem; letter-spacing: 0.03em; text-transform: uppercase;">{{ ucfirst($liveItem->ngnMitQueue->subscribable->billing_frequency) }}</small>
                                                    </div>
                                                </section>

                                                <!-- Row 4: Payment Reference and Fire Date with Countdown -->
                                                <section class="mb-1 text-center" aria-label="Payment reference and fire date">
                                                    <div style="color: #bdc3c7; font-size: 1.0rem;">
                                                        <span style="color: #ffffff; font-weight: 600; font-size: 0.95rem;"><small style="font-size: 0.55rem; letter-spacing: 0.04em;">REF</small> {{ substr($liveItem->judopay_payment_reference, -8) }}</span>
                                                        <span style="color: #ffffff; font-weight: 600; margin-left: 15px; font-size: 0.9rem;"><small style="font-size: 0.55rem; letter-spacing: 0.04em;">FIRE</small> {{ $liveItem->mit_fire_date->format('D, M d, Y H:i') }}</span>
                                                        @if(!$liveItem->fired)
                                                            @php
                                                                $now = \Carbon\Carbon::now();
                                                                $fireDate = \Carbon\Carbon::parse($liveItem->mit_fire_date);
                                                                $diffInMinutes = $now->diffInMinutes($fireDate, false);
                                                                $diffInHours = $now->diffInHours($fireDate, false);
                                                                $diffInDays = $now->diffInDays($fireDate, false);

                                                                if ($diffInMinutes < 0) {
                                                                    $fireText = 'OVERDUE BY ' . abs($diffInHours) . ' HOUR' . (abs($diffInHours) == 1 ? '' : 'S');
                                                                    $fireColor = '#e74c3d'; // Red for overdue
                                                                } elseif ($diffInMinutes < 60) {
                                                                    $fireText = 'FIRES IN ' . $diffInMinutes . ' MINUTE' . ($diffInMinutes == 1 ? '' : 'S');
                                                                    $fireColor = '#e74c3c'; // Red - imminent
                                                                } elseif ($diffInHours < 24) {
                                                                    $fireText = 'FIRES IN ' . $diffInHours . ' HOUR' . ($diffInHours == 1 ? '' : 'S');
                                                                    $fireColor = '#f39c12'; // Orange - today
                                                                } elseif ($diffInDays == 1) {
                                                                    $fireText = 'FIRES TOMORROW';
                                                                    $fireColor = '#3498db'; // Blue
                                                                } else {
                                                                    $fireText = 'FIRES IN ' . $diffInDays . ' DAY' . ($diffInDays == 1 ? '' : 'S');
                                                                    $fireColor = '#ffffff'; // White
                                                                }
                                                            @endphp
                                                            <span style="color: {{ $fireColor }}; font-weight: 700; margin-left: 15px; font-size: 0.9rem; text-transform: uppercase;">
                                                                {{ $fireText }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </section>

                                                <!-- Row 5: Status Flags and Actions -->
                                                <section class="d-flex align-items-center justify-content-space-between" aria-label="Live queue status flags" style="justify-content: space-between;">
                                                    <!-- VIEW Button on left -->
                                                    <a href="{{ route('page.judopay.subscribe', $liveItem->ngnMitQueue->subscribable->id) }}" class="btn btn-sm" style="background: linear-gradient(44deg, #3498db, #2980b9); color: white; font-weight: 800; padding: 6px 12px; border: none; cursor: pointer; border-radius: 0; text-decoration: none;" aria-label="View subscription">
                                                        <i class="fa fa-eye" aria-hidden="true"></i> VIEW
                                                    </a>

                                                    <!-- Status flags/actions on right -->
                                                    <div class="text-right">
                                                        <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                                                            <!-- Fired Status -->
                                                            @if($liveItem->fired)
                                                                <span class="badge" style="background: linear-gradient(44deg, #27ae60, #2ecc71); color: white; font-weight: 800; padding: 4px 6px; font-size: 0.75rem;" aria-label="Fired: Yes">
                                                                    <i class="fa fa-check" aria-hidden="true"></i> FIRED
                                                                </span>
                                                            @else
                                                                <span class="badge" style="background: linear-gradient(44deg, #e74c3c, #c0392b); color: white; font-weight: 800; padding: 4px 6px; font-size: 0.75rem;" aria-label="Fired: No">
                                                                    <i class="fa fa-clock" aria-hidden="true"></i> PENDING
                                                                </span>
                                                            @endif

                                                            <!-- Retry Count -->
                                                            @if($liveItem->retry > 0)
                                                                <span class="badge" style="background: linear-gradient(44deg, #f39c12, #e67e22); color: white; font-weight: 800; padding: 4px 6px; font-size: 0.75rem;" aria-label="Retry count: {{ $liveItem->retry }}">
                                                                    RETRY {{ $liveItem->retry }}
                                                                </span>
                                                            @endif

                                                            <!-- Payment Status - Check THIS specific attempt's status -->
                                                            @if($liveItem->fired && !$liveItem->cleared)
                                                                <span class="badge" style="background: linear-gradient(44deg, #e67e22, #d35400); color: white; font-weight: 800; padding: 4px 6px; font-size: 0.75rem;" aria-label="Payment declined">
                                                                    <i class="fa fa-ban" aria-hidden="true"></i> DECLINED
                                                                </span>
                                                            @elseif($liveItem->fired && $liveItem->cleared)
                                                                <span class="badge" style="background: linear-gradient(44deg, #27ae60, #2ecc71); color: white; font-weight: 800; padding: 4px 6px; font-size: 0.75rem;" aria-label="Payment successful">
                                                                    <i class="fa fa-check" aria-hidden="true"></i> SUCCESS
                                                                </span>
                                                            @endif

                                                            <!-- Authorized By -->
                                                            <span style="color: #bdc3c7; font-weight: 600; font-size: 0.8rem; white-space: nowrap;">
                                                                <i class="fa fa-user" aria-hidden="true"></i> {{ optional($liveItem->authorizedBy)->name ?? 'Unknown' }}
                                                            </span>
                                                        </div>

                                                        <!-- STOP Button - Only show if not fired and fire date hasn't passed -->
                                                        @if(!$liveItem->fired && $liveItem->mit_fire_date->gt(\Carbon\Carbon::now()))
                                                            <div class="mt-2">
                                                                <form method="POST" action="{{ route('page.judopay.stop-live-queue', $liveItem->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to stop this payment? This will remove it from the live queue.');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm" style="background: linear-gradient(44deg, #e74c3c, #c0392b); color: white; font-weight: 800; padding: 6px 16px; border: none; cursor: pointer; font-size: 0.85rem; border-radius: 0;" aria-label="Stop payment">
                                                                        <i class="fa fa-stop-circle" aria-hidden="true"></i> STOP
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </section>
                                            </div>
                                        </article>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center p-5">
                                <i class="fa fa-rocket" style="font-size: 3rem; color: #95a5a6; margin-bottom: 1rem;"></i>
                                <h5 style="color: #acb7b7; text-transform: uppercase; letter-spacing: 0.05em; font-size: 2.2rem;">No live queue items</h5>
                                <p style="color: #bdc3c7; font-size: 1rem;">No payments are currently in the live Queue.</p>
                            </div>
                        @endif
                    </x-ui.panel>
                </div>
            </div>
        </section>
    @endcan

@endsection

<script>
function toggleAllCheckboxes(selectAllCheckbox) {
    const itemCheckboxes = document.querySelectorAll('input[name="ngn_mit_queue_ids[]"]');
    itemCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
}

function validateSelection() {
    const checkedItems = document.querySelectorAll('input[name="ngn_mit_queue_ids[]"]:checked');
    if (checkedItems.length === 0) {
        return false;
    }
    return true;
}

// ---------- MIT Filters / Search / View Toggle ----------
(function() {
    const state = {
        status: 'all',
        view: 'cards',
        searchText: '',
        type: 'all',
        frequency: 'all',
        due: 'all',
    };

    function normalise(str) {
        return (str || '').toString().toLowerCase().trim();
    }

    function updateMitFilterCount() {
        const visible = document.querySelectorAll('.mit-item-card:not(.d-none)').length;
        const total = document.querySelectorAll('.mit-item-card').length;
        const countEl = document.getElementById('mit-filter-count');
        if (countEl) {
            if (visible === total) {
                countEl.textContent = `Showing all ${total} items`;
            } else {
                countEl.textContent = `Showing ${visible} of ${total} items`;
            }
        }
    }

    function applyMitFilters() {
        const items = document.querySelectorAll('.mit-item-card');
        let visibleCount = 0;
        
        items.forEach(card => {
            let visible = true;

            const cat = card.dataset.mitCategory || 'expected';
            const type = card.dataset.mitType || '';
            const frequency = card.dataset.mitFrequency || '';
            const due  = card.dataset.mitDue || '';

            const name     = normalise(card.dataset.mitName);
            const phone    = normalise(card.dataset.mitPhone);
            const reg      = normalise(card.dataset.mitReg);
            const contract = normalise(card.dataset.mitContract);
            const invoice  = normalise(card.dataset.mitInvoice);

            const haystack = [name, phone, reg, contract, invoice].join(' ');
            const searchLower = normalise(state.searchText);

            if (state.status !== 'all' && cat !== state.status) visible = false;
            if (state.type !== 'all' && type !== state.type) visible = false;
            if (state.frequency !== 'all' && frequency !== state.frequency) visible = false;
            if (state.due !== 'all' && due !== state.due) visible = false;
            if (searchLower && !haystack.includes(searchLower)) visible = false;

            card.classList.toggle('d-none', !visible);
            const wrapper = card.closest('.mit-item-wrapper');
            if (wrapper) wrapper.classList.toggle('d-none', !visible);
            
            if (visible) visibleCount++;
        });
        
        updateMitFilterCount();
    }

    function setView(view) {
        state.view = view;
        const wrappers = document.querySelectorAll('.mit-item-wrapper');
        wrappers.forEach(w => {
            if (view === 'list') {
                w.classList.remove('col-md-4');
                w.classList.add('col-12', 'mit-list-row');
            } else {
                w.classList.add('col-md-4');
                w.classList.remove('col-12', 'mit-list-row');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Status buttons
        document.querySelectorAll('#mit-status-filters [data-mit-status]').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('#mit-status-filters .btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                state.status = this.dataset.mitStatus || 'all';
                applyMitFilters();
            });
        });

        // View toggle
        document.querySelectorAll('#mit-view-toggle [data-mit-view]').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('#mit-view-toggle .btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                setView(this.dataset.mitView || 'cards');
            });
        });

        // Summary cards click => status filter
        document.querySelectorAll('.mit-summary-card[data-mit-filter]').forEach(card => {
            card.addEventListener('click', function() {
                const filter = this.dataset.mitFilter || 'all';
                const btn = document.querySelector('#mit-status-filters [data-mit-status="' + filter + '"]');
                if (btn) btn.click();
            });
        });

        // Search text
        const searchInput = document.getElementById('mit-search-text');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                state.searchText = this.value;
                applyMitFilters();
            });
        }

        // Type filter
        const typeSelect = document.getElementById('mit-filter-type');
        if (typeSelect) {
            typeSelect.addEventListener('change', function() {
                state.type = this.value || 'all';
                applyMitFilters();
            });
        }

        // Frequency filter
        const frequencySelect = document.getElementById('mit-filter-frequency');
        if (frequencySelect) {
            frequencySelect.addEventListener('change', function() {
                state.frequency = this.value || 'all';
                applyMitFilters();
            });
        }

        // Due filter
        const dueSelect = document.getElementById('mit-filter-due');
        if (dueSelect) {
            dueSelect.addEventListener('change', function() {
                state.due = this.value || 'all';
                applyMitFilters();
            });
        }

        // Default view
        setView('cards');
        applyMitFilters();
    });
})();

// ---------- Live MIT Search (simple text + type) ----------
(function() {
    const liveState = {
        searchText: '',
        type: 'all',
        frequency: 'all',
    };

    function normalise(str) {
        return (str || '').toString().toLowerCase().trim();
    }

    function updateLiveFilterCount() {
        const visible = document.querySelectorAll('.live-mit-item:not(.d-none)').length;
        const total = document.querySelectorAll('.live-mit-item').length;
        const countEl = document.getElementById('live-mit-filter-count');
        if (countEl) {
            if (visible === total) {
                countEl.textContent = `Showing all ${total} items`;
            } else {
                countEl.textContent = `Showing ${visible} of ${total} items`;
            }
        }
    }

    function applyLiveFilters() {
        const items = document.querySelectorAll('.live-mit-item');
        let visibleCount = 0;
        
        items.forEach(card => {
            let visible = true;
            const type = card.dataset.liveType || '';
            const frequency = card.dataset.liveFrequency || '';
            const name     = normalise(card.dataset.liveName);
            const phone    = normalise(card.dataset.livePhone);
            const reg      = normalise(card.dataset.liveReg);
            const contract = normalise(card.dataset.liveContract);
            const invoice  = normalise(card.dataset.liveInvoice);
            const haystack = [name, phone, reg, contract, invoice].join(' ');
            const searchLower = normalise(liveState.searchText);

            if (liveState.type !== 'all' && type !== liveState.type) visible = false;
            if (liveState.frequency !== 'all' && frequency !== liveState.frequency) visible = false;
            if (searchLower && !haystack.includes(searchLower)) visible = false;

            card.classList.toggle('d-none', !visible);
            const wrapper = card.closest('.live-item-wrapper');
            if (wrapper) wrapper.classList.toggle('d-none', !visible);
            
            if (visible) visibleCount++;
        });
        
        updateLiveFilterCount();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('live-mit-search-text');
        const typeSelect  = document.getElementById('live-mit-filter-type');
        const frequencySelect = document.getElementById('live-mit-filter-frequency');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                liveState.searchText = this.value;
                applyLiveFilters();
            });
        }

        if (typeSelect) {
            typeSelect.addEventListener('change', function() {
                liveState.type = this.value || 'all';
                applyLiveFilters();
            });
        }

        if (frequencySelect) {
            frequencySelect.addEventListener('change', function() {
                liveState.frequency = this.value || 'all';
                applyLiveFilters();
            });
        }

        applyLiveFilters();
    });
})();

// ---------- Billing Frequency Toggle (works for both MIT and Live MIT) ----------
document.addEventListener('DOMContentLoaded', function() {
    // Billing frequency badge click to toggle billing day visibility
    document.querySelectorAll('.billing-frequency-toggle').forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const targetId = this.dataset.billingDayTarget;
            if (targetId) {
                const dayBadge = document.getElementById(targetId);
                if (dayBadge) {
                    const isHidden = dayBadge.style.display === 'none' || !dayBadge.style.display;
                    dayBadge.style.display = isHidden ? 'inline-block' : 'none';
                }
            }
        });
    });
});
</script>
