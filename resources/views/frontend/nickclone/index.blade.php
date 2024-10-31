@extends('frontend.layouts.master')

@section('content')

<div class="shop_product_detailS">

    <div class="container pt-3">

        <div class="row container__show">
            <div class="col-lg-6 col-md-12 shop_product_detailS__col">
                <div class="gallery" style="overflow: hidden">
                    <div class="swiper gallery-slider">
                        <div class="swiper-wrapper">

                            @if($category == 1)
                                @foreach(explode('|',$data->image_extension) as $val)
                                    <div class="swiper-slide">
                                        <a data-fancybox="gallerycoverDetail" href="{{ \App\Library\MediaHelpers::mediaNick($val) }}">
                                            <img src="{{ \App\Library\MediaHelpers::mediaNick($val) }}" alt="">
                                        </a>
                                    </div>
                                @endforeach

                            @else
                                <img src="{{ \App\Library\MediaHelpers::mediaNick($image) }}" alt="">
                            @endif
                        </div>
                        @if($category == 1)
                        <div class="swiper-button-prev">
                            <i class="fas fa-chevron-left"></i>
                        </div>
                        <div class="swiper-button-next">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                        @endif
                    </div>
                    @if($category == 1)
                    <div class="swiper gallery-thumbs gallery-thumbsmaxheadth">
                        <div class="swiper-wrapper">
                            @foreach(explode('|',$data->image_extension) as $val)
                                <div class="swiper-slide">
                                    <a data-fancybox="gallerycoverDetail" href="{{ \App\Library\MediaHelpers::mediaNick($val) }}">
                                        <img src="{{ \App\Library\MediaHelpers::mediaNick($val) }}" alt="">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 col-md-12 gallery__right">
                <div class="row gallery__row">
                    <div class="col-md-12">
                        <div class="row gallery__01">
                            <div class="col-md-12 gallery__01__row">
                                <div class="row">
                                    <div class="col-auto">
                                        <span class="gallery__01__span">Mã số:</span>
                                    </div>
                                    <div class="col-md-8 col-8 pl-0">
                                        <span class="gallery__01__span">#{{ $data->randId }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 gallery__01__row2">
                                <div class="row">
                                    <div class="col-auto">
                                        <span class="gallery__02__span">Danh mục:</span>
                                    </div>
                                    <div class="col-md-8 col-8  pl-0">
                                        <a class="theashow"  href="javascript:void(0)">
                                            <span class="gallery__02__span">{{ isset($data_category->custom->title) ? $data_category->custom->title :  $data_category->title }}</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(isset($card_percent) && isset($atm_percent))
                        <div class="col-md-12 gallery__pt">
                            <div class="row gallery__02">
                                <div class="col-md-12 gallery__01__row">
                                    <div class="row">
                                        <div class="col-md-5 col-sm-5 col-5">
                                            <div class="row text-left">
                                                <div class="col-md-12">
                                                    <span class="gallery__02__span__02">THẺ CÀO</span>
                                                </div>
                                                <div class="col-md-12">
                                                    {{--                                                    @dd(formatPrice($card_percent*$data->price/100))--}}
                                                    @if(formatPrice($card_percent*$data->price/100) == '')
                                                        <span class="gallery__01__span__02">{{ str_replace(',','.',number_format(round($card_percent*$data->price/100))) }} CARD</span>
                                                    @else
                                                        <span class="gallery__01__span__02">{{ formatPrice($card_percent*$data->price/100) }} CARD</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-2 col-2 gallery__01__span__02md">
                                            <div class="row text-center">
                                                <div class="col-md-12">
                                                    <span class="hoac">Hoặc</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-sm-5 col-5">
                                            <div class="row text-right">
                                                <div class="col-md-12">
                                                    <span class="gallery__02__span__02">ATM chỉ cần</span>
                                                </div>
                                                <div class="col-md-12">
                                                    @if(formatPrice($atm_percent*$data->price/100) == '')
                                                        <span class="gallery__01__span__02">{{ str_replace(',','.',number_format(round($atm_percent*$data->price/100))) }} ATM</span>
                                                    @else
                                                        <span class="gallery__01__span__02">{{ formatPrice($atm_percent*$data->price/100) }} ATM</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-md-12 gallery__pt">
                            <div class="row gallery__02">
                                <div class="col-md-12 gallery__01__row">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-5 col-5">
                                            <div class="row text-left">
                                                <div class="col-md-12">
                                                    <span class="gallery__02__span__02">ATM</span>
                                                </div>
                                                <div class="col-md-12">
                                                    @if(formatPrice($data->price) == '')
                                                        <span class="gallery__01__span__02">{{ round($data->price) }} ATM</span>
                                                    @else
                                                        <span class="gallery__01__span__02">{{ formatPrice($data->price) }} ATM</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(isset($data->groups))
                        <?php $att_values = $data->groups ?>
                        @foreach($att_values as $att_value)
                            @if(isset($att_value->module) && $att_value->module == 'acc_label' && $att_value->is_slug_override == null)
                                <div class="col-md-12">
                                    <div class="row gallery__03">
                                        <div class="col-md-12 gallery__01__row">
                                            <div class="row">
                                                <div class="col-auto span__dangky__auto">
                                                    <i class="fas fa-angle-right"></i>
                                                </div>
                                                <div class="col-md-4 col-4 pl-0">
                                                    <span class="span__dangky">{{ $att_value->parent->title??null }}</span>
                                                </div>
                                                <div class="col-md-6 col-6 pl-0">
                                                    <span class="span__dangky">{{ $att_value->title??null }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                    @if(isset($data->params) && isset($data->params->ext_info))
                        <?php $params = json_decode(json_encode($data->params->ext_info),true) ?>
                        @if(!is_null($dataAttribute) && count($dataAttribute)>0)
                            @foreach($dataAttribute as $index=>$att)
                                @if($att->position == 'text')
                                    @if(isset($att->childs))
                                        @foreach($att->childs as $child)
                                            @foreach($params as $key => $param)
                                                @if($key == $child->id && $child->is_slug_override == null)
                                                    <div class="col-md-12">
                                                        <div class="row gallery__03">
                                                            <div class="col-md-12 gallery__01__row">
                                                                <div class="row">
                                                                    <div class="col-auto span__dangky__auto">
                                                                        <i class="fas fa-angle-right"></i>
                                                                    </div>
                                                                    <div class="col-md-4 col-4 pl-0">
                                                                        <span class="span__dangky">{{ $child->title }}</span>
                                                                    </div>
                                                                    <div class="col-md-6 col-6 pl-0">
                                                                        <span class="span__dangky">{{ $param }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    @endif

                                @endif
                            @endforeach
                        @endif
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
