<a href="#" class="btn btn-sm btn-primary request-tol-btn" data-entry-id="{{ $entry->id }}">
    <i class="la la-file"></i> Request TOL
</a>

<script>
$(document).on('click', '.request-tol-btn', function(e){
    e.preventDefault();
    var id = $(this).data('entry-id');

    $.ajax({
        url: "{{ url('admin/pcn-case/request-tol') }}/" + id,
        type: "POST",
        data: {_token: "{{ csrf_token() }}"},
        success: function(res){
            if(res.success){
                alert(res.message);
                if(res.pdf) window.open(res.pdf, '_blank');
            }else{
                alert(res.message);
            }
        },
        error: function(){ alert('Error creating TOL request'); }
    });
});
</script>
