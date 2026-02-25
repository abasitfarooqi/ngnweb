$(document).ready(function () {
    function fetchSpendingTotals(memberId) {
        if (!memberId) return;

        $.ajax({
            url: '/ngn-admin/club-member/fetch-spending-totals/' + memberId,
            type: 'GET',
            success: function (data) {
                if (data.total_spending !== undefined) {
                    $("[name='total_spending']").val('£' + parseFloat(data.total_spending).toFixed(2));
                }

                if (data.total_unpaid !== undefined) {
                    $("[name='total_unpaid']").val('£' + parseFloat(data.total_unpaid).toFixed(2));
                }
            }
        });
    }

    // Run fetch when member changes
    $(document).on('change', "[name='club_member_id']", function () {
        fetchSpendingTotals($(this).val());
    });

    // Run once on page load
    var initialMemberId = $("[name='club_member_id']").val();
    if (initialMemberId) {
        fetchSpendingTotals(initialMemberId);
    }
});
