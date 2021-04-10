/**
 * Created by saniok on 28.09.15.
 */
$(document).ready(function () {
    $("dl.tabs").each(function () {
        $(this).find("dt:first").addClass("selected");
        $(this).find("dd:first").addClass("selected");
    });

    $("dl.tabs dt").click(e => {
        $(e.target)
            .siblings().removeClass("selected").end()
            .next("dd").addBack().addClass("selected");
        return false;
    });

    $(document).on('click', '#categories-ajax-container tr.has-sub-category .title', e => {
        var row = $(e.target).parents('tr.has-sub-category');
        if ($(row).hasClass('open')) {
            closeCategoryRow($(row).data('id'));
            $(row).addClass('closed');
            $(row).removeClass('open');
        } else {
            $('.sub-category-for-' + $(row).data('id')).show();
            $(row).addClass('open');
            $(row).removeClass('closed');
        }
    });

    function closeCategoryRow(id) {
        $('.sub-category-for-' + id).each(function () {
            if ($(this).hasClass('has-sub-category')) {
                $(this).hide();
                closeCategoryRow($(this).data('id'));
            } else {
                $(this).hide();
            }
        });
    }
});
