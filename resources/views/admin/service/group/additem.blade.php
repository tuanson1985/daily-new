{{Form::open(array('route'=>array('admin.'.$module.'.destroy',0),'class'=>'form-horizontal','id'=>'form-delete','method'=>'DELETE'))}}
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel"> {{__('Danh sách trong nhóm')}}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <i aria-hidden="true" class="ki ki-close"></i>
    </button>
</div>
<div class="modal-body">
    <input type="text" value="" id="id-group" style="display: none">
    <div class="form-group row">
        <div class="col-12 col-md-12">
            <label>{{ __('Tìm kiếm') }}</label>
            <div class="input-icon">
                <input type="text" class="form-control" id="txtSearch" placeholder="Search...">
                <span>
                                    <i class="flaticon2-search-1 icon-md"></i>
                                </span>
            </div>

            <div class="nav-search-in-value" style="display: none;">
                <div id="result-search">

                </div>
                <style>
                    #result-search{
                        background-color: #ffffff;
                        background-clip: padding-box;
                        border: 1px solid #E4E6EF;
                        padding: 10px;
                    }
                    #result-search .rs-item{
                        margin-bottom: 10px;
                    }
                    #result-search .rs-item:hover{
                        background-color: #f7f8fa !important;
                    }

                    #result-search .rs-item a .info{
                        margin-left: 10px;
                    }
                    #result-search .rs-item a .info p{
                        margin-bottom: 0.2rem;
                    }


                </style>

            </div>

            @if ($errors->has('title'))
                <span class="form-text text-danger">{{ $errors->first('title') }}</span>
            @endif
        </div>
    </div>
    <div class="form-group row">
        <div class="col-12 col-md-12">
            <table class="table table-bordered table-hover table-checkable">
                <thead>
                    <tr>
                        <th>Danh sách game</th>
                    </tr>
                </thead>
                {{--<tbody id="list-item">--}}
                <tbody class="dd" id="nestable">
                    {{--@if(isset($datas))
                        {!! $datas !!}
                    @endif--}}
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="hidden" name="id" class="id" value=""/>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Đóng')}}</button>
</div>
{{ Form::close() }}

