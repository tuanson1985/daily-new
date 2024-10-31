{{-- Extends layout --}}
@extends('admin._layouts.master')

{{-- Content --}}
@section('content')


    <div class="card card-custom" id="kt_page_sticky_card">
        <div class="card-header">
            <div class="card-title">
                <h3 class="card-label">
                    {{__($page_breadcrumbs[0]['title'])}} <i class="mr-2"></i>
                </h3>
            </div>
            <div class="card-toolbar">
                @if(empty($cat->module))
                <a href="{{ route('admin.acc.cat-auto-edit', [$cat->parent_table??'game_auto_properties', $cat->parent_id]) }}" class="btn btn-sm btn-outline-primary"><< Trở lại mục cha</a>
                @endif
            </div>

        </div>

        <div class="card-body">
            {!! $childs->links('vendor.pagination.bootstrap-4') !!}
            <!--begin: Datatable-->
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Game</th>
                        <th>ID ingame</th>
                        <th>Tên</th>
                        <th>Ảnh</th>
                        <th>Key</th>
                        <th class="text-center">Tác vụ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($childs as $key => $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->provider }}</td>
                        <td>{{ $item->idkey }}</td>
                        <td><a href="{{ route('admin.acc.cat-auto-edit', ['game_auto_properties', $item->id]) }}">{{ $item->name }}</a></td>
                        <td><img src="{{ \App\Library\MediaHelpers::media($item->thumb) }}" class="img-fluid" style="max-height: 80px; width: auto;"></td>
                        <td>{{ $item->key }}</td>
                        <td class="text-center">
                            @if($item->childs_count > 0)
                            <a href="{{ route('admin.acc.cat-auto-edit', ['game_auto_properties', $item->id]) }}"><i class="fa fa-eye"></i>{{ $item->childs_count }} mục con</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <!--end: Datatable-->
            {!! $childs->links('vendor.pagination.bootstrap-4') !!}
        </div>
    </div>
@endsection

@section('styles')
@endsection
@section('scripts')
    <script>
    </script>
@endsection