
<p class="font-size-h4 font-size-lg-h2">Tổng sản lượng</p>
<ol class="dd-list" id="config-total-quantity">
    @foreach(config('module.telegram.report.total_output.module') as $key => $module)
        <li class="dd-item nested-list-item">
            <div class="nested-list-content" style="padding-left: 10px;">
                <div class="m-checkbox">
                    <label class="checkbox checkbox-outline">
                        <input type="checkbox" class="is-module" data-index="" data-key="{{ @$module['key'] }}">
                        <span></span>
                        {{ $module['title'] ?? '' }}
                    </label>
                </div>
            </div>
            <ol class="dd-list">
                @foreach($module['indexs'] as $key_index => $index)
                    <li class="dd-item nested-list-item">
                        <div class="nested-list-content" style="padding-left: 10px;">
                            <div class="m-checkbox">
                                <label class="checkbox checkbox-outline">
                                    <input type="checkbox" class="is-index" data-key="{{ @$index['key'] }}" data-default="{{ @$index['default'] }}" data-parent="{{ @$module['key'] }}">
                                    <span></span>
                                    {{ $index['title'] ?? '' }}
                                </label>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ol>
        </li>
    @endforeach
</ol>
<div class="separator separator-solid my-7"></div>
<p class="font-size-h4 font-size-lg-h2">Người dùng</p>
<ol class="dd-list" id="config-user">
    @foreach(config('module.telegram.report.user.module') as $key => $module)
        <li class="dd-item nested-list-item">
            <div class="nested-list-content" style="padding-left: 10px;">
                <div class="m-checkbox">
                    <label class="checkbox checkbox-outline">
                        <input type="checkbox" class="is-module" data-index="" data-key="{{ @$module['key'] }}">
                        <span></span>
                        {{ $module['title'] ?? '' }}
                    </label>
                </div>
            </div>
            <ol class="dd-list">
                @foreach($module['indexs'] as $key_index => $index)
                    <li class="dd-item nested-list-item">
                        <div class="nested-list-content" style="padding-left: 10px;">
                            <div class="m-checkbox">
                                <label class="checkbox checkbox-outline">
                                    <input type="checkbox" class="is-index" data-key="{{ @$index['key'] }}" data-default="{{ @$index['default'] }}" data-parent="{{ @$module['key'] }}">
                                    <span></span>
                                    {{ $index['title'] ?? '' }}
                                </label>
                            </div>
                        </div>

                    </li>
                @endforeach
            </ol>
        </li>
    @endforeach
</ol>
