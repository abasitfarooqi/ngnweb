@extends('livewire.agreements.migrated.frontend.main_master')

@section('title', isset($item) ? $item->description : '')

@section('content')

        <!-- Page title -->
        <div class="page-title parallax parallax1 pagehero-header">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="pagehero-title-heading xt">
                            <h1 class="title">{{$title}}</h1>
                        </div><!-- /.pagehero-title-heading xt -->
                        <div class="breadcrumbs">
                            <ul>
                                <li><a href="/">Home Page</a></li>
                                <li><a href="/shop">Shop</a></li>
                                <li><a href="/category/{{$category_id}}">{{ucfirst($category)}}</a></li>
                                <li><a href="/product/{{$product_id}}">{{$title}}</a></li>
                            </ul>
                        </div><!-- /.breadcrumbs -->
                    </div><!-- /.col-md-12 -->
                </div><!-- /.row -->
            </div><!-- /.container -->
        </div><!-- /.page-title -->

        <section class="flat-row main-shop shop-detail style-1">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="flat-image-box clearfix">
                            <div class="inner padding-top-4">
                                <ul class="product-list-fix-image">
                                    <li>
                                        <img loading="lazy" src="{{$item->image_url}}" alt="Image">
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div><!-- /.col-md-6 -->

                    <div class="col-md-6">
                        <div class="divider h0"></div>
                        <div class="product-detail">
                            <div class="inner">
                                <form action="{{ route('store.cart', $item->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="content-detail form-group">
                                        <h2 class="product-title form-control" value=" {{$item->description}}" name="name">{{$item->description}}</h2>
                                        <div class="flat-star style-1">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star-half-o"></i>
                                            <i class="fa fa-star-half-o"></i>
                                            <span>(1)</span>
                                        </div>
                                        <p>{{ html_entity_decode($item->extended_description) }}</p>
                                        <div class="price margin-top-24">
                                            <ins><span class="amount" value="{{$item->price}}" name="price" id="price">£{{$item->price}}</span></ins>
                                        </div>
                                        <ul class="product-infor style-1">
                                            <li><span>Size: {{ $item->variation }}</span></li>
                                            <li><span>Colour: {{ $item->colour }}</span></li>
                                        </ul>
                                        <div class="product-categories margin-top-22">
                                            <span>SKU: </span><a href="#">{{ $item->sku }}</a>
                                        </div>
                                        <div class="product-categories margin-top-22">
                                            <span>Category: </span><a href="#">{{ucfirst($category)}}</a>
                                        </div>
                                        <div class="product-tags">
                                            <span>Tags: </span><a href="#"></a> <a href="#"></a> <a href="#"></a> <a href="#"></a>
                                        </div>
                                        <div class="product-quantity margin-top-35">
                                            <div class="quantity">
                                                <input type="text" value="1" name="quantity" class="quantity-number">
                                                <span class="inc quantity-button">+</span>
                                                <span class="dec quantity-button">-</span>
                                            </div>
                                            <button type="submit" class="add-to-cart">ADD TO CART</button>
                                            <div class="box-like">
                                                <a href="#" class="like"><i class="fa fa-heart-o"></i></a>
                                            </div>
                                        </div>
                                        <ul class="flat-socials margin-top-46">
                                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                            <li><a href="#"><i class="fa fa-pinterest"></i></a></li>
                                            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                                        </ul>
                                    </div>
                                </form>
                            </div>
                        </div><!-- /.product-detail -->
                    </div>
                </div><!-- /.row -->
            </div><!-- /.container -->
        </section><!-- /.flat-row -->

        <section class="flat-row shop-detail-content style-1">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="flat-tabs style-1 has-border">
                            <div class="inner">
                                <ul class="menu-tab">
                                    <li class="active">Description</li>
                                    <li>Additional information</li>
                                    <li>Reviews</li>
                                </ul>
                                <div class="content-tab">
                                    <div class="content-inner">
                                        <div class="flat-grid-box col2 border-width border-width-1 has-padding clearfix">
                                            <div class="grid-row image-left clearfix">
                                                <div class="grid-item">
                                                    <div class="thumb text-center">
                                                        <img loading="lazy" src="{{$item->image_url}}" alt="Image" width="50%">
                                                    </div>
                                                </div><!-- /.grid-item -->
                                                <div class="grid-item">
                                                    <div class="text-wrap">
                                                        <h6 class="title">{{$item->description}}</h6>
                                                        <p>{{ html_entity_decode($item->extended_description) }}</p>
                                                    </div>
                                                </div>
                                            </div><!-- /.grid-row -->
                                        </div><!-- /.flat-grid-box -->
                                    </div><!-- /.content-inner -->
                                    <div class="content-inner">
                                        <div class="inner max-width-40">
                                            <table>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div><!-- /.content-inner -->
                                    <div class="content-inner">
                                        <div class="inner max-width-83 padding-top-33">
                                            <ol class="review-list">
                                                <li class="review">

                                                </li><!--  /.review    -->
                                            </ol><!-- /.review-list -->
                                            <div class="comment-respond review-respond" id="respond">
                                                <div class="comment-reply-title margin-bottom-14">
                                                    <h5>Write a review</h5>
                                                    <p>Your email address will not be published. Required fields are marked *</p>
                                                </div>
                                                <form novalidate="" class="comment-form review-form" id="commentform" method="post" action="#">
                                                    <p class="flat-star style-2">
                                                        <label>Rating*:</label>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                    </p>
                                                    <p class="comment-form-comment">
                                                        <label>Review*</label>
                                                        <textarea class="" tabindex="4" name="comment" required> </textarea>
                                                    </p>
                                                    <p class="comment-name">
                                                        <label>Name*</label>
                                                        <input type="text" aria-required="true" size="30" value="" name="name" id="name">
                                                    </p>
                                                    <p class="comment-email">
                                                        <label>Email*</label>
                                                        <input type="email" size="30" value="" name="email" id="email">
                                                    </p>
                                                    <p class="comment-form-notify clearfix">
                                                        <input type="checkbox" name="check-notify" id="check-notify"> <label for="check-notify">Notify me of new posts by email</label>
                                                    </p>
                                                    <p class="form-submit">
                                                        <button class="comment-submit">Submit</button>
                                                    </p>
                                                </form>
                                            </div><!-- /.comment-respond -->
                                        </div>
                                    </div><!-- /.content-inner -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section><!-- /.shop-detail-content -->


@endsection
