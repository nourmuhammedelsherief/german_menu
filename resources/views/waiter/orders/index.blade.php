@extends('waiter.lteLayout.master')


@section('title')
    @lang('dashboard.waiter_orders')
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">
    <!-- Theme style -->
    <style>
        a.btn {
            color: #FFF !important;
        }
    </style>
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>@lang('dashboard.waiter_orders')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/restaurant/home') }}">
                                @lang('messages.control_panel')
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{ route('restaurant.waiter.orders.index') }}"></a>
                            @lang('dashboard.waiter_orders')
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
                <h3>
                    <a href="{{ route('waiter.orders.index') }}"
                        class="btn btn-{{ empty(request('status')) ? 'primary' : 'info' }}  ">{{ trans('dashboard.all') }}</a>
                    <a href="{{ route('waiter.orders.index') }}?status=pending"
                        class="btn btn-{{ request('status') == 'pending' ? 'primary' : 'info' }}  ">{{ trans('dashboard.pending') }}</a>

                    <a href="{{ route('waiter.orders.index') }}?status=in_progress"
                        class="btn btn-{{ request('status') == 'in_progress' ? 'primary' : 'info' }}  ">{{ trans('dashboard.in_progress') }}</a>



                </h3>
                <div class="card">

                    <!-- /.card-header -->
                    <div class="card-body " id="table-content">
                        @include('waiter.orders.includes.updated_table')
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- Modal -->
    <div class="modal fade" id="changeStatus" tabindex="-1" role="dialog" aria-labelledby="changeStatusLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeStatusLabel">{{ trans('dashboard.change_status') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('waiter.orders.change-status') }}" method="post">
                    @csrf
                    <input type="hidden" name="order_id" value="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label"> @lang('dashboard.status') </label>
                            <select name="status" class="form-control">
                                <option value="" selected> @lang('messages.choose') </option>
                                <option value="in_progress">{{ trans('dashboard.in_progress') }}</option>
                                <option value="completed">{{ trans('dashboard.completed') }}</option>
                                <option value="canceled">{{ trans('dashboard.canceled') }}</option>

                            </select>
                            @if ($errors->has('status'))
                                <span class="help-block">
                                    <strong style="color: red;">{{ $errors->first('status') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ trans('dashboard.close') }}</button>
                        <button type="button" class="btn btn-primary save-change">{{ trans('dashboard.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('admin/js/sweetalert.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/ui-sweetalert.min.js') }}"></script>
    <script>
        var pendingCount = {{ $pendingCount }};
        $(function() {
            $("#example1").DataTable({
                order: [
                    [6, 'desc']
                ],
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'All'],
                ],
            });

            setInterval(() => {
                $.ajax({
                    url: "{{ route('waiter.orders.index') }}?status={{request('status')}}",
                    method: 'GET',
                    headers: {
                        Accept: 'application/json'
                    },
                    success: function(json) {

                        if (json.status == 1 && json.pending_count != pendingCount) {
                            
                            pendingCount = json.pending_count;
                            console.log('updated ' + pendingCount);
                            $('#table-content').html(json.data);
                            $("#example1").DataTable({
                                order: [
                                    [6, 'desc']
                                ],
                                lengthMenu: [
                                    [10, 25, 50, 100, -1],
                                    [10, 25, 50, 100, 'All'],
                                ],
                            });
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr);
                    },
                });
            }, 5000);

        });
    </script>
    <script>
        $(document).ready(function() {
            $('body').on('click', '.change-status', function() {
                var tag = $(this);
                $('#changeStatus input[name=order_id]').val(tag.data('id'));
                $('#changeStatus select[name=status]').val('');
                $('#changeStatus select[name=status]').select2();
            });

            $('body').on('click', '.save-change', function() {
                var tag = $(this);
                var formData = new FormData($('#changeStatus form')[0]);
                var id = $('#changeStatus input[name=order_id]').val();
                $.ajax({
                    url: "{{ route('restaurant.waiter.orders.change-status') }}",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(json) {
                        console.log(json);
                        if (json.status == true) {
                            var content = '';
                            toastr.success(json.message);
                            var val = $('#changeStatus select[name=status]').val();
                            if (val == 'in_progress') {
                                var content =
                                    '<span class="badge badge-primary">{{ trans('dashboard.in_progress') }}</span>';
                            } else if (val == 'completed') {
                                var content =
                                    '<span class="badge badge-success">{{ trans('dashboard.completed') }}</span>';

                            } else if (val == 'canceled') {
                                var content =
                                    '<span class="badge badge-danger">{{ trans('dashboard.canceled') }}</span>';
                            }
                            $('#row-' + id + ' td.change-status').html(content);
                            $('#changeStatus').modal('hide');
                        } else {
                            toastr.error(json.message);

                        }
                    },
                    error: function(xhr) {
                        console.log(xhr);
                        toastr.error('لا يمكن التعديل الان');
                        $('#changeStatus').modal('hide');
                    },
                });
            });
            $('body').on('click', '.delete_data', function() {
                var id = $(this).attr('data');
                var swal_text = '{{ trans('messages.delete') }} ' + $(this).attr('data_name');
                var swal_title = "{{ trans('messages.deleteSure') }}";

                swal({
                    title: swal_title,
                    text: swal_text,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-warning",
                    confirmButtonText: "{{ trans('messages.sure') }}",
                    cancelButtonText: "{{ trans('messages.close') }}"
                }, function() {

                    window.location.href = "{{ url('/') }}" +
                        "/restaurant/waiter/orders/delete/" + id;

                });

            });
        });
    </script>
@endsection
