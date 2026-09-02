<nav
    class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
            <i class="icon-base ti tabler-menu-2 icon-md"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center justify-content-between w-100" id="navbar-collapse">
        <!-- Quick Search Bar -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item navbar-search-wrapper px-md-0 px-2 mb-0 d-flex align-items-center">
                <i class="icon-base ti tabler-search icon-md me-2 text-muted"></i>
                <span class="d-none d-md-inline-block text-body-secondary fw-normal small">
                    Quick Search (Ctrl+/)
                </span>
            </div>
        </div>

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- Theme Style Switcher -->
            <li class="nav-item dropdown me-2 me-xl-0">
                <a
                    class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
                    id="nav-theme"
                    href="javascript:void(0);"
                    data-bs-toggle="dropdown"
                    aria-label="Toggle Theme">
                    <i class="icon-base ti tabler-sun icon-22px theme-icon-active text-heading"></i>
                    <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
                    <li>
                        <button
                            type="button"
                            class="dropdown-item align-items-center active"
                            data-bs-theme-value="light"
                            aria-pressed="false">
                            <span><i class="icon-base ti tabler-sun icon-22px me-3" data-icon="sun"></i>Light</span>
                        </button>
                    </li>
                    <li>
                        <button
                            type="button"
                            class="dropdown-item align-items-center"
                            data-bs-theme-value="dark"
                            aria-pressed="true">
                            <span><i class="icon-base ti tabler-moon-stars icon-22px me-3" data-icon="moon-stars"></i>Dark</span>
                        </button>
                    </li>
                    <li>
                        <button
                            type="button"
                            class="dropdown-item align-items-center"
                            data-bs-theme-value="system"
                            aria-pressed="false">
                            <span><i class="icon-base ti tabler-device-desktop-analytics icon-22px me-3" data-icon="device-desktop-analytics"></i>System</span>
                        </button>
                    </li>
                </ul>
            </li>
            <!-- / Theme Style Switcher -->

            <!-- Quick HRM Apps & Shortcuts Dropdown -->
            <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-2 me-xl-0">
                <a
                    class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
                    href="javascript:void(0);"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="outside"
                    aria-expanded="false"
                    title="Quick Navigation">
                    <i class="icon-base ti tabler-layout-grid-add icon-22px text-heading"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end p-0" style="min-width: 320px;">
                    <div class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center py-3">
                            <h6 class="mb-0 me-auto fw-bold">HRM Quick Shortcuts</h6>
                            <span class="badge bg-label-primary">Enterprise</span>
                        </div>
                    </div>
                    <div class="dropdown-shortcuts-list scrollable-container p-2">
                        <div class="row row-bordered g-2">
                            {{-- Employee Directory --}}
                            @can('employees.view')
                                <div class="dropdown-shortcuts-item col-6 p-2 text-center">
                                    <a href="{{ route('admin::employees.index') }}" class="d-block text-decoration-none p-2 rounded hover-bg-light">
                                        <div class="avatar avatar-md mx-auto mb-2">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <i class="icon-base ti tabler-users icon-22px"></i>
                                            </span>
                                        </div>
                                        <h6 class="mb-0 fs-tiny fw-bold text-heading">Employees</h6>
                                        <small class="text-muted fs-tiny d-block">Directory</small>
                                    </a>
                                </div>
                            @endcan

                            {{-- Attendance Logs --}}
                            @can('attendance.view')
                                <div class="dropdown-shortcuts-item col-6 p-2 text-center">
                                    <a href="{{ route('admin::attendance.index') }}" class="d-block text-decoration-none p-2 rounded hover-bg-light">
                                        <div class="avatar avatar-md mx-auto mb-2">
                                            <span class="avatar-initial rounded bg-label-success">
                                                <i class="icon-base ti tabler-clock-check icon-22px"></i>
                                            </span>
                                        </div>
                                        <h6 class="mb-0 fs-tiny fw-bold text-heading">Attendance</h6>
                                        <small class="text-muted fs-tiny d-block">Daily Logs</small>
                                    </a>
                                </div>
                            @endcan

                            {{-- Leave Approvals --}}
                            @can('leaves.view')
                                <div class="dropdown-shortcuts-item col-6 p-2 text-center">
                                    <a href="{{ route('admin::leaves.requests') }}" class="d-block text-decoration-none p-2 rounded hover-bg-light">
                                        <div class="avatar avatar-md mx-auto mb-2">
                                            <span class="avatar-initial rounded bg-label-warning">
                                                <i class="icon-base ti tabler-calendar-time icon-22px"></i>
                                            </span>
                                        </div>
                                        <h6 class="mb-0 fs-tiny fw-bold text-heading">Leaves</h6>
                                        <small class="text-muted fs-tiny d-block">Requests</small>
                                    </a>
                                </div>
                            @endcan

                            {{-- Payroll Disbursements --}}
                            @can('payroll.view')
                                <div class="dropdown-shortcuts-item col-6 p-2 text-center">
                                    <a href="{{ route('admin::payroll.index') }}" class="d-block text-decoration-none p-2 rounded hover-bg-light">
                                        <div class="avatar avatar-md mx-auto mb-2">
                                            <span class="avatar-initial rounded bg-label-info">
                                                <i class="icon-base ti tabler-file-dollar icon-22px"></i>
                                            </span>
                                        </div>
                                        <h6 class="mb-0 fs-tiny fw-bold text-heading">Payroll</h6>
                                        <small class="text-muted fs-tiny d-block">Disbursements</small>
                                    </a>
                                </div>
                            @endcan

                            {{-- AI HR Suite --}}
                            @can('ai.use')
                                <div class="dropdown-shortcuts-item col-6 p-2 text-center">
                                    <a href="{{ route('admin::ai.index') }}" class="d-block text-decoration-none p-2 rounded hover-bg-light">
                                        <div class="avatar avatar-md mx-auto mb-2">
                                            <span class="avatar-initial rounded bg-label-danger">
                                                <i class="icon-base ti tabler-sparkles icon-22px"></i>
                                            </span>
                                        </div>
                                        <h6 class="mb-0 fs-tiny fw-bold text-heading">AI Suite</h6>
                                        <small class="text-muted fs-tiny d-block">GenAI Tools</small>
                                    </a>
                                </div>
                            @endcan

                            {{-- Self-Service Portal --}}
                            <div class="dropdown-shortcuts-item col-6 p-2 text-center">
                                <a href="{{ route('portal::dashboard') }}" class="d-block text-decoration-none p-2 rounded hover-bg-light">
                                    <div class="avatar avatar-md mx-auto mb-2">
                                        <span class="avatar-initial rounded bg-label-secondary">
                                            <i class="icon-base ti tabler-user-circle icon-22px"></i>
                                        </span>
                                    </div>
                                    <h6 class="mb-0 fs-tiny fw-bold text-heading">My Portal</h6>
                                    <small class="text-muted fs-tiny d-block">Self-Service</small>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <!-- / Quick HRM Apps & Shortcuts Dropdown -->

            <!-- Dynamic Notifications Dropdown -->
            <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
                <a
                    class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
                    href="javascript:void(0);"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="outside"
                    aria-expanded="false"
                    title="Notifications">
                    <span class="position-relative">
                        <i class="icon-base ti tabler-bell icon-22px text-heading"></i>
                        @if(($topbarUnreadCount ?? 0) > 0)
                            <span class="badge rounded-pill bg-danger badge-dot badge-notifications border position-absolute top-0 start-100 translate-middle"></span>
                        @endif
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end p-0" style="min-width: 350px;">
                    <li class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center py-3">
                            <h6 class="mb-0 me-auto fw-bold">Notifications</h6>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-label-primary me-2">{{ $topbarUnreadCount ?? 0 }} New</span>
                                @if(($topbarUnreadCount ?? 0) > 0)
                                    <form method="POST" action="{{ route('admin::notifications.markAllRead') }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-icon btn-text-secondary rounded-pill btn-sm" title="Mark all as read">
                                            <i class="icon-base ti tabler-mail-opened icon-18px"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </li>
                    <li class="dropdown-notifications-list scrollable-container" style="max-height: 380px; overflow-y: auto;">
                        <ul class="list-group list-group-flush">
                            @forelse($topbarNotifications ?? [] as $notif)
                                @php
                                    $isUnread = is_null($notif->read_at) && $notif->status !== 'read';
                                    $icon = 'tabler-bell';
                                    $color = 'primary';
                                    if ($notif->type === 'leave') {
                                        $icon = 'tabler-calendar-event';
                                        $color = 'warning';
                                    } elseif ($notif->type === 'payroll' || $notif->type === 'payroll_disbursed') {
                                        $icon = 'tabler-currency-rupee';
                                        $color = 'success';
                                    } elseif ($notif->type === 'announcement') {
                                        $icon = 'tabler-speakerphone';
                                        $color = 'info';
                                    } elseif ($notif->type === 'expense') {
                                        $icon = 'tabler-receipt';
                                        $color = 'primary';
                                    }
                                @endphp
                                <li class="list-group-item list-group-item-action dropdown-notifications-item {{ $isUnread ? 'bg-light-subtle' : 'marked-as-read' }}">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar avatar-sm">
                                                <span class="avatar-initial rounded-circle bg-label-{{ $color }}">
                                                    <i class="icon-base ti {{ $icon }} icon-18px"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="small mb-1 fw-bold text-heading">{{ $notif->title }}</h6>
                                            <small class="mb-1 d-block text-body text-truncate" style="max-width: 220px;">{{ $notif->message }}</small>
                                            <small class="text-body-secondary">{{ $notif->created_at ? $notif->created_at->diffForHumans() : 'Recently' }}</small>
                                        </div>
                                        @if($isUnread)
                                            <div class="flex-shrink-0 dropdown-notifications-actions ms-2">
                                                <span class="badge badge-dot bg-primary"></span>
                                            </div>
                                        @endif
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center py-4 text-muted">
                                    <i class="icon-base ti tabler-bell-off icon-32px d-block mb-1 text-muted"></i>
                                    <small>No notifications found</small>
                                </li>
                            @endforelse
                        </ul>
                    </li>
                    <li class="border-top">
                        <div class="d-grid p-3">
                            <a class="btn btn-primary btn-sm d-flex align-items-center justify-content-center" href="{{ route('admin::notifications.index') }}">
                                <span class="align-middle">View All Notifications</span>
                                <i class="icon-base ti tabler-arrow-right ms-1 icon-14px"></i>
                            </a>
                        </div>
                    </li>
                </ul>
            </li>
            <!-- / Dynamic Notifications Dropdown -->

            <!-- Dynamic User Dropdown -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a
                    class="nav-link dropdown-toggle hide-arrow p-0"
                    href="javascript:void(0);"
                    data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <span class="avatar-initial rounded-circle bg-label-primary fw-bold">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li>
                        <div class="dropdown-item mt-0 py-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <span class="avatar-initial rounded-circle bg-label-primary fw-bold">
                                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold">{{ Auth::user()->name ?? 'User' }}</h6>
                                    <small class="badge bg-label-primary text-capitalize py-0 px-1 mt-1">
                                        {{ str_replace('_', ' ', Auth::user()->roles->first()->name ?? 'Staff') }}
                                    </small>
                                    @if(isset($currentEmployee) && $currentEmployee)
                                        <small class="text-muted d-block mt-1 fs-tiny">
                                            {{ $currentEmployee->designation->name ?? '' }} ({{ $currentEmployee->employee_code }})
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="dropdown-divider my-1"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('portal::dashboard') }}">
                            <i class="icon-base ti tabler-user-circle me-3 icon-md text-primary"></i>
                            <span class="align-middle fw-medium">My Self-Service Portal</span>
                        </a>
                    </li>
                    @can('dashboard.view')
                        <li>
                            <a class="dropdown-item" href="{{ route('admin::dashboard.admin') }}">
                                <i class="icon-base ti tabler-smart-home me-3 icon-md text-info"></i>
                                <span class="align-middle">Admin Dashboard</span>
                            </a>
                        </li>
                    @endcan
                    @can('settings.manage')
                        <li>
                            <a class="dropdown-item" href="{{ route('admin::settings.company') }}">
                                <i class="icon-base ti tabler-settings me-3 icon-md text-secondary"></i>
                                <span class="align-middle">Company Settings</span>
                            </a>
                        </li>
                    @endcan
                    <li>
                        <div class="dropdown-divider my-1"></div>
                    </li>
                    <li>
                        <div class="d-grid px-2 pt-2 pb-1">
                            <form method="POST" action="{{ route('admin::logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger d-flex w-100 justify-content-center align-items-center">
                                    <span class="align-middle">Sign Out</span>
                                    <i class="icon-base ti tabler-logout ms-2 icon-14px"></i>
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </li>
            <!-- / Dynamic User Dropdown -->
        </ul>
    </div>
</nav>
