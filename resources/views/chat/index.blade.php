@component("layouts.app", ['bottomChat' => false])
    @section("head")
        <link rel="stylesheet" href="{{ asset('app-assets/css/pages/app-chat.min.css') }}">
        <link href="{{ asset("css/chat/mainChat.css") }}" rel="stylesheet">
    @endsection

    @section("modals")
        <div class="modal fade" id="multiMessage" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                <div class="modal-content border-0 box-shadow-0" style="max-height: calc(100vh - 3.5rem);">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <form id="multi-chat-form">
                            <div class="form-group">
                                {!! Form::label('multi_contacts', ucfirst(__('contacts')), ['class' => 'col-form-label']) !!}
                                {!! Form::select('multi_contacts', [], null, ['class' => 'form-control', 'multiple']) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('multi_messages', ucfirst(__('message')), ['class' => 'col-form-label']) !!}
                                {!! Form::textarea('multi_messages', null, ['class' => 'form-control', 'rows' => 5, 'maxlength' => 512]) !!}
                            </div>
                            {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block submit-ajax', 'type' => 'submit']) !!}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section("scripts")
        <script>
            const contacts = @json($contacts);
            const userId = {{ auth()->user()->id }};
        </script>
        <script src="{{ asset('app-assets/js/scripts/pages/app-chat.min.js?1.0.1') }}"></script>
    @endsection

    <div class="chat-application">
        <div class="content-area-wrapper m-0">
            <div class="sidebar-left">
                <div class="sidebar">
                    <!-- User Chat profile area -->
                    <!--<div class="chat-profile-sidebar">
                        <header class="chat-profile-header">
                            <span class="close-icon">
                                <i class="feather icon-x"></i>
                            </span>
                            <div class="header-profile-sidebar">
                                <div class="avatar">
                                    <img src="" alt="user_avatar" height="70" width="70">
                                    <span class="avatar-status-online avatar-status-lg"></span>
                                </div>
                                <h4 class="chat-user-name">John Doe</h4>
                            </div>
                        </header>
                        <div class="profile-sidebar-area">
                            <div class="scroll-area">
                                <h6>About</h6>
                                <div class="about-user">
                                    <fieldset class="mb-0">
                                        <textarea data-length="120" class="form-control char-textarea" id="textarea-counter" rows="5" placeholder="About User">Dessert chocolate cake lemon drops jujubes. Biscuit cupcake ice cream bear claw brownie brownie marshmallow.</textarea>
                                    </fieldset>
                                    <small class="counter-value float-right"><span class="char-count">108</span> / 120 </small>
                                </div>
                                <h6 class="mt-3">Status</h6>
                                <ul class="list-unstyled user-status mb-0">
                                    <li class="pb-50">
                                        <fieldset>
                                            <div class="vs-radio-con vs-radio-success">
                                                <input type="radio" name="userStatus" value="online" checked="checked">
                                                <span class="vs-radio">
                                                    <span class="vs-radio--border"></span>
                                                    <span class="vs-radio--circle"></span>
                                                </span>
                                                <span class="">Active</span>
                                            </div>
                                        </fieldset>
                                    </li>
                                    <li class="pb-50">
                                        <fieldset>
                                            <div class="vs-radio-con vs-radio-danger">
                                                <input type="radio" name="userStatus" value="busy">
                                                <span class="vs-radio">
                                                    <span class="vs-radio--border"></span>
                                                    <span class="vs-radio--circle"></span>
                                                </span>
                                                <span class="">Do Not Disturb</span>
                                            </div>
                                        </fieldset>
                                    </li>
                                    <li class="pb-50">
                                        <fieldset>
                                            <div class="vs-radio-con vs-radio-warning">
                                                <input type="radio" name="userStatus" value="away">
                                                <span class="vs-radio">
                                                    <span class="vs-radio--border"></span>
                                                    <span class="vs-radio--circle"></span>
                                                </span>
                                                <span class="">Away</span>
                                            </div>
                                        </fieldset>
                                    </li>
                                    <li class="pb-50">
                                        <fieldset>
                                            <div class="vs-radio-con vs-radio-secondary">
                                                <input type="radio" name="userStatus" value="offline">
                                                <span class="vs-radio">
                                                    <span class="vs-radio--border"></span>
                                                    <span class="vs-radio--circle"></span>
                                                </span>
                                                <span class="">Offline</span>
                                            </div>
                                        </fieldset>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>-->
                    <!--/ User Chat profile area -->
                    <!-- Chat Sidebar area -->
                    <div class="sidebar-content card">
                        <span class="sidebar-close-icon">
                            <i class="feather icon-x"></i>
                        </span>
                        <div class="chat-fixed-search" style="z-index: 10;">
                            <div class="d-flex align-items-center">
                                <!--<div class="sidebar-profile-toggle position-relative d-inline-flex">
                                    <div class="avatar">
                                        <img src="" alt="user_avatar" height="40" width="40">
                                        <span class="avatar-status-online"></span>
                                    </div>
                                    <div class="bullet-success bullet-sm position-absolute"></div>
                                </div>-->
                                <fieldset class="form-group position-relative has-icon-left mx-1 my-0 w-100">
                                    <input type="text" class="form-control round" id="chat-search" placeholder="Search or start a new chat">
                                    <div class="form-control-position">
                                        <i class="feather icon-search"></i>
                                    </div>
                                </fieldset>
                                <div class="dropdown">
                                    <button class="btn p-0 waves-effect waves-light" type="button" id="report-menu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-bars" style="margin-left: .2rem;"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="report-menu" x-placement="bottom-end">
                                        <a class="dropdown-item" href="#multiMessage" data-toggle="modal" data-target="#multiMessage"> Send mass message</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="users-list" class="chat-user-list list-group position-relative">
                            <h3 class="primary p-1 mb-0">Chats</h3>
                            <ul class="chat-users-list-wrapper media-list" id="chats-list"></ul>
                            <h3 class="primary p-1 mb-0">Contacts</h3>
                            <ul class="chat-users-list-wrapper media-list" id="contacts-list"></ul>
                        </div>
                    </div>
                    <!--/ Chat Sidebar area -->

                </div>
            </div>
            <div class="content-right">
                <div class="content-wrapper">
                    <div class="content-header row">
                    </div>
                    <div class="content-body">
                        <div class="chat-overlay"></div>
                        <section class="chat-app-window">
                            <div class="start-chat-area">
                                <span class="mb-1 start-chat-icon feather icon-message-square"></span>
                                <h4 class="py-50 px-1 sidebar-toggle start-chat-text">Start Conversation</h4>
                            </div>
                            <div class="active-chat d-none position-relative">
                                <div class="chat_navbar">
                                    <header class="chat_header d-flex justify-content-between align-items-center p-1">
                                        <div class="vs-con-items d-flex align-items-center">
                                            <div class="sidebar-toggle d-block d-lg-none mr-1"><i
                                                    class="feather icon-menu font-large-1"></i></div>
                                            <!--<div class="avatar user-profile-toggle m-0 m-0 mr-1">
                                                <img src="" alt="" height="40" width="40" />
                                                <span class="avatar-status-busy"></span>
                                            </div>-->
                                            <h6 class="mb-0"></h6>
                                        </div>
                                        <!--<span class="favorite"><i class="feather icon-star font-medium-5"></i></span>-->
                                    </header>
                                </div>
                                <div class="user-chats">
                                    <div class="chats">
                                        <div class="text-center more-data-spinner">
                                            <div class="spinner-border"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="chat-app-form">
                                    <form class="chat-app-input d-flex" action="javascript:void(0);">
                                        <label for="appendImage" class="btn btn-primary">
                                            <i class="fas fa-paperclip"></i>
                                            <input type="file" accept="image/jpeg, image/png" id="appendImage" class="d-none">
                                        </label>
                                        <input type="text" class="form-control message mr-1 ml-50" id="iconLeft4-1" placeholder="Type your message" maxlength="2000">
                                        <button type="submit" class="btn btn-primary send"><i class="far fa-paper-plane d-lg-none"></i> <span class="d-none d-lg-block">Send</span></button>
                                    </form>
                                </div>
                                <div class="preview-image" id="imageToSend" tabindex="-1">
                                    <div class="main">
                                        <div class="preview-header">
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
                        </section>
                        <!-- User Chat profile right area -->
                        <!--<div class="user-profile-sidebar">
                            <header class="user-profile-header">
                                <span class="close-icon">
                                    <i class="feather icon-x"></i>
                                </span>
                                <div class="header-profile-sidebar">
                                    <div class="avatar">
                                        <img src="" alt="user_avatar" height="70" width="70">
                                        <span class="avatar-status-busy avatar-status-lg"></span>
                                    </div>
                                    <h4 class="chat-user-name">Felecia Rower</h4>
                                </div>
                            </header>
                            <div class="user-profile-sidebar-area p-2">
                                <h6>About</h6>
                                <p></p>
                            </div>
                        </div>-->
                        <!--/ User Chat profile right area -->
                    </div>
                </div>
            </div>
        </div>
    </div>

@endcomponent
