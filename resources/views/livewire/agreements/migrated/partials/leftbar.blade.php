<!-- ========== Left Sidebar Start ========== -->
<div class="left-side-menu">

    <div class="h-100" data-simplebar>

        <!-- User box -->
        {{-- <div class="user-box text-center"> --}}

        {{-- <img src="/assets/images/logo-4.png" alt="user-img" title="Neguinho"
                class="rounded-circle img-thumbnail avatar-md"> --}}
        {{--
            <div class="dropdown">
                <a href="#" class="user-name dropdown-toggle h5 mt-2 mb-1 d-block" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    {{ Auth::user()->first_name }}
                </a>
                <div class="dropdown-menu user-pro-dropdown"> --}}

        <!-- item-->
        {{-- <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-user me-1"></i>
                        <span>My Account</span>
                    </a> --}}

        <!-- item-->
        {{-- <a href="{{ route('logout') }}" class="dropdown-item notify-item">
                        <i class="fe-log-out me-1"></i>
                        <span>Logout</span>
                    </a>

                </div>
            </div>

            <p class="text-muted left-user-info">Super User</p>

            <ul class="list-inline">

                <li class="list-inline-item">
                    <a href="{{ route('logout') }}">
                        <i class="mdi mdi-power"></i>
                    </a>
                </li>
            </ul>
        </div> --}}

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <ul id="side-menu">


                <li>
                    <a href="/ngn-admin/">
                        <i class="mdi mdi-view-dashboard-outline"></i>
                        <span> BACK </span>
                    </a>
                </li>

                {{-- Motorbikes --}}
                <li>
                    <a href="#sidebarExpages" data-bs-toggle="collapse">
                        <i class="mdi mdi-motorbike"></i>
                        <span> Motorbikes </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarExpages">
                        <ul class="nav-second-level">
                            <li>
                                <a href="/admin/renting/motorbikes/create">Add New</a>
                            </li>
                            <li>
                                <a href="/ngn-admin/ebike_manager">E-bikes Manager</a>
                            </li>
                            {{-- <li>
                                <a href="/admin/renting/motorbikes">List</a>
                            </li> --}}
                        </ul>
                    </div>
                </li>
                {{-- Motorbikes END --}}

                {{-- Booking Managment --}}
                <li>
                    <a href="#sidebarBookingMan" data-bs-toggle="collapse">
                        <i class="mdi mdi-account-multiple-plus-outline"></i>
                        <span> Booking Management </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarBookingMan">
                        <ul class="nav-second-level">
                            <li>
                                <a href="/admin/renting/bookings/new">New Booking</a>
                            </li>
                            <li>
                                <a href="/admin/renting/bookings">Bookings Management</a>
                            </li>
                            <li>
                                <a href="/admin/renting/bookings/inactive">Inactive Bookings</a>
                            </li>
                            <li>
                                <a href="/admin/renting/bookings/history">Booking Details</a>
                            </li>
                            <li>
                                <a href="/admin/renting/bookings/invoice-dates-all">Booking Invoice Dates</a>
                            </li>
                            <li>
                                <a href="/admin/renting/bookings/change-start-date">Change Booking Start Date</a>
                            </li>
                            {{-- <li>
                                <a href="#">Customer Invoices</a>
                            </li> --}}
                            <!-- <li>
                                <a href="/admin/renting/agreement">Agreement</a>
                            </li> -->
                        </ul>
                    </div>
                </li>
                {{-- Booking Managment END --}}

                {{-- SPARE PARTS >> --}}
                {{-- <li class="menu-title mt-2"> SPARE PARTS </li> --}}

                {{-- <li>
                    <a href="/admin/spareparts/">
                        <i class="mdi mdi-view-dashboard-outline"></i>
                        {{-- <span class="badge bg-success rounded-pill float-end">9+</span> --}}
                {{--  <span> Sp. Parts Dashboard </span>
                </a>
                </li> --}}
                {{--
                <li>
                    <a href="#sideparSparePartsPage" data-bs-toggle="collapse">
                        <i class="mdi mdi-file-multiple-outline"></i>
                        <span> Spare Parts </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sideparSparePartsPage">
                        <ul class="nav-second-level">
                            <li>
                                <a href="/admin/spareparts/create-pr">1. Purchase Request</a>
                            </li>
                            <li>
                                <a href="/admin/spareparts/view-all-pr">2. View Purchase Request</a>
                            </li>
                        </ul>
                    </div>
                </li> --}}
                {{-- SPARE PARTS << --}}

                {{-- Reports Menu >> --}}

                {{-- <li class="menu-title mt-2"> REPORTS </li>

                <li>
                    <a href="#sidebarReppages" data-bs-toggle="collapse">
                        <i class="mdi mdi-file-multiple-outline"></i>
                        <span> Renting Reports </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarReppages">
                        <ul class="nav-second-level">
                            <li>
                                <a href="/">1. Overview</a>
                            </li>
                            <li>
                                <a href="#">2. Available Now</a>
                            </li>
                            <li>
                                <a href="#">3. Occupied</a>
                            </li>
                            <li>
                                <a href="#">4. ...</a>
                            </li>
                        </ul>
                    </div>
                </li> --}}

                {{-- Reports Menu << --}}

                <!-- SYSTEM SETTINGS -->
                {{-- <li>
                    <a href="#paymentmethods" data-bs-toggle="collapse">
                        <i class="mdi mdi-file-multiple-outline"></i>
                        <span> Payment Methods </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="paymentmethods">
                        <ul class="nav-second-level">
                            <li>
                                <a href="#">Add New</a>
                            </li>
                            <li>
                                <a href="#">List</a>
                            </li>
                        </ul>
                    </div> --}}
                {{-- </li> <!-- END SYSTEM SETTINGS --> --}}

                {{-- Routa --}}
                {{-- <li class="menu-title mt-2"> ROTA </li> --}}
                <!-- SYSTEM SETTINGS -->
                {{-- <li>
                    <a href="#rota-menu" data-bs-toggle="collapse">
                        <i class="mdi mdi-file-multiple-outline"></i>
                        <span> ROTA </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="rota-menu">
                        <ul class="nav-second-level">
                            <li>
                                <a href="/admin/rotas-view">VIEW</a>
                            </li>
                        </ul>
                    </div>
                </li> --}}
                <!-- END SYSTEM SETTINGS -->

                {{-- Shop --}}
                {{-- <li class="menu-title mt-2"> Shop </li> --}}
                {{-- Shop Menu --}}
                {{-- <li>
                    <a href="#shop-menu" data-bs-toggle="collapse">
                        <i class="mdi mdi-file-multiple-outline"></i>
                        <span> MOTORBIKE FOR SALE </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="shop-menu">
                        <ul class="nav-second-level">
                            <li>
                                <a href="/admin/shop/add-for-sale">ADD FOR SALE</a>
                            </li>
                            <li>
                                <a href="/admin/shop/used-for-sale">LIST OF USED</a>
                            </li>
                            <li>
                                <a href="/admin/shop">SOLD</a>
                            </li>

                        </ul>
                    </div>
                </li> --}}
                <!-- END SYSTEM SETTINGS -->
                {{-- <li class="menu-title mt-2"> SYSTEM SETTINGS </li>

                <li>
                    <a href="#customer-link" data-bs-toggle="collapse">
                        <i class="mdi mdi-file-multiple-outline"></i>
                        <span> LOGS </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="customer-link">
                        <ul class="nav-second-level">
                            <li>
                                <a href="/admin/customers/upload-links">UPLOAD DOCS LOGS</a>
                            </li>
                            <li>
                                <a href="/admin/customers/agreements-links">AGREEMENT LINK LOGS</a>
                            </li>
                        </ul>
                    </div>
                </li> --}}

            </ul>
        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->
