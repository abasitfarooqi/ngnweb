<!-- This CSS will ensure the button is aligned with the pagination -->
<style>
    .export-pos-btn {
        display: inline-block;
        margin-left: 15px; /* Space between pagination and the button */
        margin-bottom: 10px; /* Align vertically with pagination */
    }
</style>

<div class="d-flex justify-content-between align-items-center">
    <div class="export-pos-btn">
        <a href="{{ url($crud->route.'/export-pos') }}" class="btn btn-primary">
            <i class="la la-file-excel"></i> Export for POS
        </a>
    </div>
</div>
