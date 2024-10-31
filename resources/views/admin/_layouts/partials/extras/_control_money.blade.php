<div class="text-white m-0 flex-grow-1 mr-3 font-size-h5">
    <p class="m-1">ID: {{auth()->user()->id}}</p>
    <p class="m-1">{{auth()->user()->username}}</p>
    <p class="m-1" style="color:#bebfcc;">{{auth()->user()->email}}</p>
    <p class="m-1">{{number_format(auth()->user()->balance , 0, ',', '.')}} VNĐ</p>
{{--    <p class="m-1" style="font-size: 12px;color: chartreuse;">(Dịch vụ: {{number_format( $price_control_total , 0, ',', '.')}} VNĐ)</p>--}}
{{--    <p class="m-1" style="font-size: 12px;color: chartreuse;">(Bán nick: {{number_format( $price_nick_control_total , 0, ',', '.')}} VNĐ)</p>--}}
</div>
