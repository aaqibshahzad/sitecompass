// CODE FOR SHOW OR HIDE USER FORM FIELD OPTIONS
jQuery(document).ready(function($) {
    var defultUserFormValue = $('#sitecompass_show_user_form').val();
    if (defultUserFormValue === 'yes') {
        $('#sitecompass_fields_to_user_form_tr').show();
        $('#sitecompass_name_to_user_form').prop('checked', true);
        $('#sitecompass_email_to_user_form').prop('checked', true);
    } else {
        $('#sitecompass_fields_to_user_form_tr').hide();
        $('#sitecompass_name_to_user_form').prop('checked', false);
        $('#sitecompass_email_to_user_form').prop('checked', false);
    }

    $('#sitecompass_show_user_form').on('change', function() {
        var userFormValue = $(this).val();
        if (userFormValue === 'yes') {
            $('#sitecompass_fields_to_user_form_tr').show();
            $('#sitecompass_name_to_user_form').prop('checked', true);
            $('#sitecompass_email_to_user_form').prop('checked', true);
        } else {
            $('#sitecompass_fields_to_user_form_tr').hide(); 
            $('#sitecompass_name_to_user_form').prop('checked', false);
            $('#sitecompass_email_to_user_form').prop('checked', false);
        }
    });

    // Removed ChatGenie API validation code - SiteCompass uses only OpenAI API
});

function updateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf("?") !== -1 ? "&" : "?";
    if (uri.match(re)) {
      return uri.replace(re, "$1" + key + "=" + value + "$2");
    } else {
      return uri + separator + key + "=" + value;
    }
  }
