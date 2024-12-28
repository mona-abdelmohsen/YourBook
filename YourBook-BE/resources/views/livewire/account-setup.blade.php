<div>
    <style>
        input[type="checkbox"] {
            width: 25px;
            height: 25px;
            background: transparent;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border: none;
            outline: none;
            position: relative;
            left: -5px;
            top: -5px;
            cursor: pointer;
        }

        input[type='checkbox']:checked:before{
            content:'\2713';
            background: white;
            color:black;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            display:flex; align-items:center; justify-content:center; font-weight:bold;
        }

        input[type="checkbox"]:checked {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
    </style>
    <div class="nav-header bg-transparent shadow-none border-0">
        <div class="nav-top w-100">
            <a href="#"><i class="feather-zap text-success display1-size me-2 ms-0"></i><span class="d-inline-block fredoka-font ls-3 fw-600 text-current font-xxl logo-text mb-0">YourBook. </span> </a>
            <a href="#" class="mob-menu ms-auto me-2 chat-active-btn"><i class="feather-message-circle text-grey-900 font-sm btn-round-md bg-greylight"></i></a>
            <a href="#" class="mob-menu me-2"><i class="feather-video text-grey-900 font-sm btn-round-md bg-greylight"></i></a>
            <a href="#" class="me-2 menu-search-icon mob-menu"><i class="feather-search text-grey-900 font-sm btn-round-md bg-greylight"></i></a>
            <button class="nav-menu me-0 ms-2"></button>

        </div>


    </div>

    <div class="row">
        <div class="col-xl-5 d-none d-xl-block p-0 vh-100 bg-image-cover bg-no-repeat" style="background-image: url(https://via.placeholder.com/800x950.png);"></div>
        <div class="col-xl-7 vh-100 align-items-center d-flex bg-white rounded-3 overflow-hidden">
            <!-- Step 1 -->

            @if($formStep == 1)
            <div class="card shadow-none border-0 ms-auto me-auto login-card" style="min-width:480px;">
                <form class="card-body rounded-0 text-left needs-validation" wire:submit="step1Submit" novalidate>

                    <h2 class="fw-700 display1-size display2-md-size mb-3">Welcome, <br>Account Setup</h2>

                    <!-- Image -->
                    <div class="row justify-content-center">
                        <div class="col-lg-12 text-center">
                            <label for="image" style="position:relative;">
                                <svg xmlns="http://www.w3.org/2000/svg" style="position: absolute;left: 30px;top: 30px;" width="40px" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 7.5h-.75A2.25 2.25 0 004.5 9.75v7.5a2.25 2.25 0 002.25 2.25h7.5a2.25 2.25 0 002.25-2.25v-7.5a2.25 2.25 0 00-2.25-2.25h-.75m0-3l-3-3m0 0l-3 3m3-3v11.25m6-2.25h.75a2.25 2.25 0 012.25 2.25v7.5a2.25 2.25 0 01-2.25 2.25h-7.5a2.25 2.25 0 01-2.25-2.25v-.75" />
                                </svg>
                                <input type="file" wire:model="photo" id="image" style="display:none;">
                                <figure class="avatar ms-auto me-auto mb-0 mt-2 w100">


                                @if ($photo)
                                        <img src="{{ $photo->temporaryUrl() }}" width="100px" height="100px" class="shadow-sm rounded-3 w-100 rounded-circle">
                                @else
                                    @if($oldPhoto)
                                        <img src="{{$oldPhoto}}" width="100px" height="100px" alt="image" class="shadow-sm rounded-circle rounded-3 w-100">
                                    @else
                                        <img src="https://via.placeholder.com/100x100.png" alt="image" width="100px" height="100px" class="shadow-sm rounded-3 w-100 rounded-circle">
                                    @endif

                                @endif

                                </figure>
                            </label>
                            <h2 class="fw-700 font-sm text-grey-900 mt-3">{{ $name }}</h2>
                            <h4 class="text-grey-500 fw-500 mb-3 font-xsss mb-4">choose profile picture</h4>
                            @error('photo') <h4 class="text-danger fw-500 mb-3 font-xsss mb-4">{{$message}}</h4> @enderror
                        </div>
                    </div>

                    <!-- Country -->
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="form-group">
                                <label class="mont-font fw-600 font-xsss" for="country">Country</label>
                                <select wire:model="country_id" wire:change="updateDialCode" id="country" class="form-control">
                                    <option value="" disabled>select country</option>
                                    @foreach($countries as $country)
                                        <option value="{{$country->id}}">{{$country->name}} </option>
                                    @endforeach
                                </select>
                                @error('country_id') <div class="text-danger fw-500 font-xsss">{{$message}}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <!-- Phone Number -->
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="form-group">
                                <label class="mont-font fw-600 font-xsss">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="dial_code">{{$dial_code}}</span>
                                    <input type="text" wire:model="phone" class="form-control" aria-describedby="dial_code">
                                </div>
                                @error('phone') <div class="text-danger fw-500 font-xsss">{{$message}}</div> @enderror
                            </div>
                        </div>

                    </div>
                    <!-- Gender -->
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="form-group">
                                <label class="mont-font fw-600 font-xsss" for="gender">Gender</label>
                                <select wire:model="gender" id="gender" class="form-control">
                                    <option value="" disabled selected>choose gender</option>
                                    <option value="0">Male</option>
                                    <option value="1">Female</option>
                                </select>
                                @error('gender') <div class="text-danger fw-500 font-xsss">{{$message}}</div> @enderror
                            </div>
                        </div>

                    </div>
                    <!-- Birth Date -->
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="form-group">
                                <label class="mont-font fw-600 font-xsss" for="birth_date">Birth Date</label>
                                <input type="date" class="form-control" id="birth_date" wire:model="birth_date">
                            </div>
                            @error('birth_date') <div class="text-danger fw-500 font-xsss">{{$message}}</div> @enderror
                        </div>

                    </div>
                    <div class="row">
                        <button type="submit" class="btn btn-success">Next</button>
                    </div>

                </form>
            </div>
            @endif
            <!-- Step 2 -->
            @if($formStep == 2)
            <div class="card shadow-none border-0 ms-auto me-auto login-card" style="min-width:480px;">
                <form class="card-body rounded-0 text-left needs-validation" wire:submit="step2Submit" novalidate>
                    <h2 class="fw-700 display1-size display2-md-size mb-3">Choose, <br>Interests</h2>

                    <ul class="d-flex flex-row flex-wrap">
                        @foreach($interests as $i)
                        <li class="border-1 border-black px-3 py-1 rounded-pill mx-1 my-2">
                            <div class="form-check">
                                <input class="form-check-input" wire:model="interests_selected" type="checkbox" value="{{$i->id}}" id="interests_{{$i->id}}">
                                <label class="form-check-label" for="interests_{{$i->id}}">
                                    {{$i->title}}
                                </label>
                            </div>
                        </li>
                        @endforeach
                    </ul>

                    <div class="d-flex justify-content-between">
                        <button wire:click.prevent="stepBack" class="btn btn-default">Back</button>
                        <button type="submit" class="btn btn-success" wire:confirm="Are you sure you want to delete this post?">
                            Next
                        </button>
                    </div>

                </form>
            </div>
            @endif
            <!-- Step 3 -->
            @if($formStep == 3)
                <div class="card shadow-none border-0 ms-auto me-auto login-card" style="min-width:480px;">
                    <form class="card-body rounded-0 text-left needs-validation" wire:submit="step3Submit" novalidate>
                        <h2 class="fw-700 display1-size display2-md-size mb-3">Follow, <br>Top Accounts</h2>

                        <div class="card w-100 shadow-xss rounded-xxl border-0 p-0 " style="max-height: 65vh;">
                            <div class="card-body d-flex align-items-center p-4 mb-0">
                                <h4 class="fw-700 mb-0 font-xssss text-grey-900">Similar Interests and Location, </h4>
                            </div>
                            <div class="d-flex flex-wrap overflow-auto">
                                @foreach($related as $user)
                                    <div class="card-body bg-transparent-card d-flex p-3 bg-greylight ms-3 me-3 rounded-3 my-1">
                                        <figure class="avatar me-2 mb-0"><img src="{{$user->avatar}}" width="50px" height="50px" alt="image" class="shadow-sm rounded-circle w45"></figure>
                                        <h4 class="fw-700 text-grey-900 font-xssss mt-2">{{$user->name}}<span class="d-block font-xssss fw-500 mt-1 lh-3 text-grey-500">12 mutual friends</span></h4>

                                        @if(!$user->followed)
                                            <a href="#" wire:loading wire:click="toggleFollow({{$user->user_id}})" class="btn-round-sm bg-white text-grey-900 feather-plus font-xss ms-auto mt-2"></a>
                                        @else
                                            <a href="#" wire:loading wire:click="toggleFollow({{$user->user_id}})" class="btn-round-sm bg-white text-grey-900 font-xss ms-auto mt-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                @endforeach


                            </div>

                        </div>

                        <div class="d-flex justify-content-between my-2">
                            <button wire:click.prevent="stepBack" class="btn btn-default">Back</button>
                            <button type="submit" class="btn btn-success">
                                Finish
                            </button>
                        </div>

                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
