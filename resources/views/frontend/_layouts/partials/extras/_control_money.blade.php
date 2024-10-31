<div class="text-white m-0 flex-grow-1 mr-3 font-size-h5">
    <p class="m-1">ID: {{auth('frontend')->user()->id}}</p>
    <p class="m-1">
        @php
            $username = str_replace('tt_', '', auth('frontend')->user()->username);
        @endphp
        {{ $username }}
    </p>
    <p class="m-1" style="color:#bebfcc;">{{auth('frontend')->user()->email}}</p>
    <p class="m-1">{{number_format(auth('frontend')->user()->balance , 0, ',', '.')}} VNĐ</p>
    <p class="m-1" style="font-size: 12px;color: chartreuse;">({{__('Dịch vụ')}}: {{number_format( ($price_control_total??0) , 0, ',', '.')}} VNĐ)</p>
</div>
