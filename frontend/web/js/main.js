"use strict";
var cartModal;

$(document).on('click', '.add-to-cart', function (e) {
    e.preventDefault();
    $.get($(this).attr('href'), function (result) {
        console.log(result)
        insertModal(result)
    });
});

$(document).on('click', '#cart-modal button[type=submit]', function (e) {
    e.preventDefault();
    $('#cart-modal').modal('hide');
    $(cartModal).on('hidden.bs.modal', function () {
        $('.modals').remove();
    });
});

$(document).on('click', '#cart-modal .delete-item-btn', function (e) {
    e.preventDefault();
    let itemBlock = $(this).parents('.cart__product__item');
    let cartModal = $('#cart-modal');
    $.get($(this).attr('href'), function (result) {
        if (result.status == true) {
            itemBlock.slideUp('fast', function () {
                $(this).remove();
                cartModal = $('#cart-modal');
                updateCartTotal();
            });
        }
    });
});

$(document).on('click', '.order-page .cart-items-list .delete-item-btn', function (e) {
    e.preventDefault();
    var container = $(this).parents('.cart-items-list');
    var itemBlock = $(this).parents('.item');
    $.get($(this).attr('href'), {charId: $(this).data('charId')}, function (result) {
        if (result.status == true) {
            itemBlock.slideUp('fast', function () {
                $(this).remove();
                $('.total span', container).text(result.totalSum);
                if ($('.item', container).size() == 1) {
                    $('.form-block, .items-block').remove();
                    $('.order-page .empty-text-block').removeClass('hide');
                }
            });
        }
    });
});


$(document).on('click', '#cart-modal .minus-btn, .order-page .items .minus-btn', function (e) {
    let input = $(this).parents('.cart__product__item').find('.price-input');
    let qty = parseInt(input.val());
    qty--;
    if (qty < 1) {
        qty = 1;
    }
    input.val(qty).trigger('change');
});

$(document).on('click', '#cart-modal .plus-btn, .order-page .items .plus-btn', function (e) {
    let input = $(this).parents('.cart__product__item').find('.price-input');
    let qty = parseInt(input.val()) + 1;
    if (qty < 1) {
        qty = 1;
    }
    input.val(qty).trigger('change');
});
$(document).on('change', '.price-input', e => {
    let input = $(e.target);
    let itemTotal = input.parents('.cart__product__item').find('.cart__price span');
    itemTotal.text(input.data('price') * input.val());
    updateCartTotal();
    $.get(input.data('url'), {itemId: input.data('id'), qty: input.val()}, function (result) {

    });
})

function updateCartTotal() {
    let priceList = $('.price-input');
    let productsTotal = $('.products-total span');
    let cart = $('#cart span');
    let countItems = 0, total = 0;

    if (priceList.length > 0) {
        priceList.each((i, input) => {
            let price = $(input).data('price');
            let count = parseInt($(input).val());
            countItems += count;
            total += count * price;
        });
        productsTotal.text(total);
        cart.text(countItems);
    }
}

$(document).on('click', '#cart', e => {
    e.preventDefault();
    $.post(e.currentTarget.href, function (result) {
        insertModal(result)
    });
    return false;
});

//Canvas Menu
$(".canvas__open").on('click', function () {
    $(".offcanvas-menu-wrapper").addClass("active");
    $(".offcanvas-menu-overlay").addClass("active");
});

$(".offcanvas-menu-overlay").on('click', function () {
    $(".offcanvas-menu-wrapper").removeClass("active");
    $(".offcanvas-menu-overlay").removeClass("active");
});

//Search Switch
$('.search-switch').on('click', function () {
    $('.search-model').fadeIn(400);
});

$('.search-close-switch').on('click', function () {
    $('.search-model').fadeOut(400, function () {
        $('#search-input').val('');
    });
});

/*------------------
    Accordin Active
--------------------*/
$('.collapse').on('shown.bs.collapse', function () {
    $(this).prev().addClass('active');
});

$('.collapse').on('hidden.bs.collapse', function () {
    $(this).prev().removeClass('active');
});

function updateOrderItemsCountainer() {
    let container = $('.order-page .cart-items-list');
    if (container.length == 1) {
        $.get($(container).data('url'), function (data) {
            let html = $('.cart-items-list', data).html();
            $(container).html(html);
        });
    }
}

function insertModal(content) {
    $('.body-wrap').before('<div class="modals">' + content + '</div>');
    cartModal = $('#cart-modal');
    $(cartModal).modal('show');
    $(cartModal).on('hidden.bs.modal', function () {
        $('.modals').remove();
    });
}

/*-----------------------
sidebar-menu-widget
--------------------------- */
$('.sidebar-menu-widget-title').on('click', e => {
    let liTitle = $(e.target).parent();
    liTitle.toggleClass('active');
    liTitle.children('.submenu').slideToggle();
})
/*-----------------------
    Filters
--------------------------- */
$('.filter-box-title').on('click', e => {
    let title = $(e.target);
    title.toggleClass('active');
    title.next('.filter-box-body').slideToggle();
})

function buildUrl() {
    let filters = $('.filter.active');
    let ids = [];
    let url = window.location.href.split('?').shift();
    if (filters.length > 0) {
        filters.each((i, item) => {
            ids.push($(item).data('filter-id'));
        });
        url = url + '?filter=' + ids.join(',');
    }
    return url;
}

$('.filter').on('click', e => {
    let filter = $(e.target);
    filter.toggleClass('active');
    window.location.href = buildUrl();
})
