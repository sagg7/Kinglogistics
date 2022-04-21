/*=========================================================================================
    File Name: fullcalendar.js
    Description: Fullcalendar
    --------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

document.addEventListener('DOMContentLoaded', function () {
    let patSelect = {
        allowClear: true,
        ajax: {
            url: '/patient/selection',
            language: 'es',
            data: (params) => {
                return {
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
            },
        }
    }, userSelect = {
        allowClear: true,
        ajax: {
            url: '/user/selection',
            data: (params) => {
                return {
                    role: 3,
                    search: params.term,
                    page: params.page || 1,
                    take: 15,
                };
            },
        }
    };
    $('#cal-event-title').select2(_.merge(patSelect, select2Lang))
        .on('select2:select', function (e) {
            $('#cal-event-cellphone').val(e.params.data.cellphone);
        });
    let userSelection = $('#cal-event-user'),
        clearModal = () => {
            startDate.val('').trigger('change');
            startSubmit.val('').trigger('change');
        },
        getUserData = () => {
            let id = userSelection.val(),
                date = startSubmit.val();
            if (id !== "" && id !== null)
                $.ajax({
                    url: '/user/getTimeOff',
                    type: 'GET',
                    data: {
                        user_id: id,
                        date: date,
                        offDays: !selectedUserData.offDays,
                    },
                    success: (res) => {
                        selectedUserData = {date: date, hours: res.hours};
                        if (res.offDays)
                            selectedUserData.offDays = res.offDays;
                        checkUserSchedule();
                    }
                });
        };
    userSelection.select2(_.merge(userSelect, select2Lang))
        .on('select2:select', function (e) {
            selectedUserData = {};
            clearModal();
            getUserData();
        })
        .on('select2:unselect', function (e) {
            selectedUserData = {};
            checkUserSchedule();
        });
    $.ajax({
        url: '/appointment/statusSelection',
        type: 'GET',
        success: (res) => {
            if (res.success) {
                $('#cal-status').select2({
                    data: res.data,
                }).on('select2:select', function (e) {
                    let item = e.params.data;
                    evtColor = colors[item.id];
                });
            }
        }
    })

    // color object for different event types
    let colors = {
        others: "#7367f0",
        attended: "#28c76f",
        not_attended: "#ea5455",
        on_hold: "#ff9f43",
    };

    let categoryBullets = $(".cal-category-bullets"),
        categoryFilters = $('.cal-category-filters'),
        userSelected = null,
        evtColor = colors.on_hold;
        //eventColor = "";

    // calendar init
    let calendarEl = document.getElementById('fc-default');

    let startDate = $("#cal-start-date"),
        pickStart = initPickadateEs(startDate).pickadate('picker'),
        startSubmit = $('[name=start_submit]'),
        startHour = $("#cal-start-hour"),
        pickHour = initPickatime(startHour, {interval: 10}).pickatime('picker'),
        hourSubmit = $('[name=hour_submit]'),
        clickedEvent = null,
        selectedUserData = {};

    let checkUserSchedule = () => {
        let addDisabledListener = (el) => {
            el.click((e) => {
                e.preventDefault();
                throwErrorMsg("La hora ya se encuentra asignada, favor de seleccionar otra hora");
            });
        }, removeDisabledListener = (el) => {
            el.off("click").click(() => {
                addDisabledListener(el);
            });
        };
        if (selectedUserData.date !== startSubmit.val()) {
            $.each(startHour.next().find('.picker__list-item'), (index, value) => {
                let el = $(value);
                el.removeClass('text-danger').prop('disabled', false);
                removeDisabledListener(el);
            });
            getUserData();
        } else
            $.each(startHour.next().find('.picker__list-item'), (index, value) => {
                let el = $(value),
                    time = moment(el.text(), 'HH:mm A').format("HH:mm");
                if (selectedUserData.hours.includes(time)) {
                    el.addClass('text-danger').prop('disabled', true);
                    addDisabledListener(el);
                } else {
                    el.removeClass('text-danger').prop('disabled', false);
                    removeDisabledListener(el);
                }
            });
        pickStart.set('enable', true);
        let dates = [];
        if (selectedUserData.offDays)
            selectedUserData.offDays.forEach((item) => {
                dates.push(moment(item).toDate());
            });
        if (dates.length > 0)
            pickStart.set('disable', dates);
    };

    pickHour.on({
        render: () => {
            checkUserSchedule();
        },
    });

    startDate.change(() => {
        checkUserSchedule();
        startHour.val('').trigger('change');
    });

    let createNewEvent = (info) => {
            return {
                id: info.event.id,
                patient: info.event.extendedProps.patient,
                cellphone: info.event.extendedProps.cellphone,
                title: info.event.title,
                start: info.event.start,
                end: info.event.end,
                description: info.event.extendedProps.description,
                color: info.event.color,
                //dataEventColor: info.event.extendedProps.dataEventColor,
                user: info.event.extendedProps.user,
                status: info.event.extendedProps.status,
                allDay: info.event.allDay,
            };
        },
        updateEvent = (event, btn = null) => {
            $.ajax({
                url: `/appointment/update/${event.id}`,
                type: 'POST',
                data: {
                    start: moment(event.start).format('YYYY-MM-DD HH:mm:ss'),
                    end: event.end ? moment(event.end).format('YYYY-MM-DD HH:mm:ss') : null,
                    description: event.description,
                    patient_id: event.patient,
                    cellphone: event.cellphone,
                    user_id: event.user.id,
                    //type: event.dataEventColor,
                    status: event.status,
                },
                complete: () => {
                    if (btn) {
                        btn.find('.spinner-border').addClass('d-none');
                        btn.find('.btn-text').removeClass('d-none').prop('disabled', false);
                    }
                },
                success: (res) => {
                    if (!res.success)
                        throwErrorMsg();
                    else {
                        if (clickedEvent) {
                            clickedEvent.remove();
                            calendar.addEvent(event);
                        }
                        $(".modal-calendar").modal("hide");
                    }

                },,
            });
        }

    let calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: ["dayGrid", "timeGrid", "interaction"],
        locale: 'es',
        customButtons: {
            addNew: {
                text: ' Agregar',
                click: function () {
                    let calDate = new Date,
                        todaysDate = calDate.toISOString().slice(0, 10);
                    $(".modal-calendar").modal("show");
                    $(".modal-calendar .cal-submit-event").addClass("d-none");
                    $(".modal-calendar .remove-event").addClass("d-none");
                    $(".modal-calendar .cal-add-event").removeClass("d-none")
                    $(".modal-calendar .cancel-event").removeClass("d-none")
                    $(".modal-calendar .add-category .chip").remove();
                    pickStart.set('select', todaysDate);
                    //startDate.prop("disabled", false);
                }
            }
        },
        header: {
            left: "addNew",
            center: "dayGridMonth,timeGridWeek,timeGridDay",
            right: "prev,title,next"
        },
        displayEventTime: false,
        navLinks: true,
        editable: true,
        allDay: true,
        navLinkDayClick: function (date) {
            $(".modal-calendar").modal("show");
            pickStart.set('select', moment(date).format('YYYY-MM-DD'), { format: 'yyyy-mm-dd' });
            //startDate.attr("disabled", true);
        },
        dateClick: function (info) {
            $(".modal-calendar").modal("show");
            pickStart.set('select', info.dateStr, { format: 'yyyy-mm-dd' });
            pickHour.set('select', moment(info.date).format('HH:m'), { format: 'HH:i' });
            //startDate.attr("disabled", true);
        },
        // displays saved event values on click
        eventClick: function (info) {
            clickedEvent = info.event;
            $(".modal-calendar").modal("show");
            $(".modal-calendar #cal-event-title")
                .html(`<option value="${info.event.extendedProps.patient}">${info.event.title}</option>`)
                .val(info.event.extendedProps.patient)
                .trigger('change');
            $(".modal-calendar #cal-event-cellphone").val(info.event.extendedProps.cellphone);
            $(".modal-calendar #cal-event-user")
                .html(`<option value="${info.event.extendedProps.user.id}">${info.event.extendedProps.user.name}</option>`)
                .val(info.event.extendedProps.user.id)
                .trigger('change');
            $(".modal-calendar #cal-status").val(info.event.extendedProps.status).trigger('change');
            pickStart.set('select', moment(info.event.start).format('YYYY-MM-DD'), { format: 'yyyy-mm-dd' });
            pickHour.set('select', moment(info.event.start).format('HH:m'), { format: 'HH:i' });
            $(".modal-calendar #cal-description").val(info.event.extendedProps.description);
            $(".modal-calendar .cal-submit-event").removeClass("d-none");
            $(".modal-calendar .remove-event").removeClass("d-none");
            $(".modal-calendar .cal-add-event").addClass("d-none");
            $(".modal-calendar .cancel-event").addClass("d-none");
            $(".calendar-dropdown .dropdown-menu").find(".selected").removeClass("selected");
        },
        events: (info, successCallback, failureCallback) => {
            $.ajax({
                url: '/appointment/getAppointments',
                data: {
                    start: moment(info.start).format('YYYY-MM-DD'),
                    end: moment(info.end).format('YYYY-MM-DD'),
                    user: userSelected,
                },
                success: (res) => {
                    res.forEach((item, i) => {
                        res[i].allDay = !item.end && item.start.split(" ")[1] === "00:00:00";
                        res[i].color = colors[item.status];
                        res[i].user = {
                            id: item.user_id,
                            name: item.user_name,
                        };
                        res[i].status = item.status;
                    });
                    successCallback(res);
                },
                error: (res) => {
                    failureCallback(res);
                }
            })
        },
        eventDrop: (info) => {
            updateEvent(createNewEvent(info));
        },
        eventResize: (info) => {
            updateEvent(createNewEvent(info));
        },
        views: {
            dayGrid: {
                eventLimit: 5,
            },
            timeGrid: {
                eventLimit: 5,
            },
            interaction: {
                eventLimit: 5,
            }
        }
    });

    // render calendar
    calendar.render();

    // appends bullets to left class of header
    $("#basic-examples .fc-right").append(categoryBullets.html()).append(categoryFilters.html());
    categoryBullets.html("");
    categoryFilters.html("");

    let userFilter = $('#filter-user');
    userFilter.select2(_.merge(
        select2Lang,
        _.merge(userSelect, {
            placeholder: "Filtrar doctor",
        })
    ))
        .on('select2:select', function (e) {
            let item = e.params.data;
            userSelected = item.id;
            calendar.refetchEvents();
        })
        .on('select2:unselect', function (e) {
            userSelected = null;
            calendar.refetchEvents();
        });
    userFilter.next().addClass('ml-0');

    // Close modal on submit button
    $(".modal-calendar .cal-submit-event").on("click", function () {
        let btn = $(this),
            titleInp = $("#cal-event-title option:selected"),
            userInp = $("#cal-event-user option:selected"),
            statusInp = $("#cal-status option:selected"),
            patient = titleInp.val(),
            cellphone = $("#cal-event-cellphone").val(),
            title = titleInp.text(),
            start = startSubmit.val(),
            startTime = hourSubmit.val(),
            date = new Date(start + ' ' + startTime),
            description = $("#cal-description").val();

        btn.find('.spinner-border').removeClass('d-none');
        btn.find('.btn-text').addClass('d-none').prop('disabled', true);

        evtColor = colors[$('#cal-status').val()];
        let event = {
            id: clickedEvent.id,
            patient,
            cellphone,
            title,
            start: date,
            description,
            color: evtColor,
            //dataEventColor: eventColor,
            allDay: startTime === "00:00:00",
            user: {
                id: userInp.val(),
                name: userInp.text(),
            },
            status: statusInp.val(),
        };

        updateEvent(event, btn);
    });

    // Remove Event
    $(".remove-event").on("click", function () {
        Swal.fire({
            title: 'Confirmación para eliminar elemento',
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#7367F0',
            cancelButtonColor: '#EA5455',
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.value)
                $.ajax({
                    url: `/appointment/delete/${clickedEvent.id}`,
                    type: 'POST',
                    success: (res) => {
                        if (!res.success)
                            throwErrorMsg();
                        else {
                            clickedEvent.remove();
                            clickedEvent = null;
                            $(".modal-calendar").modal("hide");
                        }
                    },
                    error: () => {
                        throwErrorMsg();
                    },
                });
        });
    });


    // reset input element's value for new event
    if ($("td:not(.fc-event-container)").length > 0) {
        $(".modal-calendar").on('hidden.bs.modal', function (e) {
            selectedUserData = {};
            $('.modal-calendar .form-control').val('').trigger('change');
            $('.modal-calendar #cal-status').prop('selectedIndex', 0).trigger('change');
            clickedEvent = null;
        })
    }

    // open add event modal on click of day
    $(".fc-day, .fc-time").click((e) => {
        $(".modal-calendar").modal("show");
        $(".calendar-dropdown .dropdown-menu").find(".selected").removeClass("selected");
        $(".modal-calendar .cal-submit-event").addClass("d-none");
        $(".modal-calendar .remove-event").addClass("d-none");
        $(".modal-calendar .cal-add-event").removeClass("d-none");
        $(".modal-calendar .cancel-event").removeClass("d-none");
        $(".modal-calendar .add-category .chip").remove();
        evtColor = colors.on_hold;
        //eventColor = "others";
    });

    // calendar add event
    $(".cal-add-event").on("click", function () {
        let btn = $(this),
            patient = $("#cal-event-title option:selected"),
            cellphone = $("#cal-event-cellphone").val(),
            userInp = $("#cal-event-user option:selected"),
            statusInp = $("#cal-status option:selected"),
            eventTitle = patient.text(),
            patientId = patient.val(),
            start = startSubmit.val(),
            startTime = hourSubmit.val(),
            date = new Date(start + ' ' + startTime),
            eventDescription = $("#cal-description").val();

        btn.find('.spinner-border').removeClass('d-none');
        btn.find('.btn-text').addClass('d-none').prop('disabled', true);

        $.ajax({
            url: '/appointment/store',
            type: 'POST',
            data: {
                start: moment(date).format('YYYY-MM-DD HH:mm:ss'),
                //end: start,
                description: eventDescription,
                patient_id: patientId,
                cellphone,
                user_id: userInp.val(),
                //type: eventColor,
                status: statusInp.val(),
            },
            complete: () => {
                btn.find('.spinner-border').addClass('d-none');
                btn.find('.btn-text').removeClass('d-none').prop('disabled', false);
            },
            success: (res) => {
                if (!res.success)
                    throwErrorMsg();
                else {
                    $(".modal-calendar").modal("hide");

                    let event = {
                        id: res.data.id,
                        patient: patientId,
                        cellphone,
                        title: eventTitle,
                        start: date,
                        description: eventDescription,
                        color: evtColor,
                        //dataEventColor: eventColor ? eventColor : 'others',
                        allDay: startTime === "00:00:00",
                        user: {
                            id: userInp.val(),
                            name: userInp.text(),
                        },
                        status: statusInp.val(),
                    };
                    calendar.addEvent(event);
                }

            },
            error: (res) => {
                let errors = `<ul class="text-left">`;
                Object.values(res.responseJSON.errors).forEach((error) => {
                    errors += `<li>${error}</li>`;
                });
                errors += `</ul>`;
                throwErrorMsg(errors, {timer: false});
            },
        });
    });
});
