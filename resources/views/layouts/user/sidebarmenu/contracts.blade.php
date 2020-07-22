@if(auth()->user()->hasRole('User') || auth()->user()->hasRole('Admin'))
<li class="nav-item has-treeview">
    <a href="#" class="nav-link">
      <i class="nav-icon fas fa-file-contract"></i>
      <p>Supply Chain<br>
        <i class="right fas fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
        <a href="/supplychain/dashboard" class="nav-link">
          <i class="far fa-circle nav-icon"></i>
          <p>Dashboard</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/supplychain/my/dashboard" class="nav-link">
          <i class="far fa-circle nav-icon"></i>
          <p>My Dashboard</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/supplychain/display/bids" class="nav-link">
          <i class="far fa-circle nav-icon"></i>
          <p>Display Bids</p>
        </a>
      </li>
    </ul>
  </li>
  @endif