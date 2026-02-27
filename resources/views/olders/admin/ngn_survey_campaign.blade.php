@extends(backpack_view('blank'))

@section('after_styles')
    <style>
        /* Custom pagination styles */
        .custom-pagination {
            display: flex;
            gap: 5px;
            align-items: center;
            justify-content: center;
        }

        .custom-pagination .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.4;
            border-radius: 0.2rem;
            min-width: 32px;
            text-align: center;
            border: 1px solid #dee2e6;
            color: #4e73df;
            text-decoration: none;
            background: white;
        }

        .custom-pagination .page-link:hover {
            background-color: #e9ecef;
        }

        .custom-pagination .active {
            background-color: #4e73df;
            border-color: #4e73df;
            color: white;
        }

        .custom-pagination .disabled {
            opacity: 0.6;
            pointer-events: none;
        }

        .custom-pagination .ellipsis {
            padding: 0.25rem 0.5rem;
            color: #6c757d;
        }
    </style>
@endsection

@section('content')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none"
             bp-section="page-header">
        <h1 class="text-capitalize mb-0" bp-section="page-heading">Ngn Survey Campaign</h1>
    </section>

    <section class="content container-fluid animated fadeIn" bp-section="content">
        <div class="row mt-4">
            <div class="col-12">
                <div class="chart-container">
                    <h5>Survey Email Campaign List</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Email Sent</th>
                                    <th>SMS Sent</th>
                                    <th>WhatsApp Sent</th>
                                    <th>Last Email Sent At</th>
                                    <th>Last SMS Sent At</th>
                                    <th>Last WhatsApp Sent At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($campaignData as $campaign)
                                    <tr>
                                        <td>{{ $campaign['fullname'] }}</td>
                                        <td>{{ $campaign['email'] }}</td>
                                        <td>{{ $campaign['phone'] }}</td>
                                        <td>{{ $campaign['is_email_sent'] ? 'Yes' : 'No' }}</td>
                                        <td>{{ $campaign['is_sms_sent'] ? 'Yes' : 'No' }}</td>
                                        <td>{{ $campaign['is_whatsapp_sent'] ? 'Yes' : 'No' }}</td>
                                        <td>{{ $campaign['last_email_sent_datetime'] }}</td>
                                        <td>{{ $campaign['last_sms_sent_datetime'] }}</td>
                                        <td>{{ $campaign['last_whatsapp_sent_datetime'] }}</td>
                                        <td>
                                            <form action="{{ route('survey.send-reminder', $campaign['id']) }}" method="POST" id="whatsappForm-{{ $campaign['id'] }}">
                                                @csrf
                                                <a href="{{ $campaign['url_whatsapp'] }}" target="_blank" class="btn btn-success" onclick="event.preventDefault(); document.getElementById('whatsappForm-{{ $campaign['id'] }}').submit(); window.open('{{ $campaign['url_whatsapp'] }}', '_blank');">Dispatch WhatsApp Reminder</a>
                                            </form>
                                        </td>
                                        <td>
                                            <form action="{{ route('survey.send-sms-reminder', $campaign['id']) }}" method="POST" id="smsForm-{{ $campaign['id'] }}">
                                                @csrf
                                                <button type="submit" class="btn btn-primary" onclick="event.preventDefault(); document.getElementById('smsForm-{{ $campaign['id'] }}').submit();" {{ $campaign['is_sms_sent'] ? 'disabled' : '' }}>Dispatch SMS Reminder</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-3">
                            @if ($surveyEmailCampaigns->hasPages())
                                <div class="custom-pagination">
                                    @if ($surveyEmailCampaigns->onFirstPage())
                                        <span class="page-link disabled">←</span>
                                    @else
                                        <a href="{{ $surveyEmailCampaigns->previousPageUrl() }}" class="page-link">←</a>
                                    @endif

                                    @php
                                        $currentPage = $surveyEmailCampaigns->currentPage();
                                        $lastPage = $surveyEmailCampaigns->lastPage();
                                        $delta = 2; // Number of pages to show before and after current page
                                    @endphp

                                    {{-- First Page --}}
                                    <a href="{{ $surveyEmailCampaigns->url(1) }}" class="page-link {{ $currentPage == 1 ? 'active' : '' }}">1</a>

                                    {{-- Left Ellipsis --}}
                                    @if ($currentPage > $delta + 2)
                                        <span class="ellipsis">...</span>
                                    @endif

                                    {{-- Page Numbers --}}
                                    @foreach (range(max(2, $currentPage - $delta), min($lastPage - 1, $currentPage + $delta)) as $i)
                                        @if ($i > 1 && $i < $lastPage)
                                            <a href="{{ $surveyEmailCampaigns->url($i) }}" class="page-link {{ $currentPage == $i ? 'active' : '' }}">{{ $i }}</a>
                                        @endif
                                    @endforeach

                                    {{-- Right Ellipsis --}}
                                    @if ($currentPage < $lastPage - ($delta + 1))
                                        <span class="ellipsis">...</span>
                                    @endif

                                    {{-- Last Page --}}
                                    @if ($lastPage > 1)
                                        <a href="{{ $surveyEmailCampaigns->url($lastPage) }}" class="page-link {{ $currentPage == $lastPage ? 'active' : '' }}">{{ $lastPage }}</a>
                                    @endif

                                    @if ($surveyEmailCampaigns->hasMorePages())
                                        <a href="{{ $surveyEmailCampaigns->nextPageUrl() }}" class="page-link">→</a>
                                    @else
                                        <span class="page-link disabled">→</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('after_scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
