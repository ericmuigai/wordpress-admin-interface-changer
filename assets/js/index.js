(function () {
    jQuery(document).ready(function ($) {
        $('#fateh-upload-btn').click(function (e) {
            e.preventDefault();
            var logo_image = wp.media({
                title: 'Insert Image',
                multiple: false
            }).open().on('select', function (e) {
                var uploaded_image = logo_image.state().get('selection').first();
                console.log(uploaded_image);
                var image_url = uploaded_image.toJSON().url;
                $('#logo_url').val(image_url);
                $("#logoImage").attr('src', image_url);
                $('#logoImage').removeClass("aic-hide")
            });
        });$('#fateh-upload-btn-menu').click(function (e) {
            e.preventDefault();
            var logo_image = wp.media({
                title: 'Insert Image',
                multiple: false
            }).open().on('select', function (e) {
                var uploaded_image = logo_image.state().get('selection').first();
                console.log(uploaded_image);
                var image_url = uploaded_image.toJSON().url;
                $('#logo_menu_url').val(image_url);
                $("#logoImageMenu").attr('src', image_url);
                $('#logoImageMenu').removeClass("aic-hide")
            });
        });
    });
})(jQuery)