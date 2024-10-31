@extends('admin._layouts.master')
@section('action_area')
    <div class="d-flex align-items-center text-right">
        <a href="/admin/minigame-category"
           class="btn btn-light-primary font-weight-bolder mr-2">
            <i class="ki ki-long-arrow-back icon-sm"></i>
            Back
        </a>
        <div class="btn-group">
            <form action="/admin/minigame-category/{{ $data->id }}/revision/{{ $log->id }}" method="post">
                @csrf
            <button type="submit" class="btn btn-success font-weight-bolder" data-form="formMain" data-submit-close="1">
                <i class="ki ki-check icon-sm"></i>
                {{__('Phục hồi')}}
            </button>
            </form>
        </div>
    </div>
@endsection

{{-- Content --}}
@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <ul style="float: left;padding-left: 0;margin-bottom: 0">
                            <li style="list-style: none;float: left;margin-top: 12px">
                                <span style="background: #A7ABC3;padding: 10px 16px;border-radius: 4px"><i class="menu-icon fas fa-user" style="color: #ffffff;font-size: 16px"></i></span>
                            </li>
                            <li style="list-style: none;float: left;margin-left: 8px;font-size: 16px">
                                <div class="row" style="margin: 0 auto;width: 100%">
                                    <div class="col-md-12 pl-0 pr-0" style="font-size: 16px">
                                        Bài viết được chỉnh sửa bởi
                                        <a href="javascript:void(0)">{{ $log->author->username }}</a>
                                    </div>
                                    <div class="col-md-12 pl-0 pr-0" style="font-size: 16px">
                                        {{ $log->created_at }}
                                    </div>
                                </div>
                            </li>

                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-custom gutter-b">

                <div class="card-body_revision">
                    <div class="row marginauto">
                        <div class="col-md-12 left_right">
                            <span class="revision_title">Tiều đề</span>
                            <div class="viewType" style="margin-top: 8px">
                                <input type="radio" name="_viewtype" id="sidebysidetitle" style="cursor: pointer" onclick="diffUsingJSTitle(0);" /> <label style="cursor: pointer" for="sidebysidetitle">Side by Side Diff</label>
                                &nbsp; &nbsp;
                                <input type="radio" name="_viewtype" id="inlinetitle" style="cursor: pointer" onclick="diffUsingJSTitle(1);" /> <label style="cursor: pointer" for="inlinetitle">Inline Diff</label>
                            </div>
                        </div>
                    </div>
                    <div class="row marginauto revision_body">

                        <div class="col-md-12 left_right">
                            <div id="diffoutputtitle"></div>
                        </div>
                    </div>

                    <div class="row marginauto">
                        <div class="col-md-12 left_right">
                            <span class="revision_title">Mô tả</span>
                            <div class="viewType" style="margin-top: 8px">
                                <input type="radio" name="_viewtype" id="sidebysidedesc" style="cursor: pointer" onclick="diffUsingJSDesc(0);" /> <label style="cursor: pointer" for="sidebysidedesc">Side by Side Diff</label>
                                &nbsp; &nbsp;
                                <input type="radio" name="_viewtype" id="inlinedesc" onclick="diffUsingJSDesc(1);" style="cursor: pointer" /> <label style="cursor: pointer" for="inlinedesc">Inline Diff</label>
                            </div>
                        </div>
                    </div>
                    <div class="row marginauto revision_body">

                        <div class="col-md-12 left_right">
                            <div id="diffoutputdesc"></div>
                        </div>

                    </div>

                    <div class="row marginauto">
                        <div class="col-md-12 left_right">
                            <span class="revision_title">Nội dung</span>
                            <div class="viewType" style="margin-top: 8px">
                                <input type="radio" name="_viewtype" id="sidebyside" style="cursor: pointer" onclick="diffUsingJS(0);" /> <label style="cursor: pointer" for="sidebyside">Side by Side Diff</label>
                                &nbsp; &nbsp;
                                <input type="radio" name="_viewtype" id="inline" style="cursor: pointer" onclick="diffUsingJS(1);" /> <label style="cursor: pointer" for="inline">Inline Diff</label>
                            </div>
                        </div>
                    </div>
                    <div class="row marginauto revision_body">

                        <div class="col-md-12 left_right">
                            <div id="diffoutput"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <textarea id="baseText" style="display: none">{!! $log->content_before !!}</textarea>
    <textarea id="newText" style="display: none">{!! $log->content_after !!}</textarea>

    <textarea id="baseTextDesc" style="display: none">{!! $log->description_before !!}</textarea>
    <textarea id="newTextDesc" style="display: none">{!! $log->description_after !!}</textarea>

    <input type="hidden" id="baseTextTitle" value="{{ $log->title_before }}">
    <input type="hidden" id="newTextTitle" value="{{ $log->title_after }}">

    <input type="hidden" id="contextSize" value="0" />

