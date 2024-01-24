<!-- resources/views/partials/journalist-sidebar.blade.php -->

       <!-- Sidebar -->
       <aside id="sidebar">
            <div class="h-100">
                <div class="sidebar-logo">
                    <a href="#">
                    <img src="/assets/img/logoWhite.png" alt="Your Logo" class="img-fluid" style=" max-width: 320px; margin-left: -50px">
                    </a>
                </div>
                <!-- Sidebar Navigation -->
                <ul class="sidebar-nav">
                    <li class="sidebar-header">
                        Tools & Components
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('journalist_dashboard') }}" class="sidebar-link">
                            <i class="fa-solid fa-sliders pe-2"></i>
                            Dashboard 
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#pages"
                            aria-expanded="false" aria-controls="pages">
                            <i class="fa-regular fa-file-lines pe-2"></i>
                            Manage Articles
                        </a>
                        <ul id="pages" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <li class="sidebar-item">
                                <a href="{{ route('journalist.add-articles') }}" class="sidebar-link">Add Articles</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{ route('journalist.pending') }}" class="sidebar-link">Pending Articles</a>
                            </li>
                            <li class="sidebar-item">
                                <a href="{{ route('journalist.published') }}" class="sidebar-link">Publised Articles</a>
                            </li>
                    
                        </ul>
                    </li>
                
                    <li class="sidebar-header">
                        Others
                    </li>
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link">
                            <i class="fa-solid fa-bell pe-2"></i>
                            Notifications
                        </a>
                    </li>
                </ul>
            </div>
        </aside>