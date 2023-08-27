@extends('restaurant.lteLayout.master')

@section('title')
    @lang('dashboard.party_orders')
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
                    <h1>@lang('dashboard.party_orders')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/restaurant/home') }}">
                                @lang('messages.control_panel')
                            </a>
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
                <div class="party-status">
                    <a href="{{ route('restaurant.party-order.index') }}?status=pending"
                        class="mb-2 ml-1 btn btn-{{ request('status') == 'pending' ? 'primary' : 'info' }}">{{ trans('dashboard._party_status.pending') }}
                        (
                        {{ App\Models\PartyOrder::where('restaurant_id', $restaurant->id)->where('status', 'pending')->count() }}
                        )</a>

                    <a href="{{ route('restaurant.party-order.index') }}?status=active"
                        class="mb-2 ml-1 btn btn-{{ request('status') == 'active' ? 'primary' : 'info' }}">{{ trans('dashboard._party_status.active') }}
                        (
                        {{ App\Models\PartyOrder::where('restaurant_id', $restaurant->id)->where('status', 'active')->count() }}
                        )</a>

                    <a href="{{ route('restaurant.party-order.index') }}?status=canceled"
                        class="mb-2 ml-1 btn btn-{{ request('status') == 'canceled' ? 'primary' : 'info' }}">{{ trans('dashboard._party_status.canceled') }}
                        (
                        {{ App\Models\PartyOrder::where('restaurant_id', $restaurant->id)->where('status', 'canceled')->count() }}
                        )</a>

                    <a href="{{ route('restaurant.party-order.index') }}?status=expire-date"
                        class="mb-2 ml-1 btn btn-{{ request('status') == 'expire-date' ? 'primary' : 'info' }}">{{ trans('dashboard._party_status.expire-date') }}
                        (
                        {{ App\Models\PartyOrder::where('restaurant_id', $restaurant->id)->where('status' , '!=' , 'cart')->where('date', '<', date('Y-m-d'))->count() }}
                        )</a>

                    <a href="{{ route('restaurant.party-order.index') }}?status=not-expire-date"
                        class="mb-2 ml-1 btn btn-{{ request('status') == 'not-expire-date' ? 'primary' : 'info' }}">{{ trans('dashboard._party_status.not-expire-date') }}
                        (
                        {{ App\Models\PartyOrder::where('restaurant_id', $restaurant->id)->where('status' , '!=' , 'cart')->where('date', '>', date('Y-m-d'))->count() }}
                        )</a>

                </div>
                <div class="card">

                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>

                                    <th> ID </th>
                                    <th> @lang('dashboard.party') </th>
                                    <th> @lang('dashboard.branch') </th>
                                    <th> @lang('dashboard.days') </th>
                                    <th> @lang('dashboard.entry.time_from') </th>
                                    <th> @lang('dashboard.entry.time_to') </th>
                                    <th> @lang('dashboard.entry.payment_method') </th>
                                    <th> @lang('dashboard.entry.payment_status') </th>
                                    <th> @lang('dashboard.entry.total_price') </th>
                                    <th> @lang('dashboard.entry.status') </th>
                                    <th> @lang('dashboard.entry.created_at') </th>
                                    <th> @lang('messages.operations') </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0; ?>
                                @foreach ($orders as $index => $item)
                                    <tr class="odd gradeX">

                                        <td>{{ $item->id }}</td>
                                        <td>
                                            @if (isset($item->party->id))
                                                <a
                                                    href="{{ route('restaurant.party.edit', $item->party->id) }}">{{ $item->party->title }}</a>
                                            @endif
                                        </td>
                                        <td>{{ isset($item->branch->id) ? $item->branch->name : '' }}</td>

                                        <td>
                                            {{ $item->date }}
                                        </td>
                                        <td>{{ $item->from_string }}</td>
                                        <td>{{ $item->to_string }}</td>
                                        <td>
                                            @if ($item->payment_type == 'bank')
                                                <span
                                                    class="badge badge-primary">{{ trans('dashboard.bank_transfer') }}</span>
                                            @elseif($item->payment_type == 'online')
                                                <span
                                                    class="badge badge-success">{{ trans('dashboard.online_payment') }}</span>
                                            @elseif($item->payment_type == 'cash')
                                                <span class="badge badge-info">{{ trans('dashboard.cash') }}</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($item->payment_status == 'paid')
                                                <span class="badge badge-success">{{ trans('dashboard.paid') }}</span>
                                            @else
                                                <span class="badge badge-secondary">{{ trans('dashboard.unpaid') }}</span>
                                            @endif
                                            @if (!empty($item->invoice_id))
                                                <p class="text-center"> {{ trans('dashboard.pay_number') }} :
                                                    {{ $item->invoice_id }}</p>
                                            @endif
                                        </td>
                                        <td>{{ $item->total_price }}</td>
                                        <th>
                                            @if ($item->status == 'pending')
                                                <span
                                                    class="badge badge-secondary">{{ trans('dashboard._party_status.pending') }}</span>
                                            @elseif($item->status == 'active')
                                                <span
                                                    class="badge badge-success">{{ trans('dashboard._party_status.active') }}</span>
                                            @elseif($item->status == 'canceled')
                                                <span
                                                    class="badge badge-danger">{{ trans('dashboard._party_status.canceled') }}</span>
                                                    @if(!empty($item->cancel_reason) and request('status') == 'canceled')
                                                    <br>
                                                         السبب : 
                                                            <br>
                                                            <span class="">    {{$item->cancel_reason}}</span>
                                                    @endif
                                            @endif
                                        </th>
                                        <td>{{ date('Y-m-d h:i A', strtotime($item->created_at)) }}</td>

                                        <td>
                                            @if ($item->status == 'pending')
                                                @if ($item->status != 'canceled')
                                                    <a class="btn btn-success confirm-order" data-toggle="modal"
                                                        data-target="#orderConfirm" data-id="{{ $item->id }}"
                                                        style="color:#FFF;">
                                                        <i class="fa fa-user-eye"></i> @lang('dashboard.reservation_confirm')
                                                    </a>
                                                @endif

                                                <a class="btn btn-warning cancel-order" href="javascript:;"
                                                    data-toggle="modal" data-target="#cancelOrder"
                                                    data-href="{{ route('restaurant.party.cancel', $item->id) }}">
                                                    @lang('dashboard.cancel')
                                                </a>
                                                @if ($item->payment_type == 'bank' and $item->payment_status == 'unpaid')
                                                    <a class="btn btn-info "
                                                        href="{{ route('restaurant.party.bank-confirm' , $item->id) }}">
                                                        تاكيد التحويل البنكي
                                                    </a>
                                                @endif
                                            @endif
                                            @if ($item->payment_type == 'bank' and !empty($item->bank_photo))
                                                <a class="btn btn-info show-image"
                                                    data-image="{{ asset($item->image_path) }}" data-toggle="modal"
                                                    data-target="#showImage-{{ $item->id }}">
                                                    <i class="fa fa-user-eye"></i> @lang('dashboard.transfer_photo')
                                                </a>

                                                <div class="modal fade" id="showImage-{{ $item->id }}" tabindex="-1"
                                                    role="dialog" aria-labelledby="showImageTitle" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="showImageTitle">
                                                                    {{ trans('dashboard.transfer_photo') }}</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="image-preview">
                                                                    <img src="{{ asset($item->bank_photo_path) }}"
                                                                        width="475" height="400">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">{{ trans('dashboard.close') }}</button>
                                                                {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <a class="delete_data btn btn-danger" data="{{ $item->id }}"
                                                data_name="{{ $index + 1 }}">
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
    <div class="modal fade" id="orderConfirm" tabindex="-1" role="dialog" aria-labelledby="orderConfirmTitle"
        aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderConfirmTitle">{{ trans('dashboard.reservation_confirm') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">{{ trans('dashboard.reservation_number') }}</label>
                        <input type="text" name="code" class="form-control" data-id="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ trans('dashboard.close') }}</button>
                    <button type="button" class="btn btn-primary "
                        id="checkReservationConfirm">{{ trans('dashboard.confirm') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cancelOrder" tabindex="-1" role="dialog" aria-labelledby="cancelOrderTitle"
        aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelOrderTitle">{{ trans('dashboard.reservation_cancel_order') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        @csrf
                        <input type="hidden" name="cancel" value="1">
                        <div class="form-group">
                            <label for="">{{ trans('dashboard.entry.reason') }}</label>
                            <textarea name="reason" id="" rows="5" class="form-control">{{ old('reason') }}</textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ trans('dashboard.close') }}</button>
                    <button type="button" class="btn btn-primary " data-dismiss="modal"
                        id="checkReservationCancel">{{ trans('dashboard.save') }}</button>
                </div>
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
        $(function() {
            $("#example1").DataTable({
                order: [
                    [10, 'desc']
                ],
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

                    window.location.href = "{{ url('/') }}" + "/restaurant/party/delete/" +
                        id;

                });

            });

            $('table').on('click', 'a.confirm-order', function() {
                $('#orderConfirm input').data('id', $(this).data('id'));
            });


            $('#checkReservationConfirm').on('click', function() {
                var input = $('#orderConfirm input');

                $.ajax({
                    url: "{{ url('restaurant/party-order/confirm') }}/" + input.data('id') +
                        '/' + input.val(),
                    method: 'GET',
                    success: function(json) {
                        console.log(json);
                        if (json.status == true) {
                            toastr.success(json.message);
                            $('#orderConfirm').modal('hide');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            toastr.error(json.message);
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr);
                        toastr.error('Fail');
                    }
                });
            });
            $('table').on('click', '.btn.cancel-order', function() {
                var tag = $(this);
                $('#cancelOrder form').attr('action', tag.data('href'));
            });
            $('#checkReservationCancel').on('click', function() {
                var input = $('#orderConfirm input');
                $('#cancelOrder form').submit();

            });
        });
    </script>
@endsection
