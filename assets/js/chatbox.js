// Code to generate random id
document.addEventListener('DOMContentLoaded', function() {
    var sitecompassSessionId = getCookie('sitecompassSessionId');
    
    // Check if the cookie already exists
    if (!sitecompassSessionId) {
        var randomId = sitecompassRandomId.randomId;

        // Set the expiration time to 1 day (24 hours)
        var expirationDate = new Date();
        expirationDate.setTime(expirationDate.getTime() + (24 * 60 * 60 * 1000));
        var expires = "; expires=" + expirationDate.toUTCString();

        document.cookie = 'sitecompassSessionId=' + randomId + expires + '; path=/';

        document.cookie = 'sitecompassSessionSet=true; path=/';
    }
});

// Function to get the value of a cookie by name
function getCookie(name) {
    var cookies = document.cookie.split(';');
    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i].trim();
        if (cookie.indexOf(name + '=') === 0) {
            return cookie.substring(name.length + 1);
        }
    }
    return null;
}

// Code for open and close chatbox
jQuery(document).ready(function() {
    // Open chat box
    jQuery('#open-chat').click(function() {
        jQuery('#chat-box').show();
        jQuery('#open-chat').hide();

        var ChatDiv = jQuery('#chatBody');
        var height = ChatDiv[0].scrollHeight;
        ChatDiv.scrollTop(height);

    });

    // Close chat box
    jQuery('#close-chat').click(function() {
        jQuery('#chat-box').hide();
        jQuery('#open-chat').show();
    });

    
});

// Function for append messages
function appendMessage(message, className) {
    // Create elements safely to prevent XSS
    var messageDiv = document.createElement('div');
    messageDiv.className = 'chat-message ' + className;
    
    var messageSpan = document.createElement('span');
    messageSpan.textContent = message; // Use textContent instead of innerHTML for safety
    
    messageDiv.appendChild(messageSpan);

    const chatBox = document.getElementById('chatBody');
    chatBox.appendChild(messageDiv);
    chatBox.scrollTop = chatBox.scrollHeight;
}

// Code for chatbot
jQuery(document).ready(function($) {
    $('#sendMsg').on('click', function() {
        getUserMessage();
    });

    $('#userMessage').keypress(function(event) {
        if (event.which === 13 && !event.shiftKey) {
            event.preventDefault();
            getUserMessage();
        }
    });

    // Code for store user information
    $('#submitUserData').on('click', function() {
        var name = jQuery('#sitecompassUserName').val();
        var email = jQuery('#sitecompassUserEmail').val();
        var phone = jQuery('#sitecompassUserPhone').val();
        // Perform AJAX request
        $.ajax({
            type: 'POST',
            url: sitecompassAjax.ajax_url,
            data: {
                name: name,
                email: email,
                phone: phone,
                action: 'sitecompass_submit_user_info'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    jQuery('#form-success-message').text(response.message);
                    jQuery('#sitecompassUserForm').addClass('sitecompass-d-none');
                    jQuery('#sitecompassChatBox').removeClass('sitecompass-d-none');
                    var randomUserId = sitecompassRandomId.randomId;
                    document.cookie = 'sitecompassUserSessionId=' + randomUserId +'; path=/';

                    document.cookie = 'sitecompassUserSessionSet=true; path=/';
                } else if (response.status === 'error') {
                    jQuery('#form-error-message').text(response.message);
                    console.log(response.message);
                }
            }
        });
    });
});


function getUserMessage() {
    var userMessage = jQuery('#userMessage').val();
    if (!userMessage) return;
    appendMessage(userMessage, 'sitecompass-user-message');
    jQuery('#chatFooter').prepend(`<div class="sitecompass-bubble">
                                    <div class="typing">
                                        <div class="dot"></div>
                                        <div class="dot"></div>
                                        <div class="dot"></div>
                                    </div>
                                </div>`);
    jQuery('#userMessage').val("");

    var sessionId = getCookie('sitecompassSessionId');
    // Perform AJAX request
    jQuery.ajax({
        type: 'POST',
        url: sitecompassAjax.ajax_url,
        data: {
            sessionId: sessionId,
            userMessage: userMessage,
            userType: 'User',
            action: 'sitecompass_send_message',
        },
        success: function(response) {
            var jsonResponse = JSON.parse(response);
            if (response.error) {
                console.error('Error: ' + response.message);
            } else {
                appendMessage(jsonResponse.data, 'sitecompass-bot-message');
                jQuery('#chatFooter .sitecompass-bubble').remove();
            }
        }
    });
}
