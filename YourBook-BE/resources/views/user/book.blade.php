<x-layout.users-layout>
    <x-slot:pageTitle>
        Book | {{$book->title}}
    </x-slot:pageTitle>

    <div id="groups" class="container">
        <!--Groups-->
        <div class="groups-grid">
            <div class="grid-header">
                <div class="header-inner">
                    <h2>{{$book->title}}</h2>
                    <div class="header-actions">
                        <div class="buttons">
                            <a href="#" class="button is-solid accent-button raised">Add Media</a>
                        </div>
                    </div>
                </div>
                <p class="m-6">{{$book->description}}</p>
            </div>

            <div class="columns is-multiline">
                <!--Group-->
                @foreach($book->mediaFiles as $media)
                    <div class="column is-3">
                        <article class="group-box">
                            <div class="box-img has-background-image" data-background="{{$media->collection_name == 'videos'? 'https://placehold.co/600x400?text=Video':$media->getUrl()}}"></div>
                            <a href="{{$media->getUrl()}}" data-fancybox="" class="box-link video-button">
                                <div class="box-img--hover has-background-image" data-background="{{$media->collection_name == 'videos'? 'https://placehold.co/600x400?text=Video':$media->getUrl()}}"></div>
                            </a>
                            <div class="box-info">
                                <span class="box-category">{{$media->name}}</span>
                                <h3 class="box-title">{{$media->getCustomProperty('description')}}</h3>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>

{{--            <!-- Load more groups -->--}}
{{--            <div class="load-more-wrap narrow-top has-text-centered">--}}
{{--                <a href="#" class="load-more-button">Load More</a>--}}
{{--            </div>--}}
        </div>
    </div>


</x-layout.users-layout>
