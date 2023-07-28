<?php

/**
 * add the interface as a shortcode
 * available to page, shortcode, widgets
 * or anywhere needed.
 */
function chatx_block_render() {
    ob_start();
    ?>
    <div class="conversations">
        <!-- Chat messages will be displayed here -->
    </div>
    <input type="text" class="user-input" placeholder="Type your prompt and press Enter" />
    <div class="chat-loader"></div>
    <?php
    return ob_get_clean();
}