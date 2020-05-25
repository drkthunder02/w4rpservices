@if(auth()->user()->hasRole('User') || auth()->user()->hasRole('Admin'))
<li class="nav-item has-treeview">
    <a href="#" class="nav-link">
      <i class="nav-icon far fa-building"></i>
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