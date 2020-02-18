$(document).ready(function () {
    $('form[data-name="sonata-ajax"]').on('submit', function (e) {
        e.preventDefault();

        var self = $(this);

        $.ajax({
            type: self.attr('method'),
            url: self.attr('action'),
            data: self.serialize(),
            success: function (data) {
                var element = self.attr('data-target');

                if (data) {
                    $('#' + element).html(data);
                }
            }
        });
    });

    if ($('.basket')) {
        var update_basket = false;

        $(".basket input[type=number]").on('change', function (e) {
            update_basket = true;
        });

        $(".basket input[type=checkbox]").on('change', function (e) {
            update_basket = true;
        });

        $(".sonata-basket-nextstep").on('click', function (e) {
            if (update_basket) {
                if (!confirm(basket_update_confirmation_message)) {
                    e.preventDefault();
                }
            }
        });
    }

    jQuery('input[type="checkbox"]:not(label.btn > input, [data-sonata-icheck="false"]), input[type="radio"]:not(label.btn > input, [data-sonata-icheck="false"])', jQuery.window)
        .iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue'
        })
        // See https://github.com/fronteed/iCheck/issues/244
        .on('ifToggled', function (e) {
            $(e.target).trigger('change');
        });
});
