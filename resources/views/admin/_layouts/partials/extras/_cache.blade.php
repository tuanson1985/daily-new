@if (auth()->user()->account_type == 1)
<div class="dropdown" style="margin-left: 15px">
    <div class="topbar-item">
        <a href="{{route('admin.shop.cache')}}" class="btn btn-light btn-text-success btn-hover-text-success font-weight-bold">Xóa cache</a>
    </div>
</div>
@endif