<img src={{ asset('logo.jpg') }}
    class="{{ request()->routeIs('login') || request()->routeIs('register') ? 'w-32 h-32' : 'w-12 h-12' }}"
    alt="logo" />
