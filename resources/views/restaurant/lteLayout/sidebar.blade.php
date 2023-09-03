<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('restaurant.home') }}" class="brand-link">
        <img src="{{ asset('/public/uploads/img/logo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light"> @lang('messages.control_panel') </span>
    </a>
    @php
        $user = Auth::guard('restaurant')->user();
    @endphp
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                @if ($user->logo != null)
                    <img src="{{ asset('/uploads/restaurants/logo/' . $user->logo) }}" class="img-circle elevation-2"
                        alt="User Image">
                @else
                    <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                        alt="User Image">
                @endif
            </div>
            <div class="info">
                <a href="javascript:;" disabled class="d-block">
                    @if (app()->getLocale() == 'ar')
                        <?php if (Auth::guard('restaurant')->check()) {
                            echo $user->name_ar;
                        } ?>
                    @else
                        <?php if (Auth::guard('restaurant')->check()) {
                            echo $user->name_en;
                        } ?>
                    @endif
                </a>
            </div>
        </div>
        @if ($user->archive == 'true')
            @php
                $url = 'https://api.whatsapp.com/send?phone=' . \App\Models\Setting::find(1)->active_whatsapp_number . '&text=';
                $content = 'Mein Konto ist archiviert. Ich möchte das Konto aktivieren';
            @endphp
            <a href="{{ $url . $content }}" class="btn btn-success" target="_blank">
                <i class="fab fa-whatsapp"></i>
                {{ trans('dashboard.contact_with_admin') }}
            </a>
        @elseif($user->subscription != null and $user->admin_activation == 'true' or $user->type == 'employee')
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    @php
                        $account_permission = \App\Models\RestaurantPermission::whereRestaurantId($user->id)
                            ->wherePermissionId(2)
                            ->first();
                    @endphp
                    @if ($user->type == 'restaurant' or $account_permission and $user->type == 'employee')
                        <li class="nav-item sidebar-title">
                            <p class=""><i class="nav-icon far fa-user"></i>  {{ trans('dashboard.account_settings') }}</p>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('RestaurantProfile') }}"
                                class="nav-link {{ strpos(URL::current(), '/restaurant/profile') !== false ? 'active' : '' }}">
                                <i class="nav-icon far fa-user"></i>
                                <p>
                                    @lang('messages.profile')
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/restaurant/barcode') }}"
                                class="nav-link {{ strpos(URL::current(), '/restaurant/barcode') !== false ? 'active' : '' }}">
                                <i class="nav-icon fa fa-barcode"></i>
                                <p>
                                    {{ app()->getLocale() == 'ar' ? 'Barcode-Druck' : 'Print Barcode' }}
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('show_restaurant_history', $user->type == 'employee' ? $user->restaurant_id : $user->id) }}"
                                class="nav-link {{ strpos(URL::current(), '/restaurant/history/' . $user->type == 'employee' ? $user->restaurant_id : $user->id) !== false ? 'active' : '' }}">
                                <i class="nav-icon fa fa-history"></i>
                                <span class="badge badge-info right">
                                    {{ \App\Models\History::whereRestaurantId($user->type == 'employee' ? $user->restaurant_id : $user->id)->count() }}
                                </span>
                                <p>
                                    @lang('messages.histories')
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('branches.index') }}"
                                class="nav-link {{ strpos(URL::current(), '/restaurant/branches') !== false ? 'active' : '' }}">
                                <i class="nav-icon far fa-flag"></i>
                                <span class="badge badge-info right">
                                    {{ \App\Models\Branch::whereRestaurantId($user->type == 'employee' ? $user->restaurant_id : $user->id)->whereIn('status', ['active', 'tentative'])->count() }}

                                </span>
                                <p>
                                    @lang('messages.branches')
                                </p>
                            </a>
                        </li>
                        {{--                        @if ($user->type == 'restaurant') --}}

                        {{--                            <li class="nav-item"> --}}
                        {{--                                <a href="{{url('/restaurant/restaurant_employees')}}" --}}
                        {{--                                   class="nav-link {{ strpos(URL::current(), '/restaurant/restaurant_employees') !== false ? 'active' : '' }}"> --}}
                        {{--                                    <i class="nav-icon fa fa-users"></i> --}}
                        {{--                                    <span class="badge badge-info right"> --}}
                        {{--                                        {{\App\Models\Restaurant::whereRestaurantId($user->id)->whereType('employee')->count()}} --}}
                        {{--                                    </span> --}}
                        {{--                                    <p> --}}
                        {{--                                        @lang('messages.restaurant_employees') --}}
                        {{--                                    </p> --}}
                        {{--                                </a> --}}
                        {{--                            </li> --}}
                        {{--                        @endif --}}
                    @endif

                    @php
                        $menu_permission = \App\Models\RestaurantPermission::whereRestaurantId($user->id)
                            ->wherePermissionId(4)
                            ->first();
                    @endphp
                    @if ($user->type == 'restaurant' or $menu_permission and $user->type == 'employee')
                        <li class="nav-item sidebar-title">
                            <p class=""> <i class="nav-icon fa fa-bars"></i> {{ trans('dashboard.side_3') }}</p>
                        </li>


                        <li class="nav-item">
                            <a href="{{ route('menu_categories.index') }}"
                                class="nav-link {{ strpos(URL::current(), '/restaurant/menu_categories') !== false ? 'active' : '' }}">
                                <i class="nav-icon fa fa-bars"></i>
                                <span class="badge badge-info right">
                                    {{ \App\Models\MenuCategory::whereRestaurantId($user->type == 'employee' ? $user->restaurant_id : $user->id)->count() }}
                                </span>
                                <p>
                                    @lang('messages.menu_categories')
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('modifiers.index') }}"
                                class="nav-link {{ strpos(URL::current(), '/restaurant/modifiers') !== false ? 'active' : '' }}">
                                <i class="nav-icon fa fa-plus"></i>
                                <span class="badge badge-info right">
                                    {{ \App\Models\Modifier::whereRestaurantId($user->type == 'employee' ? $user->restaurant_id : $user->id)->count() }}
                                </span>
                                <p>
                                    @lang('messages.modifiers')
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('additions.index') }}"
                                class="nav-link {{ strpos(URL::current(), '/restaurant/additions') !== false ? 'active' : '' }}">
                                <i class="nav-icon fa fa-plus"></i>
                                <span class="badge badge-info right">
                                    {{ \App\Models\Option::whereRestaurantId($user->type == 'employee' ? $user->restaurant_id : $user->id)->count() }}
                                </span>
                                <p>
                                    @lang('messages.options')
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('posters.index') }}"
                                class="nav-link {{ strpos(URL::current(), '/restaurant/posters') !== false ? 'active' : '' }}">
                                <i class="nav-icon fa fa-poo-storm"></i>
                                <span class="badge badge-info right">
                                    {{ \App\Models\RestaurantPoster::whereRestaurantId($user->type == 'employee' ? $user->restaurant_id : $user->id)->count() }}
                                </span>
                                <p>
                                    @lang('messages.posters')
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('products.index') }}"
                                class="nav-link {{ strpos(URL::current(), '/restaurant/products') !== false ? 'active' : '' }}">
                                <i class="nav-icon fa fa-project-diagram"></i>
                                <span class="badge badge-info right">
                                    {{ \App\Models\Product::whereRestaurantId($user->type == 'employee' ? $user->restaurant_id : $user->id)->count() }}
                                </span>
                                <p>
                                    @lang('messages.products')
                                </p>
                            </a>
                        </li>
                    @endif
                    @php
                        $sales_permission = \App\Models\RestaurantPermission::whereRestaurantId($user->id)
                            ->wherePermissionId(5)
                            ->first();
                    @endphp
                    @if ($user->type == 'restaurant' or $sales_permission and $user->type == 'employee')
                        <li class="nav-item sidebar-title">
                            <i class="nav-icon fa fa-users"></i>
                            <p class="">{{ trans('dashboard.side_4') }}</p>
                        </li>
                        <!--<li class="nav-item">-->
                        <!--    <a href="{{ route('my_restaurant_users') }}"-->
                        <!--       class="nav-link {{ strpos(URL::current(), '/restaurant/my_restaurant_users') !== false ? 'active' : '' }}">-->
                        <!--        <i class="nav-icon fa fa-users"></i>-->
                        <!--        <span class="badge badge-info right">-->
                        <!--        {{ \DB::select('select count(DISTINCT(user_id)) as count from orders where restaurant_id = ' . $user->id)[0]->count }}-->
                        <!--    </span>-->
                        <!--        <p>-->
                        <!--            @lang('messages.my_restaurant_users')-->
                        <!--        </p>-->
                        <!--    </a>-->
                        <!--</li>-->

                        <li
                            class="nav-item {{ strpos(URL::current(), '/restaurant/ads') !== false ? 'active' : '' }}">
                            <a href="{{ route('restaurant.ads.index') }}"
                                class="nav-link {{ strpos(URL::current(), '/restaurant/ads') !== false ? 'active' : '' }}">
                                <i class="fas fa-external-link-alt"></i>
                                <p>
                                    @lang('dashboard.ads')
                                </p>
                            </a>
                        </li>

                        <li
                            class="nav-item {{ strpos(URL::current(), '/restaurant/offers') !== false ? 'active' : '' }}">
                            <a href="{{ url('/restaurant/offers') }}"
                                class="nav-link {{ strpos(URL::current(), '/restaurant/offers') !== false ? 'active' : '' }}">
                                <i class="fas fa-gift"></i>
                                <p>
                                    @lang('messages.offers')
                                </p>
                            </a>
                        </li>
                        <li
                            class="nav-item {{ strpos(URL::current(), '/restaurant/sliders') !== false ? 'active' : '' }}">
                            <a href="{{ url('/restaurant/sliders') }}?type=home"
                                class="nav-link {{ (strpos(URL::current(), '/restaurant/sliders') and request('type') == 'home') !== false ? 'active' : '' }}">
                                <i class="fas fa-sliders-h"></i>
                                <p>
                                    @lang('messages.sliders')
                                </p>
                            </a>
                        </li>

                        {{-- rate --}}

                        <li
                            class="nav-item has-treeview {{ strpos(URL::current(), '/restaurant/feedback') !== false ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ strpos(URL::current(), '/restaurant/feedback') !== false ? 'active' : '' }}">
                                <i class="fas fa-comment-dots"></i>
                                <p>
                                    @lang('dashboard.client_rate')
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                {{-- rate --}}

                                <li class="nav-item">
                                    <a href="{{ route('restaurant.feedback.index') }}"
                                        class="nav-link {{ strpos(URL::current(), '/restaurant/feedbackx') !== false ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>
                                            @lang('dashboard.client_feedback')
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('restaurant/feedback/branch') }}"
                                        class="nav-link {{ strpos(URL::current(), '/restaurant/feedback/branchx') !== false ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>
                                            @lang('dashboard.branches')
                                        </p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{ url('restaurant/feedback/branch_setting') }}"
                                        class="nav-link {{ strpos(URL::current(), '/restaurant/feedback/branch_settingx') !== false ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>
                                            @lang('dashboard.feedback_setting')
                                        </p>
                                    </a>
                                </li>

                            </ul>
                        </li>
                        {{-- contact_us --}}
                        <li
                            class="nav-item has-treeview {{ (strpos(URL::current(), '/restaurant/contact_us') !== false or
                            isUrlActive('link_contact_us') or
                            strpos(URL::current(), '/restaurant/sliders') and request('type') == 'contact_us' or
                            strpos(URL::current(), '/restaurant/sliders') and request('type') == 'contact_us_client')
                                ? 'menu-open'
                                : '' }}">
                            <a href="#"
                                class="nav-link {{ (strpos(URL::current(), '/restaurant/contact_us') !== false or
                                isUrlActive('link_contact_us') or
                                strpos(URL::current(), '/restaurant/sliders') and request('type') == 'contact_us' or
                                strpos(URL::current(), '/restaurant/sliders') and request('type') == 'contact_us_client')
                                    ? 'active'
                                    : '' }}">
                                <i class="fas fa-share-square"></i>
                                <p>
                                    @lang('dashboard.bio_link')
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ url('restaurant/link_contact_us') }}"
                                        class="nav-link {{ Request::is('restaurant/link_contact_us') !== false ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>
                                            @lang('dashboard.link_contact_us')
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('restaurant/contact_us') }}"
                                        class="nav-link {{ Request::is('restaurant/contact_us') !== false ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>
                                            @lang('dashboard.default_link')
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item ">
                                    <a href="{{ url('/restaurant/sliders') }}?type=contact_us"
                                        class="nav-link {{ (strpos(URL::current(), '/restaurant/sliders') and request('type') == 'contact_us') !== false ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>
                                            @lang('dashboard.slider_contact_us')
                                        </p>
                                    </a>
                                </li>

                                <li class="nav-item ">
                                    <a href="{{ url('/restaurant/sliders') }}?type=contact_us_client"
                                        class="nav-link {{ (strpos(URL::current(), '/restaurant/sliders') and request('type') == 'contact_us_client') !== false ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>
                                            @lang('dashboard.slider_contact_us_client')
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('restaurant/contact_us/settings') }}"
                                        class="nav-link {{ strpos(URL::current(), '/restaurant/contact_us/settings') !== false ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>
                                            @lang('dashboard.default_link_settings')
                                        </p>
                                    </a>
                                </li>

                            </ul>
                        </li>

                    @endif
                    @php
                        $info_permission = \App\Models\RestaurantPermission::whereRestaurantId($user->id)
                            ->wherePermissionId(6)
                            ->first();
                    @endphp
                    @if ($user->type == 'restaurant' or $info_permission and $user->type == 'employee')
                        <li class="nav-item sidebar-title">
                            <i class="nav-icon fas fa-info"></i>
                            <p class="">{{ trans('dashboard.side_5') }}</p>
                        </li>
                        {{-- information --}}
                        <li class="nav-item">
                            <a href="{{ route('restaurant.home_icons.index') }}"
                                class="nav-link {{ strpos(URL::current(), '/restaurant/home_icons') !== false ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    @lang('dashboard.home_icons')
                                </p>
                            </a>
                        </li>
                        {{-- social --}}
                        <li class="nav-item">
                            <a href="{{ url('/restaurant/socials') }}"
                                class="nav-link {{ strpos(URL::current(), '/restaurant/socials') !== false ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    @lang('messages.socials')
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/restaurant/deliveries') }}"
                                class="nav-link {{ strpos(URL::current(), '/restaurant/deliveries') !== false ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    @lang('messages.deliveries')
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/restaurant/sensitivities') }}"
                                class="nav-link {{ strpos(URL::current(), '/restaurant/sensitivities') !== false ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    @lang('messages.sensitivities')
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ url('/restaurant/information') }}"
                                class="nav-link {{ strpos(URL::current(), '/restaurant/information') !== false ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    @lang('messages.information')
                                </p>
                            </a>
                        </li>
                        @if (auth('restaurant')->check() and auth('restaurant')->user()->enable_bank == 'true')
                            <li class="nav-item">
                                <a href="{{ route('restaurant.banks.index') }}"
                                    class="nav-link {{ isUrlActive('banks') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>
                                        @lang('messages.banks')
                                    </p>
                                </a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a href="{{ url('/restaurant/res_branches') }}"
                                class="nav-link {{ strpos(URL::current(), '/restaurant/res_branches') !== false ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    @lang('messages.branches_location')
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ url('/restaurant/my-information') }}"
                                class="nav-link {{ strpos(URL::current(), '/restaurant/my-information') !== false ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    @lang('messages.my_information')
                                </p>
                            </a>
                        </li>

                    @endif

                    {{-- <li class="nav-item">
                        <a href="{{ url('/restaurant/related_code') }}"
                           class="nav-link {{ strpos(URL::current(), '/restaurant/related_code') !== false ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>
                                @lang('dashboard.header_footer')
                            </p>
                        </a>
                    </li> --}}
                </ul>
            </nav>
        @else
            @php
                $url = 'https://api.whatsapp.com/send?phone=' . \App\Models\Setting::find(1)->active_whatsapp_number . '&text=';
                $content = 'Ich habe ein neues Konto bei Ihnen registriert und möchte die zur Aktivierung des Kontos erforderlichen Verfahren abschließen';
            @endphp
            <a href="{{ $url . $content }}" class="btn btn-success" target="_blank">
                <i class="fab fa-whatsapp"></i>
                {{ app()->getLocale() == 'ar' ? 'Um den Testzeitraum zu aktivieren, klicken Sie hier' : 'To Have The Tentative Period Click Here' }}
            </a>

        @endif
        <!-- Sidebar Menu -->

        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
