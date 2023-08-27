@extends('admin.lteLayout.master')

@section('title')
    @if ($type != 'online')
        @lang('dashboard.attendances')
    @else
        {{ trans('dashboard.attendances_online') }}
    @endif
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">
    <!-- Theme style -->
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        @if ($type != 'online')
                            @lang('dashboard.attendances')
                        @else
                            {{ trans('dashboard.attendances_online') }}
                        @endif
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/admin/home') }}">
                                @lang('messages.control_panel')
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{ route('admin.attendance.index') }}"></a>
                            @lang('dashboard.attendances')
                        </li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    @include('flash::message')
    <section class="content">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <!-- /.card-header -->
                    <div class="card-body">
                        @if (auth('admin')->user()->role == 'admin')
                            <div class="alert alert-secondary text-center">عدد الموظفين الاونلاين: {{ $employeeOnline }}
                            </div>
                        @endif
                        <div class="filter" style="margin: 30px 0;">
                            <form action="" method="GET">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="">{{ trans('dashboard.employee') }}</label>
                                        <select name="admin_id" id="admin_id" class="select2 form-control">
                                            <option value="" selected>الكل</option>
                                            @foreach ($admins as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ request('admin_id') == $item->id ? 'selected' : '' }}>
                                                    {{ $item->name }} ( {{ trans('dashboard.' . $item->role) }} )
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- year --}}
                                    <div class="form-group col-md-2">
                                        <label class="control-label"> @lang('dashboard.year') </label>
                                        <select name="year" id="year" class="select2 form-control">
                                            <option value="">الكل </option>
                                            @for ($i = $firstYear; $i <= date('Y'); $i++)
                                                <option value="{{ $i }}"
                                                    {{ request('year') == $i ? 'selected' : '' }}>
                                                    {{ $i }}</option>
                                            @endfor
                                        </select>
                                        @if ($errors->has('year'))
                                            <span class="help-block">
                                                <strong style="color: red;">{{ $errors->first('year') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="control-label"> @lang('dashboard.month') </label>
                                        <select name="month" id="month" class="select2 form-control">
                                            <option value="">الكل </option>
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}"
                                                    {{ request('month') == $i ? 'selected' : '' }}>
                                                    {{ $i }}</option>
                                            @endfor
                                        </select>
                                        @if ($errors->has('month'))
                                            <span class="help-block">
                                                <strong style="color: red;">{{ $errors->first('month') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label class="control-label"> @lang('dashboard.day') </label>
                                        <select name="day" id="day" class="select2 form-control">
                                            <option value="">الكل </option>
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}"
                                                    {{ request('day') == $i ? 'selected' : '' }}>
                                                    {{ $i }}</option>
                                            @endfor
                                        </select>
                                        @if ($errors->has('day'))
                                            <span class="help-block">
                                                <strong style="color: red;">{{ $errors->first('day') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    {{-- <div class="col-md-3">
                                        <label for="">{{ trans('dashboard.entry.start_date') }}</label>
                                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                                            class="form-control">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="">{{ trans('dashboard.entry.end_date') }}</label>
                                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                                            class="form-control">
                                    </div> --}}

                                    <div class="col-md-3" style="margin-top: 30px;">
                                        <button type="submit"
                                            class="btn btn-primary">{{ trans('dashboard.search') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @if (auth('admin')->user()->role == 'admin' and !empty($userTimer))
                            <p class="alert alert-info text-center">اجمالي مدة العمل
                                @if (request('day') > 0)
                                    {{ trans('dashboard.day') }}
                                @elseif(request('month') > 0)
                                    {{ trans('dashboard.month') }}
                                @elseif(request('year') > 0)
                                    {{ trans('dashboard.year') }}
                                @endif
                                : {{ $userTimer }}
                            </p>
                        @endif
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                            <input type="checkbox" class="group-checkable"
                                                data-set="#sample_1 .checkboxes" />
                                            <span></span>
                                        </label>
                                    </th>
                                    <th></th>
                                    <th>@lang('dashboard.employee')</th>
                                    <th>@lang('dashboard.role')</th>
                                    <th>@lang('dashboard.day')</th>
                                    <th>@lang('dashboard.start_time')</th>
                                    <th>@lang('dashboard.end_time')</th>
                                    <th>@lang('dashboard.work_timer')</th>
                                    <th>@lang('dashboard.work_details')</th>
                                    <th>@lang('messages.operations')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0; ?>
                                @foreach ($items as $item)
                                    <tr class="odd gradeX">
                                        <td>
                                            <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                <input type="checkbox" class="checkboxes" value="1" />
                                                <span></span>
                                            </label>
                                        </td>
                                        <td><?php echo ++$i; ?></td>
                                        <td>
                                            @if (isset($item->admin->id))
                                                <a
                                                    href="{{ route('admins.show', $item->admin_id) }}">{{ $item->admin->name }}</a>
                                            @endif
                                        </td>
                                        <td>
                                            @if (isset($item->admin->id))
                                                @if ($item->admin->role == 'admin')
                                                    <span class="badge badge-secondary"> مدير </span>
                                                @elseif($item->admin->role == 'sales')
                                                    <span class="badge badge-success"> مبيعات </span>
                                                @elseif($item->admin->role == 'developer')
                                                    <span class="badge badge-danger"> مطور </span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-info">{{ $item->day_name }}</span> <br>
                                            {{ $item->day }}
                                        </td>
                                        <td> {{ date('h:i A', strtotime($item->start_date)) }} </td>
                                        <td> {{ $type == 'online' ? null : date('h:i A', strtotime($item->end_date)) }}
                                        </td>
                                        <td>{{ gmdate('H:i:s', $item->timer) }}</td>
                                        <td>{!! $item->details !!}</td>

                                        <td>

                                            <a class="delete_item btn btn-danger" data="{{ $item->id }}"
                                                data_name="{{ $item->name_ar }}">
                                                <i class="fa fa-key"></i> @lang('messages.delete')
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('admin/js/sweetalert.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/ui-sweetalert.min.js') }}"></script>
    <script>
        $(function() {
            $("#example1").DataTable({
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'All'],
                ],
            });
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            var CSRF_TOKEN = $('meta[name="X-CSRF-TOKEN"]').attr('content');

            $('body').on('click', '.delete_item', function() {
                var id = $(this).attr('data');

                var swal_text = 'حذف ' + $(this).attr('data_name') + '؟';
                var swal_title = 'هل أنت متأكد من الحذف ؟';

                swal({
                    title: swal_title,
                    text: swal_text,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-warning",
                    confirmButtonText: "تأكيد",
                    cancelButtonText: "إغلاق",
                    closeOnConfirm: false
                }, function() {

                    {{-- var url = '{{ route("imageProductRemove", ":id") }}'; --}}

                    {{-- url = url.replace(':id', id); --}}

                    window.location.href = "{{ url('/') }}" + "/admin/attendance/delete/" +
                        id;


                });

            });

        });
    </script>
@endsection
