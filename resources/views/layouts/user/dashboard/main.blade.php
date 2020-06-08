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
          @if(auth()->user()->hasRole('User') || auth()->user()->hasRole('Admin'))
          @include('layouts.user.sidebarmenu.general')
          @endif
          <!-- End General Items -->  
          <!-- Wiki Items -->
          @include('layouts.user.sidebarmenu.wiki')
          <!-- End Wiki Items -->
          <!-- Moon Items -->
          @include('layouts.user.sidebarmenu.moon')
          <!-- End Moon Items -->
          <!-- SRP Items -->
          @include('layouts.user.sidebarmenu.srp')
          <!-- SRP Items -->
          <!-- Contracts -->
          @include('layouts.user.sidebarmenu.contracts')
          <!-- End Contracts -->
          <!-- Structures -->
          @include('layouts.user.sidebarmenu.structures')
          <!-- End SRP Admin -->
          <!-- Blacklist -->
          @include('layouts.user.sidebarmenu.blacklist')
          <!-- End Blacklist -->
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>