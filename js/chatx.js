jQuery(document).ready(function($) {
    // Define the function to send the user message to the ChatGPT API
    function sendChatMessage(message) {
        $.ajax({
            url: chatx_settings.api_endpoint,
            type: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + chatx_settings.api_key
            },
            data: JSON.stringify({
                messages: [
                    {
                        role: 'user',
                        content: message
                    }
                ],
                model: chatx_settings.model,
            }),
            success: function(response) {
                // Process the response from the ChatGPT API
                var chatResponse = response.choices[0].message.content;

                // Display the chat response on the page
                $('.conversations').append('<div class="groupChats chat-prompts"><div class="prompts"><div class="prompt-avt">ChatX</div><div class="prompt-message"><div class="message">'+ chatResponse +'</div></div></div></div>');
                $('.chat-loader').fadeOut(200);
            },
            error: function(error) {
                $('.conversations').append('<div class="groupChats chat-prompts"><div class="prompts"><div class="prompt-avt">ChatX</div><div class="prompt-message"><div class="message">'+ error.message ? error.message : error.statusText +'</div></div></div></div>');
                $('.chat-loader').fadeOut(200);
            }
        });
    }

    //  Append user prompts to the screen
    function displayPrompt(message) {
        //  Display in the conversation container
        $('.conversations').append(
            '<div class="groupChats user-prompts"><div class="prompts"><div class="prompt-avt">Guest</div><div class="prompt-message"><div class="message">'+ message +'</div></div></div></div>'
        )
        $('.chat-loader').fadeIn(200);
    }

    // Example event listener for user input
    $('.user-input').on('keyup', function(e) {
        if (e.key === 'Enter') {
            var message = $(this).val();
            displayPrompt(message);
            sendChatMessage(message);
            $(this).val('');
        }
    });
});
