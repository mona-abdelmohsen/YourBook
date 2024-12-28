<x-layout.profile-layout :user="$user">

    <x-slot:title>
        Books | {{$user->name}}
    </x-slot:title>

    <div class="columns">
        <div class="column">
            <div class="box-heading">
                <div class="dropdown photos-dropdown is-spaced is-neutral dropdown-trigger">
                    <div>
                        <div class="button">
                            <span>All Pictures</span>
                            <i data-feather="chevron-down"></i>
                        </div>
                    </div>
                    <div class="dropdown-menu" role="menu">
                        <div class="dropdown-content">
                            <a href="#" class="dropdown-item">
                                <div class="media">
                                    <i data-feather="tag"></i>
                                    <div class="media-content">
                                        <h3>Tagged Photos</h3>
                                        <small>Photos whith this user tagged.</small>
                                    </div>
                                </div>
                            </a>
                            <a class="dropdown-item">
                                <div class="media">
                                    <i data-feather="clock"></i>
                                    <div class="media-content">
                                        <h3>Recent</h3>
                                        <small>View most recent photos.</small>
                                    </div>
                                </div>
                            </a>
                            <a class="dropdown-item">
                                <div class="media">
                                    <i data-feather="heart"></i>
                                    <div class="media-content">
                                        <h3>Popular</h3>
                                        <small>View popular photos.</small>
                                    </div>
                                </div>
                            </a>
                            <a class="dropdown-item">
                                <div class="media">
                                    <i data-feather="book-open"></i>
                                    <div class="media-content">
                                        <h3>Albums</h3>
                                        <small>See all albums.</small>
                                    </div>
                                </div>
                            </a>
                            <hr class="dropdown-divider" />
                            <a class="dropdown-item modal-trigger" data-modal="albums-modal">
                                <div class="media">
                                    <i data-feather="plus"></i>
                                    <div class="media-content">
                                        <h3>Add Photos</h3>
                                        <small>Upload pictures.</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="button-wrap">
                    <button type="button" class="button is-active">Recent</button>
                    <button type="button" class="button">Albums</button>
                </div>
            </div>

            <div class="image-grid-wrap">
                <div class="image-grid">
                    @for ($i = 0; $i < $books->count(); $i = $i + 3)
                    <!--Grid Row-->
                    <div class="image-row">
                        @for($j = 0; (3*$i)+$j < $books->count() && $j < 3; $j++)
                        <!--Photo-->
                        <div class="flex-1 has-background-image" data-background="{{$books[3*$i+$j]->getFirstMediaUrl('cover')}}">
                            <a href="{{route('book.show', $books[3*$i+$j]->id)}}">
                                <div class="overlay"></div>
                            </a>

                            <div class="image-owner">
                                <img class="avatar" src="{{$user->profilePicture()}}" alt="" />
                                <div class="name">{{$books[3*$i+$j]->title}}}</div>
                            </div>
                            <div class="photo-time">{{\Carbon\Carbon::parse($books[3*$i+$j]->created_at)->diffForHumans()}}</div>
                            <a class="photo-like">
                                <div class="inner">
                                    <div class="like-overlay"></div>
                                    <i data-feather="heart"></i>
                                </div>
                            </a>
                        </div>
                        @endfor
                    </div>
                    @endfor

                </div>

                <!-- Load more photos -->
                <div class="load-more-wrap has-text-centered">
                    <a href="#" class="load-more-button">Load More</a>
                </div>
            </div>
        </div>
    </div>
</x-layout.profile-layout>
