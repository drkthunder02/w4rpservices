<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="#" class="brand-link">
    <span class="brand-text font-weight-light">W4RP Admin Dashboard</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="info">
        <a href="#" class="d-block">{{ auth()->user()->getName() }}</a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- General Administrative Stuff -->
        @include('layouts.admin.sidebarmenu.general')
        <!-- End General Administrative Stuff -->
        <!-- SRP Admin -->
        @include('layouts.admin.sidebarmenu.srp')
        <!-- End SRP Admin -->
        <!-- Mining Tax Admin -->
        @include('layouts.admin.sidebarmenu.miningtax')
        <!-- End Mining Tax Admin -->
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
  @hasSection('sidebar')
  @yeild('sidebar')
  @endif
</aside>
