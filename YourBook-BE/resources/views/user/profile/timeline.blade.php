<x-layout.profile-layout :user="$user">
    <div class="columns">
        <div id="profile-timeline-widgets" class="column is-4">
            <!-- Basic Infos widget -->
            <!-- html/partials/pages/profile/timeline/widgets/basic-infos-widget.html -->
            <div class="box-heading">
                <h4>Basic Infos</h4>
                <div class="dropdown is-neutral is-spaced is-right dropdown-trigger">
                    <div>
                        <div class="button">
                            <i data-feather="more-vertical"></i>
                        </div>
                    </div>
                    <div class="dropdown-menu" role="menu">
                        <div class="dropdown-content">
                            <a href="profile-about.html" class="dropdown-item">
                                <div class="media">
                                    <i data-feather="eye"></i>
                                    <div class="media-content">
                                        <h3>View</h3>
                                        <small>View user details.</small>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="dropdown-item">
                                <div class="media">
                                    <i data-feather="search"></i>
                                    <div class="media-content">
                                        <h3>Related</h3>
                                        <small>Search for related users.</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="basic-infos-wrapper">
                <div class="card is-profile-info">
                    <div class="info-row">
                        <div>
                            <span>Studied at</span>
                            <a class="is-inverted">Georgetown University</a>
                        </div>
                        <i class="mdi mdi-school"></i>
                    </div>
                    <div class="info-row">
                        <div>
                            <span>Married to</span>
                            <a class="is-inverted">Dan Walker</a>
                        </div>
                        <i class="mdi mdi-heart"></i>
                    </div>
                    <div class="info-row">
                        <div>
                            <span>From</span>
                            <a class="is-inverted">Melbourne, AU</a>
                        </div>
                        <i class="mdi mdi-earth"></i>
                    </div>
                    <div class="info-row">
                        <div>
                            <span>Lives in</span>
                            <a class="is-inverted">Los Angeles, CA</a>
                        </div>
                        <i class="mdi mdi-map-marker"></i>
                    </div>
                    <div class="info-row">
                        <div>
                            <span>Followers</span>
                            <a class="is-muted">258 followers</a>
                        </div>
                        <i class="mdi mdi-bell-ring"></i>
                    </div>
                </div>
            </div>

            <!-- Photos widget -->
            <!-- html/partials/pages/profile/timeline/widgets/photos-widget.html -->
            <div class="box-heading">
                <h4>Photos</h4>
                <div class="dropdown is-neutral is-spaced is-right dropdown-trigger">
                    <div>
                        <div class="button">
                            <i data-feather="more-vertical"></i>
                        </div>
                    </div>
                    <div class="dropdown-menu" role="menu">
                        <div class="dropdown-content">
                            <a class="dropdown-item">
                                <div class="media">
                                    <i data-feather="image"></i>
                                    <div class="media-content">
                                        <h3>View Photos</h3>
                                        <small>View all your photos</small>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="dropdown-item">
                                <div class="media">
                                    <i data-feather="tag"></i>
                                    <div class="media-content">
                                        <h3>Tagged</h3>
                                        <small>View photos you are tagged in.</small>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="dropdown-item">
                                <div class="media">
                                    <i data-feather="folder"></i>
                                    <div class="media-content">
                                        <h3>Albums</h3>
                                        <small>Open my albums.</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="is-photos-widget">
                <img src="https://via.placeholder.com/200x200" data-demo-src="../assets/img/demo/widgets/photos/1.jpg" alt="" />
                <img src="https://via.placeholder.com/200x200" data-demo-src="../assets/img/demo/widgets/photos/2.jpg" alt="" />
                <img src="https://via.placeholder.com/200x200" data-demo-src="../assets/img/demo/widgets/photos/3.jpg" alt="" />
                <img src="https://via.placeholder.com/200x200" data-demo-src="../assets/img/demo/widgets/photos/4.jpg" alt="" />
                <img src="https://via.placeholder.com/200x200" data-demo-src="../assets/img/demo/widgets/photos/5.jpg" alt="" />
                <img src="https://via.placeholder.com/200x200" data-demo-src="../assets/img/demo/widgets/photos/6.jpg" alt="" />
                <img src="https://via.placeholder.com/200x200" data-demo-src="../assets/img/demo/widgets/photos/7.jpg" alt="" />
                <img src="https://via.placeholder.com/200x200" data-demo-src="../assets/img/demo/widgets/photos/8.jpg" alt="" />
                <img src="https://via.placeholder.com/200x200" data-demo-src="../assets/img/demo/widgets/photos/9.jpg" alt="" />
                <img src="https://via.placeholder.com/200x200" data-demo-src="../assets/img/demo/widgets/photos/10.jpg" alt="" />
                <img src="https://via.placeholder.com/200x200" data-demo-src="../assets/img/demo/widgets/photos/11.jpg" alt="" />
                <img src="https://via.placeholder.com/200x200" data-demo-src="../assets/img/demo/widgets/photos/12.jpg" alt="" />
            </div>

            <!-- Star friends widget -->
            <!-- html/partials/pages/profile/timeline/widgets/star-friends-widget.html -->
            <div class="box-heading">
                <h4>Friends</h4>
                <div class="dropdown is-neutral is-spaced is-right dropdown-trigger">
                    <div>
                        <div class="button">
                            <i data-feather="more-vertical"></i>
                        </div>
                    </div>
                    <div class="dropdown-menu" role="menu">
                        <div class="dropdown-content">
                            <a class="dropdown-item">
                                <div class="media">
                                    <i data-feather="users"></i>
                                    <div class="media-content">
                                        <h3>All Friends</h3>
                                        <small>View all friends.</small>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="dropdown-item">
                                <div class="media">
                                    <i data-feather="heart"></i>
                                    <div class="media-content">
                                        <h3>Family</h3>
                                        <small>View family members.</small>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="dropdown-item">
                                <div class="media">
                                    <i data-feather="briefcase"></i>
                                    <div class="media-content">
                                        <h3>Work Relations</h3>
                                        <small>View work related friends.</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="friend-cards-list">
                <div class="card is-friend-card">
                    <div class="friend-item">
                        <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/dan.jpg" alt="" data-user-popover="1" />
                        <div class="text-content">
                            <a>Dan Walker</a>
                            <span>4 mutual friend(s)</span>
                        </div>
                        <div class="star-friend">
                            <i data-feather="star"></i>
                        </div>
                    </div>

                    <div class="friend-item">
                        <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/milly.jpg" alt="" data-user-popover="7" />
                        <div class="text-content">
                            <a>Milly Augustine</a>
                            <span>3 mutual friend(s)</span>
                        </div>
                        <div class="star-friend is-active">
                            <i data-feather="star"></i>
                        </div>
                    </div>

                    <div class="friend-item">
                        <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/edward.jpeg" alt="" data-user-popover="5" />
                        <div class="text-content">
                            <a>Edward Mayers</a>
                            <span>35 mutual friend(s)</span>
                        </div>
                        <div class="star-friend is-active">
                            <i data-feather="star"></i>
                        </div>
                    </div>

                    <div class="friend-item">
                        <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/stella.jpg" alt="" data-user-popover="2" />
                        <div class="text-content">
                            <a>Stella Bergmann</a>
                            <span>48 mutual friend(s)</span>
                        </div>
                        <div class="star-friend">
                            <i data-feather="star"></i>
                        </div>
                    </div>

                    <div class="friend-item">
                        <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/elise.jpg" alt="" data-user-popover="6" />
                        <div class="text-content">
                            <a>Elise Walker</a>
                            <span>1 mutual friend(s)</span>
                        </div>
                        <div class="star-friend">
                            <i data-feather="star"></i>
                        </div>
                    </div>

                    <div class="friend-item">
                        <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/nelly.png" alt="" data-user-popover="9" />
                        <div class="text-content">
                            <a>Nelly Schwartz</a>
                            <span>11 mutual friend(s)</span>
                        </div>
                        <div class="star-friend">
                            <i data-feather="star"></i>
                        </div>
                    </div>
                </div>
            </div>


        </div>

        <div class="column is-8">
            <div id="profile-timeline-posts" class="box-heading">
                <h4>Posts</h4>
                <div class="button-wrap">
                    <button type="button" class="button is-active">Recent</button>
                    <button type="button" class="button">Popular</button>
                </div>
            </div>

            <div class="profile-timeline">
                <!-- Timeline post 1 -->
                <!-- html/partials/pages/profile/posts/timeline-post1.html -->
                <!-- Timeline POST #1 -->
                <div class="profile-post">
                    <!-- Timeline -->
                    <div class="time is-hidden-mobile">
                        <div class="img-container">
                            <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/jenna.png" alt="" />
                        </div>
                    </div>
                    <!-- Post -->
                    <div class="card is-post">
                        <!-- Main wrap -->
                        <div class="content-wrap">
                            <!-- Header -->
                            <div class="card-heading">
                                <div class="user-block">
                                    <div class="image">
                                        <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/jenna.png" data-user-popover="0" alt="" />
                                    </div>
                                    <div class="user-info">
                                        <a href="#">Jenna Davis</a>
                                        <span class="time">October 17 2018, 11:03am</span>
                                    </div>
                                </div>

                                <div class="dropdown is-spaced is-right is-neutral dropdown-trigger">
                                    <div>
                                        <div class="button">
                                            <i data-feather="more-vertical"></i>
                                        </div>
                                    </div>
                                    <div class="dropdown-menu" role="menu">
                                        <div class="dropdown-content">
                                            <a href="#" class="dropdown-item">
                                                <div class="media">
                                                    <i data-feather="bookmark"></i>
                                                    <div class="media-content">
                                                        <h3>Bookmark</h3>
                                                        <small>Add this post to your bookmarks.</small>
                                                    </div>
                                                </div>
                                            </a>
                                            <a class="dropdown-item">
                                                <div class="media">
                                                    <i data-feather="bell"></i>
                                                    <div class="media-content">
                                                        <h3>Notify me</h3>
                                                        <small>Send me the updates.</small>
                                                    </div>
                                                </div>
                                            </a>
                                            <hr class="dropdown-divider" />
                                            <a href="#" class="dropdown-item">
                                                <div class="media">
                                                    <i data-feather="flag"></i>
                                                    <div class="media-content">
                                                        <h3>Flag</h3>
                                                        <small>In case of inappropriate content.</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Header -->

                            <!-- Post body -->
                            <div class="card-body">
                                <!-- Post body text -->
                                <div class="post-text">
                                    <p>
                                        Today i visited this amazing little fashion store in Church street.
                                        Everything is handmade, from skirts to bags. Their products really
                                        have an outstanding quality. If you don't know them already, well
                                        it's time to make your move!
                                    </p>

                                    <p></p>
                                </div>
                                <!-- Featured image -->
                                <div class="post-image">
                                    <a data-fancybox="profile-post1" data-lightbox-type="comments" data-thumb="../assets/img/demo/unsplash/8.jpg" href="https://via.placeholder.com/1600x900" data-demo-href="../assets/img/demo/unsplash/8.jpg">
                                        <img src="https://via.placeholder.com/1600x900" data-demo-src="../assets/img/demo/unsplash/8.jpg" alt="" />
                                    </a>
                                    <!-- Post actions -->
                                    <div class="like-wrapper">
                                        <a href="javascript:void(0);" class="like-button">
                                            <i class="mdi mdi-heart not-liked bouncy"></i>
                                            <i class="mdi mdi-heart is-liked bouncy"></i>
                                            <span class="like-overlay"></span>
                                        </a>
                                    </div>

                                    <div class="fab-wrapper is-share">
                                        <a href="javascript:void(0);" class="small-fab share-fab modal-trigger" data-modal="share-modal">
                                            <i data-feather="link-2"></i>
                                        </a>
                                    </div>

                                    <div class="fab-wrapper is-comment">
                                        <a href="javascript:void(0);" class="small-fab">
                                            <i data-feather="message-circle"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- /Post body -->

                            <!-- Post footer -->
                            <div class="card-footer">
                                <!-- Followers -->
                                <div class="likers-group">
                                    <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/milly.jpg" data-user-popover="7" alt="" />
                                    <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/david.jpg" data-user-popover="4" alt="" />
                                    <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/nelly.png" data-user-popover="9" alt="" />
                                </div>
                                <div class="likers-text">
                                    <p>
                                        <a href="#">Milly</a>,
                                        <a href="#">David</a>
                                    </p>
                                    <p>and 1 more liked this</p>
                                </div>
                                <!-- Post statistics -->
                                <div class="social-count">
                                    <div class="likes-count">
                                        <i data-feather="heart"></i>
                                        <span>32</span>
                                    </div>
                                    <div class="shares-count">
                                        <i data-feather="link-2"></i>
                                        <span>4</span>
                                    </div>
                                    <div class="comments-count">
                                        <i data-feather="message-circle"></i>
                                        <span>5</span>
                                    </div>
                                </div>
                            </div>
                            <!-- /Post footer -->
                        </div>
                        <!-- /Main wrap -->

                        <!-- Comments -->
                        <div class="comments-wrap is-hidden">
                            <!-- Header -->
                            <div class="comments-heading">
                                <h4>Comments <small>(5)</small></h4>
                                <div class="close-comments">
                                    <i data-feather="x"></i>
                                </div>
                            </div>
                            <!-- Header -->

                            <!-- Comments body -->
                            <div class="comments-body has-slimscroll">
                                <!-- Comment -->
                                <div class="media is-comment">
                                    <!-- User image -->
                                    <div class="media-left">
                                        <div class="image">
                                            <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/bobby.jpg" data-user-popover="8" alt="" />
                                        </div>
                                    </div>
                                    <!-- Content -->
                                    <div class="media-content">
                                        <a href="#">Bobby Brown</a>
                                        <span class="time">1 hour ago</span>
                                        <p>
                                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
                                            eiusmod tempo incididunt ut labore et dolore magna aliqua. Ut enim
                                            ad minim veniam, quis nostrud exercitation ullamco laboris
                                            consequat.
                                        </p>
                                        <!-- Comment actions -->
                                        <div class="controls">
                                            <div class="like-count">
                                                <i data-feather="thumbs-up"></i>
                                                <span>1</span>
                                            </div>
                                            <div class="reply">
                                                <a href="#">Reply</a>
                                            </div>
                                        </div>
                                        <!-- Nested Comment -->
                                        <div class="media is-comment">
                                            <!-- User image -->
                                            <div class="media-left">
                                                <div class="image">
                                                    <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/daniel.jpg" data-user-popover="3" alt="" />
                                                </div>
                                            </div>
                                            <!-- Content -->
                                            <div class="media-content">
                                                <a href="#">Daniel Wellington</a>
                                                <span class="time">3 minutes ago</span>
                                                <p>
                                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed
                                                    do eiusmod tempo incididunt ut labore et dolore magna aliqua.
                                                </p>
                                                <!-- Comment actions -->
                                                <div class="controls">
                                                    <div class="like-count">
                                                        <i data-feather="thumbs-up"></i>
                                                        <span>4</span>
                                                    </div>
                                                    <div class="reply">
                                                        <a href="#">Reply</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Right side dropdown -->
                                            <div class="media-right">
                                                <div class="dropdown is-spaced is-right is-neutral dropdown-trigger">
                                                    <div>
                                                        <div class="button">
                                                            <i data-feather="more-vertical"></i>
                                                        </div>
                                                    </div>
                                                    <div class="dropdown-menu" role="menu">
                                                        <div class="dropdown-content">
                                                            <a class="dropdown-item">
                                                                <div class="media">
                                                                    <i data-feather="x"></i>
                                                                    <div class="media-content">
                                                                        <h3>Hide</h3>
                                                                        <small>Hide this comment.</small>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                            <div class="dropdown-divider"></div>
                                                            <a href="#" class="dropdown-item">
                                                                <div class="media">
                                                                    <i data-feather="flag"></i>
                                                                    <div class="media-content">
                                                                        <h3>Report</h3>
                                                                        <small>Report this comment.</small>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /Nested Comment -->
                                    </div>
                                    <!-- Right side dropdown -->
                                    <div class="media-right">
                                        <div class="dropdown is-spaced is-right is-neutral dropdown-trigger">
                                            <div>
                                                <div class="button">
                                                    <i data-feather="more-vertical"></i>
                                                </div>
                                            </div>
                                            <div class="dropdown-menu" role="menu">
                                                <div class="dropdown-content">
                                                    <a class="dropdown-item">
                                                        <div class="media">
                                                            <i data-feather="x"></i>
                                                            <div class="media-content">
                                                                <h3>Hide</h3>
                                                                <small>Hide this comment.</small>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a href="#" class="dropdown-item">
                                                        <div class="media">
                                                            <i data-feather="flag"></i>
                                                            <div class="media-content">
                                                                <h3>Report</h3>
                                                                <small>Report this comment.</small>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Comment -->

                                <!-- Comment -->
                                <div class="media is-comment">
                                    <!-- User image -->
                                    <div class="media-left">
                                        <div class="image">
                                            <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/mike.jpg" data-user-popover="12" alt="" />
                                        </div>
                                    </div>
                                    <!-- Content -->
                                    <div class="media-content">
                                        <a href="#">Mike Lasalle</a>
                                        <span class="time">Yesterday</span>
                                        <p>
                                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
                                            eiusmod tempo incididunt ut labore et dolore magna aliqua.
                                        </p>
                                        <!-- Comment actions -->
                                        <div class="controls">
                                            <div class="like-count">
                                                <i data-feather="thumbs-up"></i>
                                                <span>3</span>
                                            </div>
                                            <div class="reply">
                                                <a href="#">Reply</a>
                                            </div>
                                        </div>
                                        <!-- Nested Comment -->
                                        <div class="media is-comment">
                                            <!-- User image -->
                                            <div class="media-left">
                                                <div class="image">
                                                    <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/lana.jpeg" data-user-popover="10" alt="" />
                                                </div>
                                            </div>
                                            <!-- Content -->
                                            <div class="media-content">
                                                <a href="#">Lana Henrikssen</a>
                                                <span class="time">3 minutes ago</span>
                                                <p>
                                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed
                                                    do eiusmod tempo incididunt ut labore et dolore magna aliqua.
                                                </p>
                                                <!-- Comment actions -->
                                                <div class="controls">
                                                    <div class="like-count">
                                                        <i data-feather="thumbs-up"></i>
                                                        <span>4</span>
                                                    </div>
                                                    <div class="reply">
                                                        <a href="#">Reply</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Right side dropdown -->
                                            <div class="media-right">
                                                <div class="dropdown is-spaced is-right is-neutral dropdown-trigger">
                                                    <div>
                                                        <div class="button">
                                                            <i data-feather="more-vertical"></i>
                                                        </div>
                                                    </div>
                                                    <div class="dropdown-menu" role="menu">
                                                        <div class="dropdown-content">
                                                            <a class="dropdown-item">
                                                                <div class="media">
                                                                    <i data-feather="x"></i>
                                                                    <div class="media-content">
                                                                        <h3>Hide</h3>
                                                                        <small>Hide this comment.</small>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                            <div class="dropdown-divider"></div>
                                                            <a href="#" class="dropdown-item">
                                                                <div class="media">
                                                                    <i data-feather="flag"></i>
                                                                    <div class="media-content">
                                                                        <h3>Report</h3>
                                                                        <small>Report this comment.</small>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /Nested Comment -->
                                    </div>
                                    <!-- Right side dropdown -->
                                    <div class="media-right">
                                        <div class="dropdown is-spaced is-right is-neutral dropdown-trigger">
                                            <div>
                                                <div class="button">
                                                    <i data-feather="more-vertical"></i>
                                                </div>
                                            </div>
                                            <div class="dropdown-menu" role="menu">
                                                <div class="dropdown-content">
                                                    <a class="dropdown-item">
                                                        <div class="media">
                                                            <i data-feather="x"></i>
                                                            <div class="media-content">
                                                                <h3>Hide</h3>
                                                                <small>Hide this comment.</small>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a href="#" class="dropdown-item">
                                                        <div class="media">
                                                            <i data-feather="flag"></i>
                                                            <div class="media-content">
                                                                <h3>Report</h3>
                                                                <small>Report this comment.</small>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Comment -->

                                <!-- Comment -->
                                <div class="media is-comment">
                                    <!-- User image -->
                                    <div class="media-left">
                                        <div class="image">
                                            <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/nelly.png" data-user-popover="9" alt="" />
                                        </div>
                                    </div>
                                    <!-- Content -->
                                    <div class="media-content">
                                        <a href="#">Nelly Schwartz</a>
                                        <span class="time">2 days ago</span>
                                        <p>
                                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
                                            eiusmod tempo incididunt ut labore et dolore magna aliqua.
                                        </p>
                                        <!-- Comment actions -->
                                        <div class="controls">
                                            <div class="like-count">
                                                <i data-feather="thumbs-up"></i>
                                                <span>1</span>
                                            </div>
                                            <div class="reply">
                                                <a href="#">Reply</a>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Right side dropdown -->
                                    <div class="media-right">
                                        <div class="dropdown is-spaced is-right is-neutral dropdown-trigger">
                                            <div>
                                                <div class="button">
                                                    <i data-feather="more-vertical"></i>
                                                </div>
                                            </div>
                                            <div class="dropdown-menu" role="menu">
                                                <div class="dropdown-content">
                                                    <a class="dropdown-item">
                                                        <div class="media">
                                                            <i data-feather="x"></i>
                                                            <div class="media-content">
                                                                <h3>Hide</h3>
                                                                <small>Hide this comment.</small>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a href="#" class="dropdown-item">
                                                        <div class="media">
                                                            <i data-feather="flag"></i>
                                                            <div class="media-content">
                                                                <h3>Report</h3>
                                                                <small>Report this comment.</small>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Comment -->
                            </div>
                            <!-- Comments body -->

                            <!-- Comments footer -->
                            <div class="card-footer">
                                <div class="media post-comment has-emojis">
                                    <!-- Textarea -->
                                    <div class="media-content">
                                        <div class="field">
                                            <p class="control">
                                                <textarea class="textarea comment-textarea" rows="5" placeholder="Write a comment..."></textarea>
                                            </p>
                                        </div>
                                        <!-- Additional actions -->
                                        <div class="actions">
                                            <div class="image is-32x32">
                                                <img class="is-rounded" src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/jenna.png" data-user-popover="0" alt="" />
                                            </div>
                                            <div class="toolbar">
                                                <div class="action is-auto">
                                                    <i data-feather="at-sign"></i>
                                                </div>
                                                <div class="action is-emoji">
                                                    <i data-feather="smile"></i>
                                                </div>
                                                <div class="action is-upload">
                                                    <i data-feather="camera"></i>
                                                    <input type="file" />
                                                </div>
                                                <a class="button is-solid primary-button raised">Post Comment</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Comments footer -->
                        </div>
                        <!-- /Comments -->
                    </div>
                    <!-- /Post -->
                </div>
                <!-- /Timeline POST #3 -->

                <!-- Timeline post 5 -->
                <!-- html/partials/pages/profile/posts/timeline-post5.html -->
                <!-- Timeline POST #5 -->
                <div class="profile-post is-simple">
                    <!-- Timeline -->
                    <div class="time is-hidden-mobile">
                        <div class="img-container">
                            <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/jenna.png" alt="" />
                        </div>
                    </div>
                    <!-- Post -->
                    <div class="card is-post">
                        <!-- Main wrap -->
                        <div class="content-wrap">
                            <!-- Header -->
                            <div class="card-heading">
                                <!-- User image -->
                                <div class="user-block">
                                    <div class="image">
                                        <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/jenna.png" data-user-popover="0" alt="" />
                                    </div>
                                    <div class="user-info">
                                        <a href="#">Jenna Davis</a>
                                        <span class="time">September 17 2018, 10:23am</span>
                                    </div>
                                </div>

                                <div class="dropdown is-spaced is-right is-neutral dropdown-trigger">
                                    <div>
                                        <div class="button">
                                            <i data-feather="more-vertical"></i>
                                        </div>
                                    </div>
                                    <div class="dropdown-menu" role="menu">
                                        <div class="dropdown-content">
                                            <a href="#" class="dropdown-item">
                                                <div class="media">
                                                    <i data-feather="bookmark"></i>
                                                    <div class="media-content">
                                                        <h3>Bookmark</h3>
                                                        <small>Add this post to your bookmarks.</small>
                                                    </div>
                                                </div>
                                            </a>
                                            <a class="dropdown-item">
                                                <div class="media">
                                                    <i data-feather="bell"></i>
                                                    <div class="media-content">
                                                        <h3>Notify me</h3>
                                                        <small>Send me the updates.</small>
                                                    </div>
                                                </div>
                                            </a>
                                            <hr class="dropdown-divider" />
                                            <a href="#" class="dropdown-item">
                                                <div class="media">
                                                    <i data-feather="flag"></i>
                                                    <div class="media-content">
                                                        <h3>Flag</h3>
                                                        <small>In case of inappropriate content.</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Header -->

                            <!-- Post body -->
                            <div class="card-body">
                                <!-- Post body text -->
                                <div class="post-text">
                                    <p>
                                        Hello guys, I need a ride because I need to go to
                                        <a href="#">#Los Angeles</a> to see a customer. I didn't have time
                                        to buy a train ticket so can anyone pick me up in the morning if he
                                        is going there too ?
                                    </p>
                                    <p></p>
                                </div>
                                <!-- Post actions -->
                                <div class="post-actions">
                                    <div class="like-wrapper">
                                        <a href="javascript:void(0);" class="like-button">
                                            <i class="mdi mdi-heart not-liked bouncy"></i>
                                            <i class="mdi mdi-heart is-liked bouncy"></i>
                                            <span class="like-overlay"></span>
                                        </a>
                                    </div>

                                    <div class="fab-wrapper is-share">
                                        <a href="javascript:void(0);" class="small-fab share-fab modal-trigger" data-modal="share-modal">
                                            <i data-feather="link-2"></i>
                                        </a>
                                    </div>

                                    <div class="fab-wrapper is-comment">
                                        <a href="javascript:void(0);" class="small-fab">
                                            <i data-feather="message-circle"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- /Post body -->

                            <!-- Post footer -->
                            <div class="card-footer">
                                <!-- Followers -->
                                <div class="likers-group">
                                    <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/daniel.jpg" data-user-popover="3" alt="" />
                                    <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/elise.jpg" data-user-popover="6" alt="" />
                                </div>
                                <div class="likers-text">
                                    <p><a href="#">Daniel</a> and <a href="#">Elise</a></p>
                                    <p>liked this</p>
                                </div>
                                <!-- Post statistics -->
                                <div class="social-count">
                                    <div class="likes-count">
                                        <i data-feather="heart"></i>
                                        <span>2</span>
                                    </div>
                                    <div class="shares-count">
                                        <i data-feather="link-2"></i>
                                        <span>0</span>
                                    </div>
                                    <div class="comments-count">
                                        <i data-feather="message-circle"></i>
                                        <span>2</span>
                                    </div>
                                </div>
                            </div>
                            <!-- /Post footer -->
                        </div>
                        <!-- /Main wrap -->

                        <!-- Post #6 comments -->
                        <div class="comments-wrap is-hidden">
                            <!-- Header -->
                            <div class="comments-heading">
                                <h4>Comments (<small>2</small>)</h4>
                                <div class="close-comments">
                                    <i data-feather="x"></i>
                                </div>
                            </div>
                            <!-- /Header -->

                            <!-- Comments body -->
                            <div class="comments-body has-slimscroll">
                                <!-- Comment -->
                                <div class="media is-comment">
                                    <!-- User image -->
                                    <div class="media-left">
                                        <div class="image">
                                            <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/elise.jpg" data-user-popover="6" alt="" />
                                        </div>
                                    </div>
                                    <!-- Content -->
                                    <div class="media-content">
                                        <a href="#">Elise Walker</a>
                                        <span class="time">2 days ago</span>
                                        <p>
                                            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
                                            eiusmod tempo incididunt ut labore et dolore magna aliqua. Ut enim
                                            ad minim veniam, quis nostrud exercitation ullamco laboris
                                            consequat.
                                        </p>
                                        <div class="controls">
                                            <div class="like-count">
                                                <i data-feather="thumbs-up"></i>
                                                <span>1</span>
                                            </div>
                                            <div class="reply">
                                                <a href="#">Reply</a>
                                            </div>
                                        </div>
                                        <!-- Comment -->
                                        <div class="media is-comment">
                                            <!-- User image -->
                                            <div class="media-left">
                                                <div class="image">
                                                    <img src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/jenna.png" data-user-popover="0" alt="" />
                                                </div>
                                            </div>
                                            <!-- Content -->
                                            <div class="media-content">
                                                <a href="#">Jenna Davis</a>
                                                <span class="time">2 days ago</span>
                                                <p>
                                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed
                                                    do eiusmod tempo incididunt ut labore et dolore magna aliqua.
                                                </p>
                                                <div class="controls">
                                                    <div class="like-count">
                                                        <i data-feather="thumbs-up"></i>
                                                        <span>0</span>
                                                    </div>
                                                    <div class="reply">
                                                        <a href="#">Reply</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Right side dropdown -->
                                            <div class="media-right">
                                                <div class="dropdown is-spaced is-right is-neutral dropdown-trigger">
                                                    <div>
                                                        <div class="button">
                                                            <i data-feather="more-vertical"></i>
                                                        </div>
                                                    </div>
                                                    <div class="dropdown-menu" role="menu">
                                                        <div class="dropdown-content">
                                                            <a class="dropdown-item">
                                                                <div class="media">
                                                                    <i data-feather="x"></i>
                                                                    <div class="media-content">
                                                                        <h3>Hide</h3>
                                                                        <small>Hide this comment.</small>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                            <div class="dropdown-divider"></div>
                                                            <a href="#" class="dropdown-item">
                                                                <div class="media">
                                                                    <i data-feather="flag"></i>
                                                                    <div class="media-content">
                                                                        <h3>Report</h3>
                                                                        <small>Report this comment.</small>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /Comment -->
                                    </div>
                                    <!-- Right side dropdown -->
                                    <div class="media-right">
                                        <div class="dropdown is-spaced is-right is-neutral dropdown-trigger">
                                            <div>
                                                <div class="button">
                                                    <i data-feather="more-vertical"></i>
                                                </div>
                                            </div>
                                            <div class="dropdown-menu" role="menu">
                                                <div class="dropdown-content">
                                                    <a class="dropdown-item">
                                                        <div class="media">
                                                            <i data-feather="x"></i>
                                                            <div class="media-content">
                                                                <h3>Hide</h3>
                                                                <small>Hide this comment.</small>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a href="#" class="dropdown-item">
                                                        <div class="media">
                                                            <i data-feather="flag"></i>
                                                            <div class="media-content">
                                                                <h3>Report</h3>
                                                                <small>Report this comment.</small>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Comment -->
                            </div>
                            <!-- /Comments body -->

                            <!-- Comments footer -->
                            <div class="card-footer">
                                <div class="media post-comment has-emojis">
                                    <!-- Textarea -->
                                    <div class="media-content">
                                        <div class="field">
                                            <p class="control">
                                                <textarea class="textarea comment-textarea" rows="5" placeholder="Write a comment..."></textarea>
                                            </p>
                                        </div>
                                        <!-- Additional actions -->
                                        <div class="actions">
                                            <div class="image is-32x32">
                                                <img class="is-rounded" src="https://via.placeholder.com/300x300" data-demo-src="../assets/img/profile/jenna.png" data-user-popover="0" alt="" />
                                            </div>
                                            <div class="toolbar">
                                                <div class="action is-auto">
                                                    <i data-feather="at-sign"></i>
                                                </div>
                                                <div class="action is-emoji">
                                                    <i data-feather="smile"></i>
                                                </div>
                                                <div class="action is-upload">
                                                    <i data-feather="camera"></i>
                                                    <input type="file" />
                                                </div>
                                                <a class="button is-solid primary-button raised">Post Comment</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /Comments footer -->
                        </div>
                        <!-- /Post #6 comments -->
                    </div>
                    <!-- /Post -->
                </div>
                <!-- /timeline POST #5 -->

            </div>
        </div>
    </div>
</x-layout.profile-layout>
