<!-- Navbar -->
<nav class="bg-white shadow-lg fixed top-0 z-50 w-full">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <img class="h-8 w-auto" src="https://picsum.photos/300/300" alt="Logo">
            </div>

            <!-- Desktop Menu (Center) -->
            <div class="hidden md:flex items-center justify-center flex-1">
                <div class="flex space-x-4">
                    <a href="{{route('home')}}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">my file</a>
                    <a href="{{route('upload')}}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Upload</a>
                    
                    <!-- Service Dropdown -->
                    {{-- <div class="relative">
                        <button id="desktopServicesButton" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center">
                            Services
                            <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="desktopServicesDropdown" class="hidden absolute z-50 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                            <div class="py-1">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Service 1</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Service 2</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Service 3</a>
                            </div>
                        </div>
                    </div> --}}

                    

                

                </div>
            </div>

            <!-- Profile Dropdown (Right) -->
            @if(isset($user_login['m_id']))
            <div class="hidden md:flex items-center">
                <div class="relative">
                    <button id="profileButton" class="flex items-center text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        <img class="h-8 w-8 rounded-full" src="https://picsum.photos/300/300" alt="Profile">
                        <span class="ml-2">{{ $user_login['m_fullname'] }}</span>
                        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                        <div class="py-1">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Your Profile</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Messages</a>
                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign out</a>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="hidden md:flex items-center space-x-3">
                <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Login</a>
                <a href="{{ route('register') }}" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md text-sm font-medium">Register</a>
            </div>
            @endif
            <!-- Mobile menu button -->
            <div class="flex items-center md:hidden">
                <button id="mobileMenuButton" class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobileMenu" class="hidden md:hidden">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{route('home')}}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">my file</a>
            <a href="{{route('upload')}}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">Upload</a>
            
            {{-- <!-- Mobile Services Dropdown -->
            <div class="relative">
                <button id="mobileServicesButton" class="w-full flex justify-between items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                    Services
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="mobileServicesDropdown" class="hidden px-4">
                    <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">Service 1</a>
                    <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">Service 2</a>
                    <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">Service 3</a>
                </div>
            </div> --}}

           

           

            <!-- Mobile Profile Section -->
            @if(isset($user_login['m_id']))
            <div class="border-t border-gray-200 pt-4">
                <button id="mobileProfileButton" class="w-full flex justify-between items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                    <div class="flex items-center">
                        <img class="h-8 w-8 rounded-full" src="https://picsum.photos/300/300" alt="Profile">
                        <span class="ml-3 text-base font-medium text-gray-700">{{ $user_login['m_fullname'] }}</span>
                    </div>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="mobileProfileDropdown" class="hidden mt-3 space-y-1">
                    <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">Your Profile</a>
                    <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">Settings</a>
                    <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">Messages</a>
                    <a href="{{ route('logout') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">Sign out</a>
                </div>
            </div>
            @else
            <div class="border-t border-gray-200 pt-4 px-3 space-y-1">
                <a href="{{ route('login') }}" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">Login</a>
                <a href="{{ route('register') }}" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium bg-blue-600 text-white hover:bg-blue-700">Register</a>
            </div>
            @endif
        </div>
    </div>
</nav>

<!-- jQuery Script -->
<script>
$(document).ready(function() {
    // Desktop dropdowns toggle
    $('#desktopServicesButton').click(function(e) {
        e.stopPropagation();
        $('#desktopServicesDropdown').toggleClass('hidden');
        // Close other desktop dropdowns
        $('#desktopProductsDropdown, #desktopResourcesDropdown, #profileDropdown').addClass('hidden');
    });

    $('#desktopProductsButton').click(function(e) {
        e.stopPropagation();
        $('#desktopProductsDropdown').toggleClass('hidden');
        // Close other desktop dropdowns
        $('#desktopServicesDropdown, #desktopResourcesDropdown, #profileDropdown').addClass('hidden');
    });

    $('#desktopResourcesButton').click(function(e) {
        e.stopPropagation();
        $('#desktopResourcesDropdown').toggleClass('hidden');
        // Close other desktop dropdowns
        $('#desktopServicesDropdown, #desktopProductsDropdown, #profileDropdown').addClass('hidden');
    });

    // Profile dropdown toggle
    $('#profileButton').click(function(e) {
        e.stopPropagation();
        $('#profileDropdown').toggleClass('hidden');
        // Close desktop dropdowns
        $('#desktopServicesDropdown, #desktopProductsDropdown, #desktopResourcesDropdown').addClass('hidden');
    });

    // Mobile menu toggle
    $('#mobileMenuButton').click(function() {
        $('#mobileMenu').toggleClass('hidden');
    });

    // Mobile dropdowns toggle
    $('#mobileServicesButton').click(function() {
        $('#mobileServicesDropdown').toggleClass('hidden');
        // Close other mobile dropdowns
        $('#mobileProductsDropdown, #mobileResourcesDropdown, #mobileProfileDropdown').addClass('hidden');
    });

    $('#mobileProductsButton').click(function() {
        $('#mobileProductsDropdown').toggleClass('hidden');
        // Close other mobile dropdowns
        $('#mobileServicesDropdown, #mobileResourcesDropdown, #mobileProfileDropdown').addClass('hidden');
    });

    $('#mobileResourcesButton').click(function() {
        $('#mobileResourcesDropdown').toggleClass('hidden');
        // Close other mobile dropdowns
        $('#mobileServicesDropdown, #mobileProductsDropdown, #mobileProfileDropdown').addClass('hidden');
    });

    $('#mobileProfileButton').click(function() {
        $('#mobileProfileDropdown').toggleClass('hidden');
        // Close other mobile dropdowns
        $('#mobileServicesDropdown, #mobileProductsDropdown, #mobileResourcesDropdown').addClass('hidden');
    });

    // Close all dropdowns when clicking outside
    $(document).click(function() {
        $('#desktopServicesDropdown, #desktopProductsDropdown, #desktopResourcesDropdown, #profileDropdown').addClass('hidden');
    });

    // Close mobile menu when clicking outside
    $(document).click(function(e) {
        if (!$(e.target).closest('#mobileMenu, #mobileMenuButton').length) {
            $('#mobileMenu').addClass('hidden');
        }
    });
});
</script>