@endsection

{{-- Styles Section --}}
@section('styles')
    <style>
        .marginauto{
            width: 100%;
            margin: 0 auto;
        }
        .left_right{
            padding-left: 0;
            padding-right: 0;
        }
        .card-body_revision{
            padding: 24px 16px 0 16px;
        }
        .revision_col_left{
            padding-left: 0;
            padding-right: 8px;
        }
        .revision_col_right{
            padding-left: 8px;
            padding-right: 0;
        }
        .revision_title{
            font-size: 16px;
            font-weight: 700;
        }
        .revision_body{
            padding: 16px;
        }
        .red {
            background: pink;
        }
        .green {
            background: lightgreen;
        }
    </style>
@endsection

{{-- Scripts Section --}}
@section('scripts')
    <link rel="stylesheet" type="text/css" href="/assets/backend/assets/css/diffview.css?v={{time()}}"/>
    <script type="text/javascript" src="/assets/backend/assets/js/diffview.js"></script>
    <script type="text/javascript" src="/assets/backend/assets/js/difflib.js"></script>

    <script type="text/javascript">
        diffUsingJS(0)
        function diffUsingJS(viewType) {
            "use strict";
            var byId = function (id) { return document.getElementById(id); },
                base = difflib.stringAsLines($('#baseText').val()),
                newtxt = difflib.stringAsLines($('#newText').val()),
                sm = new difflib.SequenceMatcher(base, newtxt),
                opcodes = sm.get_opcodes(),
                diffoutputdiv = byId("diffoutput"),
                contextSize = $('#contextSize').val();

            diffoutputdiv.innerHTML = "";
            contextSize = contextSize || null;

            diffoutputdiv.appendChild(diffview.buildView({
                baseTextLines: base,
                newTextLines: newtxt,
                opcodes: opcodes,
                baseTextName: "Nội dung cũ",
                newTextName: "Nội dung mới",
                contextSize: null,
                viewType: viewType
            }));
        }

        diffUsingJSDesc(0)
        function diffUsingJSDesc(viewType) {
            "use strict";
            var byId = function (id) { return document.getElementById(id); },
                baseDesc = difflib.stringAsLines($('#baseTextDesc').val()),
                newtxtDesc = difflib.stringAsLines($('#newTextDesc').val()),
                smDesc = new difflib.SequenceMatcher(baseDesc, newtxtDesc),
                opcodesDesc = smDesc.get_opcodes(),
                diffoutputdivDesc = byId("diffoutputdesc"),
                contextSize = $('#contextSize').val();

            diffoutputdivDesc.innerHTML = "";
            contextSize = contextSize || null;

            diffoutputdivDesc.appendChild(diffview.buildView({
                baseTextLines: baseDesc,
                newTextLines: newtxtDesc,
                opcodes: opcodesDesc,
                baseTextName: "Mô tả cũ",
                newTextName: "Mô tả mới",
                contextSize: null,
                viewType: viewType
            }));
        }

        diffUsingJSTitle(0)
        function diffUsingJSTitle(viewType) {
            "use strict";
            var byId = function (id) { return document.getElementById(id); },
                baseTitle = difflib.stringAsLines($('#baseTextTitle').val()),
                newtxtTitle = difflib.stringAsLines($('#newTextTitle').val()),
                smTitle = new difflib.SequenceMatcher(baseTitle, newtxtTitle),
                opcodesTitle = smTitle.get_opcodes(),
                diffoutputdivTitle = byId("diffoutputtitle"),
                contextSize = $('#contextSize').val();

            diffoutputdivTitle.innerHTML = "";
            contextSize = contextSize || null;

            diffoutputdivTitle.appendChild(diffview.buildView({
                baseTextLines: baseTitle,
                newTextLines: newtxtTitle,
                opcodes: opcodesTitle,
                baseTextName: "Tiêu đề cũ",
                newTextName: "Tiêu đề mới",
                contextSize: null,
                viewType: viewType
            }));
        }

    </script>
@endsection



