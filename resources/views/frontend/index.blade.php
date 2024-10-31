{{-- Extends layout --}}
@extends('frontend._layouts.master')

{{-- Content --}}
@section('content')

    <h1>Merchant</h1>

@endsection

@section('styles')

@endsection

{{-- Scripts Section --}}
@section('scripts')

    <script type="text/javascript">
        $('.parrent').on('click', function(e){
            var id= $(this).attr('rel');

            $(".children_"+id).toggle();

        });
    </script>
@endsection
