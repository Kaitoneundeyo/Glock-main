<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper" style="overflow-y: auto;">
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
                    <i class="fas fa-fire"></i><span>DASHBOARD</span>
                </a>
            </li>

            <li class="menu-header">Menu</li>
            <li class="{{ request()->routeIs('tampil.index') ? 'active' : '' }}">
                <a href="{{ route('tampil.index') }}" class="nav-link">
                    <i class="fas fa-home"></i><span>HOME</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('gambar.index') ? 'active' : '' }}">
                <a href="{{ route('coba.index') }}" class="nav-link">
                    <i class="fas fa-image"></i><span>KERANJANG</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('user.index') ? 'active' : '' }}">
                <a href="{{ route('user.index') }}" class="nav-link">
                    <i class="fas fa-users"></i><span>USER</span>
                </a>
            </li>
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
            <li class="{{ request()->routeIs('stokmasuk.index') ? 'active' : '' }}">
                <a href="{{ route('stokmasuk.index') }}" class="nav-link">
                    <i class="fas fa-arrow-down"></i><span>STOK MASUK</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('harga.index') ? 'active' : '' }}">
                <a href="{{ route('harga.index') }}" class="nav-link">
                    <i class="fas fa-dollar-sign"></i><span>HARGA</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('gambar.index') ? 'active' : '' }}">
                <a href="{{ route('gambar.index') }}" class="nav-link">
                    <i class="fas fa-image"></i><span>GAMBAR PRODUK</span>
                </a>
            </li>
            <li>
                <a href="{{ route('logout') }}" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i><span>LOGOUT</span>
                </a>
            </li>
        </ul>
    </aside>
</div>

