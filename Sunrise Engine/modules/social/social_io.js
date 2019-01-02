//Alexander Gluschenko (4-10-2015)

var audioSource = new Audio("/modules/social/assets/notification.mp3");
audioSource.autoplay = false;

//

function ShowSocialSidebarWindow()
{
    ShowBox("Меню", Find("social_sidebar_links").innerHTML, 300);
}

//

function EditBox(id, sig) {
    var text = Find("edit_text_" + id).innerHTML;
    var attachments = Find("edit_attachments_" + id).innerHTML;

    var m = '\
        <div style="position: relative;">\
            <textarea id="edit_text_field" class="text_input" maxlength="20480" style="width: 98%; height: 200px;">' + text + '</textarea>\
            <div class="space"></div>\
            <div id="edit_button" class="button back4 at_center">Отправить</div>\
            <!--<div class="button radial_button attach_btn back3" style="bottom: 0px; right: 0px;" onclick="SlideToggle();"></div>-->\
        </div>\
    ';

    ShowWindow("Редактирование", m);
    Find("edit_button").onclick = function () {
        var text = Find("edit_text_field").value;

        EditPost(id, text, attachments, sig, function (markup) {
            Find("text_" + id).innerHTML = markup;
            CloseWindow();
        });
    };
}

//

function CreatePost(recipient_type, recipient_id, text, attachments, sig, oncreate) {
    if (!oncreate) var oncreate = function (markup) { };

    //

    if (text != "" || attachments != "") {
        ApiMethod("social.post", { recipient_type: recipient_type, recipient: recipient_id, text: text, attachments: ToJSON(attachments), sig: sig, markup: true }, function (data) {
            if (data.response != null) {

                if (data.response != false) {
                    if (data.response.markup) {
                        oncreate(data.response.markup);
                    }
                }
                else {
                    ShowModal("Ошибка", "Неверное заполнение полей", 2);
                }

            }

            console.log(data);
        });
    }
}

function EditPost(id, text, attachments, sig, onEdit) {
    if (!onEdit) var onEdit = function (markup) { };

    //

    if (text != "" || attachments != "") {
        ApiMethod("social.posts.edit", { id: id, text: text, attachments: attachments, sig: sig, markup: true }, function (data) {
            if (data.response != null) {

                if (data.response != false) {
                    if (data.response.markup) {
                        onEdit(data.response.markup);
                    }
                }
                else {
                    ShowModal("Ошибка", "Неверное заполнение полей", 2);
                }

            }

            console.log(data);
        });
    }
}

function DeletePost(id, sig, post_block) {
    ApiMethod("social.posts.delete", { id: id, sig: sig }, function (data) {
        if (data.response != null) {
            if (data.response == 1) {
                Hide(post_block);
                Show("deleted_" + post_block);
            }

            if (data.response == 0) console.log("Could not delete post");
        }
    });
}

function RestorePost(id, sig, post_block) {
    ApiMethod("social.posts.restore", { id: id, sig: sig }, function (data) {
        if (data.response != null) {
            if (data.response == 1) {
                Show(post_block);
                Hide("deleted_" + post_block);
            }

            if (data.response == 0) console.log("Could not restore post");
        }
    });
}

//

function ShowUploadDialog() {
    ShowWindow("Загрузка фотографии", Find("upload_form").innerText);
}

var _UploadWindowCallback = function (content_obj) {
    AJAXLayout.Reload();
};

function Upload() {
    var file = $("#file")[0].files[0];

    SlideHide("upload_form_main");
    SlideShow("upload_form_loading");
    SlideHide("upload_form_file");
    SlideHide("upload_form_error");

    AJAX.UploadFile(file, function (response) {
        console.log(response.srcElement.responseText);

        if (response.srcElement.responseText != "") {
            var content_obj = FromJSON(response.srcElement.responseText);

            Write("uploaded_file", "<img style=\"width: 400px;\" alt=\"\" src=\"" + content_obj.data + "\"/>");

            _UploadWindowCallback(content_obj);

            //

            setTimeout(function () {
                SlideHide("upload_form_main");
                SlideHide("upload_form_loading");
                SlideShow("upload_form_file");
                SlideHide("upload_form_error");
            }, 1000);
        }
        else {
            setTimeout(function () {
                SlideHide("upload_form_main");
                SlideHide("upload_form_loading");
                SlideHide("upload_form_file");
                SlideShow("upload_form_error");
            }, 1000);
        }
    }, "/modules/social/user_upload.php");
}

