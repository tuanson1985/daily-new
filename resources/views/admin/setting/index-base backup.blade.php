{{-- Extends layout --}}
@extends('admin._layouts.master')

{{-- Content --}}
@section('content')
    <form class="form">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">
                        Sticky Form Actions <i class="mr-2"></i>
                    </h3>
                </div>
                <div class="card-toolbar">
                    <a href="#" class="btn btn-light-primary font-weight-bolder mr-2">
                        <i class="ki ki-long-arrow-back icon-sm"></i>
                        Back
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary font-weight-bolder">
                            <i class="ki ki-check icon-sm"></i>
                            Save Form
                        </button>
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        </button>
                        <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                            <ul class="nav nav-hover flex-column">
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon flaticon2-reload"></i>
                                        <span class="nav-text">Save & continue</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon flaticon2-add-1"></i>
                                        <span class="nav-text">Save & add new</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon flaticon2-power"></i>
                                        <span class="nav-text">Save & exit</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="form-group ">
                    <label>Full Name:</label>
                    <input type="email" class="form-control " placeholder="Enter full name"/>
                    <span class="form-text text-muted">Please enter your full name</span>
                </div>

                <div class="form-group is-invalid">
                    <label>Email address:</label>
                    <input type="email" class="form-control" placeholder="Enter email"/>
                    <span class="form-text text-muted">We'll never share your email with anyone else</span>
                </div>

                <div class="separator separator-dashed my-5"></div>

                <div class="form-group">
                    <label>Subscription</label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                        <input type="text" class="form-control" placeholder="99.9"/>
                    </div>
                </div>

                <div class="form-group">
                    <label>Communication:</label>
                    <div class="checkbox-list">
                        <label class="checkbox checkbox-outline">
                            <input type="checkbox"/>
                            <span></span>
                            Email
                        </label>
                        <label class="checkbox checkbox-outline">
                            <input type="checkbox"/>
                            <span></span>
                            SMS
                        </label>
                        <label class="checkbox checkbox-outline">
                            <input type="checkbox"/>
                            <span></span>
                            Phone
                        </label>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col text-right">
                        <a href="#" class="btn btn-light-primary font-weight-bolder mr-2">
                            <i class="ki ki-long-arrow-back icon-sm"></i>
                            Back
                        </a>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary font-weight-bolder">
                                <i class="ki ki-check icon-sm"></i>
                                Save Form
                            </button>
                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            </button>
                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                <ul class="nav nav-hover flex-column">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <i class="nav-icon flaticon2-reload"></i>
                                            <span class="nav-text">Save & continue</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <i class="nav-icon flaticon2-add-1"></i>
                                            <span class="nav-text">Save & add new</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <i class="nav-icon flaticon2-power"></i>
                                            <span class="nav-text">Save & exit</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </form>

@endsection

{{-- Styles Section --}}
@section('styles')

@endsection
{{-- Scripts Section --}}
@section('scripts')

@endsection
