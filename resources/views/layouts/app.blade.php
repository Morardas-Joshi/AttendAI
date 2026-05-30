<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard</title>

    <!-- Fonts & CSS -->
    <link href="{{ asset('attendai/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('attendai/css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>

<body id="page-top">

    <div id="wrapper">

        <!-- 🔵 SIDEBAR -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center"
                href="{{ route(auth()->user()->role . '.dashboard') }}">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="sidebar-brand-text mx-3">AttendAI</div>
            </a>

            <hr class="sidebar-divider">

            <!-- Dashboard -->
            <li class="nav-item {{ request()->routeIs('*dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route(auth()->user()->role . '.dashboard') }}">
                    <i class="fas fa-tachometer-alt text-primary"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            @if(auth()->user()->role === 'admin')

            <div class="sidebar-heading">Admin</div>

            <!-- Students -->
            <li class="nav-item {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.students') }}">
                    <i class="fas fa-users text-success"></i>
                    <span>Students</span>
                </a>
            </li>

            <!-- Faculty -->
            <li class="nav-item {{ request()->routeIs('admin.faculty*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.faculty') }}">
                    <i class="fas fa-user-tie text-warning"></i>
                    <span>Faculty</span>
                </a>
            </li>

            <!-- Classes -->
            <li class="nav-item {{ request()->routeIs('admin.classes*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.classes') }}">
                    <i class="fas fa-book text-info"></i>
                    <span>Classes</span>
                </a>
            </li>

            <!-- Subjects -->
            <li class="nav-item {{ request()->routeIs('admin.subjects*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.subjects') }}">
                    <i class="fas fa-book-open text-danger"></i>
                    <span>Subjects</span>
                </a>
            </li>

            <!-- Reports -->
            <li class="nav-item {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReports">
                    <i class="fas fa-chart-bar text-light"></i>
                    <span>Reports</span>
                </a>
                <div id="collapseReports" class="collapse {{ request()->routeIs('admin.reports*') ? 'show' : '' }}" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{ request()->routeIs('admin.reports') ? 'active' : '' }}" href="{{ route('admin.reports') }}">All Logs</a>
                        <a class="collapse-item {{ request()->routeIs('admin.reports.students') ? 'active' : '' }}" href="{{ route('admin.reports.students') }}">Student Summary</a>
                    </div>
                </div>
            </li>

            @endif

            <!-- Faculty Menu -->
            @if(auth()->user()->role === 'faculty')
            <div class="sidebar-heading">Faculty</div>

            <li class="nav-item {{ request()->routeIs('faculty.sessions*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('faculty.sessions') }}">
                    <i class="fas fa-check-circle text-success"></i>
                    <span>Sessions</span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('faculty.profile') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('faculty.profile') }}">
                    <i class="fas fa-user-circle text-info"></i>
                    <span>My Profile</span>
                </a>
            </li>
            @endif

            <!-- Student Menu -->
            @if(auth()->user()->role === 'student')
            <div class="sidebar-heading">Student Panel</div>

            <li class="nav-item {{ request()->routeIs('student.join*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.join') }}">
                    <i class="fas fa-calendar-check text-info"></i>
                    <span>Join Session</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReports"
                    aria-expanded="true" aria-controls="collapseReports">
                    <i class="fas fa-chart-line text-warning"></i>
                    <span>Reports</span>
                </a>
                <div id="collapseReports" class="collapse {{ request()->is('student/performance*') || request()->is('student/my-attendance*') ? 'show' : '' }}" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{ request()->routeIs('student.my_attendance') ? 'active' : '' }}" href="{{ route('student.my_attendance') }}">Attendance History</a>
                        <a class="collapse-item {{ request()->routeIs('student.performance') ? 'active' : '' }}" href="{{ route('student.performance') }}">Performance Stats</a>
                    </div>
                </div>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">Account</div>

            <li class="nav-item {{ request()->routeIs('student.profile') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.profile') }}">
                    <i class="fas fa-user-circle text-info"></i>
                    <span>My Profile</span>
                </a>
            </li>
            @endif

            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggle -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- 🔵 CONTENT -->
        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                <!-- 🔵 TOPBAR -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">

                    <ul class="navbar-nav ml-auto">

                        <!-- User -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                                <span class="mr-2 text-gray-600 small">
                                    {{ auth()->user()->name ?? 'User' }}
                                </span>
                                <img class="img-profile rounded-circle" style="object-fit: cover;"
                                     src="{{ auth()->user()->profile_photo ? asset(auth()->user()->profile_photo) : asset('attendai/img/undraw_profile.svg') }}">
                            </a>

                            <div class="dropdown-menu dropdown-menu-right shadow">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item" type="submit">
                                        <i class="fas fa-sign-out-alt mr-2"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </li>

                    </ul>
                </nav>

                <!-- 🔵 MAIN CONTENT -->
                <div class="container-fluid mt-4">
                    @yield('content')
                </div>

            </div>

            <!-- Footer -->
            <footer class="bg-white text-center py-3">
                <span>© AttendAI</span>
            </footer>

        </div>

    </div>

    <!-- JS -->
    <script src="{{ asset('attendai/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('attendai/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('attendai/js/sb-admin-2.min.js') }}"></script>

</body>

</html>