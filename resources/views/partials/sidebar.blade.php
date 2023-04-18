<!--**********************************
            Sidebar start
        ***********************************-->
<div class="nk-sidebar">
    <div class="nk-nav-scroll">
        <ul class="metismenu" id="menu">
            <li>
                <a href="{{ route('dashboard') }}" aria-expanded="false">
                    <i class="icon-home menu-icon"></i><span class="nav-text">Dashboard</span>
                </a>
            </li>
            @if (in_array(Auth::user()->role_id, [1, 3]))            
            <li>
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="fa fa-database"></i> <span class="nav-text">Master Data</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('account.index') }}"><i class="icon-people"></i> Accounts</a></li>
                    <li><a href="{{ route('role.index') }}"><i class="fa fa-sitemap"></i> Roles</a></li>
                    <li><a href="{{ route('department.index') }}"><i class="fa fa-sitemap"></i> Departments</a></li>
                    <li><a href="{{ route('subdepartment.index') }}"><i class="fa fa-sitemap"></i> Sub Departments</a></li>
                    <li><a href="{{ route('employee.index') }}"><i class="icon-people"></i> Employees</a></li>
                    <li><a href="{{ route('holiday.index') }}"><i class="fa-solid fa-calendar-day"></i> Holidays Date</a></li>
                </ul>
            </li>
            @endif
            @if (in_array(Auth::user()->role_id, [1, 2]))            
            <li>
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="fa fa-microchip"></i> <span class="nav-text">Master Devices</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('location.index') }}"><i class="icon-location-pin"></i> Data Location</a></li>
                    <li><a href="{{ route('attendance.index') }}"><i class="icon-screen-smartphone"></i> Attendance Device</a></li>
                    <li><a href="{{ route('doorlock.index') }}"><i class="icon-screen-desktop"></i> Doorlock Device</a></li>
                    <li><a href="{{ route('remark.index') }}"><i class="icon-notebook"></i> Remarks</a></li>
                    <li><a href="{{ route('schedule.index') }}"><i class="icon-notebook"></i> Schedule Management</a></li>
                </ul>
            </li>
            @endif
            @if (in_array(Auth::user()->role_id, [1, 4]))
            <li>
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="icon-user-following"></i> <span class="nav-text">Management Attendances</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('leave-absence.index') }}"><i class="icon-graph"></i> Leave and Absence</a></li>
                </ul>
            </li>
            <li>
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="icon-credit-card"></i> <span class="nav-text">Payroll System</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('payroll.employee.list') }}"><i class="icon-note"></i> Payroll</a></li>
                </ul>
            </li>                
            @endif
            @if (in_array(Auth::user()->role_id, [1, 2]))            
            <li>
                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="icon-printer"></i> <span class="nav-text">Reports</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('absence.index') }}"><i class="icon-people"></i> Absence Report</a></li>
                    <li><a href="{{ route('doorlock.index') }}"><i class="icon-list"></i> Doorlock Report</a></li>
                    <li><a href="{{ route('outmonitoring.index') }}"><i class="icon-list"></i> Out Room Report</a></li>
                </ul>
            </li>
            @endif
            <li>
                <a href="{{ route('auth.logout') }}" aria-expanded="false">
                    <i class="icon-logout menu-icon"></i><span class="nav-text">Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>
<!--**********************************
            Sidebar end
        ***********************************-->
