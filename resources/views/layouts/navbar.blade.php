<nav class="navbar navbar-expand-sm bg-light">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">W4RP</a>
        </div>
        <ul class="navbar-nav">
            @can('isAdmin')
            <li class="nav-item">
                <a class="nav-link" href="/dashboard/finances">Finances</a>
            </li>
            @endcan
            <li class="nav-item">
                <a class="nav-link" href="/moons/display">Display Moons</a>
            </li>
            @can('isAdmin')
            <li class="nav-item">
                <a class="nav-link" href="/moons/addmoon">Add Moon</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/moons/updatemoon">Update Moon</a>
            </li>
            @endcan
            <li class="nav-item">
                <a class="nav-link" href="/wiki/register">Wiki Registration</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/wiki/changepassword">Wiki Password Change</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/logout">Logout</a>
            </li>
        </ul>
    </div>
</nav>