@extends('admin.lteLayout.master')

@section('title')
    @lang('messages.add') @lang('dashboard.attendances')
@endsection

@section('styles')
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> @lang('messages.add') @lang('dashboard.attendances') </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/admin/home') }}">@lang('messages.control_panel')</a>
                        </li>
                        {{-- <li class="breadcrumb-item active">
                            <a href="{{route('admin.attendance.index')}}">
                                @lang('dashboard.attendances')
                            </a>
                        </li> --}}
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    @include('flash::message')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">@lang('messages.add') @lang('dashboard.attendances') </h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form role="form" action="{{ route('admin.attendance.store') }}" method="post"
                            enctype="multipart/form-data">
                            <input type='hidden' name='_token' value='{{ Session::token() }}'>

                            <div class="card-body">
                                @if (!isset($work->id))
                                    <div class="start-work text-center">
                                        <h5 class="text-center mb-3">ابدأ العمل الان : </h5>
                                        <a href="{{ route('admin.attendance.start') }}" class="btn btn-primary">
                                            ابدأ العمل</a>
                                    </div>
                                @else
                                    <input type="hidden" name="id" value="{{ $work->id }}">
                                    <div class="form-group">
                                        <label class="control-label"> @lang('dashboard.work_details') <span style="color:red">*</span>
                                        </label>
                                        <textarea name="details" type="text" class="form-control" required placeholder="@lang('dashboard.details')">{{ old('details') }}</textarea>
                                        @if ($errors->has('details'))
                                            <span class="help-block">
                                                <strong style="color: red;">{{ $errors->first('details') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary">نهاية العمل</button>
                                    </div>
                                @endif
                            </div>
                            <!-- /.card-body -->

                        </form>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('submit', 'form', function() {
                $('button').attr('disabled', 'disabled');
            });
        });
    </script>
@endsection
