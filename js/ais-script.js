//Load color picker assets
(function($) {
    //Add color picker to all inputs that have 'color-field' class
    $(function() {
        $('.ais-colors').wpColorPicker();
    });
})(jQuery);
var upload_image_button = false;
jQuery(document).ready(function() {
    jQuery('.upload-img').click(function() {
        upload_image_button = true;
        formfieldID = jQuery(this).prev().attr("id");
        formfield = jQuery("#" + formfieldID).attr('name');
        tb_show('', 'media-upload.php?type=image&wlcms=true&amp;TB_iframe=true');
        if (upload_image_button == true) {
            var oldFunc = window.send_to_editor;
            window.send_to_editor = function(html) {
                imgurl = jQuery('img', html).attr('src');
                jQuery("#" + formfieldID).val(imgurl);
                tb_remove();
                window.send_to_editor = oldFunc;
                jQuery(formfieldID + '_thumb').html("<img height='65' src='" + imgurl + "'/>");
            }
        }
        upload_image_button = false;
    });
})