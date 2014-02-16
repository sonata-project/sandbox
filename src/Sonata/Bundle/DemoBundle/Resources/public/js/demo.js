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
});