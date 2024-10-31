<div class="row mt-5">
    <div class="col-md-12">
        <div class="row">
            <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px;">
                Số giao dịch: <b id="total_record">{{ number_format($datatable->total_record) }}</b> - Tổng tiền: <b id="total_price">{{ number_format($datatable->total_price) }}</b>
            </div>
            <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px;">
                Tổng số tiền CTV nhận: <b id="total_real_received_price_ctv">{{ number_format($datatable->total_real_received_price_ctv??0) }}</b>
            </div>

            <div class="col-lg-12 m--margin-bottom-10-tablet-and-mobile" style="font-size: 14px;">
                Tổng lợi nhuận: <b id="total_profit">{{ number_format($datatable->total_profit??0) }}</b>
            </div>
        </div>
    </div>
    <div class="col-auto" style="margin-left: auto">

    </div>
</div>
