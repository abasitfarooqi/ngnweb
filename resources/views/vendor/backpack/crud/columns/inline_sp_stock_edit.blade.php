<div style="display: flex; align-items: center;">
    <input
        type="number"
        value="{{ $entry->{$column['name']} }}"
        class="form-control sp-inline-stock-input"
        data-id="{{ $entry->id }}"
        data-field="{{ $column['name'] }}"
        style="width: 80px;"
        readonly
    />
    <button
        class="btn btn-sm btn-link sp-inline-edit-btn"
        onclick="spEnableStockEdit(this)"
        style="margin-left: 5px;">
        <i class="la la-edit"></i>
    </button>
    <button
        class="btn btn-sm btn-link sp-inline-save-btn d-none"
        onclick="spSaveStock(this)"
        style="margin-left: 5px;">
        <i class="la la-save"></i>
    </button>
</div>
