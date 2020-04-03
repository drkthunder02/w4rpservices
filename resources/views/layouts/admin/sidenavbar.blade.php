
<nav class="col-md-2 d-none d-md-block bg-light sidebar">
    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            @if(auth()->user()->hasRole('Admin'))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropDownMenuLink" role="button" data-toggle="dropdown" aria-haspop="true" aria-expanded="false">General Admin</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/admin/dashboard/users">Users</a>
                    <a class="dropdown-item" href="/admin/dashboard/taxes">Taxes</a>
                    <a class="dropdown-item" href="/admin/dashboard/logins">Allowed Logins</a>
                    <a class="dropdown-item" href="/admin/dashboard/wiki">Wiki</a>
                    <a class="dropdown-item" href="/admin/dashboard/journal">Wallet Journal</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropDownMenuLink" role="button" data-toggle="dropdown" aria-haspop="true" aria-expanded="false">Flex Structures</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/flex/display">Display</a>
                    <a class="dropdown-item" href="/flex/display/add">Add</a>
                </div>
            </li>
            @endif
            @if(auth()->user()->hasPermission('moon.admin'))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropDownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Moon Admin</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/moons/admin/display/rentals">Display Moons</a>
                    <a class="dropdown-item" href="/moons/admin/updatemoon">Update Moon</a>
                    <a class="dropdown-item" href="/moons/admin/display/request">Moon Request</a>
                </div>
            </li>
            @endif
            @if(auth()->user()->hasPermission('srp.admin'))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropDownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">SRP Admin</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/srp/admin/display">SRP Admin Dashboard</a>
                    <a class="dropdown-item" href="/srp/admin/statistics">SRP Statistics</a>
                    <a class="dropdown-item" href="/srp/admin/costcodes/display">SRP Admin Cost Codes</a>
                    <a class="dropdown-item" href="/srp/admin/display/history">SRP History</a>
                </div>
            </li>
            @endif
            @if(auth()->user()->hasPermission('contract.admin'))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropDownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Contract Admin</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/contracts/admin/display">Admin Dashboard</a>
                    <a class="dropdown-item" href="/contracts/admin/new">New Contract</a>
                </div>
            </li>
            @endif
        </ul>
    </div>
</nav>
