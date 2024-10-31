    <div class="wizard wizard-1" id="kt_wizard" data-wizard-state="step-first" data-wizard-clickable="false">
        <div class="wizard-nav border-bottom">
            <div class="wizard-steps p-8 p-lg-10">
                <div class="wizard-step" data-wizard-type="step" data-wizard-state="current">
                    <div class="wizard-label">
                        <i class="wizard-icon flaticon-settings-1"></i>
                        <h3 class="wizard-title">1. Cài đặt ứng dụng xác thực</h3>
                    </div>
                </div>
                <div class="wizard-step" data-wizard-type="step">
                    <div class="wizard-label">
                        <i class="wizard-icon flaticon-responsive"></i>
                        <h3 class="wizard-title">2. Kích hoạt bảo mật</h3>
                    </div>
                </div>
                <div class="wizard-step" data-wizard-type="step">
                    <div class="wizard-label">
                        <i class="wizard-icon flaticon-globe"></i>
                        <h3 class="wizard-title">5. Sử dụng</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center my-10 px-8 my-lg-15 px-lg-10">
            <div class="col-xl-12 col-xxl-7">
                <form class="form" id="kt_form">
                    <div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
                        <h3 class="mb-10 font-weight-bold text-dark">Tải ứng dụng xác thực</h3>
                        <p>Trước tiên, bạn cần tải và cài đặt một ứng dụng xác thực trên điện thoại di động của bạn. 
                            Các ứng dụng phổ biến cho việc này bao gồm "Google Authenticator" và "Authy." 
                            Hãy tìm và tải ứng dụng xác thực từ cửa hàng ứng dụng của bạn.</p> 
                            <div class="text-center">
                                <a data-fancybox="gallerycoverDetail" href="/assets/backend/images/2fa_6.jpg">
                                    <img src="/assets/backend/images/2fa_6.jpg" style="max-width:200px;max-height:300px" alt="" >
                                 </a>
                            </div>
                            <br>
                        <h3 class="mb-10 font-weight-bold text-dark">Đăng nhập vào tài khoản dành cho Google 2FA</h3> 
                        <p>Với Google 2FA có chế độ sử dụng không cần tài khoản, nhưng để đảm bảo tính bảo mật, các bạn nên sử dụng tài khoản gmail được liên kết để đăng nhập vào Google 2Fa</p>                      
                    </div>
                    <div class="pb-5" data-wizard-type="step-content">
                        <h4 class="mb-10 font-weight-bold text-dark">Hướng dẫn liên kết và kích hoạt bảo mật với tài khoản Google 2FA</h4>
                        <p>
                            Khi đã đăng nhập thành công với Google 2FA, để thêm mới liên kết, bạn lựa chọn thêm mã để tạo một liên kết mới. Sau đó chọn phương thức quét mã QR:
                        </p>
                        <div class="text-center">
                            <a data-fancybox="gallerycoverDetail" href="/assets/backend/images/2fa_1.png" class="mr-4">
                                <img src="/assets/backend/images/2fa_1.png" style="max-width:200px;max-height:300px" alt="" >
                             </a>
                            <a data-fancybox="gallerycoverDetail" href="/assets/backend/images/2fa_3.jpg" class="ml-4">
                                <img src="/assets/backend/images/2fa_3.jpg" style="max-width:200px;max-height:300px" alt="" >
                             </a>
                        </div>
                        <br>
                        <h4 class="mb-10 font-weight-bold text-dark">Kích hoạt bảo mật trên hệ thống QLTT</h4>
                        <p>Bạn truy cập vào phần "Bảo mật tài khoản" (ở cuối bài hướng dẫn này ) hoặc link <a target="_blank" href="{{route('admin.security-2fa.setup')}}">liên kết</a></p> để vào trang liên kết trên hệ thống
                        <p>Hệ thống sẽ tự động tạo ra một mã QR, khi đó bạn sử dụng tài khoản Google 2FA để quét mã xác thực </p>
                        <div class="text-center">
                            <a data-fancybox="gallerycoverDetail" href="/assets/backend/images/2fa_2.png">
                                <img src="/assets/backend/images/2fa_2.png" style="max-width:200px;max-height:300px" alt="" >
                             </a>
                        </div>
                        <br>
                        <p>Sau đó, bạn nhập mã code được hiển thị trên Google 2FA vào ô input và bấm xác nhận để tiến hành liên kết</p>
                        <div class="text-center">
                            <a data-fancybox="gallerycoverDetail" href="/assets/backend/images/2fa_7.jpg">
                                <img src="/assets/backend/images/2fa_7.jpg" style="max-width:200px;max-height:300px" alt="" >
                             </a>
                        </div>
                        <br>
                        <p>Khi hệ thống thông báo thành công, tức là hệ thống đã liên kết và kích hoạt bảo mật với Google 2FA thành công</p>
                    </div>
                    <div class="pb-5" data-wizard-type="step-content">
                        <h4 class="mb-10 font-weight-bold text-dark">Hướng dẫn sử dụng</h4>
                        <p>Hiện tại Phương thức bảo mật với Google 2FA đang được tích hợp với tác vụ với input yêu cầu nhập là <b>"Mã bảo mật Google 2FA"</b></p>
                        <div class="text-center">
                            <a data-fancybox="gallerycoverDetail" href="/assets/backend/images/2fa_4.png">
                                <img src="/assets/backend/images/2fa_4.png" style="max-width:200px;max-height:300px" alt="" >
                             </a>
                            <a data-fancybox="gallerycoverDetail" href="/assets/backend/images/2fa_5.png">
                                <img src="/assets/backend/images/2fa_5.png" style="max-width:200px;max-height:300px" alt="" >
                             </a>
                        </div>
                        <p>Bạn lấy mã hiển thị trên ứng dụng để sử dụng cho các tác vụ nêu trên</p>
                        <p>Mã xác nhận trên Google 2FA sẽ thay đổi liên tục trong vòng 60s. Vì vậy bạn không nên lưu trữ mã này.</p>
                        <p>Đối với hệ thống QLTT, tên ứng dụng liên kết mặc định sẽ là: <h5><b>{{\Request::getHost().":".Auth::user()->username.'-'.Auth::user()->email}}</b></h5></p>
                    </div>
                    <div class="d-flex justify-content-between border-top mt-5 pt-10">
                        <div class="mr-2">
                            <button type="button" class="btn btn-light-primary font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-prev">Quay lại</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-success font-weight-bolder text-uppercase px-9 py-4" id="btn-submit-go-2fa" data-href="{{route('admin.security-2fa.setup')}}" data-wizard-type="action-submit">Bắt đầu cài đặt</button>
                            <button type="button" class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-next">Tiếp theo</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>