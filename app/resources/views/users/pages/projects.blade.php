@extends('layouts.app')
@section('content')

<main id="content" role="main">
    <!-- breadcrumb -->
    <div class="bg-gray-13 bg-md-transparent">
        <div class="container">
            <!-- breadcrumb -->
            <div class="my-md-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a href="{{route('index')}}">Home</a></li>
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">Projects</li>
                    </ol>
                </nav>
            </div>
            <!-- End breadcrumb -->
        </div>
    </div>
    <!-- End breadcrumb -->

    <div class="container">
        <div class="row">
            <div class="col-xl-12 col-wd">
                <div class="max-width-1100-wd">
                    @foreach ($projects as $project )
                    <article class="card mb-13 border-0">
                        <div class="js-slick-carousel u-slick overflow-hidden"
                            data-pagi-classes="js-pagination u-slick__pagination u-slick__pagination--long u-slick__pagination--hover mb-0">
                            @php $imag = json_decode($project->images) @endphp
                            @foreach ($imag as $proj )
                            <div class="js-slide">
                                <a href="" class="d-block"><img class="img-fluid" src="{{asset('/images/projects/'.$proj)}}" alt="Image Description"></a>
                            </div>
                            @endforeach
                        </div>
                            <div class="card-body  pb-0 px-0">
                            <h4 class="mb-3"><a href="#">{{$project->title}}</a></h4>
                            <p>{!! $project->content !!}.</p>
                            
                    </article>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</main>

@endsection