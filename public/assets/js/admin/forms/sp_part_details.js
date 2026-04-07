$(document).ready(function () {
    function updateStockFields(partId) {
        if (!partId) {
            return;
        }

        $.ajax({
            url: '/ngn-admin/sp-stock-handler/fetch-part-data',
            type: 'GET',
            data: { id: partId },
            success: function (data) {
                $('input[name="part_number"]').val(data.part_number);
                $('input[name="catford_stock"]').val(data.catford_stock);
                $('input[name="tooting_stock"]').val(data.tooting_stock);
                $('input[name="sutton_stock"]').val(data.sutton_stock);
            },
        });
    }

    $('select[name="sp_part_id"]').change(function () {
        updateStockFields($(this).val());
    });
});
