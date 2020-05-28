<!-- Moon Admin -->
@if(auth()->user()->hasPermission('moon.admin'))
<li class="nav-item has-treeview">
  <a href="#" class="nav-link">
    <i class="nav-icon fas fa-tachometer-alt"></i>
    <p>
      Moon Admin<br>
      <i class="right fas fa-angle-left"></i>
    </p>
  </a>
  <ul class="nav nav-treeview">
    <li class="nav-item">
      <a href="/moons/admin/display/rentals" class="nav-link">
        <i class="far fa-circle nav-icon"></i>
        <p>Display Moons</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="/moons/admin/updatemoon" class="nav-link">
        <i class="far fa-circle nav-icon"></i>
        <p>Update Moon</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="/moons/admin/display/request" class="nav-link">
        <i class="far fa-circle nav-icon"></i>
        <p>Moon Request</p>
      </a>
    </li>
  </ul>
</li>
@endif
<!-- End Moon Admin -->