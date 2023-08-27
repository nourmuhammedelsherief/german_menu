@extends('restaurant.lteLayout.master')

@section('title')
    @lang('dashboard.sms')
@endsection

@section('style')
    <link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/sweetalert.css') }}">
    <!-- Theme style -->
    <style>
        table.phones td ,         table.phones th{
            text-align: center;
        }
     
    </style>
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>@lang('dashboard.sms')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{url('/restaurant/home')}}">
                                @lang('messages.control_panel')
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{route('restaurant.sms.index')}}"></a>
                            @lang('dashboard.sms')
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
                    <a href="{{route('restaurant.sms.sendSms')}}" class="btn btn-info">
                        <i class="fa fa-plus"></i>
                        @lang('dashboard.send_sms')
                    </a>
                </h3>
                <div class="card">

                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="group-checkable"
                                               data-set="#sample_1 .checkboxes"/>
                                        <span></span>
                                    </label>
                                </th>
                                <th></th>
                                <th> @lang('dashboard.entry.message') </th>
                                <th> @lang('dashboard.message_count') </th>
                                <th> @lang('dashboard.phones') </th>
                                <th> @lang('dashboard.entry.created_at') </th>
                                
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 0 ?>
                            @foreach($items as $item)
                                <tr class="odd gradeX">
                                    <td>
                                        <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                            <input type="checkbox" class="checkboxes" value="1"/>
                                            <span></span>
                                        </label>
                                    </td>
                                    <td><?php echo ++$i ?></td>
                                    <td>
                                        {{$item->message}}
                                    </td>
                                    <td>{{$item->message_count}}</td>
                                    <td>
                                        <a href="javascript:;"  class="phone-details" data-toggle="modal" data-target="#phones" data-id="{{$item->id}}">
                                            
                                            @foreach ($item->phones as $index => $t)
                                                @if($index < 3)
                                                    {{$t->phone}} , 
                                                @endif
                                            @endforeach
                                            @if($item->phones->count() > 3 ) ... المذيد @endif
                                        </a>
                                    </td>
                                    <td>{{date('Y-m-d h:i A' , strtotime($item->created_at))}}</td>

                             
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
    {{$items->links()}}
    <!-- /.row -->
    </section>
    <div class="modal fade" id="phones">
        <div class="modal-dialog">
            <div class="modal-content ">
                <div class="modal-header">
                    <h4 class="modal-title">
                        @lang('messages.icon')
                    </h4>
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                 
                </div>
                <div class="modal-footer ">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">
                        @lang('messages.close')
                    </button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection

@section('scripts')

    <script src="{{asset('dist/js/adminlte.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/jquery.dataTables.js')}}"></script>
    <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}"></script>
    <script src="{{ URL::asset('admin/js/sweetalert.min.js') }}"></script>
    <script src="{{ URL::asset('admin/js/ui-sweetalert.min.js') }}"></script>
    <script>
        $(function () {

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
        $(document).ready(function () {
            $('table').on('click' , '.phone-details' , function(){
                var tag = $(this);
                var modal = $('#phones');
                console.log(tag.data());
                modal.find('.modal-body').html('<div class="text-center mt-5 mb-5"><span class="loader"></span></div>');
                $.ajax({
                    url : "{{route('restaurant.sms.phone')}}" , 
                    method : 'GET' , 
                    data : {
                        id : tag.data('id') , 
                    },
                    success: function(json){
                        console.log(json);
           
                            modal.find('.modal-body').html(json.data);
                        
                    } , 
                    error: function(xhr){
                        console.log(xhr);
                        modal.find('.modal-body').html('');
                        toastr.error('fail');
                    }, 
                });
            });
            
            $('body').on('click', '.delete_data', function () {
                var id = $(this).attr('data');
                var swal_text = '{{trans('messages.delete')}} ' + $(this).attr('data_name');
                var swal_title = "{{trans('messages.deleteSure')}}";

                swal({
                    title: swal_title,
                    text: swal_text,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-warning",
                    confirmButtonText: "{{trans('messages.sure')}}",
                    cancelButtonText: "{{trans('messages.close')}}"
                }, function () {

                    window.location.href = "{{ url('/') }}" + "/restaurant/sms/delete/" + id;

                });

            });
        });
    </script>
@endsection

