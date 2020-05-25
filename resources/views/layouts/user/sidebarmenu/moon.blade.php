<li class="nav-item has-treeview">
    <a href="#" class="nav-link">
      <i class="nav-icon far fa-moon"></i>
      <p>Moons<br>
        <i class="right fas fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      @if(auth()->user()->hasRole('User') || auth()->user()->hasRole('Admin'))
      <li class="nav-item">
        <a href="/moons/display/all" class="nav-link">
          <i class="far fa-circle nav-icon"></i>
          <p>Display All Moons</p>
        </a>
      </li>
      @endif
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
      @if(auth()->user()->hasRole('User') || auth()->user()->hasRole('Admin'))
      <li class="nav-item">
        <a href="/moons/display/request" class="nav-link">
          <i class="far fa-circle nav-icon"></i>
          <p>Moon Reservation</p>
        </a>
      </li>
      @endif
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