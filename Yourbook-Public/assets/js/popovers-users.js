/*! popovers-users.js | Friendkit | © Css Ninja. 2019-2020 */
"use strict";function getUserPopovers(){$("*[data-user-popover]").each((function(){var n=$(this),s=$(this).attr("data-user-popover"),e=feather.icons["message-circle"].toSvg(),a=feather.icons["more-horizontal"].toSvg(),o=feather.icons["map-pin"].toSvg(),i=feather.icons.users.toSvg(),r=feather.icons.bookmark.toSvg();$.ajax({url:"assets/data/api/users/users.json",async:!0,dataType:"json",success:function(t){n.webuiPopover({trigger:"hover",placement:"auto",width:300,padding:!1,offsetLeft:0,offsetTop:20,animation:"pop",cache:!1,content:function(){setTimeout((function(){$(".loader-overlay").removeClass("is-active")}),1e3);return'\n                                <div class="profile-popover-block">\n\n                                    <div class="loader-overlay is-active">\n                                        <div class="loader is-loading"></div>\n                                    </div>\n\n                                    <div class="profile-popover-wrapper">\n                                        <div class="popover-cover">\n                                            <img src="'+t[s].cover_image+'">\n                                            <div class="popover-avatar">\n                                                <img class="avatar" src="'+t[s].profile_picture+'">\n                                            </div>\n                                        </div>\n\n                                        <div class="popover-meta">\n                                            <span class="user-meta">\n                                                <span class="username">'+t[s].first_name+" "+t[s].last_name+'</span>\n                                            </span>\n                                            \x3c!--span class="job-title">'+t[s].title+'</span--\x3e\n                                            <div class="common-friends">\n                                                '+i+'\n                                                <div class="text">\n                                                    '+t[s].common_friends+' mutual friend(s)\n                                                </div>\n                                            </div>\n                                            <div class="user-location">\n                                                '+o+'\n                                                <div class="text">\n                                                    From <a href="#">'+t[s].location+'</a>\n                                                </div>\n                                            </div>\n                                        </div>\n                                    </div>\n                                    <div class="popover-actions">\n\n                                        <a href="#" class="popover-icon">\n                                            '+a+'\n                                        </a>\n                                        <a href="#" class="popover-icon">\n                                            '+r+'\n                                        </a>\n                                        <a href="#" class="popover-icon">\n                                            '+e+"\n                                        </a>\n                                    </div>\n                                </div>\n                            "}})}})}))}$((function(){getUserPopovers()}));