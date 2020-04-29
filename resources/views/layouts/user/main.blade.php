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
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>General<br>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/profile" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Profile</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/logistics/fuel/structures" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Jump Gate Fuel</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="https://buyback.w4rp.space" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Buyback Program</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/wormholes/display" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Wormholes</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/wormholes/form" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Wormhole Form</p>
                </a>
              </li>
            </ul>
          </li>
          @endif
          <!-- End General Items -->  
          <!-- Moon Items -->
          @if(auth()->user()->hasRole('User') || auth()->user()->hasRole('Renter') || auth()->user()->hasRole('Admin'))
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Moons<br>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/moons/display/all" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Display All Moons</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/moons/display/rentals" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Display Rental Moons</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/moons/display/form/worth" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Moon Worth</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/moons/display/request" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Moon Reservation</p>
                </a>
              </li>
              @if(auth()->user()->hasPermission('corp.lead') && auth()->user()->hasEsiScope('esi-industry.read_corporation_mining.v1'))
              <li class="nav-item">
                <a href="/moons/ledger/display/moons" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Mining Ledger</p>
                </a>
              </li>
              @endif
              @if(auth()->user()->isMoonRenter())
              <li class="nav-item">
                <a href="/moons/ledger/display/rentals" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Moon Rental Ledger</p>
                </a>
              </li>
              @endif
            </ul>
          </li>
          @endif
          <!-- End Moon Items -->
          <!-- SRP Items -->
          @if(auth()->user()->hasRole('User') || auth()->user()->hasRole('Admin'))
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>SRP<br>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/srp/form/display" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>SRP Form</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/srp/display/costcodes" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Cost Codes</p>
                </a>
              </li>
            </ul>
          </li>
          @endif
          <!-- SRP Items -->
          <!-- Contracts -->
          @if(auth()->user()->hasPermission('moon.admin'))
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Contracts<br>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/contracts/display/all" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Display All Contracts</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/contracts/display/public" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Display Public Contracts</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/contracts/display/private" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Display Private Contracts</p>
                </a>
              </li>
            </ul>
          </li>
          @endif
          <!-- End Contracts -->
          <!-- Structures -->
          @if(auth()->user()->hasPermission('srp.admin'))
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Structures<br>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @if(auth()->user()->hasPermission('fc.team'))
              <li class="nav-item">
                <a href="/structures/display/requests" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Display Requests</p>
                </a>
              </li>
              @endif
              <li class="nav-item">
                <a href="/structures/display/form" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Request Form</p>
                </a>
              </li>
            </ul>
          </li>
          @endif
          <!-- End SRP Admin -->
          <!-- Contract Admin -->
          @if(auth()->user()->hasPermission('contract.admin'))
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Contract Admin<br>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/contracts/admin/display" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Admin Dashboard</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/contracts/admin/new" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>New Contract</p>
                </a>
              </li>
            </ul>
          </li>
          @endif
          <!-- End Contract Admin -->
          <!-- Blacklist -->
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Blacklist<br>
                <i class="right fas fa-angle-left"></i>
                </p>
            </a>
          </li>
          <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="/blacklist/display" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Display</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="/blacklist/display/search" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Search</p>
                </a>
            </li>
            @if(auth()->user()->hasPermission('alliance.recruiter'))
            <li class="nav-item">
                <a href="/blacklist/display/add" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add To</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="/blacklist/display/remove" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Remove From</p>
                </a>
            </li>
            @endif
          </ul>
          <!-- End Blacklist -->
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>