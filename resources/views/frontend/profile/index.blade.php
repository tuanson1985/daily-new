{{-- Extends layout --}}
@extends('frontend._layouts.master')
@section('action_area')
    <div class="d-flex align-items-center text-right">
    </div>
@endsection
{{-- Content --}}
@section('content')
    <!--begin::Card-->
    <div class="card card-custom">

        <!--begin::Card header-->
        <div class="card-header card-header-tabs-line nav-tabs-line-3x">
            <!--begin::Toolbar-->
            <div class="card-toolbar">
                <ul class="nav nav-tabs nav-bold nav-tabs-line nav-tabs-line-3x">
                    <!--begin::Item-->
                    <li class="nav-item mr-3">
                        <a class="nav-link active" data-toggle="tab" aria-controls="kt_user_edit_tab_1" role="tab" href="#kt_user_edit_tab_1">
                  <span class="nav-icon">
                     <span class="svg-icon">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             xmlns:xlink="http://www.w3.org/1999/xlink"
                             width="24px" height="24px" viewBox="0 0 24 24"
                             version="1.1">
                           <g stroke="none" stroke-width="1" fill="none"
                              fill-rule="evenodd">
                              <polygon points="0 0 24 0 24 24 0 24"/>
                              <path
                                  d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z"
                                  fill="#000000" fill-rule="nonzero"/>
                              <path
                                  d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z"
                                  fill="#000000" opacity="0.3"/>
                           </g>
                        </svg>
                         <!--end::Svg Icon-->
                     </span>
                  </span>
                            <span class="nav-text font-size-lg">{{__('Hồ sơ cá nhân')}}</span>
                        </a>
                    </li>
                    <li class="nav-item mr-3">
                        <a class="nav-link" role="tab"   data-toggle="tab" aria-controls="kt_user_edit_tab_3" href="#kt_user_edit_tab_3">
                  <span class="nav-icon">
                     <span class="svg-icon">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             xmlns:xlink="http://www.w3.org/1999/xlink"
                             width="24px" height="24px" viewBox="0 0 24 24"
                             version="1.1">
                           <g stroke="none" stroke-width="1" fill="none"
                              fill-rule="evenodd">
                              <rect x="0" y="0" width="24" height="24"/>
                              <path
                                  d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                  fill="#000000" opacity="0.3"/>
                              <path
                                  d="M12,11 C10.8954305,11 10,10.1045695 10,9 C10,7.8954305 10.8954305,7 12,7 C13.1045695,7 14,7.8954305 14,9 C14,10.1045695 13.1045695,11 12,11 Z"
                                  fill="#000000" opacity="0.3"/>
                              <path
                                  d="M7.00036205,16.4995035 C7.21569918,13.5165724 9.36772908,12 11.9907452,12 C14.6506758,12 16.8360465,13.4332455 16.9988413,16.5 C17.0053266,16.6221713 16.9988413,17 16.5815,17 C14.5228466,17 11.463736,17 7.4041679,17 C7.26484009,17 6.98863236,16.6619875 7.00036205,16.4995035 Z"
                                  fill="#000000" opacity="0.3"/>
                           </g>
                        </svg>
                         <!--end::Svg Icon-->
                     </span>
                  </span>
                            <span class="nav-text font-size-lg">{{__('Đổi mật khẩu')}}</span>
                        </a>
                    </li>
                    <!--end::Item-->
                </ul>
            </div>
            <!--end::Toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body">
            <div class="tab-content">
                <!--begin::Tab-->
                <div class="tab-pane show active px-7" id="kt_user_edit_tab_1" role="tabpanel" id="kt_user_edit_tab_1">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-xl-2"></div>
                        <div class="col-xl-7 my-2">
                        {{--                  <form class="form" action="{{ route('admin.post-security') }}" method="POST" id="form-security">--}}
                        {{--                     {{ csrf_field() }}--}}
                        <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-form-label col-3 text-lg-right text-left">Avatar</label>
                                <div class="col-9">
                                    <div class="image-input image-input-empty image-input-outline"
                                         id="kt_user_edit_avatar"
                                         style="background-image: url('{{asset('assets/backend/themes/media/users/blank.png')}}')">
                                        <div class="image-input-wrapper"></div>
                                        <label
                                            class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                            data-action="change" data-toggle="tooltip" title=""
                                            data-original-title="Change avatar">
                                            <i class="fa fa-pen icon-sm text-muted"></i>
                                            <input type="file" name="profile_avatar" accept=".png, .jpg, .jpeg"/>
                                            <input type="hidden" name="profile_avatar_remove"/>
                                        </label>
                                        <span
                                            class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                            data-action="cancel" data-toggle="tooltip" title="Cancel avatar">
                           <i class="ki ki-bold-close icon-xs text-muted"></i>
                           </span>
                                        <span
                                            class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                            data-action="remove" data-toggle="tooltip" title="Remove avatar">
                           <i class="ki ki-bold-close icon-xs text-muted"></i>
                           </span>
                                    </div>
                                </div>
                            </div>
                            <!--end::Group-->
                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-form-label col-3 text-lg-right text-left">Email</label>
                                <div class="col-9">
                                    <div class="input-group input-group-lg ">
                                        <div class="input-group-prepend">
                              <span class="input-group-text">
                              <i class="la la-at"></i>
                              </span>
                                        </div>
                                        <input type="text" class="form-control form-control-lg" readonly
                                               value="{{Auth::guard('frontend')->user()->email}}" placeholder="Email"/>
                                    </div>
                                </div>
                            </div>
                        {{-- <div class="form-group row">
                           <label class="col-form-label col-3 text-lg-right text-left"></label>
                           <div class="col-9">
                              <a href="{{route('admin.email')}}">Cập nhật Email</a>
                           </div>
                        </div> --}}
                        <!--end::Group-->
                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-form-label col-3 text-lg-right text-left">{{__('Số điện thoại')}}</label>
                                <div class="col-9">
                                    <div class="input-group input-group-lg ">
                                        <div class="input-group-prepend">
                              <span class="input-group-text">
                              <i class="la la-phone"></i>
                              </span>
                                        </div>
                                        <input type="text" class="form-control form-control-lg" readonly
                                               value="{{Auth::guard('frontend')->user()->phone}}" placeholder="Phone"/>
                                    </div>
                                </div>
                            </div>
                            <!--end::Group-->
                            <!--begin::Group-->
                            <div class="form-group row">
                                <label class="col-form-label col-3 text-lg-right text-left">{{__('Số dư')}}</label>
                                <div class="col-9">
                                    <div class="input-group input-group-lg ">
                                        <div class="input-group-prepend">
                              <span class="input-group-text">
                              <i class="la la-dollar"></i>
                              </span>
                                        </div>
                                        <input type="text" class="form-control form-control-lg" readonly
                                               value="{{currency_format(Auth::guard('frontend')->user()->balance)}}" placeholder="{{__('Số dư')}}"/>
                                    </div>
                                </div>
                            </div>
                            {{--                  </form>--}}
                        </div>
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Tab-->
                <!--begin::Tab-->
                <div class="tab-pane px-7" id="kt_user_edit_tab_3" role="tabpanel" id="kt_user_edit_tab_3">
                    <form class="form" action="{{ route('frontend.postChangeCurrentPassword') }}" method="POST">
                    {{ csrf_field() }}
                    <!--begin::Body-->
                        <div class="card-body">
                            <!--begin::Row-->
                            <div class="row">
                                <div class="col-xl-2"></div>
                                <div class="col-xl-7">
                                    <!--begin::Group-->
                                    <div class="form-group row">
                                        <label class="col-form-label col-3 text-lg-right text-left">{{__('Mật khẩu cũ')}}</label>
                                        <div class="col-9">
                                            <input class="form-control form-control-lg  mb-1"
                                                   type="password" name="old_password" value=""/>
                                        </div>
                                    </div>
                                    <!--end::Group-->
                                    <!--begin::Group-->
                                    <div class="form-group row">
                                        <label class="col-form-label col-3 text-lg-right text-left">{{__('Mật khẩu mới')}}</label>
                                        <div class="col-9">
                                            <input class="form-control form-control-lg " type="password" name="password" value=""/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-3 text-lg-right text-left">{{__('Xác nhận mật khẩu mới')}}</label>
                                        <div class="col-9">
                                            <input class="form-control form-control-lg " type="password" name="password_confirmation" value=""/>
                                        </div>
                                    </div>
                                    <!--end::Group-->
                                    <!--begin::Group-->
                                    <!--end::Group-->
                                </div>
                            </div>
                            <!--end::Row-->
                        </div>
                        <!--end::Body-->
                        <!--begin::Footer-->
                        <div class="card-footer pb-0">
                            <div class="row">
                                <div class="col-xl-2"></div>
                                <div class="col-xl-7">
                                    <div class="row">
                                        <div class="col-3"></div>
                                        <div class="col-9">
                                            <button type="submit" class="btn btn-light-primary font-weight-bold">{{ __('Cập nhật') }}</button>
                                            <button type="reset" class="btn btn-clean font-weight-bold">{{ __('Hủy') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Footer-->
                    </form>
                </div>
                <!--end::Tab-->
            </div>
        </div>
        <!--begin::Card body-->
    </div>
    <!--end::Card-->

    <input type="text" id="security" value="{{Auth::guard('frontend')->user()->is_security}}" hidden>
@endsection
{{-- Styles Section --}}
@section('styles')
@endsection
{{-- Scripts Section --}}
@section('scripts')
    <script src="{{asset('assets/backend/themes/js/pages/custom/user/edit-user.js')}}"></script>
    <script>

        $(document).ready(function () {

            $('body').on('click', '.load-modal', function(e) {
                e.preventDefault();
                var curModal = $('#LoadModal');
                curModal.find('.modal-content .modal-body').html("<div class=\" overlay overlay-block\"><div class=\"overlay-layer rounded bg-primary-o-20\"><div class=\"spinner spinner-track spinner-primary mr-15 \"></div></div></div>");
                curModal.modal('show').find('.modal-content').load($(this).attr('rel'));
            });
            $('#search_aside_menu').donetyping(function() {
                var q = $(this).val();
                if(q == null || q == "" || q == undefined){
                    $('.nav-search-in-values').css('display','none');
                    $('.xoatimkiem').css('display','none');
                    return false
                }
            }, 300);


            $('body').on('click','.closemenusiba',function(){
                $('#kt_aside').removeClass('aside-on');
                $("#kt_aside").removeData("offcanvas-aside");
                $('.aside-overlay').remove();

            })

            $('body').on('click','.xoatimkiem',function(){
                $('#search_aside_menu').val('');
                $('.nav-search-in-values').css('display','none');
                $('.xoatimkiem').css('display','none');
                const KEYWORD = changeTitleToSlug($('#search_aside_menu').val());

                let index = 0;
                $("#kt_aside_menu .menu-nav li").each(function () {
                    var textone = $(this).text().toLowerCase();

                    textone = changeTitleToSlug(textone);

                    $(this).toggle(textone.indexOf(KEYWORD) > -1);

                });
                // return false
            })

            $('#search_aside_menu').on('keyup', function () {
                var abc = $(this).val();

                if (abc == null || abc == '' || abc == undefined){}else {
                    $('.xoatimkiem').css('display','block');
                }

                const KEYWORD = changeTitleToSlug($(this).val());

                let index = 0;
                $("#kt_aside_menu .menu-nav li").each(function () {
                    var textone = getTextWithSpaces(this);
                    // var textone = $(this).text().toLowerCase();

                    textone = changeTitleToSlug(textone);

                    $(this).toggle(textone.indexOf(KEYWORD) > -1);

                    if (textone.indexOf(KEYWORD) > -1) {
                        let pre_el = $(this).prev();
                        let i = $(this).index();
                        while (i) {
                            if (pre_el.hasClass('menu-section') && !$(this).hasClass('menu-section')) {
                                pre_el.show();
                                break;
                            }
                            pre_el = pre_el.prev();
                            i--;
                        }
                        index++;
                    }
                });
                let index2 = 0;
                $("#kt_aside_menu .menu-nav .menu-item-submenu").each(function () {
                    var textone = $(this).children('a').find('.menu-text').text().toLowerCase();
                    textone = changeTitleToSlug(textone);
                    if (textone.indexOf(KEYWORD) > -1 ) {
                        $(this).find('.menu-subnav').children().show();
                        let pre_el = $(this).prev();
                        let i = $(this).index();
                        while (i) {
                            if (pre_el.hasClass('menu-section')) {
                                pre_el.show();
                                break;
                            }
                            pre_el = pre_el.prev();
                            i--;
                        }

                        index2++;
                    }

                    // $(this).addClass('menu-item-open');
                });
                let index3 = 0;
                $("#kt_aside_menu .menu-nav .menu-section").each(function () {
                    var textone = $(this).find('.menu-text').text().toLowerCase();
                    textone = changeTitleToSlug(textone);
                    if (textone.indexOf(KEYWORD) > -1) {
                        let next_el = $(this).next();
                        while (next_el.hasClass('menu-item')){
                            next_el.show();
                            next_el.find('.menu-subnav').children().show();
                            next_el = next_el.next();

                        }
                    }
                    index3++;
                })

                $("#kt_aside_menu .menu-nav li.menu-item-parent").hide();

                var html = '';

                if (parseInt(index) == 0){
                    html += '<span class="kocodulieumenu" style="color: red;font-size: 14px;padding: 8px 12px">';
                    html += 'Dữ liệu cần tìm không tồn tại';
                    html += '</span>';
                }else {
                    html = $('#kt_aside_menu').html();
                }

                $('#result-search-menu').html(html);
                $('.nav-search-in-values').css('display','block');
                $('.nav-search-in-value-load-more').css('display','none');
            });

            $('body').on('click', '#result-search-menu .menu-toggle', function(e) {
                e.preventDefault();
                let addclasss = $(this).parent();
                // alert(addclasss);
                if (addclasss.hasClass('menu-item-open')) {
                    addclasss.removeClass('menu-item-open');

                    return false;
                }
                addclasss.addClass('menu-item-open');
            });

            function changeTitleToSlug(title) {
                var slug;


                //Đổi chữ hoa thành chữ thường
                slug = title.toLowerCase();

                //Đổi ký tự có dấu thành không dấu
                slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a');
                slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e');
                slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i');
                slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o');
                slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u');
                slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y');
                slug = slug.replace(/đ/gi, 'd');
                //Xóa các ký tự đặt biệt
                slug = slug.replace(/\`|\~|\!|\@|\#|\||\$|\%|\^|\&|\*|\(|\)|\+|\=|\,|\.|\/|\?|\<|\'|\"|\:|\;|_/gi, '');
                //Đổi khoảng trắng thành ký tự gạch ngang
                slug = slug.replace(/ /gi, "-");
                //Đổi nhiều ký tự gạch ngang liên tiếp thành 1 ký tự gạch ngang
                //Phòng trường hợp người nhập vào quá nhiều ký tự trắng
                slug = slug.replace(/\-\-\-\-\-/gi, '-');
                slug = slug.replace(/\-\-\-\-/gi, '-');
                slug = slug.replace(/\-\-\-/gi, '-');
                slug = slug.replace(/\-\-/gi, '-');
                //Xóa các ký tự gạch ngang ở đầu và cuối
                slug = '@' + slug + '@';
                slug = slug.replace(/\@\-|\-\@|\@/gi, '');
                //In slug ra textbox có id “slug”

                return slug;
            }
            $('body').on('click','.btn-access-user',function(){
                var id = $(this).data('id');
                $('#access_user .id-access-user').attr('value', id);
                $('#LoadModal').modal('hide');
                $('#access_user').modal('show');
            });

        });

        function collectTextNodes(element, texts) {
            for (var child= element.firstChild; child!==null; child= child.nextSibling) {
                if (child.nodeType===3)
                    texts.push(child);
                else if (child.nodeType===1)
                    collectTextNodes(child, texts);
            }
        }
        function getTextWithSpaces(element) {
            var texts= [];
            collectTextNodes(element, texts);
            for (var i= texts.length; i-->0;)
                texts[i]= texts[i].data;
            return texts.join('>');
        }

        $(document).ready(function () {
            let $scroll = $('#kt_aside_menu');
            let elm_active = $('.menu-item-active');
            let root_elm = $('.menu-item-submenu.menu-item-open');
            if(root_elm.length) {
                $scroll.scrollTop(root_elm.position().top + $scroll.scrollTop() - $scroll.height()/4);
            } else {
                $scroll.scrollTop(elm_active.position().top + $scroll.scrollTop() - ($scroll.height()/2 - (elm_active.height() / 2)));
            }
        });
    </script>
@endsection
