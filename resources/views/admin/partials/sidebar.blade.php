<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo">
        <a href="{{ Route::has('admin::dashboard.admin') ? route('admin::dashboard.admin') : 'javascript:void(0);' }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <span class="text-primary">
                    <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z" fill="currentColor"/>
                        <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616"/>
                        <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z" fill="currentColor"/>
                    </svg>
                </span>
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-3">HRM Portal</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
            <i class="icon-base ti tabler-x d-block d-xl-none"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        {{-- 1. Dashboard --}}
        @canany(['dashboard.view', 'reports.view'])
            <li class="menu-item {{ request()->routeIs('admin::dashboard.*') ? 'active' : '' }}">
                <a href="{{ Route::has('admin::dashboard.admin') ? route('admin::dashboard.admin') : 'javascript:void(0);' }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-smart-home"></i>
                    <div data-i18n="Dashboard">Dashboard</div>
                </a>
            </li>
        @endcanany

        {{-- Employee Self-Service (ESS) Portal --}}
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Employee Self-Service">My Portal</span>
        </li>
        <li class="menu-item {{ request()->routeIs('portal::*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-user-circle"></i>
                <div data-i18n="Self-Service Portal">Self-Service</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('portal::dashboard*') ? 'active' : '' }}">
                    <a href="{{ route('portal::dashboard') }}" class="menu-link">
                        <div data-i18n="My Dashboard">My Dashboard</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('portal::attendance.*') ? 'active' : '' }}">
                    <a href="{{ route('portal::attendance.index') }}" class="menu-link">
                        <div data-i18n="My Attendance">My Attendance</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('portal::leaves.*') ? 'active' : '' }}">
                    <a href="{{ route('portal::leaves.index') }}" class="menu-link">
                        <div data-i18n="My Leaves">My Leaves</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('portal::expenses.*') ? 'active' : '' }}">
                    <a href="{{ route('portal::expenses.index') }}" class="menu-link">
                        <div data-i18n="My Expenses">My Claims</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('portal::payroll.*') ? 'active' : '' }}">
                    <a href="{{ route('portal::payroll.index') }}" class="menu-link">
                        <div data-i18n="My Payslips">My Payslips</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('portal::performance.*') ? 'active' : '' }}">
                    <a href="{{ route('portal::performance.index') }}" class="menu-link">
                        <div data-i18n="My Goals">My OKRs</div>
                    </a>
                </li>
            </ul>
        </li>

        {{-- 2. Workforce Management --}}
        @canany(['employees.view', 'employees.create', 'documents.view', 'attendance.view', 'attendance.manage', 'shifts.view', 'shifts.manage', 'leaves.view', 'leaves.create', 'leaves.approve', 'holidays.view', 'holidays.manage'])
            <li class="menu-header small">
                <span class="menu-header-text" data-i18n="Workforce">Workforce</span>
            </li>

            {{-- Employees --}}
            @canany(['employees.view', 'employees.create', 'employees.edit', 'documents.view'])
                <li class="menu-item {{ request()->routeIs('admin::employees.*') || request()->routeIs('admin::documents.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-users"></i>
                        <div data-i18n="Employees">Employees</div>
                    </a>
                    <ul class="menu-sub">
                        @canany(['employees.view', 'employees.edit'])
                            <li class="menu-item {{ request()->routeIs('admin::employees.index') ? 'active' : '' }}">
                                <a href="{{ Route::has('admin::employees.index') ? route('admin::employees.index') : 'javascript:void(0);' }}" class="menu-link">
                                    <div data-i18n="Employee Directory">Employee Directory</div>
                                </a>
                            </li>
                        @endcanany
                        @can('employees.create')
                            <li class="menu-item {{ request()->routeIs('admin::employees.create') ? 'active' : '' }}">
                                <a href="{{ Route::has('admin::employees.create') ? route('admin::employees.create') : 'javascript:void(0);' }}" class="menu-link">
                                    <div data-i18n="Add Employee">Add Employee</div>
                                </a>
                            </li>
                        @endcan
                        @can('documents.view')
                            <li class="menu-item {{ request()->routeIs('admin::documents.*') ? 'active' : '' }}">
                                <a href="{{ Route::has('admin::documents.index') ? route('admin::documents.index') : 'javascript:void(0);' }}" class="menu-link">
                                    <div data-i18n="Documents">Documents</div>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            {{-- Attendance & Shifts --}}
            @canany(['attendance.view', 'attendance.manage', 'shifts.view', 'shifts.manage'])
                <li class="menu-item {{ request()->routeIs('admin::attendance.*') || request()->routeIs('admin::shifts.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-calendar-time"></i>
                        <div data-i18n="Attendance & Shifts">Attendance &amp; Shifts</div>
                    </a>
                    <ul class="menu-sub">
                        @canany(['attendance.view', 'attendance.manage'])
                            <li class="menu-item {{ request()->routeIs('admin::attendance.*') ? 'active' : '' }}">
                                <a href="{{ Route::has('admin::attendance.index') ? route('admin::attendance.index') : 'javascript:void(0);' }}" class="menu-link">
                                    <div data-i18n="Attendance Logs">Attendance Logs</div>
                                </a>
                            </li>
                        @endcanany
                        @canany(['shifts.view', 'shifts.manage'])
                            <li class="menu-item {{ request()->routeIs('admin::shifts.*') ? 'active' : '' }}">
                                <a href="{{ Route::has('admin::shifts.index') ? route('admin::shifts.index') : 'javascript:void(0);' }}" class="menu-link">
                                    <div data-i18n="Shift Schedules">Shift Schedules</div>
                                </a>
                            </li>
                        @endcanany
                    </ul>
                </li>
            @endcanany

            {{-- Leaves & Holidays --}}
            @canany(['leaves.view', 'leaves.approve', 'leaves.create', 'leaves.manage', 'holidays.view', 'holidays.manage'])
                <li class="menu-item {{ request()->routeIs('admin::leaves.*') || request()->routeIs('admin::holidays.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-calendar-due"></i>
                        <div data-i18n="Leaves & Holidays">Leaves &amp; Holidays</div>
                    </a>
                    <ul class="menu-sub">
                        @canany(['leaves.view', 'leaves.approve', 'leaves.create'])
                            <li class="menu-item {{ request()->routeIs('admin::leaves.requests') || request()->routeIs('admin::leaves.index') ? 'active' : '' }}">
                                <a href="{{ Route::has('admin::leaves.requests') ? route('admin::leaves.requests') : (Route::has('admin::leaves.index') ? route('admin::leaves.index') : 'javascript:void(0);') }}" class="menu-link">
                                    <div data-i18n="Leave Requests">Leave Requests</div>
                                </a>
                            </li>
                        @endcanany
                        @canany(['leaves.view', 'leaves.manage'])
                            <li class="menu-item {{ request()->routeIs('admin::leaves.types') ? 'active' : '' }}">
                                <a href="{{ Route::has('admin::leaves.types') ? route('admin::leaves.types') : 'javascript:void(0);' }}" class="menu-link">
                                    <div data-i18n="Leave Types & Balances">Leave Types &amp; Balances</div>
                                </a>
                            </li>
                        @endcanany
                        @canany(['holidays.view', 'holidays.manage'])
                            <li class="menu-item {{ request()->routeIs('admin::holidays.*') ? 'active' : '' }}">
                                <a href="{{ Route::has('admin::holidays.index') ? route('admin::holidays.index') : 'javascript:void(0);' }}" class="menu-link">
                                    <div data-i18n="Holidays Calendar">Holidays Calendar</div>
                                </a>
                            </li>
                        @endcanany
                    </ul>
                </li>
            @endcanany
        @endcanany

        {{-- 3. Payroll & Finance --}}
        @canany(['payroll.view', 'payroll.generate', 'payroll.pay', 'payroll.manage', 'expenses.view', 'expenses.create', 'expenses.approve'])
            <li class="menu-header small">
                <span class="menu-header-text" data-i18n="Finance & Payroll">Finance &amp; Payroll</span>
            </li>

            {{-- Payroll --}}
            @canany(['payroll.view', 'payroll.generate', 'payroll.pay', 'payroll.manage'])
                <li class="menu-item {{ request()->routeIs('admin::payroll.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-file-dollar"></i>
                        <div data-i18n="Payroll">Payroll</div>
                    </a>
                    <ul class="menu-sub">
                        @canany(['payroll.view', 'payroll.generate'])
                            <li class="menu-item {{ request()->routeIs('admin::payroll.index') ? 'active' : '' }}">
                                <a href="{{ Route::has('admin::payroll.index') ? route('admin::payroll.index') : 'javascript:void(0);' }}" class="menu-link">
                                    <div data-i18n="Payroll Batches">Payroll Batches</div>
                                </a>
                            </li>
                        @endcanany
                        @canany(['payroll.manage', 'payroll.view'])
                            <li class="menu-item {{ request()->routeIs('admin::payroll.structures') ? 'active' : '' }}">
                                <a href="{{ Route::has('admin::payroll.structures') ? route('admin::payroll.structures') : 'javascript:void(0);' }}" class="menu-link">
                                    <div data-i18n="Salary Structures">Salary Structures</div>
                                </a>
                            </li>
                        @endcanany
                    </ul>
                </li>
            @endcanany

            {{-- Expense Claims --}}
            @canany(['expenses.view', 'expenses.create', 'expenses.approve'])
                <li class="menu-item {{ request()->routeIs('admin::expenses.*') ? 'active' : '' }}">
                    <a href="{{ Route::has('admin::expenses.index') ? route('admin::expenses.index') : 'javascript:void(0);' }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-receipt-2"></i>
                        <div data-i18n="Expense Claims">Expense Claims</div>
                    </a>
                </li>
            @endcanany
        @endcanany

        {{-- 4. Performance & Talent --}}
        @canany(['performance.view', 'performance.manage', 'performance.review'])
            <li class="menu-header small">
                <span class="menu-header-text" data-i18n="Performance">Performance</span>
            </li>

            <li class="menu-item {{ request()->routeIs('admin::performance.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-target-arrow"></i>
                    <div data-i18n="Performance">Performance</div>
                </a>
                <ul class="menu-sub">
                    @canany(['performance.view', 'performance.manage'])
                        <li class="menu-item {{ request()->routeIs('admin::performance.goals') ? 'active' : '' }}">
                            <a href="{{ Route::has('admin::performance.goals') ? route('admin::performance.goals') : 'javascript:void(0);' }}" class="menu-link">
                                <div data-i18n="Goals & OKRs">Goals &amp; OKRs</div>
                            </a>
                        </li>
                    @endcanany
                    @canany(['performance.review', 'performance.manage', 'performance.view'])
                        <li class="menu-item {{ request()->routeIs('admin::performance.reviews') ? 'active' : '' }}">
                            <a href="{{ Route::has('admin::performance.reviews') ? route('admin::performance.reviews') : 'javascript:void(0);' }}" class="menu-link">
                                <div data-i18n="Appraisals & Reviews">Appraisals &amp; Reviews</div>
                            </a>
                        </li>
                    @endcanany
                </ul>
            </li>
        @endcanany

        {{-- 5. Communication & AI Suite --}}
        @canany(['announcements.view', 'announcements.manage', 'ai.use', 'notifications.view', 'notifications.manage'])
            <li class="menu-header small">
                <span class="menu-header-text" data-i18n="Communication & AI">Communication &amp; AI</span>
            </li>

            {{-- Announcements --}}
            @canany(['announcements.view', 'announcements.manage'])
                <li class="menu-item {{ request()->routeIs('admin::announcements.*') ? 'active' : '' }}">
                    <a href="{{ Route::has('admin::announcements.index') ? route('admin::announcements.index') : 'javascript:void(0);' }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-speakerphone"></i>
                        <div data-i18n="Notice Board">Notice Board</div>
                    </a>
                </li>
            @endcanany

            {{-- AI HR Suite --}}
            @can('ai.use')
                <li class="menu-item {{ request()->routeIs('admin::ai.*') ? 'active' : '' }}">
                    <a href="{{ Route::has('admin::ai.index') ? route('admin::ai.index') : 'javascript:void(0);' }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-sparkles"></i>
                        <div data-i18n="AI HR Suite">AI HR Suite</div>
                    </a>
                </li>
            @endcan

            {{-- Notifications Hub --}}
            @canany(['notifications.view', 'notifications.manage'])
                <li class="menu-item {{ request()->routeIs('admin::notifications.*') ? 'active' : '' }}">
                    <a href="{{ Route::has('admin::notifications.index') ? route('admin::notifications.index') : 'javascript:void(0);' }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-bell"></i>
                        <div data-i18n="Notifications">Notifications</div>
                    </a>
                </li>
            @endcanany
        @endcanany

        {{-- 6. Administration & Settings --}}
        @canany(['reports.view', 'roles.view', 'roles.manage', 'settings.manage'])
            <li class="menu-header small">
                <span class="menu-header-text" data-i18n="Administration">Administration</span>
            </li>

            {{-- Reports & Analytics --}}
            @can('reports.view')
                <li class="menu-item {{ request()->routeIs('admin::reports.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-chart-pie"></i>
                        <div data-i18n="Reports & Analytics">Reports &amp; Analytics</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ request()->routeIs('admin::reports.headcount') ? 'active' : '' }}">
                            <a href="{{ Route::has('admin::reports.headcount') ? route('admin::reports.headcount') : 'javascript:void(0);' }}" class="menu-link">
                                <div data-i18n="Headcount Analytics">Headcount Analytics</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('admin::reports.attendance') ? 'active' : '' }}">
                            <a href="{{ Route::has('admin::reports.attendance') ? route('admin::reports.attendance') : 'javascript:void(0);' }}" class="menu-link">
                                <div data-i18n="Attendance Reports">Attendance Reports</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('admin::reports.leaves') ? 'active' : '' }}">
                            <a href="{{ Route::has('admin::reports.leaves') ? route('admin::reports.leaves') : 'javascript:void(0);' }}" class="menu-link">
                                <div data-i18n="Leave Reports">Leave Reports</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('admin::reports.payroll') ? 'active' : '' }}">
                            <a href="{{ Route::has('admin::reports.payroll') ? route('admin::reports.payroll') : 'javascript:void(0);' }}" class="menu-link">
                                <div data-i18n="Payroll Reports">Payroll Reports</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            {{-- Roles & Permissions --}}
            @canany(['roles.view', 'roles.manage'])
                <li class="menu-item {{ request()->routeIs('admin::roles.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-shield-lock"></i>
                        <div data-i18n="Roles & Permissions">Roles &amp; Permissions</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ request()->routeIs('admin::roles.index') ? 'active' : '' }}">
                            <a href="{{ Route::has('admin::roles.index') ? route('admin::roles.index') : 'javascript:void(0);' }}" class="menu-link">
                                <div data-i18n="Roles List">Roles List</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('admin::roles.permissions') ? 'active' : '' }}">
                            <a href="{{ Route::has('admin::roles.permissions') ? route('admin::roles.permissions') : 'javascript:void(0);' }}" class="menu-link">
                                <div data-i18n="Permissions Matrix">Permissions Matrix</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcanany

            {{-- Settings --}}
            @can('settings.manage')
                <li class="menu-item {{ request()->routeIs('admin::settings.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon icon-base ti tabler-settings"></i>
                        <div data-i18n="Settings">Settings</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ request()->routeIs('admin::settings.departments') ? 'active' : '' }}">
                            <a href="{{ Route::has('admin::settings.departments') ? route('admin::settings.departments') : 'javascript:void(0);' }}" class="menu-link">
                                <div data-i18n="Departments">Departments</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('admin::settings.designations') ? 'active' : '' }}">
                            <a href="{{ Route::has('admin::settings.designations') ? route('admin::settings.designations') : 'javascript:void(0);' }}" class="menu-link">
                                <div data-i18n="Designations">Designations</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('admin::settings.company') ? 'active' : '' }}">
                            <a href="{{ Route::has('admin::settings.company') ? route('admin::settings.company') : 'javascript:void(0);' }}" class="menu-link">
                                <div data-i18n="Company Profile">Company Profile</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan
        @endcanany
    </ul>
</aside>
