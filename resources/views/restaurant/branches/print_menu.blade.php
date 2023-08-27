
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>

        <style>

            .page-header{
             
            }
            @page {
                header: page-header;
                footer: page-footer;
            }
            *{
                text-align: right;
                font-family: "tajawal" ,"sans-serif"; 
                direction: rtl;
                
            }
            
            .text-center{
                text-align: center !important;
            }
            table{
                width: 100%;
            }
            table th{
                text-align: right !important;
            }
            .product-table tr th,
            .product-table tr td{
                font-size: 12px;
                padding: 10px;
                text-align: center;
            }
            .product-table tr th{
                background-color:#a2a2a2;
            }
            .product-table tr:nth-child(even) td{
                background-color:#f4f4f4;
                
                
            }
            .page-footer td{
                
            }
            .image-v{
                width: 100px;
                height: 100px;
                border:1px solid #CCC;
                border-radius: 10px;
            }
            .image-v img{
                width: 100%;
                height: 100%;
                border-radius: 10px;
            }
        </style>
    </head>
    <body>
        <htmlpageheader name="page-header">
            <table width="100%" style="padding:10px 0;border-bottom:3px solid #CCC;">
                <tr>
                   
                    <td width="33%" align="right" style="font-weight:bold;">  @if(!empty($restaurantImage))
                        
                         <img src="{{$restaurantImage}}" style="width:70px;height:70px;border-radius: 4em" alt="">
                        
                       
                    @endif
                    {{$branch->name}}</td>
                    <td width="33%" style="text-align: left;font-size:12px;">    صنع بحب   في ايزي مينو</td>
                    
                </tr>
            </table>

            
        </htmlpageheader>
        
       
        
        <section class="content">
      
            <div class="row">
                <div class="col-12">
                    
                    <div class="card">
    
                        <!-- /.card-header -->
                        <div class="card-body">
                           @foreach ($menuCategories as $count => $category)
    
                                <div class="menu-category">
                                    <h2 class="text-center mx-4">{{$category->name}}</h2>
                                    <table id="" class="table product-table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                            
                                                <th></th>
                                                <th> @lang('dashboard.product_image') </th>
                                                
                                                <th style="width:33%"> @lang('messages.name') </th>
                                                <th>{{ trans('dashboard.size') }}</th>
                                                <th style="width:50px;">{{ trans('dashboard.calories') }}</th>
                                                
                                                
                                                <th>{{ trans('dashboard.entry.price') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = 0 ?>
                                        @foreach ($category->products as $product)
                                            @if($product->sizes->count() > 0)
                                            @foreach ($product->sizes as $size)
                                                <tr>
                                                    <td>{{++$i}}</td>
                                                    <td>
                                                        
                                                        @if(isset($images['m-' . $product->id]) and !empty($images['m-' . $product->id]))
                                                        <img src="{{$images['m-' . $product->id]}}" style="width:80px;height:80px;border:2px soild #CCC;" alt="">
                                                        @endif
                                                    
                                                    </td>
                                                    <td>{{$product->name}}</td>
                                                    <td>{{$size->name}}</td>
                                                    <td>{{$product->calories}}</td>
                                                    
                                                    <td>
                                                        {{$size->price}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @else 
                                                <tr>
                                                    <td>{{++$i}}</td>
                                                    <td>
                                                        
                                                        @if(isset($images['m-' . $product->id]) and !empty($images['m-' . $product->id]))
                                                        <img src="{{$images['m-' . $product->id]}}" style="width:100px;height:100px;" alt="">
                                                        @endif
                                                    
                                                    </td>
                                                    <td>{{$product->name}}</td>
                                                    <td></td>
                                                    <td>{{$product->calories}}</td>
                                                    
                                                    <td>
                                                       
                                                    {{$product->price}}
                                                        
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if(count($menuCategories) > ($count + 1))
                                <pagebreak>
                                @endif
                           @endforeach
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
                <!-- /.col -->
            </div>
        <!-- /.row -->
        </section>

        <htmlpagefooter name="page-footer">
            <table width="100%" style="padding:10px 0;">
                <tr>
                   
                    <td width="33%" align="right" style="font-size:12px;">{PAGENO}/{nbpg}</td>
                    <td width="33%" style="text-align: left;font-size:12px;">صنع بحب   في ايزي مينو</td>
                    
                </tr>
            </table>
        </htmlpagefooter>
    </body>
    </html>