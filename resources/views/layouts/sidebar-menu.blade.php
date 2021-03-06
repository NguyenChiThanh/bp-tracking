<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        @can('isPartnerUser')
        <li class="nav-item">
            <router-link to="/campaigns" class="nav-link">
                <i class="nav-icon fas fa-list orange"></i>
                <p>
                    Campaign
                </p>
            </router-link>
        </li>
        @endcan

        @can('isPMCUser')
        <li class="nav-item">
            <router-link to="/dashboard" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt blue"></i>
                <p>
                    Dashboard
                </p>
            </router-link>
        </li>

        <li class="nav-item">
            <router-link to="/company" class="nav-link">
                <i class="nav-icon fas fa-hospital-alt yellow"></i>
                <p>
                    Company
                </p>
            </router-link>
        </li>
        <li class="nav-item">
            <router-link to="/brand" class="nav-link">
                <i class="nav-icon fas fa-apple-alt pink"></i>
                <p>
                    Brand
                </p>
            </router-link>
        </li>

        <!--        <li class="nav-item">-->
        <!--            <router-link to="/brands" class="nav-link">-->
        <!--                <i class="nav-icon fas fa-heart purple"></i>-->
        <!--                <p>-->
        <!--                    Brands-->
        <!--                </p>-->
        <!--            </router-link>-->
        <!--        </li>-->


        <li class="nav-item">
            <router-link to="/campaigns" class="nav-link">
                <i class="nav-icon fas fa-list orange"></i>
                <p>
                    Campaign
                </p>
            </router-link>
        </li>

        <li class="nav-item">
            <router-link to="/stores" class="nav-link">
                <i class="nav-icon fas fa-store green"></i>
                <p>
                    Stores
                </p>
            </router-link>
        </li>

        <li class="nav-item">
            <router-link to="/channels" class="nav-link">
                <i class="nav-icon fas fa-columns pink"></i>
                <p>
                    Channels
                </p>
            </router-link>
        </li>

        <li class="nav-item">
            <router-link to="/positions" class="nav-link">
                <i class="nav-icon fas fa-icons pink"></i>
                <p>
                    Positions
                </p>
            </router-link>
        </li>
        @endcan

        @can('isMod')
        <li class="nav-item">
            <router-link to="/users" class="nav-link">
                <i class="fa fa-users nav-icon blue"></i>
                <p>Users</p>
            </router-link>
        </li>
        @endcan


        @can('isAdmin')
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-cog green"></i>
                <p>
                    Settings
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <router-link to="/utility" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                            Import/Export Result
                        </p>
                    </router-link>
                </li>
                <li class="nav-item">
                    <router-link to="/product/category" class="nav-link">
                        <i class="nav-icon fas fa-list-ol green"></i>
                        <p>
                            Category
                        </p>
                    </router-link>
                </li>
                <li class="nav-item">
                    <router-link to="/product/tag" class="nav-link">
                        <i class="nav-icon fas fa-tags green"></i>
                        <p>
                            Tags
                        </p>
                    </router-link>
                </li>

                <li class="nav-item">
                    <router-link to="/developer" class="nav-link">
                        <i class="nav-icon fas fa-cogs white"></i>
                        <p>
                            Developer
                        </p>
                    </router-link>
                </li>
            </ul>
        </li>

        @endcan


        <li class="nav-item">
            <a href="#" class="nav-link" onclick="event.preventDefault();
          document.getElementById('logout-form').submit();">
                <i class="nav-icon fas fa-power-off red"></i>
                <p>
                    {{ __('Logout') }}
                </p>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>
    </ul>
</nav>
