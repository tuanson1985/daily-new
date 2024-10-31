{{--@if (count($errors->all()) > 0)--}}
{{--<div class="alert alert-danger alert-block">--}}
{{--<button type="button" class="close" data-dismiss="alert">&times;</button>--}}
{{--<strong>Error !</strong> Please check the form below for errors.--}}
{{--</div>--}}
{{--@endif--}}
{{--@if ($message = Session::get('success_custom'))--}}
{{--<div class="alert alert-success alert-block">--}}
{{--<button type="button" class="close" data-dismiss="alert">&times;</button>--}}
{{--<strong>Success !</strong>--}}
{{--@if(is_array($message)) @foreach ($message as $m) {{ $m }} @endforeach--}}
{{--@else {{ $message }} @endif--}}
{{--</div>--}}
{{--@endif--}}
{{--@if ($message = Session::get('success'))--}}
{{--<div class="alert alert-success alert-block">--}}
{{--<button type="button" class="close" data-dismiss="alert">&times;</button>--}}
{{--<strong>Success !</strong>--}}
{{--@if(is_array($message)) @foreach ($message as $m) {{ $m }} @endforeach--}}
{{--@else {{ $message }} @endif--}}
{{--</div>--}}
{{--@endif--}}
{{--@if ($message = Session::get('error'))--}}
{{--<div class="alert alert-danger alert-block">--}}
{{--<button type="button" class="close" data-dismiss="alert">&times;</button>--}}
{{--<h4>Error !</h4>--}}
{{--@if(is_array($message)) @foreach ($message as $m) {{ $m }} @endforeach--}}
{{--@else {{ $message }} @endif--}}
{{--</div>--}}
{{--@endif--}}
{{--@if ($message = Session::get('error_custom'))--}}
{{--<div class="alert alert-danger alert-block">--}}
{{--<button type="button" class="close" data-dismiss="alert">&times;</button>--}}
{{--<strong>Error !</strong>--}}
{{--@if(is_array($message)) @foreach ($message as $m) {{ $m }} @endforeach--}}
{{--@else {{ $message }} @endif--}}
{{--</div>--}}
{{--@endif--}}
{{--@if ($message = Session::get('warning'))--}}
{{--<div class="alert alert-warning alert-block">--}}
{{--<button type="button" class="close" data-dismiss="alert">&times;</button>--}}
{{--<h4>Warning</h4>--}}
{{--@if(is_array($message)) @foreach ($message as $m) {{ $m }} @endforeach--}}
{{--@else {{ $message }} @endif--}}
{{--</div>--}}
{{--@endif--}}
{{--@if ($message = Session::get('info'))--}}
{{--<div class="alert alert-info alert-block">--}}
{{--<button type="button" class="close" data-dismiss="alert">&times;</button>--}}
{{--<h4>Info</h4>--}}
{{--@if(is_array($message)) @foreach ($message as $m) {{ $m }} @endforeach--}}
{{--@else {{ $message }} @endif--}}
{{--</div>--}}
{{--@endif--}}

<script>
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-center",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "4000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
@if($message = Session::get('success'))
		toastr.success('{{$message}}');
@elseif($message = Session::get('error'))
        toastr.error('{{$message}}');
@endif

@if($messages=$errors->all())
		toastr.error('{{$messages[0]}}');
@endif

</script>

{{--@if($errors->has())--}}
{{--@foreach ($errors->all() as $error)--}}
{{--<div>{{ $error }}</div>--}}
{{--@endforeach--}}
{{--@endif--}}



