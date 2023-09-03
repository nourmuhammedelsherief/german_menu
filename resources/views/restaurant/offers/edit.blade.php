@extends('restaurant.lteLayout.master')

@section('title')
    @lang('messages.edit') @lang('messages.offers')
@endsection

@section('style')
    <link rel="stylesheet" href="{{ URL::asset('admin/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/select2-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('admin/css/bootstrap-fileinput.css') }}">
    <link href="{{ asset('admin') }}/bootstrap-fileinput/css/fileinput.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('admin/bootstrap-fileinput/css/fileinput.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/bootstrap-fileinput/css/fileinput-ltr.min.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1> @lang('messages.edit') @lang('messages.offers') </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/restaurant/home') }}">@lang('messages.control_panel')</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{ route('offers.index') }}">
                                @lang('messages.offers')
                            </a>
                        </li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-8">
                    @include('flash::message')
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">@lang('messages.edit') @lang('messages.offers') </h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form role="form" action="{{ route('offers.update', $offer->id) }}" method="post"
                            enctype="multipart/form-data">
                            <input type='hidden' name='_token' value='{{ Session::token() }}'>

                            <div class="card-body">
                                <div class="form-group">
                                    <label class="control-label"> @lang('messages.name') </label>
                                    <input name="name" type="text" class="form-control" value="{{ $offer->name }}"
                                        placeholder="@lang('messages.name')">
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                {{-- image editor --}}
                                <div class="form-group image-editor-preview ">
                                    <div class="col-md-12">
                                        <span class="fileinput-new"> {{ trans('messages.photo') }}</span>
                                        <br>
                                        <div dir=ltr class="file-loading">
                                            <input type="file" name="photo" id="normal-image" accept=".png,.jpg,.jpeg"
                                                class="file" data-browse-on-zone-click="true">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="alert alert-warning" role="alert">
                                <h4 class="alert-heading">{{ trans('dashboard.explain') }}</h4>
                                <p>{{ trans('dashboard.image_warning_size', ['size' => 'Die Breite entspricht dem Eineinhalbfachen der Länge']) }}</p>
                                <hr>
                                <p class="mb-0">{!! trans('dashboard.image_resize_hint') !!}
                                    <a href="https://redketchup.io/image-resizer" target="__blank" style="color : #007bff;"
                                        title="Eine Website zum Ändern der Bildgröße">Eine Website zum Ändern der Bildgröße</a>
                                </p>
                            </div>
                            @method('PUT')
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>

        </div><!-- /.container-fluid -->
    </section>

    @php
        $itemId = $offer->id;
        $editorRate = [3, 4];
        $imageUploaderUrl = route('restaurant.offer.update_image');
    @endphp
    @include('restaurant.products.product_image_modal')
@endsection
@section('scripts')
    <script src="{{ asset('admin') }}/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
    <script src="{{ asset('admin/bootstrap-fileinput/js/plugins/piexif.min.js') }}"></script>
    <script src="{{ asset('admin/bootstrap-fileinput/js/plugins/sortable.min.js') }}"></script>
    <script src="{{ asset('admin/bootstrap-fileinput/js/plugins/purify.min.js') }}"></script>
    <script src="{{ asset('admin/bootstrap-fileinput/js/fileinput.min.js') }}"></script>
    <script src="{{ asset('admin/bootstrap-fileinput/themes/fa/theme.js') }}"></script>



    <script src="{{ asset('admin/bootstrap-fileinput/locales/en.js') }}"></script>
    <script type="text/javascript">
        $("#normal-image").fileinput({
            uploadUrl: "{{ route('restaurant.offer.update_image') }}",
            // enableResumableUpload: true,
            resumableUploadOptions: {
                // uncomment below if you wish to test the file for previous partial uploaded chunks
                // to the server and resume uploads from that point afterwards
                // testUrl: "http://localhost/test-upload.php"
            },
            uploadExtraData: {
                '_token': '{{ csrf_token() }}', // for access control / security 
                @if (isset($offer->id))
                    'action': 'edit',
                    'item_id': {{ $offer->id }},
                @else

                    'action': 'create',
                @endif
            },
            ltr: true,
            language: 'en',
            maxFileCount: 1,
            allowedFileTypes: ['image'],
            allowedFileExtensions: ['image'],
            showCancel: true,
            showRemove: true,
            showUpload: true,
            showCancel: true,
            initialPreview: [
                @if (isset($offer->id) and !empty($offer->photo))
                    '{{ asset($offer->image_path) }}'
                @endif
            ],
            maxFilePreviewSize: 50240,
            initialPreviewAsData: true,
            overwriteInitial: true,

            initialPreviewAsData: true,
            initialPreviewFileType: 'image',
            initialPreviewConfig: [
                @if (isset($offer->id) and !empty($offer->photo))
                    {
                        caption: "Image",
                        // previewAsData: true,
                        key: "1"
                    }
                @endif
            ],
            theme: 'fa',
            // deleteUrl: "http://localhost/file-delete.php"
        }).on('fileuploaded', function(event, previewId, index, fileId) {
            var response = previewId.response;

            if (response.status == true) {
                $('input[name=image_name]').val(response.photo);
            }
        }).on('fileuploaderror', function(event, data, msg) {
            // console.log('File Upload Error', 'ID: ' + data.fileId + ', Thumb ID: ' + data.previewId);
            console.log(msg);
            console.log(data);
        }).on('filebatchuploadcomplete', function(event, preview, config, tags, extraData) {
            console.log('completed');
        });

        function yesnoCheck() {
            if (document.getElementById('yesCheck').checked) {
                document.getElementById('ifYes').style.display = 'none';
            } else {
                document.getElementById('ifYes').style.display = 'block';
            }
        }
    </script>
    <script>
        $(".delete_image").click(function() {
            var id = $(this).attr('id');
            var url = '{{ route('imageOfferRemove', ':id') }}';

            url = url.replace(':id', id);

            //alert(image_id );
            $.ajax({
                url: url,
                type: 'GET',
                success: function(result) {
                    if (!result.message) {
                        $(".img_" + id).fadeOut('1000');
                    }

                }
            });
        });
    </script>
@endsection
