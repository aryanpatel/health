jQuery(document).ready(function ($) {
//select2
    $("select").select2();
    var tiptip_args = {
        'attribute': 'data-tip',
        'fadeIn': 50,
        'fadeOut': 50,
        'delay': 200
    };
    var init_tooltips = function () {
        $('.tips, .help_tip, .bmps-tool-tip').tipTip(tiptip_args);

        // Add tiptip to parent element for widefat tables
        $('.parent-tips').each(function () {
            $(this).closest('a, th').attr('data-tip', $(this).data('tip')).tipTip(tiptip_args).css('cursor', 'help');
        });
    }
    init_tooltips();
    //Daily Parking
    var dailyChecked = function () {
        var checkedDaily = $("#_rental_type_daily").is(":checked");
        if (checkedDaily === true) {
            $(".show_if_daily").show();
            $(".show_if_daily").removeClass("hide");
        } else {
            $("#_hourly_price").val('');
            $("#_daily_price").val('');
            $(".show_if_daily").hide();
            $(".show_if_daily").addClass("hide");
        }
    }
    dailyChecked();
    $("#_rental_type_daily").on("click", dailyChecked);

    //Monthly Parking
    var montlyChecked = function () {
        var checkedDaily = $("#_rental_type_monthly").is(":checked");
        if (checkedDaily === true) {
            $(".show_if_monthly").show();
            $(".show_if_monthly").removeClass("hide");
        } else {
            $("#_monthly_price").val('');
            $(".show_if_monthly").hide();
            $(".show_if_monthly").addClass("hide");
        }
    }
    montlyChecked();
    $("#_rental_type_monthly").on("click", montlyChecked);

    // Hide empty panels/tabs after display.
    jQuery('.parking_data_tabs a').click(function () {
        var targetDivId = $(this).attr('href').replace("#","");
        //Hide all tabls
        $(".bmps_options_panel").addClass('hidden');
        //Show Target Tab
        $("#"+targetDivId).removeClass("hidden");
    });
    
});