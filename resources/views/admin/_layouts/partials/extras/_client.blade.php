@if (auth()->user()->account_type == 1)
   @if (isset($data) && count($data) > 0)
   <div class="dropdown">
      <div class="topbar-item">
         <div class="dropdown bootstrap-select form-control datatable-input datatable-input-select">
            <select id="select-client" class="form-control datatable-input datatable-input-select selectpicker select-client" data-live-search="true" title=" {{\Session::has('shop_name') ? \Session::get('shop_id') .' - '. \Session::get('shop_name') : '-- Tất cả shop --'}} " tabindex="null">
               <option class="bs-title-option" value="">Tất cả shop</option>
               @foreach ($data as $key => $item)
                  <option value="{{$item->id}}">{{$item->id}} - {{$item->domain}}</option>
               @endforeach
            </select>
            <div class="dropdown-menu" style="max-height: 241px; overflow: hidden; min-height: 58px;">
               <div class="bs-searchbox">
                  <input type="search" class="form-control" autocomplete="off" role="combobox" aria-label="Search" aria-controls="bs-select-1" aria-autocomplete="list">
               </div>
               <div class="inner show" role="listbox" id="bs-select-1" tabindex="-1" style="max-height: 171px; overflow-y: auto; min-height: 0px;">
                  <ul class="dropdown-menu inner show" role="presentation" style="margin-top: 0px; margin-bottom: 0px">
                        <li><a role="option" class="dropdown-item" id="bs-select-1-0" tabindex="0"><span class="text">Tất cả shop</span></a></li>
                     @foreach ($data as $key => $item)
                           <li><a role="option" class="dropdown-item" id="bs-select-1-0" tabindex="0"><span class="text">{{$item->id}} - {{$item->domain}}</span></a></li>
                     @endforeach
                  </ul>
               </div>
            </div>
         </div>
      </div>
   </div>
   <style>
      @media (min-width: 992px){
         :not(.input-group) > .bootstrap-select.form-control:not([class*="col-"]){
            min-width: 200px !important;
         }
      }
      .select-client .dropdown-menu.show{
         max-height: 200px!important;
      }
   </style>
   @endif
@endif
