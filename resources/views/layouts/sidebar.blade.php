<div class="main-sidebar sidebar-style-2 overflow-y-auto">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="#">Raymuna</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="#">RY</a>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-header">DASHBOARD</li>
            <li class="{{ request()->routeIs('home.index') ? 'active' : '' }}">
                <a href="{{ route('home.index') }}" class="nav-link">
                    <i class="fas fa-chart-line"></i><span>DASHBOARD</span>
                </a>
            </li>

            <li class="menu-header">Menu</li>
            @if(auth()->user()->role == 'kasir' || auth()->user()->role == 'kepala_gudang')
                <li class="{{ request()->routeIs('laporan.index') ? 'active' : '' }}">
                    <a href="{{ route('laporan.index') }}" class="nav-link">
                        <i class="fas fa-file-alt"></i><span>LAPORAN</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('order.index') ? 'active' : '' }}">
                    <a href="{{ route('order.index') }}" class="nav-link">
                        <i class="fas fa-clipboard-list"></i><span>ORDERAN MASUK</span>
                    </a>
                </li>
            @endif
            <li class="{{ request()->routeIs('tampil.index') ? 'active' : '' }}">
                <a href="{{ route('tampil.index') }}" class="nav-link">
                    <i class="fas fa-home"></i><span>BERANDA</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('coba.index') ? 'active' : '' }}">
                <a href="{{ route('coba.index') }}" class="nav-link flex items-center gap-2">
                    <div class="relative">
                        <i class="fas fa-shopping-basket"></i>
                        @if($cartCount > 0)
                            <span
                                class="absolute -top-2 -right-2 bg-red-600 text-white text-[10px] font-bold rounded-full min-w-[18px] h-5 px-1 flex items-center justify-center text-center text-xs shadow">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </div>
                    <span>KERANJANG</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('checkout.transactions') ? 'active' : '' }}">
                <a href="{{ route('checkout.transactions') }}" class="nav-link">
                    <i class="fas fa-money-bill-wave"></i><span>TRANSAKSI</span>
                </a>
            </li>
            @if(auth()->user()->role == 'admin_gudang' || auth()->user()->role == 'kepala_gudang')

                <li class="{{ request()->routeIs('kategori.index') ? 'active' : '' }}">
                    <a href="{{ route('kategori.index') }}" class="nav-link">
                        <i class="fas fa-tags"></i><span>KATEGORI</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('produk.index') ? 'active' : '' }}">
                    <a href="{{ route('produk.index') }}" class="nav-link">
                        <i class="fas fa-box"></i><span>PRODUK</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('stokmasuk.index') ? 'active' : '' }}">
                    <a href="{{ route('stokmasuk.index') }}" class="nav-link">
                        <i class="fas fa-arrow-down"></i><span>STOK MASUK</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('stokkeluar.index') ? 'active' : '' }}">
                    <a href="{{ route('stokkeluar.index') }}" class="nav-link">
                        <i class="fas fa-undo"></i><span>STOK KELUAR</span>
                    </a>
                </li>
            @endif
            @if(auth()->user()->role == 'kepala_gudang')
                <li class="{{ request()->routeIs('user.index') ? 'active' : '' }}">
                    <a href="{{ route('user.index') }}" class="nav-link">
                        <i class="fas fa-users"></i><span>PENGGUNA</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('supplier.index') ? 'active' : '' }}">
                    <a href="{{ route('supplier.index') }}" class="nav-link">
                        <i class="fas fa-truck"></i><span>SUPPLIER</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('invoice.index') ? 'active' : '' }}">
                    <a href="{{ route('invoice.index') }}" class="nav-link">
                        <i class="fas fa-file-invoice"></i><span>INVOICE</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('gambar.index') ? 'active' : '' }}">
                    <a href="{{ route('gambar.index') }}" class="nav-link">
                        <i class="fas fa-image"></i><span>GAMBAR PRODUK</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('harga.index') ? 'active' : '' }}">
                    <a href="{{ route('harga.index') }}" class="nav-link">
                        <i class="fas fa-dollar-sign"></i><span>HARGA</span>
                    </a>
                </li>
            @endif

            <li>
                <a href="{{ route('logout') }}" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i><span>LOGOUT</span>
                </a>
            </li>
        </ul>
    </aside>
</div>
