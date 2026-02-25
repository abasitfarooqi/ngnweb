$(document).ready(function () {
    function fetchRemainingBalance(memberId) {
        if (!memberId) return;

        $.ajax({
            url: '/ngn-admin/club-member/fetch-remaining-balance/' + memberId,
            type: 'GET',
            success: function (data) {
                if (data.remaining_balance !== undefined) {
                    $("[name='remaining_balance']").val(data.remaining_balance);
                }

                // Save today-purchase flag into a hidden field
                if ($("[name='has_today_purchases']").length === 0) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'has_today_purchases',
                        value: data.has_today_purchases ? 1 : 0
                    }).appendTo('form');
                } else {
                    $("[name='has_today_purchases']").val(data.has_today_purchases ? 1 : 0);
                }
            }
        });
    }

    // Always attach submit handler
    $("form").off("submit.hasTodayCheck").on("submit.hasTodayCheck", function (e) {
        var hasToday = $("[name='has_today_purchases']").val();
        if (hasToday == "1") {
            var confirmInclude = confirm(
                "This member has purchases from today. Do you want to redeem them as well?"
            );
            $("[name='include_today']").val(confirmInclude ? 1 : 0);
        }
    });

    // Run fetch when member changes
    $(document).on('change', "[name='club_member_id']", function () {
        fetchRemainingBalance($(this).val());
    });

    // Run once on page load
    var initialMemberId = $("[name='club_member_id']").val();
    if (initialMemberId) {
        fetchRemainingBalance(initialMemberId);
    }
});
