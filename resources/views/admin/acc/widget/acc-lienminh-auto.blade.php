@php($props = $data->game_auto_props->groupBy('key'))
<div class="card-body">
    <div class="p2 mb-3">
        @foreach($props as $key => $list)
            <span class="badge badge-secondary mr-2 mb-2">
                {{ count($list) }} {{ config('etc.acc.'.$data->category->position.'_auto_prop')[$key] }}
            </span>
        @endforeach
    </div>
    <ul class="nav nav-tabs">
        @php($i=0)
        @foreach($props as $key => $item)
            @if(!in_array($key, ['skins', 'chromas']))
            <li class="nav-item">
                <a class="nav-link {{ $i == 0? 'active': '' }}" data-toggle="tab" href="#{{ $key }}-tab" role="tab">{{ config('etc.acc.lienminh_auto_prop')[$key] }}</a>
            </li>
            @php($i++)
            @endif
        @endforeach
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#rank-tab" role="tab">Rank</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        @php($i=0)
        @foreach($props as $key => $list)
            @if(!in_array($key, ['skins', 'chromas']))
            <div class="tab-pane fade {{ $i == 0? 'show active': '' }}" id="{{ $key }}-tab" role="tabpanel">
                <div class="row">
                    @foreach($list as $k => $item)
                        <div class="col-md-6">
                            <div class="card mb-2">
                                <div class="card-header py-2">{{ config('etc.acc.lienminh_auto_prop')[$item->key] }} #{{ $k+1 }}</div>
                                <div class="card-body">
                                    <div class="row">
                                        @include('admin.acc.widget.auto-detail-item', ['item' => $item, 'boder' => 'success'])
                                        @if($key == 'champions' && !empty($props['skins']))
                                            @foreach($props['skins']->where('parent_id', $item->id) as $j => $value)
                                                @include('admin.acc.widget.auto-detail-item', ['item' => $value, 'boder' => 'info'])
                                                @if(!empty($props['chromas']))
                                                @foreach($props['chromas']->where('parent_id', $value->id) as $h => $chroma)
                                                    @include('admin.acc.widget.auto-detail-item', ['item' => $chroma, 'boder' => 'secondary'])
                                                @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>  
                    @endforeach
                </div>
            </div>
            @php($i++)
            @endif
        @endforeach
        <div class="tab-pane fade" id="rank-tab" role="tabpanel">
            <div class="mb-2 p-2">
                <span>Level:</span> <b>{{ $data->params->rank_level??null }}</b>
            </div>
            @if(!empty($data->params->rank_info))
                <table class="table">
                    @foreach($data->params->rank_info as $i => $item)
                        @if($i == 0)
                            <tr>
                                @foreach($item as $key => $value)
                                <th>{{ $key }}</th>
                                @endforeach
                            </tr>
                        @endif
                        <tr>
                            @foreach($item as $key => $value)
                            <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>
    </div>
</div>