//

function ShowChoosePhotoDialog()
{
    ShowLoader();
	
    ApiMethod("social.photos.get", {}, function(data){
        if(data.response != null){
            if(data.response != 0){
                ShowWindow("Выберите фотографию", GetChoosePhotoMarkup(data.response));
            }
            else{
                ShowModal("Ошибка", "Необходима авторизация", 2);
            }
        }
		
        HideLoader();
    });
}

var _ChooseWindowCallback = function (content_obj) {
    alert("Choosen!");
    console.log(content_obj);
};

var _ChoosePhotosData = null;

function GetChoosePhotoMarkup(data)
{
    _ChoosePhotosData = data;
    //
    var m = "";
	
    m += "<div style=\"text-align: center;\">";
	
    for(var i = 0; i < data.length; i++)
    {
        m += "<img alt='' class='content_image' src='" + data[i].data + "' onclick='_ChooseWindowCallback(_ChoosePhotosData[" + i + "]); CloseWindow();'/>";
    }
	
    m += "</div>";
	
    return m;
}

//

function AddAttachment(atts_str, attachment) {
    if (atts_str == "") atts_str = "[]";
    var atts = FromJSON(atts_str);

    atts[atts.length] = attachment;

    return ToJSON(atts);
}

function Attachment(type, data) {
    this.type = type;
    this.data = data;
}

//

function CheckNotification(id, sig, block_id) {
    ApiMethod("social.notifications.check", { id: id, sig: sig }, function (data) {
        if (data.response != null) {
            if (data.response == 1) {
                Find(block_id).className = "";
                Find(block_id).onmouseout = "";
            }

            if (data.response == 0) console.log("Could not check a notification");
        }
    });
}

//

function UpdateFeed() {
    if(ServerData.isSocialLogged){
        ApiMethod("social.account.poll", {}, function (data) {
            if (data.response != null) 
            {
                PopupNotifications = data.response.notifications;
            }
        });
    }
}

var LastNotCount = 0;
var PopupNotifications = [];

setInterval(function () {
    UpdatePopups(PopupNotifications);
}, 1000);

function UpdatePopups(notifications) {
    var count = notifications.length;

    var popup_height = 110;

    //

    if (Exists("notifications_counter")) {
        if (count > 0) Write("notifications_counter", count);
        else Write("notifications_counter", "");
    }
    //
    if (Exists("popup_area")) {

        //Clear("popup_area");
        var max_popups = 5;
        var popups_count = 0;
        var new_popups = 0;

        notifications = notifications.reverse();

        for (var i = 0; i < notifications.length; i++) {
            var block_id = "popup_" + notifications[i].id;

            if (!Exists(block_id)) {
                var canDisplay = popups_count < max_popups && notifications[i].created > ServerData.ServerTime;

                if (canDisplay) {
                    var m = NotificationBox(notifications[i].owner_profile, block_id, notifications[i].message, notifications[i].link);
                    WriteEnd("popup_area", m);
                    //
                    new_popups++;
                }
            }

            if (Exists(block_id)) {
                if (Find(block_id).innerHTML != "") {
                    popups_count++;
                }
            }
        }
        //
        if (new_popups > 0) audioSource.play();
        //

        //Find("popup_area").style.height = (popup_height * popups_count) + "px";
    }

    //
    LastNotCount = count;
}

//

function ShowNotifications() {
    if (Exists("notifications_box")) {
        if (Hidden("notifications_box")) {
            SlideShow("notifications_box");

            
            ApiMethod("social.notifications.get", { count: 100 }, function (data) {
                if (data.response != null) {
                    Write("notifications_list", data.response.notifications_list);
                    SlideShow("notifications_box");
                }
            });
        } else {
            SlideHide("notifications_box");
        }
    }
}

function InitPopupArea() {
    if (!Exists("popup_area")) {
        var m = "\
            <div id='popup_area' class='popup_area'>\
                <div id='popup_boxes'>\
                </div>\
            </div>\
        ";

        var popup_wrap = document.createElement('div');
        popup_wrap.id = "popup_wrap";

        document.body.appendChild(popup_wrap);
        popup_wrap.innerHTML = m;
    }
}

window.onload = function () {
    InitPopupArea();
};

