jQuery(document).ready(function ($) {
    $(document.body).on('bmps-init-tabbed-panels', function () {
        $('ul.wc-tabs').show();
        $('ul.wc-tabs a').click(function (e) {
            e.preventDefault();
            var panel_wrap = $(this).closest('div.panel-wrap');
            $('ul.wc-tabs li', panel_wrap).removeClass('active');
            $(this).parent().addClass('active');
            $('div.panel', panel_wrap).hide();
            $($(this).attr('href')).show();
        });
        $('div.panel-wrap').each(function () {
            $(this).find('ul.wc-tabs li').eq(0).find('a').click();
        });
    }).trigger('bmps-init-tabbed-panels');
});