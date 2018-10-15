<nav class="navbar navbar-expand-sm bg-light">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">W4RP</a>
        </div>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="/dashboard/profile">Profile</a>
            </li>
            @can('isAdmin')
            <li class="nav-item">
                <a class="nav-link" href="/dashboard/finances">Finances</a>
            </li>
            @endcan
            <li class="nav-item">
                <a class="nav-link" href="/dashboard/moons">Display Moons</a>
            </li>
            @can('isAdmin')
            <li class="nav-item">
                <a class="nav-link" href="/dashboard/addmoon">Add Moon</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/dashboard/updatemoon">Update Moon</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/dashboard/moonmine">Moon Mining</a>
            </li>
            @endcan
            <li class="nav-item">
                <a class="nav-link" href="/logout">Logout</a>
            </li>
        </ul>
    </div>
</nav>