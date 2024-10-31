<div class="col-md-3 col-6">
    <div class="border border-{{ $boder }} p-1 mb-2 mr-2">
        <div class="text-center">{{ config('etc.acc.lienminh_auto_prop')[$item->key] }}<br><b>{{ $item->name }}</b></div>
        <img src="{{ \App\Library\MediaHelpers::media($item->thumb) }}" alt="" class="img-fluid">
        @if(!empty($item->pivot->level))
            <div>Level: <b>{{ $item->pivot->level }}</b></div>
            <div>Point: <b>{{ $item->pivot->point }}</b></div>
            <div>Grade: <b>{{ $item->pivot->grade }}</b></div>
        @endif
        @if(!empty($item->meta['level']))
            <div>Level: <b>{{ $item->meta['level'] }}</b></div>
        @endif
    </div>
</div>