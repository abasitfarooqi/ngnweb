@extends(backpack_view('blank'))

@section('header')
    <section class="container-fluid">
        <h2>Dashboard</h2>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Widget 1</div>
                <div class="card-body">
                    Content for Widget 1
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Widget 2</div>
                <div class="card-body">
                    Content for Widget 2
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Weekly Users</div>
                <div class="card-body">
                    @widget('WeeklyUsers', ['type' => 'chart', 'height' => 500, 'width' => '100%', 'refresh' => 60])
                </div>
            </div>
        </div>
    </div>
@endsection
