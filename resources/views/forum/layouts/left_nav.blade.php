<!-- left side -->
<div class="col-lg-3">
    <aside class="leftside-bar">
        <!-- menu-item-wraper -->
        <div class="menu-item-wraper">
            <div class="nav-menu">
                <div class="navmenu-item-wraper">
                    <a href="{{ route('forum') }}" class="menu-item">
                        <i id="iconhome" class="fa-solid fa-house"></i>
                        <h6 id="menuhome" class="menu-name">Home</h6>
                    </a>
                    <a href="{{ route('forum.popularPosts') }}" class="menu-item">
                        <i id="iconpopular" class="fa-solid fa-star"></i>
                        <h6 id="menupopular" class="menu-name ">Popular</h6>
                    </a>
                    @foreach ($forumCategories as $fc)
                        <a href="/forum/category/{{ $fc->id }}/posts" class="menu-item">
                            <i id="iconfc{{ $fc->id }}" class="{{ $fc->icon }}"></i>
                            <h6 id="menufc{{ $fc->id }}" class="menu-name ">{{ $fc->category }}</h6>
                        </a>
                    @endforeach

                    <a href="{{ route('forum.bookmarks') }}" class="menu-item">
                        <i id="iconbookmark" class="fa-solid fa-bookmark"></i>
                        <h6 id="menubookmark" class="menu-name ">Bookmarks</h6>
                    </a>



                </div>
            </div>
            <!-- menu-item-wraper / -->
            <!-- latest-topics-menu -->
            <div class="latest-topics-menu">
                <div class="latest-topics-wraper">
                    <h6 class="menu-title">TOPICS</h6>
                    <div class="latest-topics-list">
                        @foreach ($topTopics as $ft)
                            @php
                                $shortenedTopic =
                                    strlen($ft->topic) > 25 ? substr($ft->topic, 0, 25) . '...' : $ft->topic;
                            @endphp
                            <a href="/forum/topic/{{ $ft->id }}/posts" class="menu-item">
                                <img src="{{ $ft->icon }}" class="img-fluid" style="max-width: 25px"></i>
                                {{-- <i id="iconft{{ $ft->id }}" class="{{ $ft->icon }}"></i> --}}
                                <h6 id="menuft{{ $ft->id }}" class="menu-name ">
                                    {{ $shortenedTopic }}</h6>
                            </a>
                        @endforeach

                        <div class="show-all-menu-wraper">
                            <div class="show-all-menu-item">
                                @foreach ($otherTopics as $oft)
                                    @php
                                        $shortenedTopic =
                                            strlen($oft->topic) > 25 ? substr($oft->topic, 0, 25) . '...' : $oft->topic;
                                    @endphp
                                    <a href="/forum/topic/{{ $oft->id }}/posts" class="menu-item">
                                        <img src="{{ $oft->icon }}" class="img-fluid" style="max-width: 25px"></i>
                                        {{-- <i id="iconft{{ $oft->id }}" class="{{ $oft->icon }}"></i> --}}
                                        <h6 id="menuft{{ $oft->id }}" class="menu-name">
                                            {{ $shortenedTopic }}</h6>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="menu-item">
                            <button class="show-all-tgl-btn">See More</button>
                        </div>

                        @if(Auth::user())
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="menu-item">
                            <span class="icon"><i class="fa-solid fa-arrow-right-from-bracket text-danger"></i></span>
                            <h6 class="menu-name ">Log Out</h6>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </a>
                        @endif

                    </div>
                </div>
            </div>
            <!-- latest-topics-menu / -->

            <!-- others-menu -->
            {{-- <div class="others-menu">
                <div class="others-item-wraper">
                    <h6 class="menu-title">Others</h6>

                    <div class="others-item-list">

                        <a href="/forum/cookie-policy" class="menu-item">
                            <i class="fas fa-cookie-bite"></i>
                            <h6 class="menu-name ">Cookie </h6>
                        </a>
                    </div>
                    <a href="/forum/policy/terms-of-service" class="menu-item">
                        <i class="fas fa-box"></i>
                        <h6 class="menu-name ">
                            Terms of Service</h6>
                    </a>
                    <a href="/forum/policy/privacy-policy" class="menu-item">
                        <i class="fab fa-fantasy-flight-games"></i>
                        <h6 class="menu-name ">
                            Privacy Policy</h6>
                    </a>
                </div>
            </div> --}}
            <!-- others-menu /-->

            <div class="copy-right-text text-center ps-5 mt-4">
                <p class="bottom-footer-text"> © Copyright {{ date('Y') }} . All Rights Reserved.</p>
            </div>
        </div>
    </aside>
</div>
<!-- left side / -->
