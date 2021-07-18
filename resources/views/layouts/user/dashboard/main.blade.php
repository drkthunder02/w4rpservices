<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="#" class="brand-link">
    <span class="brand-text font-weight-light">W4RP</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="info">
        <a href="/profile" class="d-block">{{ auth()->user()->getName() }}</a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- General Items -->
        @include('layouts.user.sidebarmenu.general')
        <!-- End General Items -->
        <!-- Finance Items -->
        @include('layouts.user.sidebarmenu.finances')
        <!-- End Finance Items -->
        <!-- Mining Tax Items -->
        @include('layouts.user.sidebarmenu.miningtax')
        <!-- End Mining Tax Items -->
        <!-- After Action Reports -->
        @include('layouts.user.sidebarmenu.reports')
        <!-- End After Action Reports -->
        <!-- SRP Items -->
        @include('layouts.user.sidebarmenu.srp')
        <!-- SRP Items -->
        <!-- Contracts -->
        @include('layouts.user.sidebarmenu.contracts')
        <!-- End Contracts -->
        <!-- Blacklist -->
        @include('layouts.user.sidebarmenu.blacklist')
        <!-- End Blacklist -->
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
  @hasSection('sidebar')
  @yield('sidebar')
  @endif
</aside>
