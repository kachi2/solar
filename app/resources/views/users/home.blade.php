@extends('layouts.app')
@section('content')


<main id="content" role="main">
	<!-- Slider Section -->
	<div class="mb-5">
		<div class="bg-img-hero" style="background-image: url('{{asset('/images/sliders/'.$sliders[0]->image)}}'); ">
			<div class="container min-height-500 overflow-hidden">
				<div class="js-slick-carousel u-slick" data-autoplay="true" data-speed="7000"
					data-pagi-classes="text-center position-absolute right-0 bottom-0 left-0 u-slick__pagination u-slick__pagination--long justify-content-start mb-3 mb-md-4 offset-xl-3 pl-2 pb-1">
					@foreach ($sliders as $slider)
					<div class="js-slide bg-img-hero-center">
						<div class="row min-height-500 py-1 py-md-0">
							<div class="offset-xl-2 col-xl-2 col-1 mt-md-2">
								<h1 class="font-size-64 text-lh-57 font-weight-light"
									data-scs-animation-in="fadeInUp">
								</h1>
							</div>
							<div class="col-xl-9 col-9  d-flex align-items-center"
								data-scs-animation-delay="500"
								data-slides-show="5"
								data-slides-scroll="7"
								data-scs-animation-in="fadeIn"
								data-responsive='[{
									"breakpoint": 992,
						"settings": {
							"slidesToShow": 5
						}
					}, {
						"breakpoint": 768,
						"settings": {
							"slidesToShow": 1
						}
					}, {
						"breakpoint": 554,
						"settings": {
							"slidesToShow": 1
						}
					}]'>
								<img class="img-fluid" src="{{asset('/images/sliders/'.$slider->image)}}" alt="Image Description">
							</div>
						</div>
					</div>
					@endforeach
					
				</div>
			</div>
		</div>
	</div>
	<!-- End Slider Section -->
	<div class="container">
		<!-- Banner -->
		<div class="mb-5">
			<div class="row">
				@foreach ($categories as  $xs)
				<div class="col-md-6 mb-4 mb-xl-0 col-xl-3">
					<a href="{{route('user.category', encrypt($xs->id))}}" class="d-black text-gray-90">
						<div class="min-height-132 py-1 d-flex bg-gray-1 align-items-center">
							<div class="col-6 col-xl-5 col-wd-6 pr-0">
								<img class="img-fluid" src="{{asset('/images/category/'.$xs->image)}}" alt="Image Description">
							</div>
							<div class="col-6 col-xl-7 col-wd-6">
								<div class="mb-2 pb-1 font-size-18 font-weight-light text-ls-n1 text-lh-23">
								 <strong>{{$xs->name}} </strong> 
								</div>
								<div class="link text-gray-90 font-weight-bold font-size-15" href="#">
									Shop now
									<span class="link__icon ml-1">
										<span class="link__icon-inner"><i class="ec ec-arrow-right-categproes"></i></span>
									</span>
								</div>
							</div>
						</div>
					</a>
				</div>
				@endforeach
				
			</div>
		</div>
		<!-- End Banner -->
		<!-- Deals-and-tabs -->
		<div class="mb-5">
			<div class="row">
				<!-- Tab Prodcut -->
				<div class="col">
					<!-- Features Section -->
					<div class="">
						<div class="tab-content" id="pills-tabContent">
							<div class="tab-pane fade pt-2 show active" id="pills-one-example1" role="tabpanel" aria-labelledby="pills-one-example1-tab">
								<ul class="row list-unstyled products-group no-gutters">
									
									@foreach ($products as $pro )
									<li class="col-6 col-wd-3 col-md-3 product-item">
										<div class="product-item__outer h-100">
											<div class="product-item__inner px-xl-4 p-3">
												<div class="product-item__body pb-xl-2">
													<div class="mb-2"><a href="{{route('product-details', encrypt($pro->id))}}" class="font-size-12 text-gray-5">{{$pro->category->name}}</a></div>
													<h5 class="mb-1 product-item__title"><a href="{{route('product-details', encrypt($pro->id))}}" class="text-blue font-weight-bold">{{$pro->name}}</a></h5>
													<div class="mb-2">
														<a href="{{route('product-details', encrypt($pro->id))}}" class="d-block text-center"><img class="img-fluid" src="{{asset('/images/products/'.$pro->image)}}" alt="Image Description"></a>
													</div>
													<div class="flex-center-between mb-1">
														<div class="prodcut-price">
															<div class="text-gray-100">₦{{number_format($pro->price)}}</div>
														</div>
														<div class="d-none d-xl-block prodcut-add-cart">
															<a href="{{route('product-details', encrypt($pro->id))}}" class="btn-add-cart btn-primary transition-3d-hover"><i class="ec ec-add-to-cart"></i></a>
														</div>
													</div>
												</div>
												<div class="product-item__footer">
													<div class="border-top pt-2 flex-center-between flex-wrap">
														{{-- <a href="../shop/compare.html" class="text-gray-6 font-size-13"><i class="ec ec-compare mr-1 font-size-15"></i> Compare</a>
														<a href="../shop/wishlist.html" class="text-gray-6 font-size-13"><i class="ec ec-favorites mr-1 font-size-15"></i> Add to Wishlist</a> --}}
													</div>
												</div>
											</div>
										</div>
									</li>
									@endforeach
									
								</ul>
							</div>
							
						</div>
						<!-- End Tab Content -->
					</div>
					<!-- End Features Section -->
				</div>
				<!-- End Tab Prodcut -->
			</div>
		</div>
		<!-- End Deals-and-tabs -->

	@if(count($recents) > 0)
		<div class="mb-6">
			<div class="position-relative">
				<div class="border-bottom border-color-1 mb-2">
					<h3 class="section-title mb-0 pb-2 font-size-22">Recently Viewed</h3>
				</div>
				<div class="js-slick-carousel u-slick position-static overflow-hidden u-slick-overflow-visble pb-7 pt-2 px-1"
					data-pagi-classes="text-center right-0 bottom-1 left-0 u-slick__pagination u-slick__pagination--long mb-0 z-index-n1 mt-3 mt-md-0"
					data-slides-show="5"
					data-slides-scroll="1"
					data-arrows-classes="position-absolute top-0 font-size-17 u-slick__arrow-normal top-10"
					data-arrow-left-classes="fa fa-angle-left right-1"
					data-arrow-right-classes="fa fa-angle-right right-0"
					data-responsive='[{
					  "breakpoint": 1400,
					  "settings": {
						"slidesToShow": 6
					  }
					}, {
						"breakpoint": 1200,
						"settings": {
						  "slidesToShow": 4
						}
					}, {
					  "breakpoint": 992,
					  "settings": {
						"slidesToShow": 3
					  }
					}, {
					  "breakpoint": 768,
					  "settings": {
						"slidesToShow": 2
					  }
					}, {
					  "breakpoint": 554,
					  "settings": {
						"slidesToShow": 2
					  }
					}]'>
					@foreach ($recents as $view )
					<div class="js-slide products-group">
						<div class="product-item">
							<div class="product-item__outer h-100">
								<div class="product-item__inner px-wd-4 p-2 p-md-3">
									<div class="product-item__body pb-xl-2">
										<div class="mb-2"><a href="{{route('product-details',encrypt($view->id))}}" class="font-size-12 text-gray-5">{{$view->category->name}}</a></div>
										<h5 class="mb-1 product-item__title"><a href="{{route('product-details', encrypt($pro->id))}}" class="text-blue font-weight-bold">{{$pro->name}}</a></h5>
										<div class="mb-2">
											<a href="{{route('product-details', encrypt($view->id))}}" class="d-block text-center"><img class="img-fluid" src="{{asset('/images/products/'.$view->image)}}" alt="Image Description"></a>
										</div>
										<div class="flex-center-between mb-1">
											<div class="prodcut-price">
												<div class="text-gray-100">₦{{number_format($view->price)}}</div>
											</div>
											<div class="d-none d-xl-block prodcut-add-cart">
												<a href="{{route('product-details', encrypt($view->id))}}" class="btn-add-cart btn-primary transition-3d-hover"><i class="ec ec-add-to-cart"></i></a>
											</div>
										</div>
									</div>
									<div class="product-item__footer">
										<div class="border-top pt-2 flex-center-between flex-wrap">
											{{-- <a href="../shop/compare.html" class="text-gray-6 font-size-13"><i class="ec ec-compare mr-1 font-size-15"></i> Compare</a>
											<a href="../shop/wishlist.html" class="text-gray-6 font-size-13"><i class="ec ec-favorites mr-1 font-size-15"></i> Wishlist</a> --}}
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					@endforeach
					
				</div>
			</div>
		</div>
		@endif
		
	</div>
</main>

@endsection
