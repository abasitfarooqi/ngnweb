<!-- resources/views/vendor/backpack/crud/columns/inline_stock_edit.blade.php -->
<div style="display: flex; align-items: center;">
    <input
        type="number"
        value="{{ $entry->{$column['name']} }}"
        class="form-control inline-stock-input"
        data-id="{{ $entry->id }}"
        data-field="{{ $column['name'] }}"
        style="width: 80px;"
        readonly
    />
    <button
        class="btn btn-sm btn-link inline-edit-btn"
        onclick="enableEdit(this)"
        style="margin-left: 5px;">
        <i class="la la-edit"></i>
    </button>
    <button
        class="btn btn-sm btn-link inline-save-btn d-none"
        onclick="saveStock(this)"
        style="margin-left: 5px;">
        <i class="la la-save"></i>
    </button>
</div>
