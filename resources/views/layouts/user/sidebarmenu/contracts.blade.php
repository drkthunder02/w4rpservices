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
        <a href="/supplychain/contracts/new" class="nav-link">
          <i class="far fa-circle nav-icon"></i>
          <p>Create New Contract</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/supplychain/contracts/delete" class="nav-link">
          <i class="far fa-circle nav-icon"></i>
          <p>Delete Contract</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/supplychain/contracts/end" class="nav-link">
          <i class="far fa-circle nav-icon"></i>
          <p>End Contract</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/supplychain/display/bids" class="nav-link">
          <i class="far fa-circle nav-icon"></i>
          <p>Display Bids</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/supplychain/display/newbid" class="nav-link">
          <i class="far fa-circle nav-icon"></i>
          <p>Bid on Contract</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/supplychain/delete/bid" class="nav-link">
          <i class="far fa-circle nav-icon"></i>
          <p>Remove Bid</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="/supplychain/modify/bid" class="nav-link">
          <i class="far fa-circle nav-icon"></i>
          <p>Modify Bid</p>
        </a>
      </li>
    </ul>
  </li>
  @endif