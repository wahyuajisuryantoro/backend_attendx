<header class="topbar">
    <div class="with-vertical">
        <nav class="navbar navbar-expand-lg p-0">
            <ul class="navbar-nav">
                <li class="nav-item nav-icon-hover-bg rounded-circle d-flex">
                    <a class="nav-link  sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                        <iconify-icon icon="solar:hamburger-menu-line-duotone" class="fs-6"></iconify-icon>
                    </a>
                </li>
                <li class="nav-item d-none d-xl-flex nav-icon-hover-bg rounded-circle">
                    <a class="nav-link" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <iconify-icon icon="solar:magnifer-linear" class="fs-6"></iconify-icon>
                    </a>
                </li>
            </ul>
            <div class="d-block d-lg-none py-9 py-xl-0">
                <img src="{{ asset('assets/images/logos/logo.png') }}" alt="matdash-img" />
            </div>
            <a class="navbar-toggler p-0 border-0 nav-icon-hover-bg rounded-circle" href="javascript:void(0)"
                data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                aria-label="Toggle navigation">
                <iconify-icon icon="solar:menu-dots-bold-duotone" class="fs-6"></iconify-icon>
            </a>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="d-flex align-items-center justify-content-between">
                    <ul class="navbar-nav flex-row mx-auto ms-lg-auto align-items-center justify-content-center">
                        <li class="nav-item dropdown">
                            <a href="javascript:void(0)"
                                class="nav-link nav-icon-hover-bg rounded-circle d-flex d-lg-none align-items-center justify-content-center"
                                type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar"
                                aria-controls="offcanvasWithBothOptions">
                                <iconify-icon icon="solar:sort-line-duotone" class="fs-6"></iconify-icon>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link moon dark-layout nav-icon-hover-bg rounded-circle"
                                href="javascript:void(0)">
                                <iconify-icon icon="solar:moon-line-duotone" class="moon fs-6"></iconify-icon>
                            </a>
                            <a class="nav-link sun light-layout nav-icon-hover-bg rounded-circle"
                                href="javascript:void(0)" style="display: none">
                                <iconify-icon icon="solar:sun-2-line-duotone" class="sun fs-6"></iconify-icon>
                            </a>
                        </li>
                        <li class="nav-item d-block d-xl-none">
                            <a class="nav-link nav-icon-hover-bg rounded-circle" href="javascript:void(0)"
                                data-bs-toggle="modal" data-bs-target="#exampleModal">
                                <iconify-icon icon="solar:magnifer-line-duotone" class="fs-6"></iconify-icon>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="javascript:void(0)" id="drop1" aria-expanded="false">
                                <div class="d-flex align-items-center gap-2 lh-base">
                                    <img src="{{ asset('assets/images/profile/user-2.jpg') }}" class="rounded-circle"
                                        width="35" height="35" alt="matdash-img" />
                                    <iconify-icon icon="solar:alt-arrow-down-bold" class="fs-2"></iconify-icon>
                                </div>
                            </a>
                            <div class="dropdown-menu profile-dropdown dropdown-menu-end dropdown-menu-animate-up"
                                aria-labelledby="drop1">
                                <div class="position-relative px-4 pt-3 pb-2">
                                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom gap-6">
                                        <img src="{{ asset('assets/images/profile/user-2.jpg') }}"
                                            class="rounded-circle" width="56" height="56" alt="matdash-img" />
                                            <div>
                                              @if(Auth::check() && Auth::user()->is_admin)
                                                  <h5 class="mb-0 fs-12">
                                                      {{ Auth::user()->username }} <span class="text-success fs-11">Admin</span>
                                                  </h5>
                                                  <p class="mb-0 text-dark">
                                                      {{ Auth::user()->email }}
                                                  </p>
                                              @else
                                                  <p class="mb-0 text-dark">Not an admin</p>
                                              @endif
                                          </div>                                          
                                    </div>
                                    <div class="message-body">
                                        <a href="{{ route('account-settings.index') }}"
                                            class="p-2 dropdown-item h6 rounded-1">
                                            Account Settings
                                        </a>
                                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                          @csrf
                                          <a href="javascript:void(0)" class="p-2 dropdown-item h6 rounded-1" onclick="this.closest('form').submit();">
                                              Sign Out
                                          </a>
                                      </form>
                                      
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <div class="app-header with-horizontal">
        <nav class="navbar navbar-expand-xl container-fluid p-0">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item d-flex d-xl-none">
                    <a class="nav-link sidebartoggler nav-icon-hover-bg rounded-circle" id="sidebarCollapse"
                        href="javascript:void(0)">
                        <iconify-icon icon="solar:hamburger-menu-line-duotone" class="fs-7"></iconify-icon>
                    </a>
                </li>
                <li class="nav-item d-none d-xl-flex align-items-center">
                    <a href="{{ route('dashboard') }}" class="text-nowrap nav-link">
                        <img src="{{asset('assets/images/logos/logo.png')}}" alt="matdash-img" />
                    </a>
                </li>
                <li class="nav-item d-none d-xl-flex align-items-center nav-icon-hover-bg rounded-circle">
                    <a class="nav-link" href="javascript:void(0)" data-bs-toggle="modal"
                        data-bs-target="#exampleModal">
                        <iconify-icon icon="solar:magnifer-linear" class="fs-6"></iconify-icon>
                    </a>
                </li>
            </ul>
            <div class="d-block d-xl-none">
                <a href="{{ route('dashboard') }}" class="text-nowrap nav-link">
                    <img src="{{ asset('assets/images/logos/logo.png') }}" alt="matdash-img" />
                </a>
            </div>
            <a class="navbar-toggler nav-icon-hover p-0 border-0 nav-icon-hover-bg rounded-circle"
                href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="p-2">
                    <i class="ti ti-dots fs-7"></i>
                </span>
            </a>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="d-flex align-items-center justify-content-between px-0 px-xl-8">
                    <ul class="navbar-nav flex-row mx-auto ms-lg-auto align-items-center justify-content-center">

                        <li class="nav-item">
                            <a class="nav-link nav-icon-hover-bg rounded-circle moon dark-layout"
                                href="javascript:void(0)">
                                <iconify-icon icon="solar:moon-line-duotone" class="moon fs-6"></iconify-icon>
                            </a>
                            <a class="nav-link nav-icon-hover-bg rounded-circle sun light-layout"
                                href="javascript:void(0)" style="display: none">
                                <iconify-icon icon="solar:sun-2-line-duotone" class="sun fs-6"></iconify-icon>
                            </a>
                        </li>
                        <li class="nav-item d-block d-xl-none">
                            <a class="nav-link nav-icon-hover-bg rounded-circle" href="javascript:void(0)"
                                data-bs-toggle="modal" data-bs-target="#exampleModal">
                                <iconify-icon icon="solar:magnifer-line-duotone" class="fs-6"></iconify-icon>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="javascript:void(0)" id="drop1" aria-expanded="false">
                                <div class="d-flex align-items-center gap-2 lh-base">
                                    <img src="{{asset('assets/images/profile/user-2.jpg')}}" class="rounded-circle"
                                        width="35" height="35" alt="matdash-img" />
                                    <iconify-icon icon="solar:alt-arrow-down-bold" class="fs-2"></iconify-icon>
                                </div>
                            </a>
                            <div class="dropdown-menu profile-dropdown dropdown-menu-end dropdown-menu-animate-up"
                                aria-labelledby="drop1">
                                <div class="position-relative px-4 pt-3 pb-2">
                                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom gap-6">
                                      <div>
                                        @if(Auth::check() && Auth::user()->is_admin)
                                            <h5 class="mb-0 fs-12">
                                                {{ Auth::user()->username }} <span class="text-success fs-11">Admin</span>
                                            </h5>
                                            <p class="mb-0 text-dark">
                                                {{ Auth::user()->email }}
                                            </p>
                                        @else
                                            <p class="mb-0 text-dark">Not an admin</p>
                                        @endif
                                    </div>                                    
                                    </div>
                                    <div class="message-body">
                                        <a href="{{ route('account-settings.index') }}"
                                            class="p-2 dropdown-item h6 rounded-1">
                                            Account Settings
                                        </a>
                                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                          @csrf
                                          <a href="javascript:void(0)" class="p-2 dropdown-item h6 rounded-1" onclick="this.closest('form').submit();">
                                              Sign Out
                                          </a>
                                      </form>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</header>
