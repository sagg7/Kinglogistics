(() => {
    const preloadbg = document.createElement("img");
    preloadbg.src = "https://s3-us-west-2.amazonaws.com/s.cdpn.io/245657/timeline1.png";

    let openChat = false;
    const chatBtn = $('#__chat_button');
    const chatBox = $('#__chat_box');
    const min = $('#minimizeChat');
    const contactList = $('#__contacts');
    const chatView = $('#__chat_view');
    const chatMessages = $('#__chat_messages');
    const chats = $('#__chats');
    const message = $("#__chat_message");
    const messageCounter = $('#__chat_button_counter');
    const scrollBars = {
        chat: {
            container: chatMessages[0],
            scroll: new PerfectScrollbar(chatMessages[0], {
                wheelPropagation: false
            }),
        },
        contacts: {
            container: contactList,
            scroll: new PerfectScrollbar(contactList[0], {
                wheelPropagation: false,
            }),
        }
    };
    let activeContact = {};
    let contacts = [];
    let currentDate = null;
    let previousDate = null;
    const clearMessages = () => {
        // Clear chat messages
        chats.html('');
    }
    const getMessageTime = (date) => {
        const today = moment().startOf('day');
        const yesterday = moment().subtract(1, 'days');
        let text = '';
        // if is today
        if (date.isSame(today, 'd'))
            text = "Today";
        else if (date.isSame(yesterday, 'd'))
            text = "Yesterday";
        else
            text = date.format('M/D/YYYY');
        return text;
    }
    const appendMessages = (messages, prepend = false) => {
        const generateDivider = (date) => {
            const text = getMessageTime(date);
            return `<div class="divider" data-date="${date.format('YYYY/MM/DD')}"><div class="divider-text">${text}</div></div>`;
        }
        const firstDivider = $(".chats .divider:first-child");
        const original_height = chats.height();
        const original_scrollTop = chatMessages.scrollTop();
        messages.forEach((item, count) => {
            const date = moment(item.created_at);
            const time = moment(item.created_at).format('h:mm A');
            let html = '';
            if (item.content) {
                html = `${item.content}`;
            }
            if (item.image) {
                html = `<div class="chat-image"><a href="#imagePreview" data-toggle="modal" data-target="#imagePreview"><img class="img-fluid" src="${item.image_url}" alt="image"></a>${html}</div>`;
            }

            if (prepend) {
                if (firstDivider.length > 0 && moment(firstDivider.data('date'), 'YYYY/MM/DD').isSame(date, 'date'))
                    firstDivider.remove();
                if (previousDate && !previousDate.isSame(date, 'date')) {
                    const divider = generateDivider(previousDate);
                    chats.prepend(divider);
                }
                previousDate = date;
            } else if (!currentDate || !currentDate.isSame(date, 'date')) {
                const divider = generateDivider(date);
                chats.append(divider);
                currentDate = date;
            }
            const lastChat = prepend ? $(".chat:first-child") : $(".chat:last-child");
            if (lastChat.length === 0 || (lastChat.hasClass("chat-left") && !item.is_driver_sender) || !lastChat.hasClass("chat-left") && item.is_driver_sender) {
                html = `<div class="message ${item.is_driver_sender ? 'left' : 'right'}">` +
                    `<div class="bubble">` + html + `<span>${time}</span></div>` +
                    `</div>`;
                if (prepend)
                    chats.prepend(html);
                else
                    chats.append(html);
            } else {
                if (prepend)
                    lastChat.find(".chat-body").prepend(html);
                else
                    lastChat.find(".chat-body").append(html);
            }
            if (prepend && messages.length === count + 1) {
                const divider = generateDivider(date);
                chats.prepend(divider);
                scrollBars.chat.container.scrollTop = original_scrollTop + chats.height() - original_height;
                chats.find('.more-data-spinner').remove();
            }
        });
        setTimeout(() => {
            scrollBars.chat.scroll.update();
            if (!prepend)
                chatMessages[0].scrollTo(0, chatMessages[0].scrollHeight);
        }, 350);
    };
    let lookup = false;
    const getChatHistory = (prepend) => {
        const more = activeContact.messages ? activeContact.messages.more : true;
        const page = activeContact.messages ? activeContact.messages.page : 1;
        const history = activeContact.messages ? activeContact.messages.history : [];

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
                        activeContact.messages = {
                            history: prepend ? [...result, ...history] : [...history, ...result],
                            more: res.pagination.more,
                            page: page + 1,
                        };
                        appendMessages((prepend ? res.results : result), prepend);
                        if (res.pagination.more)
                            chats.prepend('<div class="text-center more-data-spinner"><div class="spinner-border"></div></div>');
                    }
                }).done(() => {
                    lookup = false;
                });
            }
        } else if (!prepend) {
            clearMessages();
            appendMessages(activeContact.messages.history);
        }
    }
    const contactHandler = () => {
        const contact = $(".__contact");
        contact.each(function(){
            const current = $(this);
            current.click(function(){
                // Remove count badge
                current.find(".__message_count").remove();
                const contact_id = current.data('userid');
                const previousContact = activeContact.id;
                activeContact = contacts.find(obj => Number(obj.id) === Number(contact_id));
                if (activeContact.id !== previousContact) {
                    clearMessages();
                    getChatHistory();
                }
                const childOffset = current.offset();
                const parentOffset = current.parent().parent().offset();
                const childTop = childOffset.top - parentOffset.top;
                //const clone = current.find('img').eq(0).clone();
                const top = childTop+12+"px";

                //$(clone).css({'top': top}).addClass("floatingImg").appendTo("#__chat_box");

                setTimeout(() => {
                    $("#__chat_profile p").addClass("animate");
                    $("#__chat_profile").addClass("animate");
                }, 100);
                setTimeout(() => {
                    chatMessages.addClass("animate");
                    $('.cx, .cy').addClass('s1');
                    setTimeout(function(){$('.cx, .cy').addClass('s2');}, 100);
                    setTimeout(function(){$('.cx, .cy').addClass('s3');}, 200);
                }, 150);

                $('.floatingImg').animate({
                    'width': "68px",
                    'left':'108px',
                    'top':'20px'
                }, 200);

                const name = current.find("p strong").html();
                //const email = current.find("p span").html();
                $("#__chat_profile p").html(name);
                //$("#__chat_profile span").html(email);

                //$(".message").not(".right").find("img").attr("src", $(clone).attr("src"));
                $('#__contacts_list').fadeOut();
                chatView.fadeIn();


                $('#__close_chat').unbind("click").click(function(){
                    $("#__chat_messages, #__chat_profile, #__chat_profile p").removeClass("animate");
                    $('.cx, .cy').removeClass("s1 s2 s3");
                    /*$('.floatingImg').animate({
                        'width': "40px",
                        'top':top,
                        'left': '12px'
                    }, 200, function(){$('.floatingImg').remove()});*/

                    setTimeout(function(){
                        chatView.fadeOut();
                        $('#__contacts_list').fadeIn();
                    }, 50);
                });
                message.focus();
            });
        });
    }
    const fillContacts = () => {
        contacts.forEach(item => {
            const time = item.latest_message ? getMessageTime(moment(item.latest_message.created_at)) : '';
            const element =
                `<div class="__contact" data-userid="${item.id}">` +
                //`<!--<img src="" />-->` +
                `<div class="__message_content">` +
                `<p>` +
                `<strong class="__contact_name">${item.name}</strong>` +
                `<span class="__message_preview truncate">${item.latest_message ? item.latest_message.content ? item.latest_message.content : '<i class="fas fa-camera"></i> Photo' : ''}</span>` +
                `</p>` +
                `<span class="__message_meta">` +
                `<span class="__message_time d-block">${time}</span>` +
                (item.unread_count ? `<span class="__message_count badge badge-primary badge-pill">${item.unread_count}</span>` : '') +
                `</span>` +
                `</div>` +
                //`<!--<div class="status inactive"></div>-->` +
                `</div>`;
            contactList.append(element);
        });
        contactHandler();
    }
    chatBtn.click(() => {
        openChat = true;
        chatBtn.addClass('hidden');
        chatBox.addClass('active');
        messageCounter.addClass('d-none');
        if (contacts.length === 0)
            $.ajax({
                url: '/chat/getContacts',
                type: 'GET',
                success: (data) => {
                    contacts = data;
                    fillContacts();
                }
            });
    });
    min.click(() => {
        openChat = false;
        chatBtn.removeClass('hidden');
        chatBox.removeClass('active');
    });
    chatMessages[0].addEventListener('ps-scroll-up', e => {
        if (chatMessages[0].scrollTop <= 25)
            getChatHistory(true);
    });
    const addMessageToView = (html) => {
        chats.append(`<div class="message right">` +
            `<div class="bubble">` + html + `</div>` +
            `</div>`);
    };
    let sentMessage = null;
    const sendMessage = (drivers = null, message = null) => {
        const formData = new FormData();
        if (!drivers && !message) {
            if (sentMessage)
                message = sentMessage;
            if (chosenFile.data) {
                formData.append('image', chosenFile.data);
                closePreview();
            }
            drivers = [activeContact.id];
        }
        formData.append('message', message);
        drivers.forEach(id => {
            formData.append('drivers[]', id);
        });
        $.ajax({
            url: '/chat/sendMessage',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: (res) => {
                if (res.success) {
                    sentMessage = null;
                    activeContact.messages.history.push(res.messages[0]);
                }
            },
            error: () => {
                throwErrorMsg();
            }
        }).always(() => {
        });
    }
    // Add message to chat
    $('#__chat_send_message').submit(() => {
        const string = message.val();
        let html = '';
        if (string !== "") {
            html = `${string}`;
            message.val("");
            sentMessage = string;
        }
        if (chosenFile.reader) {
            html = `<div class="chat-image"><a href="#imagePreview" data-toggle="modal" data-target="#imagePreview">` +
                `<img class="img-fluid" src="${chosenFile.reader.result}" alt="image"></a>${html}`;
        }
        if (html === '')
            return false;
        html += `<span>${moment().format('h:mm A')}</span>`;
        addMessageToView(html);
        chatMessages[0].scrollTo(0, chatMessages[0].scrollHeight);
        sendMessage();
    });
    /*
    * ADD IMAGE SEND FUNCTIONALITY
    */
    let chosenFile = {};
    const toSend = $('#__image_to_send'),
        closeBtn = toSend.find('.close-btn'),
        toSendContent = toSend.find('.content-body');
    const closePreview = () => {
        toSend.removeClass('open');
        chosenFile = {};
        setTimeout(() => {
            toSendContent.html('');
        }, 300);
    }
    $('#__chat_append').change((e) => {
        const files = $(e.currentTarget);
        if (files.val() !== '') {
            const data = files.prop('files')[0];
            const reader = new FileReader();
            reader.readAsDataURL(data);
            reader.onload = () => {
                toSendContent.html(`<img class="img-fluid mx-auto" src="${reader.result}" alt="image"></a>`);
                toSendContent.removeClass('d-none');
                toSend.addClass('open');
            }
            chosenFile = {reader, data};
            files.val('');
        }
    });
    closeBtn.click(() => {
        closePreview();
    });
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && toSend.hasClass('open')) {
            closePreview();
        }
    });

    // Filter
    $("#__search_contact").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        if (value !== "") {
            contactList.find(".__contact .__message_content p").filter(function () {
                $(this).closest('.__contact').toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        } else {
            // If filter box is empty
            contactList.find(".__contact").show();
        }
    });
    // Preview images on modal
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

    const organizeMessages = (contactId, message, type) => {
        const showPreview = () => {
            const menu = $(`.__contact[data-userid="${contactId}"]`),
                preview = menu.find('.__message_preview'),
                time = menu.find('.__message_time');
            preview.text(message.content.substring(0, 100));
            if (type === 'received') {
                const meta = menu.find('.__message_meta'),
                    badge = meta.find('.__message_count');
                if (badge.length > 0) {
                    badge.text(Number(badge.text()) + 1);
                } else {
                    meta.append(`<span class="__message_count badge badge-primary badge-pill">1</span>`);
                }
                time.text(moment(message.created_at).format('h:mm A'));
            }
        };
        if (activeContact.id === contactId) {
            appendMessages([message]);
            showPreview();
        } else {
            const contact = contacts.find(obj => Number(obj.id) === Number(contactId));
            if (contact.messages)
                contact.messages.history.push(message);
            showPreview();
        }
    };
    window.Echo.private(`chat.${userId}`)
        .listen('NewChatMessage', e => {
            if (!openChat)
                messageCounter.removeClass('d-none');
            if (contacts.length > 0) {
                const message = e.message;
                message.newly_received = true;
                organizeMessages(Number(message.driver_id), message, 'received');
            }
        });
})();
