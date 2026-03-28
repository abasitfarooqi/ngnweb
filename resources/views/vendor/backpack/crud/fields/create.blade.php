{{-- resources/views/vendor/backpack/crud/fields/create.blade.php --}}
@extends(backpack_view('blank'))

@section('content')
@endsection

@push('after_scripts')
    <script>
        $(document).ready(function() {
            $('select[name="customer_id"]').select2({
                ajax: {
                    url: '/ngn-admin/finance-application/fetch/customer',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1
            });
        });
    </script>
@endpush
