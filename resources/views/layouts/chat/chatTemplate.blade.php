<link href="{{ asset("css/chat/bottomChat.css") }}" rel="stylesheet">

<div id="__bottom_chat">
    <button class="btn btn-round" id="__chat_button">
        <i class="fas fa-comment-dots"></i>
    </button>
    <div id="__chat_box">
        <div id="__contacts_list">
            <div id="__chat_topmenu">
                <button class="btn" id="minimizeChat"><i class="fas fa-window-minimize"></i></button>
            </div>

            <div id="__contacts"></div>

            <div id="__search_box">
                <input type="text" id="__search_contact" placeholder="Search contacts..." />
            </div>

        </div>

        <div id="__chat_view" class="p1">
            <div id="__chat_profile">

                <div id="__close_chat">
                    <div class="cy"></div>
                    <div class="cx"></div>
                </div>

                <p id="__contact_name"></p>
                <!--<span>miro@badev@gmail.com</span>-->
            </div>
            <div id="__chat_messages">
                <div id="__chats">
                    <label>Thursday 02</label>

                    <div class="message left">
                        <!--<img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/245657/1_copy.jpg" />-->
                        <div class="bubble">
                            Really cool stuff!
                            <span>3 min</span>
                        </div>
                    </div>

                    <div class="message right">
                        <!--<img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/245657/2_copy.jpg" />-->
                        <div class="bubble">
                            Can you share a link for the tutorial?
                            <span>1 min</span>
                        </div>
                    </div>

                    <div class="message left">
                        <!--<img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/245657/1_copy.jpg" />-->
                        <div class="bubble">
                            Yeah, hold on
                            <span>Now</span>
                        </div>
                    </div>
                </div>
            </div>

            <form action="javascript:void(0);" id="__chat_send_message">
                <label type="button" for="__chat_append" class="btn primary d-flex align-items-center">
                    <i class="fas fa-paperclip mx-auto"></i>
                    <input type="file" accept="image/jpeg, image/png" class="d-none" id="__chat_append">
                </label>
                <input type="text" name="__chat_message" id="__chat_message" placeholder="Send message...">
                <button type="submit" id="__chat_input" class="btn primary"><i class="fas fa-paper-plane"></i></button>
            </form>

            <div class="__preview_image" id="__image_to_send" tabindex="-1">
                <div class="main">
                    <div class="__preview_header">
                        <span class="cursor-pointer close-btn">×</span>
                        <hr class="mb-0">
                    </div>
                    <div class="d-flex align-items-center flex-wrap">
                        <div class="preview-body col">
                            <div class="content-body text-center d-flex align-items-center"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
