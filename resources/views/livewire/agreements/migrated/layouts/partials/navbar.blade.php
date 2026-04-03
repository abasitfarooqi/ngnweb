<header class="container mb-3">
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">NGN</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">DASHBOARD</a>
                    </li>
                    <li>
                        <a class="nav-link" href="/users">CLIENTS</a>
                    </li>
                    <li>
                        <a class="nav-link" href="/motorcycles">MOTORCYCLES</a>
                    </li>
                    <li>
                        <a class="nav-link" href="/rentalpayments">PAYMENTS</a>
                    </li>

                </ul>
                <!-- <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form> -->
                <div class="d-flex" style="padding-left: 5px;">
                    @auth
                        {{ auth()->user()->name }}
                        <div class="text-end">
                            <a href="{{ route('logout.perform') }}" class="btn btn-outline-danger me-2">Logout</a>
                        </div>
                    @endauth

                    @guest
                        <div class="text-end">
                            <a href="{{ route('login.perform') }}" class="btn btn-outline-danger me-2">Login</a>
                            <a href="{{ route('register.perform') }}" class="btn btn-warning">Sign-up</a>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>
</header>
