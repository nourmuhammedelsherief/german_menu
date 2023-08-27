<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{route('waiter.orders.index')}}" class="brand-link">
        <img src="{{asset('/uploads/img/logo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <span class="brand-text font-weight-light"> @lang('messages.control_panel') </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                @if(Auth::guard('waiter')->user()->logo != null)
                    <img src="{{asset('/uploads/restaurants/logo/' . Auth::guard('waiter')->user()->logo)}}"
                         class="img-circle elevation-2" alt="User Image">
                @else
                    <img src="{{asset('dist/img/user2-160x160.jpg')}}" class="img-circle elevation-2" alt="User Image">
                @endif
            </div>
            <div class="info">
                <a href="javascript:;" class="d-block">
                    <?php if (Auth::guard('waiter')->check()) {
                        echo Auth::guard('waiter')->user()->name;
                    } ?>
                </a>
            </div>
        </div>

    @php
        $employee = auth('waiter')->user();
       
    @endphp
    <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('waiter.orders.index') }}"
                        class="nav-link {{ (
                        isUrlActive('orders'))
                            ? 'active'
                            : '' }}">
                        <i class="fas fa-wine-glass nav-icon"></i>
                        <p>
                            @lang('dashboard.waiter_orders')
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="javascript:;"
                        class="nav-link ">
                       
                        <p>
                            <form action="{{route('waiter.logout')}}" method="post">
                            @csrf
                            <button class="btn btn-danger" style="width:100%;"> <i class="fas fa-sign-out-alt"></i> {{ trans('dashboard.logout') }}</button>
                            </form>
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
