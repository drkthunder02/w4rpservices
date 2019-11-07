<div class="container-fluid">
    <nav class="navbar navbar-expand-sm navbar-light bg-light">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="/dashboard">W4RP</a>
        <ul class="navbar-nav mr-auto">
            @if(auth()->user()->hasRole('User') || auth()->user()->hasRole('Renter') || auth()->user()->hasRole('Admin'))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdoownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Moons</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    @if(auth()->user()->hasRole('User') || auth()->user()->hasRole('Renter'))
                    <a class="dropdown-item" href="/moons/display">Display Moons</a>
                    <a class="dropdown-item" href="/moons/display/form/worth">Moon Worth</a>
                    @endif
                    @if(auth()->user()->hasRole('Admin'))
                    <a class="dropdown-item" href="/moons/admin/display">Display Moons</a>
                    <a class="dropdown-item" href="/moons/display/form/worth">Moon Worth</a>
                    <a class="dropdown-item" href="/moons/admin/updatemoon">Update Moon</a>
                    <a class="dropdown-item" href="/moons/admin/journal">Journal</a>
                    @endif
                    @if(auth()->user()->hasPermission('logistics.manager'))
                    <a class="dropdown-item" href="/moons/logistics/display">Moons for Logistics</a>
                    @endif
                </div>
            </li>
            @endif
            @if(auth()->user()->hasRole('User') || auth()->user()->hasRole('Admin'))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropDownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">SRP</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/srp/form/display">SRP Form</a>
                    <a class="dropdown-item" href="/srp/display/costcodes">Cost Codes</a>
                    @if(auth()->user()->hasPermission('srp.admin'))
                    <a class="dropdown-item" href="/srp/admin/display">SRP Admin Dashboard</a>
                    <a class="dropdown-item" href="/srp/admin/statistics">SRP Statistics</a>
                    <a class="dropdown-item" href="/srp/admin/costcodes/display">SRP Admin Cost Codes</a>
                    <a class="dropdown-item" href="/srp/admin/display/history">SRP History</a>
                    @endif
                </div>
            </li>
            @endif
            @if(auth()->user()->hasRole('User') || auth()->user()->hasRole('Admin'))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropDownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Contracts</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/contracts/display/all">Display All Contracts</a>
                    <a class="dropdown-item" href="/contracts/display/public">Display Public Contracts</a>
                    <a class="dropdown-item" href="/contracts/display/private">Display Private Contracts</a>
                    @if(auth()->user()->hasPermission('contract.admin'))
                    <a class="dropdown-item" href="/contracts/admin/display">Admin Dashboard</a>
                    <a class="dropdown-item" href="/contracts/admin/new">New Contract</a>
                    @endif
                </div>
            </li>
            @endif
            @if(auth()->user()->hasRole('User') || auth()->user()->hasRole('Admin'))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropDownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Structures</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    @if(auth()->user()->hasPermission('fc.team'))
                    <a class="dropdown-item" href="/structures/display/requests">Display Requests</a>
                    @endif
                    <a class="dropdown-item" href="/structures/display/form">Request Form</a>
                </div>
            </li>
            @endif
            @if(auth()->user()->hasRole('User') || auth()->user()->hasRole('Admin'))
                <a class="nav-link" href="/logistics/fuel/structures">Jump Gate Fuel</a>
            @endif
            @if(auth()->user()->hasRole('User') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Renter'))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Wiki</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="https://www.w4rp.space">Wiki</a>
                    <a class="dropdown-item" href="/wiki/register">Registration</a>
                    <a class="dropdown-item" href="/wiki/changepassword">Change Password</a>
                </div>
            </li>
            @endif
            @if(auth()->user()->hasRole('User') || auth()->user()->hasRole('Admin'))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Others</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="https://buyback.w4rp.space">Buyback Program</a>
                </div>
            </li>
            @endif
        </ul>
        <ul class="navbar-nav m1-auto">
            <li class="nav-item">
                <a class="nav-link" href="/scopes/select">Add Esi Scopes</a>
            </li>
            @if(auth()->user()->hasRole('Admin'))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropDownMenuLink" role="button" data-toggle="dropdown" aria-haspop="true" aria-expanded="false">Admin</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/admin/dashboard/users">Users</a>
                    <a class="dropdown-item" href="/admin/dashboard/taxes">Taxes</a>
                    <a class="dropdown-item" href="/admin/dashboard/logins">Allowed Logins</a>
                    <a class="dropdown-item" href="/admin/dashboard/purgewiki">Wiki Purge</a>
                </div>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link" href="/profile">Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/logout">Logout</a>
            </li>
        </ul>
    </nav>
</div>