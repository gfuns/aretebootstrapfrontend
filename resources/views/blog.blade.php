@extends('layouts.app')

@section('content')
@section('title', env('APP_NAME') . ' | Blog Posts ')
<style>
    .truncate-text {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        /* Limit to 3 lines */
        -webkit-box-orient: vertical;
        overflow: hidden;
        white-space: normal;
        /* Use 'normal' to allow wrapping */
        text-overflow: ellipsis;
    }
</style>
<main class="main">
    <section class="section-box">
        <div class="breadcrumb-cover page_speed_160581955" style="background:url({{ asset('files/pages/Search.png') }}">
            <div class="container">
                <div class="row">
                    <div class="col-lg-9">
                        <h2 class="mb-10" style="color:#fff">Blog Posts</h2>
                        <span class=" font-regular text-white">Get the latest news, updates and tips</span>
                    </div>
                    <div class="col-lg-3 text-md-end">
                        <ul class="breadcrumbs ">
                            <li><a href="/"><span class="fi-rr-home icon-home"></span>
                                    Home </a></li>
                            <li><a href="/blog">Blog Posts</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section-box mt-50">
        <div class="post-loop-grid">
            <div class="container">
                <div class="text-left">
                    <h4 class="section-title mb-10 wow animate__animated animate__fadeInUp">All Blog Posts</h4>
                    <p class="font-lg color-text-paragraph-2 wow animate__animated animate__fadeInUp">Don't
                        miss the trending news</p>
                </div>
                <div class="row mt-30">
                    <div class="col-lg-12">
                        <div class="box-filters-job">
                            <div class="row">
                                <div class="col-xl-8 col-lg-8 jobs-listing-container">
                                    <aside class="col-lg-8 widget widget_search mb-10">
                                        <div class="search-form">
                                            <form role="search" method="GET" action="">
                                                <input type=text placeholder="Search..." value="{{ $search }}"
                                                    name=q><button type=submit><i class="fi-rr-search"></i></button>
                                            </form>
                                        </div>
                                    </aside>
                                </div>
                                <div class="col-xl-4 col-lg-4 text-lg-end mt-sm-15 d-none d-lg-block">
                                    <div class="display-flex2">
                                        <div class="box-border"><span class="text-sort_by">Sort by:</span>
                                            <div class="dropdown dropdown-sort"><button class="btn dropdown-toggle"
                                                    id="dropdownSort2" type=button data-bs-toggle="dropdown"
                                                    aria-expanded="false" data-bs-display="static"><span>Newest</span><i
                                                        class="fi-rr-angle-small-down"></i></button>
                                                <ul class="dropdown-menu dropdown-menu-light"
                                                    aria-labelledby="dropdownSort2">
                                                    <li><a class="dropdown-item @if ($filter == 'desc') active @endif"
                                                            href="{{ url()->current() }}?filter=desc"> Newest
                                                        </a></li>
                                                    <li><a class="dropdown-item @if ($filter == 'asc') active @endif"
                                                            href="{{ url()->current() }}?filter=asc"> Oldest
                                                        </a></li>
                                                </ul>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            @foreach ($blogPosts as $blog)
                                @php
                                    $wordCount = str_word_count($blog->blog_post);
                                    $readingSpeed = 50; // Adjust as needed
                                    $readingTime = ceil($wordCount / $readingSpeed);
                                @endphp
                                <div class="col-lg-4 mb-30">
                                    <div class="card-grid-3 hover-up">
                                        <div class="text-center card-grid-3-image "><a
                                                href="{{route("blogDetails", [$blog->slug])}}">
                                                <figure><img src="{{ $blog->cover_photo }}"
                                                        alt="{{ $blog->post_title }}">
                                                </figure>
                                            </a></div>
                                        <div class="card-block-info">
                                            @if (isset($blog->tags))
                                                <div class="tags mb-15">
                                                    @php
                                                        $blogTags = explode(', ', $blog->tags);
                                                    @endphp
                                                    @foreach ($blogTags as $tag)
                                                        <a class="btn btn-tag"
                                                            style="cursor: pointer">{{ $tag }}</a>&nbsp;
                                                    @endforeach
                                                </div>
                                            @endif
                                            <h5><a
                                                    href="{{route("blogDetails", [$blog->slug])}}">{{ $blog->post_title }}</a>
                                            </h5>
                                            <p class="mt-10 color-text-paragraph font-sm truncate-text">
                                                {{ strip_tags($blog->blog_post) }}</p>
                                            <div class="card-2-bottom mt-20">
                                                <div class="row">
                                                    <div class="col-lg-6 col-6">
                                                        <div class="d-flex"><img class="img-rounded"
                                                                src="{{ $blog->user->profile_photo }}">
                                                            <div class="info-right-img"><span
                                                                    class="font-sm font-bold color-brand-1 op-70">{{ $blog->user->first_name . ' ' . $blog->user->last_name }}</span><br><span
                                                                    class="font-xs color-text-paragraph-2">{{ date_format($blog->created_at, 'M d, Y') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 text-md-end col-6 pt-15"><span
                                                            class="color-text-paragraph-2 font-xs">
                                                            {{ $readingTime }} mins to read
                                                        </span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach


                            @if (count($blogPosts) < 1)
                                <div class="col-xl-12 col-12 job-items job-empty">
                                    <div class="text-center mt-4"><i class="fi fi-rr-sad text-3xl"></i>
                                        <h3 class="mt-2">No Blog Post</h3>
                                        <div class="mt-2 text-muted"> There are no blog posts found with your
                                            queries. </div>
                                    </div>
                                </div>
                            @endif

                            @if (count($blogPosts) > 0 && $marker != null)
                                <div class="paginationssss">
                                    <div class="row g-2 pt-3 ms-4 me-4">
                                        <div class="col-md-9 mt-2">
                                            Showing {{ $marker['begin'] }} to {{ $marker['end'] }} of
                                            {{ number_format($lastRecord) }} Records
                                        </div>

                                        <div class="col-md-3">{{ $blogPosts->appends(request()->input())->links() }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
    </section>
</main>
<script type="text/javascript">
    document.getElementById("blog").classList.add('active');
</script>

@endsection
