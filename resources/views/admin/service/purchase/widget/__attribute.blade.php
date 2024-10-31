<div class="row mt-5">
    <div class="col-md-12">
        <div class="row">
            <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                <b>Sản phẩm: </b>
            </div>
            @foreach($attributes??[] as $attribute)
                <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
                    {{ $attribute->description }}: <b>{{ $attribute->total }}</b>
                </div>
            @endforeach
        </div>
    </div>
    <div class="col-auto" style="margin-left: auto">

    </div>
</div>
