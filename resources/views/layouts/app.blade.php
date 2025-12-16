<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CRM FruitStand')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        /* Realistic Sticker Design */
        .sticker {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            position: relative;
            overflow: visible;
            transform: rotate(-5deg);
            animation: gentleBounce 3s ease-in-out infinite;
            filter: drop-shadow(0 8px 16px rgba(0,0,0,0.25));
        }

        .sticker::before {
            content: '';
            position: absolute;
            inset: -8px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            z-index: -1;
            animation: pulse 2s ease-in-out infinite;
        }

        .sticker::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            background: white;
            z-index: -1;
        }

        .sticker img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #FFD700;
            box-shadow: inset 0 2px 8px rgba(0,0,0,0.1);
        }

        @keyframes gentleBounce {
            0%, 100% { transform: rotate(-5deg) translateY(0px); }
            50% { transform: rotate(-5deg) translateY(-8px); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.8; }
            50% { opacity: 1; }
        }

        /* Enhanced Sidebar */
        .sidebar { 
            overflow-y: auto;
            background: linear-gradient(180deg, #047857 0%, #065f46 100%);
        }
        
        header { 
            position: sticky; 
            top: 0; 
            z-index: 10;
            background: linear-gradient(90deg, #ffffff 0%, #f9fafb 100%);
        }

        /* Improved Dropdown Styles */
        .dropdown {
            position: relative;
        }

        .dropdown-arrow {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dropdown:hover .dropdown-arrow {
            transform: rotate(180deg);
        }

        .dropdown-content {
            max-height: 0;
            overflow: hidden;
            padding-left: 2.5rem;
            opacity: 0;
            transform: translateY(-10px);
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                        opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                        transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dropdown:hover .dropdown-content {
            max-height: 300px;
            opacity: 1;
            transform: translateY(0);
            margin-top: 0.5rem;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 0.75rem;
            margin-bottom: 0.25rem;
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.9rem;
            opacity: 0;
            transform: translateX(-10px);
        }

        .dropdown:hover .dropdown-item {
            opacity: 1;
            transform: translateX(0);
        }

        .dropdown:hover .dropdown-item:nth-child(1) {
            transition-delay: 0.05s;
        }

        .dropdown:hover .dropdown-item:nth-child(2) {
            transition-delay: 0.1s;
        }

        .dropdown:hover .dropdown-item:nth-child(3) {
            transition-delay: 0.15s;
        }

        .dropdown-item:hover {
            background-color: #059669;
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        /* Nav Link Enhancements */
        .nav-link {
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: #FFD700;
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .nav-link:hover::before,
        .nav-link.active::before {
            transform: scaleY(1);
        }

        /* Logo Text Gradient */
        .logo-text {
            background: linear-gradient(135deg, #e2e7ecff 0%, #ebf0faff 50%, #bcc0c5ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        /* Scrollbar Styling */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 10px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }
    </style>
</head>
<body class="bg-gray-100">

<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="sidebar text-white w-72 flex-shrink-0 hidden md:flex flex-col p-6 shadow-2xl">
        
        <!-- Sticker Logo Section -->
        <div class="flex flex-col items-center mb-8">
            <div class="sticker flex items-center justify-center mb-4">
                <img src="https://i.pinimg.com/1200x/14/62/b5/1462b52e52d9d649230491b952f54916.jpg" 
                     alt="Durian Fruit">
            </div>
            <h1 class="text-2xl font-bold text-center logo-text bg-white px-4 py-2 rounded-lg shadow-md">
                CRM FruitStand
            </h1>
            <p class="text-xs text-green-200 mt-2 font-medium">Fruit Management System</p>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="flex-1 flex flex-col space-y-2">
            <!-- Dashboard - Visible to ALL roles -->
            <a href="{{ route('dashboard') }}" class="nav-link flex items-center p-3 rounded-lg hover:bg-green-600 hover:scale-105 transition shadow-sm {{ request()->routeIs('dashboard') ? 'bg-green-600 active' : '' }}">
                <span class="material-icons mr-3">dashboard</span> 
                <span class="font-medium">Dashboard</span>
            </a>
            
            @if(in_array(auth()->user()->role, ['admin', 'manager']))
                <!-- ADMIN & MANAGER - Inventory Section -->
                
               <!-- Inventory with Dropdown -->
<div class="dropdown">
    <a href="{{ route('inventory.index') }}" class="nav-link flex items-center p-3 rounded-lg hover:bg-green-600 hover:scale-105 transition shadow-sm {{ request()->routeIs('inventory.*') && !request()->routeIs('inventory.reports') && !request()->routeIs('inventory.audit') ? 'bg-green-600 active' : '' }}">
        <span class="material-icons mr-3">inventory_2</span> 
        <span class="font-medium">Inventory</span>
        <span class="material-icons ml-auto text-sm dropdown-arrow">expand_more</span>
    </a>
    <!-- Dropdown Content -->
    <div class="dropdown-content">
        <a href="{{ route('inventory.create') }}" class="dropdown-item {{ request()->routeIs('inventory.create') ? 'bg-green-600' : '' }}">
            <span class="material-icons mr-2" style="font-size: 18px;">add_box</span> Add Product
        </a>
        <a href="{{ route('stockins.index') }}" class="dropdown-item {{ request()->routeIs('stockins.*') ? 'bg-green-600' : '' }}">
            <span class="material-icons mr-2" style="font-size: 18px;">add_circle</span> Stock-In
        </a>
        <a href="{{ route('inventory.audit') }}" class="dropdown-item {{ request()->routeIs('inventory.audit') ? 'bg-green-600' : '' }}">
            <span class="material-icons mr-2" style="font-size: 18px;">history</span> 
            Audit Log
        </a>
        <a href="{{ route('inventory.reports') }}" class="dropdown-item {{ request()->routeIs('inventory.reports') ? 'bg-green-600' : '' }}">
            <span class="material-icons mr-2" style="font-size: 18px;">assessment</span> 
            Inventory Reports
        </a>
    </div>
</div>

                <!-- Supplier Transaction Dropdown -->
                <div class="dropdown">
                    <a href="#" class="nav-link flex items-center p-3 rounded-lg hover:bg-green-600 hover:scale-105 transition shadow-sm">
                        <span class="material-icons mr-3">local_shipping</span> 
                        <span class="font-medium">Supplier</span>
                        <span class="material-icons ml-auto text-sm dropdown-arrow">expand_more</span>
                    </a>
                    <div class="dropdown-content">
                        <a href="{{ route('suppliers.index') }}" class="dropdown-item {{ request()->routeIs('suppliers.*') ? 'bg-green-600' : '' }}">
                            <span class="material-icons mr-2" style="font-size: 18px;">list_alt</span> All Suppliers
                        </a>
                        <a href="{{ route('supplier.transactions') }}" class="dropdown-item {{ request()->routeIs('supplier.transactions') ? 'bg-green-600' : '' }}">
                            <span class="material-icons mr-2" style="font-size: 18px;">receipt_long</span> Transactions
                        </a>
                    </div>
                </div>
            @endif

           <!-- Archive Dropdown -->
            <div class="dropdown">
                <a href="#" 
                   class="nav-link flex items-center p-3 rounded-lg hover:bg-green-600 hover:scale-105 transition shadow-sm">
                    <span class="material-icons mr-3">inventory</span>
                    <span class="font-medium">Archive</span>
                    <span class="material-icons ml-auto text-sm dropdown-arrow">expand_more</span>
                </a>

                <div class="dropdown-content">
                    @if(auth()->user()->role === 'admin')
                        <!-- ADMIN sees both -->
                        <a href="{{ route('archive.suppliers') }}" 
                           class="dropdown-item {{ request()->routeIs('archive.suppliers') ? 'bg-green-600' : '' }}">
                            <span class="material-icons mr-2" style="font-size: 18px;">folder_special</span>
                            Supplier Archive
                        </a>

                        <a href="{{ route('archive.customers') }}" 
                           class="dropdown-item {{ request()->routeIs('archive.customers') ? 'bg-green-600' : '' }}">
                            <span class="material-icons mr-2" style="font-size: 18px;">folder_shared</span>
                            Customer Archive
                        </a>

                    @elseif(auth()->user()->role === 'manager')
                        <!-- MANAGER sees only Supplier Archive -->
                        <a href="{{ route('archive.suppliers') }}" 
                           class="dropdown-item {{ request()->routeIs('archive.suppliers') ? 'bg-green-600' : '' }}">
                            <span class="material-icons mr-2" style="font-size: 18px;">folder_special</span>
                            Supplier Archive
                        </a>

                    @elseif(auth()->user()->role === 'cashier')
                        <!-- CASHIER sees only Customer Archive -->
                        <a href="{{ route('archive.customers') }}" 
                           class="dropdown-item {{ request()->routeIs('archive.customers') ? 'bg-green-600' : '' }}">
                            <span class="material-icons mr-2" style="font-size: 18px;">folder_shared</span>
                            Customer Archive
                        </a>
                    @endif
                </div>
            </div>

           @if(in_array(auth()->user()->role, ['admin', 'cashier']))
                <!-- ADMIN & CASHIER - Sales Section -->
                
                <a href="{{ route('customers.index') }}" 
                   class="nav-link flex items-center p-3 rounded-lg hover:bg-green-600 hover:scale-105 transition shadow-sm 
                   {{ request()->routeIs('customers.*') ? 'bg-green-600 active' : '' }}">
                    <span class="material-icons mr-3">people</span> 
                    <span class="font-medium">Customers</span>
                </a>

                <a href="{{ route('sales.index') }}" 
                   class="nav-link flex items-center p-3 rounded-lg hover:bg-green-600 hover:scale-105 transition shadow-sm 
                   {{ request()->routeIs('sales.*') ? 'bg-green-600 active' : '' }}">
                    <span class="material-icons mr-3">sell</span> 
                    <span class="font-medium">Sales</span>
                </a>

                <a href="{{ route('sales.report') }}" 
                   class="nav-link flex items-center p-3 rounded-lg hover:bg-green-600 hover:scale-105 transition shadow-sm 
                   {{ request()->routeIs('sales.report') ? 'bg-green-600 active' : '' }}">
                    <span class="material-icons mr-3">bar_chart</span> 
                    <span class="font-medium">Sales Report</span>
                </a>
            @endif

            @if(auth()->user()->role === 'admin')
                <!-- ADMIN ONLY - User Management Section -->
                
                <!-- Divider -->
                <div class="border-t border-green-500 my-3 opacity-30"></div>
                
                <div class="text-xs text-green-200 px-3 py-2 font-bold uppercase tracking-wider flex items-center">
                    <span class="material-icons mr-2" style="font-size: 16px;">admin_panel_settings</span>
                    User Management
                </div>

                <!-- View All Users -->
                <a href="{{ route('users.index') }}" class="nav-link flex items-center p-3 rounded-lg hover:bg-green-600 hover:scale-105 transition shadow-sm {{ request()->routeIs('users.index') ? 'bg-green-600 active' : '' }}">
                    <span class="material-icons mr-3">manage_accounts</span> 
                    <span class="font-medium">All Users</span>
                </a>
                
                <!-- Create Manager with Dropdown -->
                <div class="dropdown">
                    <a href="{{ route('users.create-manager') }}" class="nav-link flex items-center p-3 rounded-lg hover:bg-green-600 hover:scale-105 transition shadow-sm {{ request()->routeIs('users.create-manager') || request()->routeIs('users.store-manager') ? 'bg-green-600 active' : '' }}">
                        <span class="material-icons mr-3">admin_panel_settings</span> 
                        <span class="font-medium">Managers</span>
                        <span class="material-icons ml-auto text-sm dropdown-arrow">expand_more</span>
                    </a>
                    <div class="dropdown-content">
                        <a href="{{ route('users.list-managers') }}" class="dropdown-item {{ request()->routeIs('users.list-managers') ? 'bg-green-600' : '' }}">
                            <span class="material-icons mr-2" style="font-size: 18px;">people</span> 
                            All Managers
                        </a>
                    </div>
                </div>
                
                <!-- Create Cashier with Dropdown -->
                <div class="dropdown">
                    <a href="{{ route('users.create-cashier') }}" class="nav-link flex items-center p-3 rounded-lg hover:bg-green-600 hover:scale-105 transition shadow-sm {{ request()->routeIs('users.create-cashier') || request()->routeIs('users.store-cashier') ? 'bg-green-600 active' : '' }}">
                        <span class="material-icons mr-3">person_add</span> 
                        <span class="font-medium">Cashiers</span>
                        <span class="material-icons ml-auto text-sm dropdown-arrow">expand_more</span>
                    </a>
                    <div class="dropdown-content">
                        <a href="{{ route('users.list-cashiers') }}" class="dropdown-item {{ request()->routeIs('users.list-cashiers') ? 'bg-green-600' : '' }}">
                            <span class="material-icons mr-2" style="font-size: 18px;">people</span> 
                            All Cashiers
                        </a>
                    </div>
                </div>
            @endif
        </nav>

        <!-- User Info & Logout Button at Bottom -->
        <div class="mt-auto pt-4 border-t border-green-500 border-opacity-30">
            <div class="bg-green-800 bg-opacity-50 rounded-lg p-3 mb-3 backdrop-blur-sm">
                <div class="flex items-center mb-2">
                    <div class="w-10 h-10 rounded-full bg-green-400 flex items-center justify-center text-green-900 font-bold text-lg mr-3">
                        {{ substr(auth()->user()->fname, 0, 1) }}{{ substr(auth()->user()->lname, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-semibold text-sm">{{ auth()->user()->fname }} {{ auth()->user()->lname }}</p>
                        <p class="text-xs text-green-300">{{ ucfirst(auth()->user()->role) }}</p>
                    </div>
                </div>
            </div>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center p-3 rounded-lg bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 transition shadow-lg text-sm font-semibold transform hover:scale-105">
                    <span class="material-icons mr-2" style="font-size: 20px;">logout</span> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-auto">

        <!-- Topbar -->
        <header class="shadow-md flex items-center justify-between px-6 py-4">
            <h2 class="text-xl font-bold text-gray-800">@yield('page_title', 'Dashboard')</h2>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-600">
                    <span class="material-icons text-gray-500 align-middle mr-1" style="font-size: 18px;">person</span>
                    Welcome, <span class="font-semibold text-green-700">{{ auth()->user()->fname }}</span>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="flex-1 p-6 bg-gradient-to-br from-gray-50 to-gray-100">
            @yield('content')
        </main>

    </div>
</div>

</body>
</html>