<header id="fh5co-header-section" class="sticky-banner">
    <div class="container">
        <div class="nav-header">
            <a href="#" class="js-fh5co-nav-toggle fh5co-nav-toggle dark"><i></i></a>
            <h1 id="fh5co-logo">
                <a href="{{ route('home') }}"><i class="icon-home"></i>1-N<span>West</span></a>
            </h1>
            <nav id="fh5co-menu-wrap" role="navigation">
                <ul class="sf-menu" id="fh5co-primary-menu">
                    <li class="{{ Request::is('/') ? 'active' : '' }}"><a href="{{ route('home') }}">Home</a></li>
<li class="{{ Request::is('properties*') ? 'active' : '' }}">
                        <a href="{{ route('properties') }}" class="fh5co-sub-ddown">Apartments</a>
                        <ul class="fh5co-sub-menu">
                            <li><a href="{{ route('properties') }}">Family</a></li>
                        </ul>
                    </li>
                    <li class="{{ Request::is('agent') ? 'active' : '' }}"><a href="{{ route('agent') }}">Staff</a></li>
                    <li class="{{ Request::is('blog') ? 'active' : '' }}"><a href="{{ route('blog') }}">Blog</a></li>
                    <li class="{{ Request::is('contact') ? 'active' : '' }}"><a href="{{ route('contact') }}">Contact</a></li>
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @endguest
                    @if (Auth::check())
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                document.getElementById('logout-form').submit()"
                            class="nav-link">
                            {{ Auth::user()->name }}
                        </a>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</header>
