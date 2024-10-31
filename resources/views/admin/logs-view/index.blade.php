{{-- Extends layout --}}
@extends('admin._layouts.master')

{{-- Content --}}
@section('content')
    <link rel="stylesheet" href="/assets/backend/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/backend/assets/css/dataTables.css">
    <link rel="stylesheet" href="/assets/backend/assets/css/style.css">
    <script src="/assets/backend/assets/js/main.js"></script>

{{--        @dd($data['folders'])--}}



    <div class="container-fluid logview">
        <div class="row logviewrow">
            <div class="col-md-12 sidebar mb-3 logviewrow__col1">
                <div class="row">
                    <div class="col-md-6">
                        <h1><i class="fa fa-calendar" aria-hidden="true" style="margin-right: 8px"></i>Log Viewer</h1>
                        {{--<p class="text-muted"><i>by Rap2h</i></p>--}}




                        <div class="custom-control custom-switch" style="padding-bottom:20px;">
                            <input type="checkbox" class="custom-control-input" id="darkSwitch">
                            {{--<label class="custom-control-label" for="darkSwitch" style="margin-top: 6px;">Dark Mode</label>--}}
                        </div>
                    </div>
                    <div class="col-auto" style="margin-left: auto">
                        <div class="list-group div-scroll">

                            <select class="form-control selectlogview" name="selectlogview">
                                @foreach($data['files'] as $file)

                                    @if(strlen(strstr($file, ".log")) > 0)
                                        @if($file != 'laravel.log')
                                            <option value="?l={{ \Illuminate\Support\Facades\Crypt::encrypt($file) }}">
                                                <a href="?l={{ \Illuminate\Support\Facades\Crypt::encrypt($file) }}"
                                                   class="list-group-item @if ($data['current_file'] == $file) llv-active @endif">
                                                    {{ $file }}
                                                </a>
                                            </option>
                                        @endif
                                    @endif

                                @endforeach
                            </select>

                        </div>
                    </div>
                </div>

            </div>
            <div class="col-12 table-container logviewrow__col2">
                @if ($data['logs'] === null)
                    <div>
                        Log file >50M, please download it.
                    </div>
                @else
                    @include('admin.logs-view.function.__getdata')
                @endif
                <div class="p-3">
                    @if($data['current_file'])
                        <a id="clean-log" href="?clean={{ \Illuminate\Support\Facades\Crypt::encrypt($data['current_file']) }}">
                            <span class="fa fa-sync"></span> Clean file
                        </a>
                        <a id="delete-log" href="?del={{ \Illuminate\Support\Facades\Crypt::encrypt($data['current_file']) }}">
                            <span class="fa fa-trash"></span> Delete file
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Styles Section --}}
@section('styles')

@endsection
{{-- Scripts Section --}}
@section('scripts')

    {{--<script src="/assets/backend/assets/js/jquery-3.2.1.js"></script>--}}
    <script src="/assets/backend/assets/js/bootstrap.min.js"></script>
    <script src="/assets/backend/assets/js/all.js"></script>
    <script src="/assets/backend/assets/js/dataTables.min.js"></script>
    <script src="/assets/backend/assets/js/data.js"></script>
    <script src="/assets/backend/assets/js/index.js"></script>
    <script>
        $(document).ready(function () {
            $(".selectlogview").on("change", function() {
                var url = $(this).val(); // get selected value
                if (url != '') { // require a URL
                    window.location = url; // redirect
                }
                return false;
            });
        })
    </script>

@endsection
