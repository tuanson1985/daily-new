<div class="fixed pin-t pin-x z-40">
{{--    <div class="bg-gradient-primary text-white h-1"></div>--}}
    @php
        $url = '';
        foreach(config('module.api-document.url') as $key =>$val){
            $url = '/'.$val;
            break;
        }
    @endphp
    <nav class="flex items-center justify-between text-black bg-navbar shadow-xs h-16" id="header-api">
        <div class="flex items-center flex-no-shrink">
            <a href="https://hqgroups.vn/" class="flex items-center logo-hqplay flex-no-shrink text-black ml-sm-4">
                <img src="https://hqgroups.vn/storage/images/AFOEYDF03y_1601431263.jpg" alt="">
            </a>

            <div class="switch">
                <input type="checkbox" name="1" id="1" v-model="sidebar" class="switch-checkbox" />
                <label class="switch-label" for="1"></label>
            </div>
        </div>
{{--        @include('admin._layouts.partials.extras._client')--}}
        <input type="text" class="nav-search" name="url-base" value="{{ config('app.url') }}/api/v1">
        <div class="block mx-4 flex items-center">

            <larecipe-button tag="a" href="{{ config('app.url') }}/admin" target="__blank" type="black" class="mx-2 px-4">
                @if(config('app.env') == "production")
                    LIVE
                @else
                    {{ config('app.env') }}
                @endif
            </larecipe-button>

            <larecipe-dropdown>
                <larecipe-button type="primary" class="flex">
                    1.0 <i class="mx-1 fa fa-angle-down"></i>
                </larecipe-button>

                <template slot="list">
                    <ul class="list-reset">
                        <li class="py-2 hover:bg-grey-lightest">
                            <a class="px-6 text-grey-darkest" href="http://127.0.0.1:8000/docs/1.0/root/api">1.0</a>
                        </li>
                    </ul>
                </template>
            </larecipe-dropdown>


        </div>

    </nav>
</div>
