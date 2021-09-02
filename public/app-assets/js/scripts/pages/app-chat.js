(function ($) {
    "use strict";
    const usersList = $('#users-list'),
        chatList = $('#chats-list'),
        contactsList = $('#contacts-list');

    if (contacts) {
        contacts.forEach(item => {
            const time = item.latest_message ? moment(item.latest_message.created_at).format('h:mm A') : '';
            const element = `<li>` +
                `<div class="pr-1">` +
                //`<span class="avatar m-0 avatar-md"><img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-2.jpg" height="42" width="42" alt="Generic placeholder image">` +
                `<i></i>` +
                `</span>` +
                `</div>` +
                `<div class="user-chat-info" data-userid="${item.id}">` +
                `<div class="contact-info">` +
                `<h5 class="font-weight-bold mb-0">${item.name}</h5>` +
                `<p class="truncate">${item.latest_message ? item.latest_message.content ? item.latest_message.content : '<i class="fas fa-camera"></i> Photo' : ''}</p>` +
                `</div>` +
                `<div class="contact-meta">` +
                `<span class="float-right mb-25">${time}</span>` +
                (item.unread_count ? `<span class="badge badge-primary badge-pill float-right">${item.unread_count}</span>` : '') +
                `</div>` +
                `</div>` +
                `<i>`;
            if (item.latest_message) {
                chatList.append(element);
            } else {
                contactsList.append(element);
            }
        });
    }

    const scrollBars = {};
    // if it is not touch device
    if (!$.app.menu.is_touch_device()) {
        // Chat user list
        if ($('.chat-application .chat-user-list').length > 0) {
            var chat_user_list = new PerfectScrollbar(".chat-user-list");
        }

        // Chat user profile
        if ($('.chat-application .profile-sidebar-area .scroll-area').length > 0) {
            var chat_user_list = new PerfectScrollbar(".profile-sidebar-area .scroll-area");
        }

        // Chat area
        if ($('.chat-application .user-chats').length > 0) {
            const chat_user = document.querySelector(".user-chats");
            scrollBars.chat = {
                container: chat_user,
                scroll: new PerfectScrollbar(chat_user, {
                    wheelPropagation: false
                }),
            };
            chat_user.addEventListener('ps-scroll-up', e => {
                if (chat_user.scrollTop <= 25)
                    getChatHistory(true);
            });
        }

        // User profile right area
        if ($('.chat-application .user-profile-sidebar-area').length > 0) {
            var user_profile = new PerfectScrollbar(".user-profile-sidebar-area");
        }
    }

    // if it is a touch device
    else {
        $(".chat-user-list").css("overflow", "scroll");
        $(".profile-sidebar-area .scroll-area").css("overflow", "scroll");
        $(".user-chats").css("overflow", "scroll");
        $(".user-profile-sidebar-area").css("overflow", "scroll");
    }


    // Chat Profile sidebar toggle
    $('.chat-application .sidebar-profile-toggle').on('click', function () {
        $('.chat-profile-sidebar').addClass('show');
        $('.chat-overlay').addClass('show');
    });

    // User Profile sidebar toggle
    $('.chat-application .user-profile-toggle').on('click', function () {
        $('.user-profile-sidebar').addClass('show');
        $('.chat-overlay').addClass('show');
    });

    // Update status by clickin on Radio
    $('.chat-application .user-status input:radio[name=userStatus]').on('change', function () {
        var $className = "avatar-status-" + this.value;
        $(".header-profile-sidebar .avatar span").removeClass();
        $(".sidebar-profile-toggle .avatar span").removeClass();
        $(".header-profile-sidebar .avatar span").addClass($className + " avatar-status-lg");
        $(".sidebar-profile-toggle .avatar span").addClass($className);
    });

    // On Profile close click
    $(".chat-application .close-icon").on('click', function () {
        $('.chat-profile-sidebar').removeClass('show');
        $('.user-profile-sidebar').removeClass('show');
        if (!$(".sidebar-content").hasClass("show")) {
            $('.chat-overlay').removeClass('show');
        }
    });

    // On sidebar close click
    $(".chat-application .sidebar-close-icon").on('click', function () {
        $('.sidebar-content').removeClass('show');
        $('.chat-overlay').removeClass('show');
    });

    // On overlay click
    $(".chat-application .chat-overlay").on('click', function () {
        $('.app-content .sidebar-content').removeClass('show');
        $('.chat-application .chat-overlay').removeClass('show');
        $('.chat-profile-sidebar').removeClass('show');
        $('.user-profile-sidebar').removeClass('show');
    });

    let activeContact = {};
    const userChat = $('.user-chats .chats');
    const getActiveContact = () => {
        const active = usersList.find('li.active');
        const driver_id = active.find('.user-chat-info').data('userid');
        activeContact = contacts.find(obj => Number(obj.id) === Number(driver_id));
    };
    const clearMessages = () => {
        userChat.html('');
    };
    const prependMessages = (messages) => {

    };
    let currentDate = null;
    let previousDate = null;
    const appendMessages = (messages, prepend = false) => {
        const today = moment().startOf('day');
        const yesterday = moment().subtract(1, 'days');
        const generateDivider = (date) => {
            let text = '';
            // if is today
            if (date.isSame(today, 'd'))
                text = "Today";
            else if (date.isSame(yesterday, 'd'))
                text = "Yesterday";
            else
                text = date.format('M/D/YYYY');
            return `<div class="divider" data-date="${date.format('YYYY/MM/DD')}"><div class="divider-text">${text}</div></div>`;
        }
        const firstDivider = $(".chats .divider:first-child");
        const chatsContainer = $(scrollBars.chat.container).find('.chats');
        const original_height = chatsContainer.height();
        const original_scrollTop = chatsContainer.scrollTop();
        messages.forEach((item, count) => {
            const date = moment(item.created_at);
            const time = moment(item.created_at).format('h:mm A');
            let html = '';
            const timeSpan = `<span class="block text-right line-height-1"><sub>${time}</sub></span>`;
            if (item.content) {
                html = `<p>${item.content}${timeSpan}</p>`;
            } else {
                html = `<div class="chat-image"><a href="#imagePreview" data-toggle="modal" data-target="#imagePreview"><img class="img-fluid" src="${item.image_url}" alt="image"></a>${timeSpan}</div>`;
            }
            html = `<div class="chat-content" data-message="${item.id}">${html}</div>`;

            const lastChat = prepend ? $(".chat:first-child") : $(".chat:last-child");
            if (prepend) {
                if (firstDivider.length > 0 && moment(firstDivider.data('date'), 'YYYY/MM/DD').isSame(date, 'date'))
                    firstDivider.remove();
                if (previousDate && !previousDate.isSame(date, 'date')) {
                    const divider = generateDivider(previousDate);
                    userChat.prepend(divider);
                }
                previousDate = date;
            } else if (!currentDate || !currentDate.isSame(date, 'date')) {
                const divider = generateDivider(date);
                userChat.append(divider);
                currentDate = date;
            }
            if (lastChat.length === 0 || (lastChat.hasClass("chat-left") && !item.is_driver_sender) || !lastChat.hasClass("chat-left") && item.is_driver_sender) {
                html = `<div class="chat ${item.is_driver_sender ? 'chat-left' : ''}">` +
                    `<div class="chat-body">` + html + `</div>` +
                    `</div>`;
                if (prepend)
                    userChat.prepend(html);
                else
                    userChat.append(html);
            } else {
                if (prepend)
                    lastChat.find(".chat-body").prepend(html);
                else
                    lastChat.find(".chat-body").append(html);
            }
            if (prepend && messages.length === count + 1) {
                const divider = generateDivider(date);
                userChat.prepend(divider);
                scrollBars.chat.container.scrollTop = original_scrollTop + chatsContainer.height() - original_height;
                userChat.find('.more-data-spinner').remove();
            }
        });
        scrollBars.chat.scroll.update();
        if (!prepend)
            scrollBars.chat.container.scrollTop = chatsContainer.height();
    };
    let lookup = false;
    const getChatHistory = (prepend = false) => {
        const more = activeContact.messages ? activeContact.messages.more : true;
        const page = activeContact.messages ? activeContact.messages.page : 1;
        const history = activeContact.messages ? activeContact.messages.history : [];
        const chatHeader = $('.chat_header'),
            headerName = chatHeader.find('h6');
        /*const getBase64FromUrl = async (url) => {
            const result = new Promise((resolve, reject) => {
                let canvas = document.createElement('CANVAS');
                const img = document.createElement('img');
                img.src = url;
                img.onload = function () {
                    canvas.height = img.height;
                    canvas.width = img.width;
                    const ctx = canvas.getContext("2d");
                    ctx.drawImage(img, 0, 0);
                    $('body').append(img).append(canvas);
                    resolve(canvas.toDataURL('image/png'));
                    canvas = null;
                };

                img.onerror = function (error) {
                    reject(throwErrorMsg('Could not load image, please check that the file is accessible and an image!'));
                };
            });
            return await result;
        }*/
        headerName.text(activeContact.name);
        if ((history.length === 0 || prepend) && more) {
            if (!lookup) {
                lookup = true;
                $.ajax({
                    url: '/chat/getChatHistory',
                    data: {
                        driver_id: activeContact.id,
                        page,
                        take: 15,
                    },
                    success: (res) => {
                        const result = [...res.results].reverse();
                        /*result.forEach((item, i) => {
                            if (item.image_url)
                                getBase64FromUrl(item.image_url).then(data => { console.log(data); item.image = data});
                        });*/
                        activeContact.messages = {
                            history: prepend ? [...result, ...history] : [...history, ...result],
                            more: res.pagination.more,
                            page: page + 1,
                        };
                        appendMessages((prepend ? res.results : result), prepend);
                        if (res.pagination.more)
                            userChat.prepend('<div class="text-center more-data-spinner"><div class="spinner-border"></div></div>');
                    }
                }).done(() => {
                    lookup = false;
                });
            }
        } else if (!prepend) {
            clearMessages();
            appendMessages(activeContact.messages.history);
        }
    };
    // Add class active on click of Chat users list
    $(".chat-application .chat-user-list ul li").on('click', function () {
        if ($('.chat-user-list ul li').hasClass('active')) {
            $('.chat-user-list ul li').removeClass('active');
        }
        $(this).addClass("active");
        $(this).find(".badge").remove();
        if ($('.chat-user-list ul li').hasClass('active')) {
            $('.start-chat-area').addClass('d-none');
            $('.active-chat').removeClass('d-none');
        } else {
            $('.start-chat-area').removeClass('d-none');
            $('.active-chat').addClass('d-none');
        }
        const previousContact = activeContact.id;
        getActiveContact();
        if (activeContact.id !== previousContact) {
            clearMessages();
            getChatHistory();
        }
    });

    // autoscroll to bottom of Chat area
    var chatContainer = $(".user-chats");
    $(".chat-users-list-wrapper li").on("click", function () {
        chatContainer.animate({scrollTop: chatContainer[0].scrollHeight}, 400)
    });

    // Favorite star click
    $(".chat-application .favorite i").on("click", function (e) {
        $(this).parent('.favorite').toggleClass("warning");
        e.stopPropagation();
    });

    // Main menu toggle should hide app menu
    $('.chat-application .menu-toggle').on('click', function (e) {
        $('.app-content .sidebar-left').removeClass('show');
        $('.chat-application .chat-overlay').removeClass('show');
    });

    // Chat sidebar toggle
    if ($(window).width() < 992) {
        $('.chat-application .sidebar-toggle').on('click', function () {
            $('.app-content .sidebar-content').addClass('show');
            $('.chat-application .chat-overlay').addClass('show');
        });
    }

    // For chat sidebar on small screen
    if ($(window).width() > 992) {
        if ($('.chat-application .chat-overlay').hasClass('show')) {
            $('.chat-application .chat-overlay').removeClass('show');
        }
    }

    // Scroll Chat area
    //$(".user-chats").scrollTop($(".user-chats > .chats").height());

    // Filter
    $(".chat-application #chat-search").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        if (value != "") {
            $(".chat-user-list .chat-users-list-wrapper li").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        } else {
            // If filter box is empty
            $(".chat-user-list .chat-users-list-wrapper li").show();
        }
    });

    let sentMessage = {};
    const sendMessage = () => {
        $.ajax({
            url: '/chat/sendMessage',
            type: 'POST',
            data: sentMessage,
            success: (res) => {
                if (res.success) {
                    sentMessage = {};
                    activeContact.messages.history.push(res.message);
                }
            },
            error: () => {
                throwErrorMsg();
            }
        });
    }
    const addMessageToView = (html) => {
        html = `<div class="chat-content">${html}</div>`;
        const lastChat = $(".chat:last-child");
        if (lastChat.length === 0 || lastChat.hasClass("chat-left")) {
            html = `<div class="chat"><div class="chat-body">${html}</div></div>`;
            $(".chats").append(html);
        } else {
            lastChat.find(".chat-body").append(html);
        }
    };
    // Add message to chat
    $('.chat-app-input').submit(() => {
        const message = $(".message"),
            string = message.val();
        if (string !== "") {
            const html = `<p>${string}<span class="block text-right line-height-1"><sub>${moment().format('h:mm A')}</sub></span></p>`;
            addMessageToView(html);
            message.val("");
            $(".user-chats").scrollTop($(".user-chats > .chats").height());
            sentMessage = {
                driver_id: activeContact.id,
                string,
            };
            sendMessage();
        }
    });
    $('#appendImage').change((e) => {
        const files = $(e.currentTarget);
        if (files.val() !== '') {
            const data = files.prop('files')[0];
            const reader = new FileReader();
            reader.readAsDataURL(data);
            reader.onload = () => {
                const html = `<div class="chat-image"><a href="#imagePreview" data-toggle="modal" data-target="#imagePreview"><img class="img-fluid" src="${reader.result}" alt="image"></a><span class="block text-right line-height-1"><sub>${moment().format('h:mm A')}</sub></span></div>`;
                addMessageToView(html);
            }
            const formData = new FormData();
            formData.append('image', data);
            formData.append('driver_id', activeContact.id);
            $.ajax({
                url: '/chat/sendMessage',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: (res) => {
                    if (res.success)
                        activeContact.messages.history.push(res.message);
                },
                error: () => {
                    throwErrorMsg();
                }
            });
            files.val('');
        }
    });
    $('#imagePreview').on('show.bs.modal', (e) => {
        const modal = $(e.currentTarget),
            modalBody = modal.find('.modal-body'),
            modalSpinner = modalBody.find('.modal-spinner'),
            content = modal.find('.content-body'),
            anchor = $(e.relatedTarget),
            chat = anchor.closest('.chat-content'),
            id = chat.data('message'),
            img = anchor.find('img');

        if (id) {
            const message = activeContact.messages.history.find(obj => Number(obj.id) === Number(id));
            $.ajax({
                url: '/s3storage/getTemporaryUrl',
                type: 'GET',
                data: {
                    url: message.image,
                },
                success: (res) => {
                    content.html(`<img src="${res}" alt="image" class="img-fluid">`);
                    modalSpinner.addClass('d-none');
                    content.removeClass('d-none');
                },
                error: () => {
                    throwErrorMsg();
                }
            });
        } else {
            content.html(`<img src="${img.attr('src')}" alt="image" class="img-fluid">`);
            modalSpinner.addClass('d-none');
            content.removeClass('d-none');
        }
    }).on('hidden.bs.modal', (e) => {
        const modal = $(e.currentTarget),
            modalBody = modal.find('.modal-body'),
            modalSpinner = modalBody.find('.modal-spinner'),
            content = modal.find('.content-body');

        content.html(``);
        modalSpinner.removeClass('d-none');
        content.addClass('d-none');
    });
    window.Echo.private('chat')
        .listen('NewChatMessage', e => {
            const message = e.message;
            message.newly_received = true;
            if (activeContact.id === message.driver_id) {
                appendMessages([message]);
            } else {
                const contact = contacts.find(obj => Number(obj.id) === Number(message.driver_id));
                if (contact.messages)
                    contact.messages.history.push(message);
                const menu = $(`.user-chat-info[data-userid="${message.driver_id}"]`),
                    preview = menu.find('.truncate'),
                    meta = menu.find('.contact-meta'),
                    badge = meta.find('.badge');
                preview.text(message.content.substring(0, 100));
                if (badge.length > 0) {
                    badge.text(Number(badge.text()) + 1);
                } else {
                    meta.append(`<span class="badge badge-primary badge-pill float-right">1</span>`);
                }
            }
        });
})(jQuery);

$(window).on("resize", function () {
    // remove show classes from sidebar and overlay if size is > 992
    if ($(window).width() > 992) {
        if ($('.chat-application .chat-overlay').hasClass('show')) {
            $('.app-content .sidebar-left').removeClass('show');
            $('.chat-application .chat-overlay').removeClass('show');
        }
    }

    // Chat sidebar toggle
    if ($(window).width() < 992) {
        if ($('.chat-application .chat-profile-sidebar').hasClass('show')) {
            $('.chat-profile-sidebar').removeClass('show');
        }
        $('.chat-application .sidebar-toggle').on('click', function () {
            $('.app-content .sidebar-content').addClass('show');
            $('.chat-application .chat-overlay').addClass('show');
        });
    }
});