function NotificationBox(profileObj, block_id, message, link) {
    var m = "\
    <div id='" + block_id + "'>\
        <div class='box popup_box'>\
            <div class='profile_header' style='height: 40px;'>\
                <div class='profile_avatar popup_profile_avatar' style='top: 5px; left: 5px; position: absolute; margin: inherit; background-image: url(" + profileObj.avatar + ");'></div>\
                <a href='" + profileObj.link + "' async' class='small_text fore3' style='top: 10px; left: 42px; position: absolute;'>" + profileObj.name + "</a>\
                <div id='window_close_button' onclick='Clear(\"" + block_id + "\");' class='window_button close_mask back4' style='top: 5px; right: 5px;'></div>\
            </div>\
            <a href='" + link + "' async'>\
                <div class='padding'>\
                    <div class='mini_text'>" + message + "</div>\
                </div>\
            </a>\
        </div>\
        <div class='space'></div>\
    </div>\
    ";

    return m;
}

//

UpdateFeed();

var UpdateRate = 10; //seconds
var FeedLoop = setInterval(function () { UpdateFeed(); }, UpdateRate * 1000);

//

var OnlineUsersListLoop = null;
function ShowOnlineWindow()
{
    OnlineUsersListLoop = setInterval(function(){
        API.CallMethod("social.users.online.get", {}, function(data){

            if(data.response != null)
            {
                var users_list = Find("online_users_list");

                for(var c = 0; c < users_list.children.length; c++)
                {
                    users_list.children[c].className = "disabled";
                }

                //

                for(var i = 0; i < data.response.users.length; i++)
                {
                    var user = data.response.users[i];
                    var markup = data.response.markup[i];

                    if(Exists("online_users_list"))
                    {
                        var item_id = "online_users_item_" + user.id + "_" + user.reg_date;
                        if(!Exists(item_id))
                        {
                            var _markup = "<div id='" + item_id + "' style='display: inline-block;'>" + markup + "</div>";

                            WriteForward("online_users_list", _markup);
                        }
                        else
                        {
                            Find(item_id).className = "";
                        }
                    }
                }
            }
        });
    }, 1000);

    ShowBox("Пользователи онлайн", "<div style='text-align: center;'><div id='online_users_list' class='text' style='display: inline-block; text-align: left;'></div></div>", 600, function(){
        clearInterval(OnlineUsersListLoop);
    });
}


var Chat = {
    OpenedChats: [],
    Open: function(wrapper, topic_id){
        if(Exists(wrapper))
        {
            var chat = Chat.ChatStruct(wrapper, topic_id);
            Chat.OpenedChats.push(chat);

            Chat.CreateLayout(chat, function(){
                Chat.GetPosts(chat);
            });
        }
    },
    ChatStruct: function(wrapper, topic_id){
        return {
            id: topic_id,
            wrapper: wrapper,
            posts: [],
            offset: 0,
        };
    },
    GetPosts: function(chat){
        API.CallMethod("social.posts.get", { topic_id: chat.id, offset: chat.offset }, function(data){
            if(Exists(chat.wrapper))
            {
                if(data.response != null)
                {
                    for(var i = 0; i < data.response.posts.length; i++)
                    {
                        var post = data.response.posts[i];

                        chat.posts.push(post);
                        chat.offset = post.id;

                        WriteEnd(chat.wrapper + "_messages", post.markup);
                        ExecuteJS(post.markup);
                        Write("post" + post.id + "_wrap_bottom", "");
                    }

                    Write(chat.wrapper + "_sent_messages", "");
                    //
                    Chat.UpdateLayout(chat);
                }
                
                setTimeout(function(){ 
                    Chat.GetPosts(chat); 
                }, 500);
            }
            

            //console.log(chat);
        });
    },
    CreateLayout: function(chat, callback){
        API.CallMethod("social.posts.chat.get", { topic_id: chat.id, wrapper: chat.wrapper }, function(data){
            if(data.response != null)
            {
                Write(chat.wrapper, data.response.markup);
                ExecuteJS(data.response.markup);
                callback();
            }
        });
    },
    UpdateLayout: function(chat){
        var Scrolling = Find(chat.wrapper + "_scroll");

        if(Scrolling.scrollTop == 0 || Scrolling.scrollTop > Scrolling.scrollHeight - Scrolling.clientHeight - 200)
        {
            Scrolling.scrollTop = 10000000;
        }
    },
};