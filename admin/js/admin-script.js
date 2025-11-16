/**
 * Admin JavaScript for SiteCompass AI plugin.
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize WordPress color picker.
        if (typeof $.fn.wpColorPicker !== 'undefined') {
            $('.sitecompass-color-field').wpColorPicker();
        }
    });

})(jQuery);
