var cartModal;

$(document).on('click', '.add-to-cart-btn', function (e) {
    e.preventDefault();
    var charId = $('.property-list .active').data('id');
    $.get($(this).attr('href'), {
        itemId: $(this).data('itemId'), charId: charId, price: $('.price span').text()
    }, function (result) {
        $('.wrap').before('<div class="modals">' + result + '</div>');
        cartModal = $('#cart-modal');
        $(cartModal).modal('show');
        $(cartModal).on('hidden.bs.modal', function () {
            $('.modals').remove();
        });
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
    var itemBlock = $(this).parents('.item');
    var cartModal = $('#cart-modal');
    $.get($(this).attr('href'), {charId: $(this).data('charId')}, function (result) {
        if (result.status == true) {
            itemBlock.slideUp('fast', function () {
                $(this).remove();
                cartModal = $('#cart-modal');
                var container = $('.cart-items-list', cartModal);
                $('.total span', container).text(result.totalSum);
                updateOrderItemsCountainer();
                if ($('.item', container).size() == 1) {
                    $(container).remove();
                    $('.buttons', cartModal).addClass('hide');
                    $('.empty-cart-text', cartModal).removeClass('hide');
                    if ($('.order-page').length == 1) {
                        $('.form-block, .items-block').remove();
                        $('.order-page .empty-text-block').removeClass('hide');
                    }
                }
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
    var input = $(this).next();
    var qty = parseInt(input.val());
    qty--;
    if (qty < 1) {
        qty = 1;
    }
    input.val(qty);
    $.get(input.data('url'), {itemId: input.data('id'), charId: input.data('charId'), qty: qty}, function (result) {
        $(input).parents('.items').find('.total span').text(result.totalPrice);
        if ($(input).parents('#cart-modal').length == 1) {
            updateOrderItemsCountainer();
        }
    });
});

$(document).on('click', '#cart-modal .plus-btn, .order-page .items .plus-btn', function (e) {
    var input = $(this).prev();
    var qty = parseInt(input.val()) + 1;
    if (qty < 1) {
        qty = 1;
    }
    input.val(qty);
    $.get(input.data('url'), {itemId: input.data('id'), charId: input.data('charId'), qty: qty}, function (result) {
        $(input).parents('.items').find('.total span').text(result.totalPrice);
        if ($(input).parents('#cart-modal').length == 1) {
            updateOrderItemsCountainer();
        }
    });
});

$('header .navbar .cart-item a').click(function (e) {
    e.preventDefault();
    $.get(this.href, function (result) {
        $('.wrap').before('<div class="modals">' + result + '</div>');
        cartModal = $('#cart-modal');
        $(cartModal).modal('show');
        $(cartModal).on('hidden.bs.modal', function () {
            $('.modals').remove();
        });
    });
});

function updateOrderItemsCountainer() {
    var container = $('.order-page .cart-items-list');
    if (container.length == 1) {
        $.get($(container).data('url'), function (data) {
            var html = $('.cart-items-list', data).html();
            $(container).html(html);
        });
    }
}
