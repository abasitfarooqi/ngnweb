@extends(backpack_view('blank'))

@php
  $breadcrumbs = [
    'Admin' => backpack_url('dashboard'),
    'Queue Monitor' => false,
  ];
@endphp

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Delayed Jobs</h3>
                    <div class="box-tools pull-right">
                        <a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/queue-monitor?queue=' . $queueName) }}"
                           class="btn btn-sm btn-default">
                            <i class="fa fa-refresh"></i> Refresh
                        </a>
                    </div>
                </div>

                <div class="box-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Queue:</strong> <code>{{ $queueName }}</code><br>
                            <strong>Redis Key:</strong> <code>{{ $redisKey }}</code><br>
                            <strong>Total Jobs:</strong> <span class="badge badge-primary">{{ $totalJobs }}</span>
                        </div>
                    </div>

                    @if(count($jobs) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                            <tr>
                                <th>#</th>
                                <th>JudopayMitQueueId</th>
                                <th>Job Class</th>
                                <th>Scheduled At</th>
                                <th>Time Until</th>
                                <th>UUID</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                                </thead>
                                <tbody>
                                    @foreach($jobs as $index => $job)
                                        <tr class="{{ $job['scheduled_at_timestamp'] < time() ? 'danger' : '' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if($job['judopay_mit_queue_id'])
                                                    <strong><a href="{{ backpack_url('judopay-mit-queue/' . $job['judopay_mit_queue_id'] . '/show') }}" target="_blank">#{{ $job['judopay_mit_queue_id'] }}</a></strong>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <code>{{ $job['job_class'] }}</code>
                                                @if(isset($job['error']))
                                                    <br><small class="text-danger">{{ $job['error'] }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $job['scheduled_at'] }}
                                                <br><small class="text-muted">Timestamp: {{ $job['scheduled_at_timestamp'] }}</small>
                                            </td>
                                            <td>
                                                @if($job['scheduled_at_timestamp'] < time())
                                                    <span class="label label-danger">Overdue</span>
                                                @else
                                                    <span class="label label-success">{{ $job['time_until'] }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($job['uuid'])
                                                    <code>{{ substr($job['uuid'], 0, 8) }}...</code>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($job['scheduled_at_timestamp'] < time())
                                                    <span class="label label-danger">Overdue</span>
                                                @elseif($job['scheduled_at_timestamp'] - time() < 60)
                                                    <span class="label label-warning">Soon</span>
                                                @else
                                                    <span class="label label-info">Scheduled</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button"
                                                        class="btn btn-xs btn-info"
                                                        data-toggle="modal"
                                                        data-target="#jobModal{{ $index }}">
                                                    <i class="fa fa-eye"></i> View
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Modal for job details -->
                                        <div class="modal fade" id="jobModal{{ $index }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                        <h4 class="modal-title">Job Details</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        @if($job['judopay_mit_queue_id'])
                                                            <h5>JudopayMitQueueId</h5>
                                                            <p><strong><a href="{{ backpack_url('judopay-mit-queue/' . $job['judopay_mit_queue_id'] . '/show') }}" target="_blank">#{{ $job['judopay_mit_queue_id'] }}</a></strong></p>
                                                        @endif

                                                        <h5>Job Class</h5>
                                                        <pre><code>{{ $job['job_class'] }}</code></pre>

                                                        <h5>Full Payload</h5>
                                                        <pre style="max-height: 400px; overflow-y: auto;">{{ json_encode($job['decoded'] ?? json_decode($job['payload'], true), JSON_PRETTY_PRINT) }}</pre>

                                                        <h5>Raw Payload</h5>
                                                        <pre style="max-height: 200px; overflow-y: auto; word-break: break-all;">{{ $job['payload'] }}</pre>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> No delayed jobs found in this queue.
                        </div>
                    @endif
                    @if(isset($debugInfo))
                        <div class="alert alert-info">
                            <h5>Debug Information:</h5>
                            <pre style="max-height: 300px; overflow-y: auto;">{{ json_encode($debugInfo, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

