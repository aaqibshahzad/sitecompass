(function ($) {
    'use strict';

    /**
     * Cookie Helpers
     */
    function getCookie(name) {
        var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? match[2] : null;
    }

    function setCookie(name, value, days) {
        var expires = '';
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + value + expires + '; path=/';
    }

    /**
     * Session ID Initialization
     */
    $(document).ready(function () {
        if (!getCookie('sitecompassSessionId')) {
            setCookie('sitecompassSessionId', sitecompassRandomId.randomId, 1);
            setCookie('sitecompassSessionSet', true, 1);
        }
    });

    /**
     * Chatbox Open / Close
     */
    $(document).on('click', '#sitecompass-open-chat', function () {
        $('#sitecompass-chat-popup').show();

        var chatBody = $('#sitecompass-chat-body');
        chatBody.scrollTop(chatBody[0].scrollHeight);
    });

    $(document).on('click', '#sitecompass-close-chat', function () {
        $('#sitecompass-chat-popup').hide();
    });

    /**
     * Append Chat Message (XSS Safe)
     */
    function appendMessage(message, className) {
        var $message = $('<div>', {
            class: 'chat-message ' + className
        }).append(
            $('<span>').text(message)
        );

        var $chatBox = $('#sitecompass-chat-body');
        $chatBox.append($message);
        $chatBox.scrollTop($chatBox[0].scrollHeight);
    }

    /**
     * Send Message Events
     */
    $(document).on('click', '#sitecompass-send-msg', sendUserMessage);

    $(document).on('keypress', '#sitecompass-user-message', function (e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            sendUserMessage();
        }
    });

    /**
     * Get Fresh Nonce
     *
     * Purpose: Fetches a fresh nonce to handle page caching and session changes.
     */
    function getFreshNonce() {
        return $.ajax({
            url: sitecompassAjax.ajax_url,
            dataType: 'json',
            method: 'POST',
            data: {
                action: 'sitecompass_get_nonce'
            }
        });
    }

    /**
     * Send User Message
     */
    function sendUserMessage() {
        var message = $('#sitecompass-user-message').val().trim();
        if (!message) {
            return;
        }

        appendMessage(message, 'sitecompass-user-message');
        $('#sitecompass-user-message').val('');

        $('#chatFooter').prepend(
            '<div class="sitecompass-bubble">' +
                '<div class="sitecompass-typing">' +
                    '<span class="dot"></span>' +
                    '<span class="dot"></span>' +
                    '<span class="dot"></span>' +
                '</div>' +
            '</div>'
        );

        // Get fresh nonce first, then send message.
        getFreshNonce().done(function (nonceResponse) {
            var freshNonce = nonceResponse.data.nonce;

            $.ajax({
                url: sitecompassAjax.ajax_url,
                dataType: 'json',
                method: 'POST',
                data: {
                    action: 'sitecompass_send_message',
                    nonce: freshNonce,
                    sessionId: getCookie('sitecompassSessionId'),
                    userMessage: message,
                    userType: 'User'
                },
                success: function (response) {
                    $('#sitecompass-chat-footer .sitecompass-bubble').remove();

                    if (response.error) {
                        console.error(response.message);
                        return;
                    }

                    appendMessage(response.data.message, 'sitecompass-bot-message');
                },
                error: function (xhr, status, error) {
                    $('#sitecompass-chat-footer .sitecompass-bubble').remove();
                    console.error('SiteCompass AJAX Error:', error);
                }
            });
        }).fail(function () {
            $('#sitecompass-chat-footer .sitecompass-bubble').remove();
            console.error('SiteCompass: Failed to get fresh nonce');
        });
    }

})(jQuery);