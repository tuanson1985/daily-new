{{-- Extends layout --}}
@extends('admin._layouts.master')


@section('action_area')

@endsection

{{-- Content --}}
@section('content')

    <div class="card card-custom" id="kt_page_sticky_card">
        <div class="card-header">
            <div class="card-title">
                <h3 class="card-label">
                    {{__('Chi tiết đơn hàng')}} #{{$data->id}} <i class="mr-2"></i>
                    <br/>
                    @if ($data->status == 1)
                    <span class="label label-lg label-pill label-inline label-success mr-2">{{config('module.store-card.status.1')}}</span>
                    {{-- @elseif()     --}}
                    @else
                        
                    @endif
                </h3>
            </div>
            <div class="card-toolbar"></div>

        </div>
        <div class="card-body">
            
        </div>
    </div>


    {{---------------all modal controll-------}}









@endsection
