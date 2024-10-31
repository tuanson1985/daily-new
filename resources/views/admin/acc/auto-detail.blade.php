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
            </div>

        </div>
        @if($data->category->position == 'lienminh')
            @include('admin.acc.widget.acc-lienminh-auto')
        @else
        <div class="card-body">
            <h5>Ảnh:</h5>
            <div class="row mb-3">
                @foreach($data->images as $key => $item)
                <div class="col-md-3 col-6">
                    <div class="border p-1 mb-2 mr-2">
                        <div class="text-center"><b>{{ $item->type }}</b></div>
                        <img src="{{ \App\Library\MediaHelpers::media($item->path) }}" alt="" class="img-fluid">
                    </div>
                </div>
                @endforeach
            </div>
            <table class="table">
                <tr>
                    <th>Thông tin</th>
                    <th>Chỉ số</th>
                </tr>
                <tr>
                    <td>Server</td>
                    <td>{{ $data->params->server??null }}</td>
                </tr>
                @foreach($data->params->info??[] as $key => $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>
                            @if(is_object($item->value) || is_array($item->value))
                                @foreach($item->value as $k => $child)
                                    @if(is_array($child))
                                    <div>{{ $child['name'] }} : <b>{{ $child['value'] }}</b></div>
                                    @elseif(is_object($child))
                                    <div>{{ $child->name }} : <b>{{ $child->value }}</b></div>
                                    @else
                                        <div>{{ $child }}</div>
                                    @endif
                                @endforeach
                            @else
                                {{ $item->value }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        @endif
    </div>
@endsection

@section('styles')
@endsection
@section('scripts')
    <script>
    </script>
@endsection