<div id="sidebar" class="c-sidebar c-sidebar-fixed c-sidebar-lg-show">

    <div class="c-sidebar-brand d-md-down-none">
        <a class="c-sidebar-brand-full h4" href="#">
            {{ trans('panel.site_title') }}
        </a>
    </div>

    <ul class="c-sidebar-nav">
        <li class="c-sidebar-nav-item">
            <a href="{{ route("admin.home") }}" class="c-sidebar-nav-link">
                <i class="c-sidebar-nav-icon fas fa-fw fa-tachometer-alt">

                </i>
                {{ trans('global.dashboard') }}
            </a>
        </li>
        @can('user_management_access')
            <li class="c-sidebar-nav-dropdown {{ request()->is("admin/permissions*") ? "c-show" : "" }} {{ request()->is("admin/roles*") ? "c-show" : "" }} {{ request()->is("admin/users*") ? "c-show" : "" }} {{ request()->is("admin/supports*") ? "c-show" : "" }} {{ request()->is("admin/email-logs*") ? "c-show" : "" }} {{ request()->is("admin/spam-ips*") ? "c-show" : "" }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-users c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.userManagement.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('permission_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.permissions.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/permissions") || request()->is("admin/permissions/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-unlock-alt c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.permission.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('role_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.roles.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/roles") || request()->is("admin/roles/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-briefcase c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.role.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('user_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.users.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/users") || request()->is("admin/users/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-user c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.user.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('support_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.supports.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/supports") || request()->is("admin/supports/*") ? "c-active" : "" }}">
                                <i class="fa-fw far fa-question-circle c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.support.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('email_log_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.email-logs.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/email-logs") || request()->is("admin/email-logs/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-envelope-open c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.emailLog.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('spam_ip_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.spam-ips.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/spam-ips") || request()->is("admin/spam-ips/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-flushed c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.spamIp.title') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        @can('pet_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.pets.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/pets") || request()->is("admin/pets/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-paw c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.pet.title') }}
                </a>
            </li>
        @endcan
        @can('booking_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.bookings.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/bookings") || request()->is("admin/bookings/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-address-book c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.booking.title') }}
                </a>
            </li>
        @endcan
        @can('pet_review_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.pet-reviews.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/pet-reviews") || request()->is("admin/pet-reviews/*") ? "c-active" : "" }}">
                    <i class="fa-fw far fa-star c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.petReview.title') }}
                </a>
            </li>
        @endcan
        @can('chat_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.chats.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/chats") || request()->is("admin/chats/*") ? "c-active" : "" }}">
                    <i class="fa-fw far fa-comment-alt c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.chat.title') }}
                </a>
            </li>
        @endcan
        @can('user_alert_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.user-alerts.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/user-alerts") || request()->is("admin/user-alerts/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-bell c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.userAlert.title') }}
                </a>
            </li>
        @endcan
        @php($unread = \App\Models\QaTopic::unreadCount())
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.messenger.index") }}" class="{{ request()->is("admin/messenger") || request()->is("admin/messenger/*") ? "c-active" : "" }} c-sidebar-nav-link">
                    <i class="c-sidebar-nav-icon fa-fw fa fa-envelope">

                    </i>
                    <span>{{ trans('global.messages') }}</span>
                    @if($unread > 0)
                        <strong>( {{ $unread }} )</strong>
                    @endif

                </a>
            </li>
            @if(file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
                @can('profile_password_edit')
                    <li class="c-sidebar-nav-item">
                        <a class="c-sidebar-nav-link {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'c-active' : '' }}" href="{{ route('profile.password.edit') }}">
                            <i class="fa-fw fas fa-key c-sidebar-nav-icon">
                            </i>
                            {{ trans('global.change_password') }}
                        </a>
                    </li>
                @endcan
            @endif
            <li class="c-sidebar-nav-item">
                <a href="#" class="c-sidebar-nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                    <i class="c-sidebar-nav-icon fas fa-fw fa-sign-out-alt">

                    </i>
                    {{ trans('global.logout') }}
                </a>
            </li>
    </ul>

</div>