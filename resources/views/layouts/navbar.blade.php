<div class="container-fluid">
    <nav class="navbar navbar-expand-sm navbar-light bg-light">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="/dashboard">W4RP</a>
        <ul class="navbar-nav mr-auto">
            @can('isAdmin')
            <li class="nav-item">
                <a class="nav-link" href="/dashboard/finances">Finances</a>
            </li>
            @endcan
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdoownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Moons</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/moons/display">Display Moons</a>
                    @can('isAdmin')
                    <a class="dropdown-item" href="/moons/addmoon">Add Moon</a>
                    <a class="dropdown-item" href="/moons/updatemoon">Update Moon</a>
                    @endcan
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdoownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Wiki</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/wiki/register">Registration</a>
                    <a class="dropdown-item" href="/wiki/changepassword">Change Password</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/logout">Logout</a>
            </li>
        </ul>
        <ul class="navbar-nav m1-auto">
            <li class="nav-item">
                <a class="nav-link">Right Link 1</a>
            </li>
        </ul>
    </nav>
</div>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary rounded">
<a class="navbar-brand" href="#">Navbar</a>
<ul class="navbar-nav mr-auto">
    <li class="nav-item active">
    <a class="nav-link">Left Link 1</a>
    </li>
    <li class="nav-item">
    <a class="nav-link">Left Link 2</a>
    </li>
</ul>
<ul class="navbar-nav ml-auto">
    <li class="nav-item">
    <a class="nav-link">Right Link 1</a>
    </li>
    <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">            Dropdown on Right</a>
    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="#">Action</a>
        <a class="dropdown-item" href="#">Another action with a lot of text inside of an item</a>
    </div>
    </li>
</ul>
</nav>