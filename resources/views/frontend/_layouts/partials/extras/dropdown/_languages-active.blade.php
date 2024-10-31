{{-- {{dd($data)}} --}}
@if (isset($data))
    <div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1">
        <img class="h-20px w-20px rounded-sm" src="{{ \App\Library\MediaHelpers::media($data->image) }}" alt=""/>
    </div>
@endif