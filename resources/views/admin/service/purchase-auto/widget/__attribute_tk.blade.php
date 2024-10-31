<div class="row mt-5">
    <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
        Số giao dịch: <b id="total_record">{{ number_format($datatable->total_record??0) }}</b> - Tổng tiền: <b id="total_price">{{ number_format($datatable->total_price??0) }}</b>
    </div>
    <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
        Số lượng ( Tiền Ingame): <b id="total_price_base">{{ number_format($datatable->total_price_base??0) }}</b>
    </div>
    <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
        Tổng phải trả NCC: <b id="total_price_input">{{ number_format($datatable->total_price_input??0) }}</b>
    </div>
    <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px ">
        Tổng lợi nhuận: <b id="total_profit">{{ number_format($datatable->total_profit??0) }}</b>
    </div>
</div>
