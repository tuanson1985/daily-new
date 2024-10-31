@extends('admin._layouts.master')

@section('action_area')
    <div class="d-flex align-items-center text-right">
        <a href="{{route('admin.'.$module.'.index')}}"
           class="btn btn-light-primary font-weight-bolder mr-2">
            <i class="ki ki-long-arrow-back icon-sm"></i>
            Back
        </a>
    </div>
@endsection
@section('content')

<div class="card card-custom" id="kt_page_sticky_card">
	<div class="card-header">
		<div class="card-title">
            <h3 class="card-label">
                {{-- {{__($page_breadcrumbs[0]['title'])}} <i class="mr-2"></i> --}}
                {{__('Danh sách domain: ')}} {{$data->title}}
            </h3>

		</div>
		<div class="card-toolbar"></div>
	</div>
	<div class="card-body">
        <div class="row" style="padding-bottom: 48px">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">

                        <select class="form-control" name="type" id="type">
                            <option>Chọn loại zone</option>
                            <option value="{{ $url }}&type=full">Full</option>
                            <option value="{{ $url }}&type=flexible">Flexible</option>
                            <option value="{{ $url }}&type=off">Off</option>
                            <option value="{{ $url }}&type=full-strict">Full Strict</option>
                        </select>
                    </div>

                    <div class="col-md-3">

                        <select class="form-control" name="per_page" id="per_page">
                            <option>Chọn số lượng hiển thị</option>
                            <option value="{{ $url }}&per_page=50">50</option>
                            <option value="{{ $url }}&per_page=100">100</option>
                            <option value="{{ $url }}&per_page=500">500</option>
                            <option value="{{ $url }}&per_page=1000">1000</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                @if (isset($status) && $status == 1)
                    <p>Tổng cộng: {{$count}} bản ghi</p>
                    {{-- <button type="button" id="btn-export" class="btn btn-primary mr-2">Xuất Excel</button> --}}
                    <table class="table table-striped mb-6" id="CFTable">
                        <thead>
                        <tr>
                            <th scope="col">STT</th>
                            <th scope="col">Domain</th>
                            <th scope="col">IP</th>
                            <th scope="col">Trạng thái</th>
                            <th scope="col">Loại</th>
                            <th scope="col">Đăng kí</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($items as $key => $item)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td>{{$item->name}}</td>
                                <td>
                                    @if(isset($item->permissions) && count($item->permissions))
                                        @php
                                            $permissions = $item->permissions;
                                        @endphp
                                        @foreach($permissions as $permission)
                                            {{ $permission->name }}
                                            : {{ $permission->content }}
                                            - proxied: {{ isset($permission->proxied) ? 'true' : 'false' }} <br>
                                        @endforeach
                                    @endif
                                </td>
                                <td>{{$item->status}}</td>
                                <td>{{$item->type}}</td>
                                <td>{{$item->original_registrar}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $items->appends(request()->query())->links('pagination::bootstrap-4') }}
                @else
                    <div class="alert alert-danger" role="alert">
                        {{$message}}
                    </div>
                @endif
            </div>
        </div>

	</div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
<script>
       $(document).ready(function () {
           $("#type").change(function(){
               window.location.href = $( "select#type" ).val();
           });

           $("#per_page").change(function(){
               window.location.href = $( "select#per_page" ).val();
           });

           $("#btn-export").click(function(){
               $("#CFTable").table2excel({
               name: "Worksheet Name",
               filename: "FileExcel",
               fileext: ".xls"
               })
           });
       });
</script>
@endsection


