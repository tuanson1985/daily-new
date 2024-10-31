<ol class="dd-list">
    @foreach($data as $key => $item)
    <li class="dd-item nested-list-item" data-order="{{ $key }}" data-id="{{ $item->id }}">
        <div class="dd-handle nested-list-handle">
            <span class="la la-arrows-alt"></span>
        </div>
        <div class="nested-list-content">
            <div class="m-checkbox">
                <label class="checkbox checkbox-outline">
                    <input type="checkbox" rel="{{ $item->id }}" class="children_of_{{ $item->parent_id }}">
                    <span></span> <i class="mr-1"><small>#{{$item->id}}</small></i> {{ $item->title }}
                    @if(!empty($item->display_type))
                        <i class="ml-2"><small> {{ config('etc.acc_property.type')[$item->display_type]??$item->display_type }}</small></i>
                    @endif
                    @if(!empty($item->position))
                        <i class="ml-2"><small>{{ config("etc.acc_property.position.{$item->position}") }}</small></i>
                    @endif
                </label>
            </div>
            <div class="btnControll">
                <i><small>{{ config("etc.acc_property.module.{$item->module}") }}</small></i>
                @if($item->module == 'acc_category')
                    <a href="{{ route('admin.acc.property.edit', [$item->module, $item->parent_id??0, $item->id]) }}?clone=1" class="btn btn-sm btn-warning">Clone</a>
                    @if(in_array($item->position, array_keys(config('etc.acc_property.auto')) ))
                        <a href="{{ route('admin.acc.cat-auto-edit', ['groups', $item->id]) }}" class="btn btn-sm btn-success">Auto</a>
                    @endif
                @endif
                <a href="{{ route('admin.acc.property.edit', [$item->module, $item->parent_id??0, $item->id]) }}" class="btn btn-sm btn-primary">Sửa</a>
                @if(empty($item->deleted_at))
                    <a href="#" class="btn btn-sm btn-danger  delete_toggle " rel="{{ $item->id }}"> Xóa </a>
                @else
                    <a href="{{ route("admin.acc.property.edit", [$item->module, $item->parent_id??0, $item->id]) }}?recover=1" class="btn btn-sm btn-success" rel="{{ $item->id }}"> Khôi phục </a>
                @endif
            </div>
        </div>
        <?php
            $keys = array_keys(config('etc.acc_property.module'));
            $child_module = $keys[array_search($module, $keys)+1]??null;
        ?>
        @if(!empty($child_module))
            @include('admin.acc.widget.category-list', ['data' => $item->childs, 'module' => $child_module, 'parent' => $item->id])
        @endif
    </li>
    @endforeach
    <a href="{{ route('admin.acc.property.edit', [$module, $parent, 0]) }}" class="btn btn-sm btn-outline-success mb-2"><i class="fa fa-plus"></i> Thêm {{ config("etc.acc_property.module.{$module}") }}</a>
</ol>
