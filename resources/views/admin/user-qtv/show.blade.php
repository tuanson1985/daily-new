

<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"> QTV - {{ $user->username }} #{{$user->id}}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <i aria-hidden="true" class="ki ki-close"></i>
    </button>
</div>

<div class="modal-body scroll-default">

    <table class="table table-striped">
        <tbody class="">
            <tr>
                <td colspan="2" style="text-align: center">Danh sách điểm bán</td>
            </tr>
            @if(isset($data) && count($data))
                @foreach($data as $shop)
                    <tr>
                        <td colspan="2">{{ $shop->domain }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>

</div>


<script>
    $(document).ready(function () {
        $('.load-modal').each(function (index, elem) {
            $(elem).unbind().click(function (e) {
                e.preventDefault();
                e.preventDefault();
                var curModal = $('#LoadModal');
                curModal.find('.modal-content').html("<div class=\"loader\" style=\"text-align: center\"><img src=\"/assets/frontend/images/loader.gif\" style=\"width: 50px;height: 50px;\"></div>");
                curModal.modal('show').find('.modal-content').load($(elem).attr('rel'));
            });
        });
    });
</script>
