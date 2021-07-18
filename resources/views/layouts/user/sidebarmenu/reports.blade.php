@if(auth()->user()->hasPermission('fc.team'))
<li class="nav-item has-treeview">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-cubes"></i>
        <p>After Action Reports<br>
        <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
          <a href="/reports/display/all" class="nav-link">
            <i class="fas fa-cog nav-icon"></i>
            <p>Display Reports</p>
          </a>
      </li>
      <li class="nav-item">
          <a href="/reports/display/report/form" class="nav-link">
            <i class="fas fa-cog nav-icon"></i>
            <p>Report Form</p>
          </a>
      </li>
    </ul>
  </li>
@endif