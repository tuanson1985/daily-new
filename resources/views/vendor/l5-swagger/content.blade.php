<div>
    <div class="sidebar" :class="[{'is-hidden': ! sidebar}]">
        <ul>
            <li style="padding: 8px 16px">
                <input type="text" class="nav-search-navbar">
            </li>
            <li>
                <h2>List Api document</h2>
                <ul id="ul-api-docs">

                    @foreach(config('module.api-document.url') as $key =>$val)
                        <li>
                            <a href="javascript:void(0)" class="url-swagger add-active-api url-swagger-{{ $key }}" data-title="{{ $key }}" data-url="{{ config('app.url') }}/{{ $val }}">{{ $key }}</a>
                        </li>
                    @endforeach
                </ul>
            </li>
        </ul>
    </div>
    <div class="documentation is-dark" :class="{'expanded': ! sidebar}">
        <hr/>
        <h1>API DOCUMENT</h1>

        @php
            $name = '';
            foreach(config('module.api-document.url') as $key =>$val){
                $name = $key;
                break;
            }
        @endphp
        <p class="title-api">Quản lý API {{ $name }}</p>
        <hr>
        <input type="text" class="search-header nav-search-input">
        <hr>
        <div id="swagger-ui"></div>
    </div>
</div>


