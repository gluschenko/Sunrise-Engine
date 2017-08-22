//Author: Alexander Gluschenko (6-02-2014, 15-07-2015, 21-08-2016, 26-02-2017)

if(String.prototype.includes != null) // ECMA 6
{
    var WebURL = GetFullURL();
    
    if(WebURL.includes("//www."))
    {
        Navigate(WebURL.replace("www.", ""));
    }
}


function ImageBox(src, description) {
    if (!description) description = "";
    
    var window_width = Math.round(screen.height * 0.9);
    if(window_width > screen.width)window_width = Math.round(screen.width * 0.9);

    var content = "<div style='text-align: center;'><img alt='" + description + "' src='" + src + "' style='max-width: 100%;' /></div>";

    if(description != ""){
        content += "<div class='space'></div>" + 
            "<div class='small_text' style='text-align: center'>" + description + "</div>" +
            "<div class='space'></div>";
    }

    ShowWindow("Фотография", content, 0, 0, window_width);
}

function DocumentBox(src) {
    var content = "<iframe src='" + src + "' style='width: 100%; height: 600px; border: 0;'></iframe>";
    var window_width = Math.round(screen.width * 0.8);

    ShowWindow("Документ", content, 0, 0, window_width);
}


/*CODE*/

var CloseCallback = function () { };

function ShowWindow(title, content, header, position, width, callback) {
    if (!header) header = 0;
    if (!position) position = 0;
    if (!width) width = 600;
    if (!callback) callback = function () { };

    //

    Find("window_layout").style.width = width + "px";
    Find("window_layout").style.marginLeft = "-" + (width / 2) + "px";
    //Find("window_layout").style.margin = "-280px -" + (width / 2) + "px 0px";

    if (position == 0) {
        Find("window_layout").className = "window";
    }
    else {
        Find("window_layout").className = "small_window";
    }

    //
    if (header == 0) {
        Find("window_header").className = "";
        Find("window_title").className = "title_text";
        Find("window_close_button").className = "window_button close";
    }

    if (header == 1) {
        Find("window_header").className = "window_header_good";
        Find("window_title").className = "title_text fore3";
        Find("window_close_button").className = "window_button close_white";
    }

    if (header == 2) {
        Find("window_header").className = "window_header_bad";
        Find("window_title").className = "title_text fore3";
        Find("window_close_button").className = "window_button close_white";
    }
    //

    CloseCallback = callback;

    Show("window_layer");
    Find("window_layer").scrollTop = 0;

    Write("window_title", title);
    Write("window_content", content);
    Find("body").style.overflow = "hidden";
    if(!Layout.isMobile())Find("body_wrap").className = "blur_filter";
    //
    ExecuteJS(content); //Исполнение JS внутри окон под вопросом
}

function CloseWindow() {
    CloseCallback();
    CloseCallback = function () { };

    Hide("window_layer");
    Write("window_title", "");
    Write("window_content", "");
    Find("body").style.overflow = "auto";
    Find("body_wrap").className = "";
}

function ShowBox(title, content, width, callback) {
    if (!width) width = 600;
    if (!callback) callback = function () { };

    ShowWindow(title, content, 0, 0, width, callback);
}

function ShowModal(title, text, header) {
    var content = "<div class='big_space'></div>\
        <div class='text' style='text-align: center;'>" + text + "</div>\
        <div class='big_space'></div>\
        <div class='button " + ((header == 1) ? "back2" : "back4") + "' style='margin: auto;' onclick='CloseWindow();'>OK</div>";

    ShowWindow(title, content, header, 1);
}

function ShowDialog(title, text, act_button, act_callback) {
    var content = "<div class='big_space'></div>\
        <div class='text' style='text-align: center;'>" + text + "</div>\
        <div class='big_space'></div>\
        <div style='text-align: center;'>\
            <div class='button back2' style='display: inline-block;' onclick='" + act_callback + "'>" + act_button + "</div>\
            <div class='button back4' style='display: inline-block;' onclick='CloseWindow();'>Отмена</div>\
        </div>";

    ShowWindow(title, content, 0, 1);
}

function ShowPanel(text, type, duration)
{
    if(!type)type = 0;
    if (!duration) duration = 3000;
    //
    var hide_panel = function () {
        SlideHide("alert_panel");
    };

    SlideShow("alert_panel");
    Find("alert_panel_text").innerText = text;

    if(type == 0){
        Find("alert_panel").className = "alert_panel panel_good";
    }
    else{
        Find("alert_panel").className = "alert_panel panel_bad";
    }

    Find("alert_panel").onclick = function () { hide_panel(); }

    setTimeout(function(){
        hide_panel();
    }, duration);
}

/**/

function ShowLoader() {
    Show("loader");
}

function HideLoader() {
    Hide("loader");
}

/**/

/*function ServerRequest(data, action, url) {
    if (!url) var url = "/engine/server.php";

    AJAX.Request({
        type: "POST",
        url: url,
        data: data,
        success: function (data) { action(data); },
    });
}*/

/*function UploadFile(file, callback, receiver) {
    if (!receiver) receiver = "/engine/upload.php";
    //
    var Data = new FormData();
    Data.append("file", file);//$("#file")[0].files[0]
    //
    var XHR = new XMLHttpRequest();

    XHR.timeout = 3600 * 1000;//час
    XHR.onreadystatechange = function (response) { console.log(response); };
    XHR.onloadend = function (response) { callback(response); };
    XHR.open("POST", receiver, true);
    XHR.send(Data);
}*/

function SlideToggle(id, type) {
    if (!type) type = "fast";
    $('#' + id).slideToggle(type);
}

function SlideShow(id, type) {
    if (!type) type = "fast";
    if (Hidden(id)) $('#' + id).slideToggle(type);
}

function SlideHide(id, type) {
    if (!type) type = "fast";
    if (!Hidden(id)) $('#' + id).slideToggle(type);
}

function ScrollTo(btnid) {
    if (Find("body").scrollTop == 0) {
        if (Find(btnid).alt != "0") $('html, body').animate({ scrollTop: Find(btnid).alt }, 'slow');
    }
    else {
        Find(btnid).alt = Find("body").scrollTop;
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    }
}

function ScrollToId(id, offset) {
    if (!offset) offset = 0;
    $('html, body').animate({ scrollTop: $("#" + id).offset().top - offset }, 'fast');
}

///

function Write(id, val) {
    Find(id).innerHTML = val;
}

function WriteForward(id, val) {
    Find(id).innerHTML = val + Find(id).innerHTML;
}

function WriteEnd(id, val) {
    Find(id).innerHTML += val;
}

function Clear(id) {
    Write(id, "");
}

function Delete(id) {
    Find(id).parentNode.removeChild(Find(id));
}

function Find(id) {
    var obj = document.getElementById(id);
    return obj;
}

function Hide(id, param) {
    if (param == null) param = "none";

    if (document.getElementById(id)) {
        document.getElementById(id).style.display = param;
    }
}

function Show(id, param) {
    if (param == null) param = "block";

    if (document.getElementById(id)) {
        document.getElementById(id).style.display = param;
    }
}

function Hidden(id) {
    if (Find(id).style.display == "none") return true;
    return false;
}

function SetVisibility(id, state, visible_param)
{
    if (!visible_param) visible_param = "block";

    if (state) {
        Show(id, visible_param);
    }
    else {
        Hide(id);
    }
}

function Exists(id) {
    if (document.getElementById(id)) return true;
    return false;
}

function ReplaceDisplayStatus(first, second) {
    var firstStatus = Find(first).style.display;
    var secondStatus = Find(second).style.display;

    Find(first).style.display = secondStatus;
    Find(second).style.display = firstStatus;
}

function Reload() {
    document.location.reload(true);
}

function ReloadAsync() {
    //NavigateAsync(document.location.href, true);
    AJAXLayout.Navigate(null, document.location.href);
}

function ToJSON(arr) {
    return JSON.stringify(arr);
}

function FromJSON(str) {
    return JSON.parse(str);
}

function Split(str, separators) { //Разбивает строку на массив по массиву сепараторов
    var mainSep = separators[0];

    for (var s = 0; s < separators.length; s++)
    {
        var Exp = new RegExp(separators[s], "g");
        str = str.replace(Exp, mainSep);
    }

    return str.split(mainSep);
}

function CutHref(href) { //для адресов вида: https://vk.com/admin.php -> /admin.php
    var href_arr = href.split("/");
    var url = "";
    for (var i = 3; i < href_arr.length; i++)
    {
        url += "/" + href_arr[i];
    }

    return url;
}

function ExecuteJS(markup) {  //Парсинг и исполнение скриптов
    var primary_arr = Split(markup, ["<script>", "<script type='text/javascript'>", "<script type=\"text/javascript\">"]);//markup.split("<script>");
    var secondary_arr = [];

    var JS = "";

    for (var i = 1; i < primary_arr.length; i++) {
        if (primary_arr.length > 1) { //1 свидетельствует наличию хоть одного <script> в тексте
            secondary_arr = primary_arr[i].split("</script>");
            var code = secondary_arr[0];
            JS += code;
        }
    }
    
    //eval(JS); - should be working, but not works
    $.globalEval(JS);

    //DebugLog("Code", JS);
}

function DebugLog(name, msg) {
    console.log(name + " -> " + msg);
}

function Navigate(url) {
    document.location = url;
}

function GetURL() {
    return location.pathname + location.search + location.hash;
}

function GetFullURL() {
    return location.protocol + "//" + location.hostname + location.pathname + location.search + location.hash;
}

// Classes

// Прикладной интерфейс
var API = {
    CallMethod: function(method, params, callback){
        var RequestData = { method: method, params: ToJSON(params) };

        AJAX.ServerRequest(RequestData, function (data) {
            var json = FromJSON(data);
            callback(json);
        }, "/engine/api_receiver.php");
    },
};


// Layout
var Layout = {
    setIcon: function(url){
        var link = document.querySelector("link[rel*='icon']") || document.createElement('link');
        link.type = 'image/x-icon';
        link.rel = 'shortcut icon';
        link.href = url;
        document.getElementsByTagName('head')[0].appendChild(link);
    },
    isMobile: function() {
        if (navigator.userAgent.match(/Android/i)
            || navigator.userAgent.match(/webOS/i)
            || navigator.userAgent.match(/iPhone/i)
            || navigator.userAgent.match(/iPad/i)
            || navigator.userAgent.match(/iPod/i)
            || navigator.userAgent.match(/BlackBerry/i)
            || navigator.userAgent.match(/Windows Phone/i)) {
            return true;
        }
        return false;
    },
};

setTimeout(function(){ AJAXLayout.Setup(); }, 0);

// AJAX Layout 2.0
var AJAXLayout = {
    LastURL: "",
    Setup: function(){
        AJAXLayout.LastURL = GetURL();

        document.addEventListener("click", function(e){
            AJAXLayout.ProcessEvent(e);
        }, false);

        window.addEventListener("popstate", function(pop){
            console.log(pop);

            if (pop.state) {
                AJAXLayout.LoadSection(pop.state.NavURL, function (section) { });
            }
            else {
                //LastUrl == "" - хак для iOS (onpopstate вызывается при загрузке страницы)
                if(AJAXLayout.LastUrl != ""){
                    AJAXLayout.LoadSection(AJAXLayout.LastUrl, function (section) { });
                }
            }
        });
    },
    ProcessEvent: function(e){
        if(e.srcElement != null)
        {
            var target = e.srcElement;
            var isAsyncLink = target.attributes.getNamedItem("async") != null && target.attributes.getNamedItem("href") != null;

            if(isAsyncLink){
                AJAXLayout.Navigate(e, target.href);
            }
        }
    },
    Navigate: function(e, url){
        var urlLocation = AJAXLayout.getLocation(url);
        var uri = urlLocation.uri;

        var isValid = !(AJAXLayout.isExternalLink(url) && urlLocation.hostname != location.hostname);

        var wheelPressed = false;
        if(e != null)wheelPressed = e.button == 1;

        if(isValid)
        {
            if(wheelPressed){
                window.open(uri);
            }
            else{
                AJAXLayout.LastUrl = GetURL();

                AJAXLayout.LoadSection(uri, function (section) {
                    history.pushState({ NavURL: section.url }, section.title, section.url);
                });
            }
            if(e != null)e.preventDefault(e);
        }
    },
    Nav: function(e, url){
        AJAXLayout.Navigate(e, url);
    },
    Reload: function(){
        AJAXLayout.Navigate(null, document.location.href);
    },
    LoadSection: function(url, callback){
        DebugLog("LoadSection", url);

        ShowLoader();

        ApiMethod("engine.sections.get", { url: url }, function (data) {
            console.log(data);

            HideLoader();

            if(data.response) {
                callback(data.response);
                //
                document.title = data.response.title;
                if(data.response.icon != "")Layout.setIcon(data.response.icon);

                window.scrollTo(0, 0);
                //
                var data_blocks = ["default_title", "default_box", "wide_title", "wide_box", "narrow_title", "narrow_box", "huge_title", "huge_box", "full_box", "semi_full_box"];
                for (var i = 0; i < data_blocks.length; i++) {
                    Clear(data_blocks[i]);
                }
                //
                var markup = data.response.markup;

                var layout = data.response.layout;

                if(layout == 0) {
                    Write("default_title", data.response.header);
                    Write("default_box", markup);
                }

                if (layout == 1) {
                    Write("wide_title", data.response.header);
                    Write("wide_box", markup);
                }

                if (layout == 2) {
                    Write("narrow_title", data.response.header);
                    Write("narrow_box", markup);
                }

                if (layout == 3) {
                    Write("huge_title", data.response.header);
                    Write("huge_box", markup);
                }

                if (layout == 4) {
                    //Write("full_title", data.response.header);
                    Write("full_box", markup);
                }

                if (layout == 5) {
                    //Write("semi_full_title", data.response.header);
                    Write("semi_full_box", markup);
                }

                ExecuteJS(markup);

                //
                SetVisibility("default_layout", layout == 0);
                SetVisibility("wide_layout", layout == 1);
                SetVisibility("narrow_layout", layout == 2);
                SetVisibility("huge_layout", layout == 3);
                SetVisibility("full_layout", layout == 4);
                SetVisibility("semi_full_layout", layout == 5);
                //
                SetVisibility("scroll_top", layout != 3);
                //
                var title_enabled = data.response.title_wrap_enabled;
                SetVisibility("default_title_wrap", title_enabled);
                SetVisibility("wide_title_wrap", title_enabled);
                SetVisibility("narrow_title_wrap", title_enabled);
                SetVisibility("hige_title_wrap", title_enabled);
                //
                var header_wrap = data.response.header_wrap_type;
                SetVisibility("empty_header_wrap", header_wrap == -1);
                SetVisibility("no_header_wrap", header_wrap == 0);
                SetVisibility("header_wrap", header_wrap == 1);
                SetVisibility("admin_header_wrap", header_wrap == 2);
            }

            if(data.error) {
                console.log("Error: " + data.error);
            }
        });
    },
    isExternalLink: function(url){
        var url_array = url.split("/");

        if(url_array.length > 0)
        {
            return (url_array[0] == "http:" || url_array[0] == "https:");
        }

        return false;
    },
    getLocation: function(url){
        var a = document.createElement("a");
        a.href = url; // => http://example.com:3000/pathname/?search=test#hash

        return {
            protocol: a.protocol, // => "http:"
            host: a.host,         // => "example.com:3000"
            hostname: a.hostname, // => "example.com"
            port: a.port,         // => "3000"
            pathname: a.pathname, // => "/pathname/"
            hash: a.hash,         // => "#hash"
            search: a.search,     // => "?search=test"
            origin: a.origin,     // => "http://example.com:3000"
            uri: a.pathname + a.search + a.hash,
        };
    },
};

// Асинхронные запросы
var AJAX = {
    Progress: { current: 0, total: 1, onChanged: function(e){ } },
    CreateXMLHTTP: function() { //Кроссбраузерный костыль на случай атомной войны (со всякими IE 6)
        var xmlhttp = null;

        if (XMLHttpRequest != null) {
            xmlhttp = new XMLHttpRequest();
        }

        if(!xmlhttp == null)
        {
            try {
                xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
            }
            catch (e) {
                try {
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                catch (e) {
                    xmlhttp = false;
                }
            }
        }
        
        return xmlhttp;
    },
    ServerRequest: function(data, action, url) {
        AJAX.Request({
            type: "POST",
            url: url,
            data: data,
            success: function (data) { action(data); },
        });
    },
    UploadFile: function(file, callback, receiver) {
        if (!receiver) receiver = "/engine/upload.php";
        //
        var Data = new FormData();
        Data.append("file", file);
        //
        var XHR = new XMLHttpRequest();

        XHR.timeout = 3600 * 1000;//час
        XHR.onreadystatechange = function (response) { console.log(response); };
        XHR.onloadend = function (response) { callback(response); };
        XHR.open("POST", receiver, true);
        //
        XHR.upload.onprogress = function(e){
            if (e.lengthComputable) {
                AJAX.Progress.current = e.loaded;
                AJAX.Progress.total = e.total;
            }
            AJAX.Progress.onChanged(e);
        };

        XHR.upload.onloadstart = function(e){
            AJAX.Progress.current = 0;
            AJAX.Progress.onChanged(e);
        }

        XHR.upload.onloadend = function(e){
            AJAX.Progress.current = e.loaded;
            AJAX.Progress.onChanged(e);
        }
        //
        XHR.send(Data);
    },
    Request: function(config) {
        var XHR = AJAX.CreateXMLHTTP(); //Создаем объект для запросов (заточено под Ишаки, но тестировать лень)
        //
        if (config.type == null) config.type = "GET";
        if (config.success == null) config.success = function (data) { console.log(data); }; //Полиморфим коллбеки (templates of callbacks)
        if (config.error == null) config.error = function (error) { console.log("AJAX error: " + error); };
        if (config.timeout == null) config.timeout = 60 * 10 * 1000;
        //
        var ToURL = function (arr) {
            var output = "";

            for (var key in arr) {
                if (typeof arr[key] != "function") {
                    output += key + "=" + encodeURIComponent(arr[key]) + "&";
                }
            }

            return output;
        };

        if (config.type == "POST" || config.type == "GET")
        {
            var POSTData = null;

            if (config.type == "POST") {
                POSTData = ToURL(config.data);
            }

            if (config.type == "GET") {
                config.url += "?" + ToURL(config.data);
            }

            //
            XHR.timeout = config.timeout;
            //XHR.responseType = "text";
            
            XHR.onprogress = function(e){
                if (e.lengthComputable) {
                    AJAX.Progress.current = e.loaded;
                    AJAX.Progress.total = e.total;
                }
                //console.log(e);
            };
            
            XHR.onloadstart = function(e){ AJAX.Progress.current = 0; }
            XHR.onloadend = function(e){ AJAX.Progress.current = e.loaded; }
            
            XHR.open(config.type, config.url, true); //true - асинхронность

            //console.log(XHR);

            if (config.type == "POST") XHR.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            XHR.onreadystatechange = function () {
                if (XHR.readyState == 4) { //Ловим момент, когда запрос совершён, а ответ от машины получен
                    if (XHR.status == 200) {
                        config.success(XHR.responseText); //Ко-ко-ко, всё получилось
                    }
                    else {
                        config.error(XHR.statusText); //Не фортануло (петлю сюда не вешаем, т.к. можно уронить сервер)
                    }
                }

            };

            XHR.send(POSTData);//Пыщ!
        }
    },
    LoadScript: function (src, onload) { //Асинхронно загружаем скрипт на сторону вёрстки
        var s = document.createElement("script");
        s.type = "text/javascript";
        s.async = true;
        s.src = src;
        s.onload = function () {
            onload();
            console.log("LoadScript: " + src);
        };

        var preScript = document.getElementsByTagName("script")[0]; //Ищем в верстке самый первый скрипт
        preScript.parentNode.insertBefore(s, preScript); //Вставляем перед ним (а больше никак)
    },
    LoadCSS: function (src) {
        var s = document.createElement("link");
        s.type = "text/css";
        s.href = src;
        s.rel = "stylesheet";
        s.media = "all";
        s.onload = function () {
            console.log("LoadCSS: " + src);
        };

        var preLink = document.getElementsByTagName("link")[0];
        preLink.parentNode.insertBefore(s, preLink);
    },
};

//

//Редактор кода с использованием ACE.js и WYSIWYG.js
var CodeEditor = { 
    Init: function (block, ondone, onSaveButton) {
        if (!ondone) ondone = function (Editor) { };

        var needSaveButton = false;
        if (!onSaveButton) 
        {
            onSaveButton = function(){ };
        }
        else
        {
            needSaveButton = true;
        }
        //

        if (Exists(block)) {
            var EditorID = block;
            var EditorObjectID = block + "_ace";
            var VisualEditorObjectID = block + "_wysiwyg";
            var EditorObjectWrapID = block + "_ace_wrap";
            var VisualEditorObjectWrapID = block + "_wysiwyg_wrap";
            //
            AJAX.LoadScript("/engine/libs/ace/ace.js", function () {
                var Editor = Find(EditorID);

                //

                var TabPanel = document.createElement("div");
                TabPanel.className = "editor_tab_panel";

                var EditorWrap = document.createElement("div");
                var TextEditorWrap = document.createElement("div");
                var VisualEditorWrap = document.createElement("div");

                TextEditorWrap.className = "editor_wrap";
                VisualEditorWrap.className = "editor_wrap";

                EditorWrap.appendChild(TextEditorWrap);
                EditorWrap.appendChild(VisualEditorWrap);

                var ToolPanel = document.createElement("div");
                ToolPanel.className = "editor_tool_panel";

                Editor.appendChild(TabPanel);
                Editor.appendChild(EditorWrap);
                Editor.appendChild(ToolPanel);

                Editor.className = "editor_body";
                //
                var TextEditorObject = document.createElement("pre");
                TextEditorObject.id = EditorObjectID;
                TextEditorObject.style.position = "absolute";
                TextEditorObject.style.margin = "0px";
                TextEditorObject.style.top = "0px";
                TextEditorObject.style.bottom = "0px";
                TextEditorObject.style.left = "0px";
                TextEditorObject.style.right = "0px";
                //
                TextEditorWrap.appendChild(TextEditorObject);
                //
                var VisualEditorObject = document.createElement("textarea");
                VisualEditorObject.id = VisualEditorObjectID;
                VisualEditorObject.className = "visual_editor_input";
                
                VisualEditorWrap.appendChild(VisualEditorObject);
                VisualEditorWrap.style.display = "none";
                //

                Editor.ACE = ace.edit(EditorObjectID);
                Editor.SetTheme = function(title){
                    Editor.ACE.setTheme("ace/theme/" + title);
                };

                Editor.SetMode = function(title){
                    Editor.ACE.session.setMode("ace/mode/" + title);
                };

                Editor.SetTheme("xcode");
                Editor.SetMode("html");
                //
                Editor.field = {};
                
                Object.defineProperty(Editor.field, "value", {
                    get: function(){
                        return Editor.ACE.getValue();
                    },
                    set: function(value){
                        Editor.ACE.setValue(value, -1);
                    },
                });

                Editor.fullscreenToggle = function () {
                    if (Editor.className == "editor_body") {
                        Editor.className = "editor_body editor_fullscreen";
                    }
                    else {
                        Editor.className = "editor_body";
                    }
                    Editor.ACE.resize();
                };

                //
                var tabs = [
                    "Код", function(){
                        TextEditorWrap.style.display = "block";
                        VisualEditorWrap.style.display = "none";

                        Editor.field.value = CodeEditor.VisualShell.getHTML();
                    },
                    "Визуальный", function(){
                        TextEditorWrap.style.display = "none";
                        VisualEditorWrap.style.display = "block";

                        CodeEditor.VisualShell.setHTML(Editor.field.value);
                    },
                ];

                for(var i = 0; i < tabs.length; i += 2)
                {
                    var tab = document.createElement("div");
                    var title = tabs[i];
                    var click = tabs[i + 1];

                    tab.className = "small_button tab back0";
                    tab.innerText = title;
                    tab.onclick = click;

                    TabPanel.appendChild(tab);
                }

                //
                var FullScreenButton = document.createElement("div");
                FullScreenButton.className = "button pic_button editor_fullscreen_button";
                FullScreenButton.onclick = function () {
                    Editor.fullscreenToggle();
                };
                ToolPanel.appendChild(FullScreenButton);
                //
                if(needSaveButton)
                {
                    var SaveButton = document.createElement("div");
                    SaveButton.className = "button editor_save_button";
                    SaveButton.innerText = "Сохранить";
                    SaveButton.onclick = function () {
                        onSaveButton();
                    };
                    ToolPanel.appendChild(SaveButton);
                }
                //
                AJAX.LoadCSS("/engine/libs/font_awesome/css/font-awesome.css");
                AJAX.LoadCSS("/engine/libs/wysiwyg/src/wysiwyg-editor.css");

                AJAX.LoadScript("/engine/libs/wysiwyg/src/wysiwyg.js", function(){
                    AJAX.LoadScript("/engine/libs/wysiwyg/src/wysiwyg-editor.js", function(){
                        //
                        CodeEditor.InitWYSIWYG(Editor, VisualEditorObjectID);
                        //
                        ondone(Editor);
                    });
                });
            });
        }
        else {
            console.log(block + " is not exists!");
        }
    },
    InitWYSIWYG: function(editor, element){
        element = "#" + element;

        $(element).wysiwyg({
            class: 'visual_editor',
            toolbar: 'top-selection',  // 'selection'|'top'|'top-selection'|'bottom'|'bottom-selection'
            buttons: {
                dummybutton1: false,// Dummy-HTML-Plugin
                dummybutton2: false,// Dummy-Button-Plugin
                // Smiley plugin
                smilies: {
                    title: 'Smilies',
                    image: '\uf118', // <img src="path/to/image.png" width="16" height="16" alt="" />
                    popup: function( $popup, $button ) {
                            var list_smilies = [];
                            
                            for(var i = 1; i <= 197; i++)
                            {
                                list_smilies.push('<img src="/engine/libs/wysiwyg/emoji/' + i + '.png" width="16" height="16" alt="" />');
                            }

                            var $smilies = $('<div/>').addClass('wysiwyg-plugin-smilies').attr('unselectable','on');

                            $.each( list_smilies, function(index, smiley) {
                                
                                $smilies.append(' ');
                                var $image = $(smiley).attr('unselectable','on');
                                // Append smiley
                                var imagehtml = ' ' + $('<div/>').append($image.clone()).html() + ' ';
                                $image
                                    .css({ cursor: 'pointer', display: 'inline' })
                                    .click(function(event) {
                                        $(element).wysiwyg('shell').insertHTML(imagehtml); // .closePopup(); - do not close the popup
                                    })
                                    .appendTo( $smilies );
                            });
                            var $container = $(element).wysiwyg('container');
                            $smilies.css({ maxWidth: parseInt($container.width()*0.33)+'px' });
                            $popup.append( $smilies );
                            // Smilies do not close on click, so force the popup-position to cover the toolbar
                            var $toolbar = $button.parents( '.wysiwyg-toolbar' );
                            if( ! $toolbar.length ) // selection toolbar?
                                return ;
                            return { // this prevents applying default position
                                left: parseInt( ($toolbar.outerWidth() - $popup.outerWidth()) / 2 ),
                                top: 22,
                            };
                    },
                    //showstatic: true,    // wanted on the toolbar
                    showselection: false    // wanted on selection
                },
                insertimage: {
                    title: 'Insert image',
                    image: '\uf030', // <img src="path/to/image.png" width="16" height="16" alt="" />
                    //showstatic: true,    // wanted on the toolbar
                    showselection: false    // wanted on selection
                },
                insertvideo: {
                    title: 'Insert video',
                    image: '\uf03d', // <img src="path/to/image.png" width="16" height="16" alt="" />
                    //showstatic: true,    // wanted on the toolbar
                    showselection: false    // wanted on selection
                },
                insertlink: {
                    title: 'Insert link',
                    image: '\uf08e' // <img src="path/to/image.png" width="16" height="16" alt="" />
                },
                // Fontname plugin
                fontname: {
                    title: 'Font',
                    image: '\uf031', // <img src="path/to/image.png" width="16" height="16" alt="" />
                    popup: function( $popup, $button ) {
                            var list_fontnames = {
                                    // Name : Font
                                    'Arial, Helvetica' : 'Arial,Helvetica',
                                    'Verdana'          : 'Verdana,Geneva',
                                    'Georgia'          : 'Georgia',
                                    'Courier New'      : 'Courier New,Courier',
                                    'Times New Roman'  : 'Times New Roman,Times'
                                };
                            var $list = $('<div/>').addClass('wysiwyg-plugin-list').attr('unselectable','on');

                            $.each( list_fontnames, function( name, font ) {
                                var $link = $('<a/>').attr('href','#')
                                                    .css( 'font-family', font )
                                                    .html( name )
                                                    .click(function(event) {
                                                        $(element).wysiwyg('shell').fontName(font).closePopup();
                                                        // prevent link-href-#
                                                        event.stopPropagation();
                                                        event.preventDefault();
                                                        return false;
                                                    });
                                $list.append( $link );
                            });
                            $popup.append( $list );
                    },
                    //showstatic: true,    // wanted on the toolbar
                    showselection: true    // wanted on selection
                },
                // Fontsize plugin
                fontsize: {
                    title: 'Size',
                    style: 'color:white;background:red',      // you can pass any property - example: "style"
                    image: '\uf034', // <img src="path/to/image.png" width="16" height="16" alt="" />
                    popup: function( $popup, $button ) {
                            // Hack: http://stackoverflow.com/questions/5868295/document-execcommand-fontsize-in-pixels/5870603#5870603
                            var list_fontsizes = [];
                            for( var i=8; i <= 11; ++i )
                                list_fontsizes.push(i+'px');
                            for( var i=12; i <= 28; i+=2 )
                                list_fontsizes.push(i+'px');
                            list_fontsizes.push('36px');
                            list_fontsizes.push('48px');
                            list_fontsizes.push('72px');
                            var $list = $('<div/>').addClass('wysiwyg-plugin-list')
                                                   .attr('unselectable','on');
                            $.each( list_fontsizes, function( index, size ) {
                                var $link = $('<a/>').attr('href','#')
                                                    .html( size )
                                                    .click(function(event) {
                                                        $(element).wysiwyg('shell').fontSize(7).closePopup();
                                                        $(element).wysiwyg('container')
                                                                .find('font[size=7]')
                                                                .removeAttr("size")
                                                                .css("font-size", size);
                                                        // prevent link-href-#
                                                        event.stopPropagation();
                                                        event.preventDefault();
                                                        return false;
                                                    });
                                $list.append( $link );
                            });
                            $popup.append( $list );
                    },
                    //showstatic: true,    // wanted on the toolbar
                    //showselection: true    // wanted on selection
                },
                // Header plugin
                header: {
                    title: 'Header',
                    style: 'color:white;background:blue',      // you can pass any property - example: "style"
                    image: '\uf1dc', // <img src="path/to/image.png" width="16" height="16" alt="" />
                    popup: function( $popup, $button ) {
                            var list_headers = {
                                    // Name : Font
                                    'Header 1' : '<h1>',
                                    'Header 2' : '<h2>',
                                    'Header 3' : '<h3>',
                                    'Header 4' : '<h4>',
                                    'Header 5' : '<h5>',
                                    'Header 6' : '<h6>',
                                    'Code'     : '<pre>'
                                };
                            var $list = $('<div/>').addClass('wysiwyg-plugin-list')
                                                   .attr('unselectable','on');
                            $.each( list_headers, function( name, format ) {
                                var $link = $('<a/>').attr('href','#')
                                                     .css( 'font-family', format )
                                                     .html( name )
                                                     .click(function(event) {
                                                        $(element).wysiwyg('shell').format(format).closePopup();
                                                        // prevent link-href-#
                                                        event.stopPropagation();
                                                        event.preventDefault();
                                                        return false;
                                                    });
                                $list.append( $link );
                            });
                            $popup.append( $list );
                    },
                    //showstatic: true,    // wanted on the toolbar
                    //showselection: false    // wanted on selection
                },
                bold: {
                    title: 'Bold (Ctrl+B)',
                    image: '\uf032', // <img src="path/to/image.png" width="16" height="16" alt="" />
                    hotkey: 'b'
                },
                italic: {
                    title: 'Italic (Ctrl+I)',
                    image: '\uf033', // <img src="path/to/image.png" width="16" height="16" alt="" />
                    hotkey: 'i'
                },
                underline: {
                    title: 'Underline (Ctrl+U)',
                    image: '\uf0cd', // <img src="path/to/image.png" width="16" height="16" alt="" />
                    hotkey: 'u'
                },
                strikethrough: {
                    title: 'Strikethrough (Ctrl+S)',
                    image: '\uf0cc', // <img src="path/to/image.png" width="16" height="16" alt="" />
                    hotkey: 's'
                },
                forecolor: {
                    title: 'Text color',
                    image: '\uf1fc' // <img src="path/to/image.png" width="16" height="16" alt="" />
                },
                highlight: {
                    title: 'Background color',
                    image: '\uf043' // <img src="path/to/image.png" width="16" height="16" alt="" />
                },
                alignleft: {
                    title: 'Left',
                    image: '\uf036', // <img src="path/to/image.png" width="16" height="16" alt="" />
                    //showstatic: true,    // wanted on the toolbar
                    showselection: false    // wanted on selection
                },
                aligncenter: {
                    title: 'Center',
                    image: '\uf037', // <img src="path/to/image.png" width="16" height="16" alt="" />
                    //showstatic: true,    // wanted on the toolbar
                    showselection: false    // wanted on selection
                },
                alignright: {
                    title: 'Right',
                    image: '\uf038', // <img src="path/to/image.png" width="16" height="16" alt="" />
                    //showstatic: true,    // wanted on the toolbar
                    showselection: false    // wanted on selection
                },
                alignjustify: {
                    title: 'Justify',
                    image: '\uf039', // <img src="path/to/image.png" width="16" height="16" alt="" />
                    //showstatic: true,    // wanted on the toolbar
                    showselection: false    // wanted on selection
                },
                subscript: {
                    title: 'Subscript',
                    image: '\uf12c', // <img src="path/to/image.png" width="16" height="16" alt="" />
                    //showstatic: true,    // wanted on the toolbar
                    showselection: true    // wanted on selection
                },
                superscript: {
                    title: 'Superscript',
                    image: '\uf12b', // <img src="path/to/image.png" width="16" height="16" alt="" />
                    //showstatic: true,    // wanted on the toolbar
                    showselection: true    // wanted on selection
                },
                indent: {
                    title: 'Indent',
                    image: '\uf03c', // <img src="path/to/image.png" width="16" height="16" alt="" />
                    //showstatic: true,    // wanted on the toolbar
                    showselection: false    // wanted on selection
                },
                outdent: {
                    title: 'Outdent',
                    image: '\uf03b', // <img src="path/to/image.png" width="16" height="16" alt="" />
                    //showstatic: true,    // wanted on the toolbar
                    showselection: false    // wanted on selection
                },
                orderedList: {
                    title: 'Ordered list',
                    image: '\uf0cb', // <img src="path/to/image.png" width="16" height="16" alt="" />
                    //showstatic: true,    // wanted on the toolbar
                    showselection: false    // wanted on selection
                },
                unorderedList: {
                    title: 'Unordered list',
                    image: '\uf0ca', // <img src="path/to/image.png" width="16" height="16" alt="" />
                    //showstatic: true,    // wanted on the toolbar
                    showselection: false    // wanted on selection
                },
                removeformat: {
                    title: 'Remove format',
                    image: '\uf12d' // <img src="path/to/image.png" width="16" height="16" alt="" />
                }
            },
            // Submit-Button
            submit: {
                title: 'Submit',
                image: '\uf00c' // <img src="path/to/image.png" width="16" height="16" alt="" />
            },
            // Other properties
            selectImage: 'Click or drop image',
            placeholderUrl: 'www.example.com',
            placeholderEmbed: '<embed/>',
            maxImageSize: [600,200],
            //filterImageType: callback( file ) {},
            onKeyDown: function( key, character, shiftKey, altKey, ctrlKey, metaKey ) {},
            onKeyPress: function( key, character, shiftKey, altKey, ctrlKey, metaKey ) {},
            onKeyUp: function( key, character, shiftKey, altKey, ctrlKey, metaKey ) {},
            onAutocomplete: function( typed, key, character, shiftKey, altKey, ctrlKey, metaKey ) {},
            onImageUpload: function( insert_image ) {
                // Example client script (without upload-progressbar):
                var iframe_name = 'legacy-uploader-' + Math.random().toString(36).substring(2);
                $('<iframe>').attr('name',iframe_name)
                    .load(function() {
                        // <iframe> is ready - we will find the URL in the iframe-body
                        var iframe = this;
                        var iframedoc = iframe.contentDocument ? iframe.contentDocument :
                            (iframe.contentWindow ? iframe.contentWindow.document : iframe.document);
                        var iframebody = iframedoc.getElementsByTagName('body')[0];
                        var image_url = iframebody.innerHTML;
                        insert_image( image_url );
                        $(iframe).remove();
                    })
                    .appendTo(document.body);
                        var $input = $(this);
                        $input.attr('name','upload-filename')
                            .parents('form')
                            .attr('action','/script.php') // accessing cross domain <iframes> could be difficult
                            .attr('method','POST')
                            .attr('enctype','multipart/form-data')
                            .attr('target',iframe_name)
                            .submit();
            },
            forceImageUpload: false,    // upload images even if File-API is present
            videoFromUrl: function( url ) {
                // Contributions are welcome :-)

                // youtube - http://stackoverflow.com/questions/3392993/php-regex-to-get-youtube-video-id
                var youtube_match = url.match( /^(?:http(?:s)?:\/\/)?(?:[a-z0-9.]+\.)?(?:youtu\.be|youtube\.com)\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/)([^\?&\"'>]+)/ );
                if( youtube_match && youtube_match[1].length == 11 )
                    return '<iframe src="//www.youtube.com/embed/' + youtube_match[1] + '" width="640" height="360" frameborder="0" allowfullscreen></iframe>';

                // vimeo - http://embedresponsively.com/
                var vimeo_match = url.match( /^(?:http(?:s)?:\/\/)?(?:[a-z0-9.]+\.)?vimeo\.com\/([0-9]+)$/ );
                if( vimeo_match )
                    return '<iframe src="//player.vimeo.com/video/' + vimeo_match[1] + '" width="640" height="360" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';

                // dailymotion - http://embedresponsively.com/
                var dailymotion_match = url.match( /^(?:http(?:s)?:\/\/)?(?:[a-z0-9.]+\.)?dailymotion\.com\/video\/([0-9a-z]+)$/ );
                if( dailymotion_match )
                    return '<iframe src="//www.dailymotion.com/embed/video/' + dailymotion_match[1] + '" width="640" height="360" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';

                // undefined -> create '<video/>' tag
            }
        })
        .change(function() {
            editor.field.value = CodeEditor.VisualShell.getHTML();
        })
        .focus(function() {
            if( typeof console != 'undefined' )
                ;//console.log( 'focus' );
        })
        .blur(function() {
            if( typeof console != 'undefined' )
                ;//console.log( 'blur' );
        });

        //

        $(element).wysiwyg('container');

        //$(element).wysiwyg('shell').bold();
        //$(element).wysiwyg('shell').forecolor('#ff0000');
        //$(element).wysiwyg('shell').setHTML('<b>some text</b>');

        CodeEditor.VisualShell = $(element).wysiwyg('shell');
    },

};

//

/* Deprecated */


function ApiMethod(method, params, callback) {
    API.CallMethod(method, params, callback);
}

function Nav(url) {
    return Navigate(url);
}

function NavAsync(url, cut_href) {
    AJAXLayout.Navigate(event, url);
}

var Sunrise = {
    isMobile: function() {
        return Layout.isMobile();
    },
};


//var LastUrl = GetURL();

/*function NavigateAsync(url, cut_href) { //cut_href - отрезает домен и протокол, если используется link.href
    if (!cut_href) cut_href = false;
    if (cut_href) url = CutHref(url);

    //

    LastUrl = GetURL();

    var StateData = { "NavURL": url };

    //history.pushState(StateData, "", url);
    AJAXLayout.LoadSection(url, function () {
        history.pushState(StateData, "", url);
    });

    return false;
}*/

/*window.onpopstate = function (pop) {
    if (pop.state) {
        AJAXLayout.LoadSection(pop.state.NavURL, function () { });
    }
    else {
        //LastUrl == "" - хак для iOS (onpopstate вызывается при загрузке страницы)
        if(LastUrl != "")AJAXLayout.LoadSection(LastUrl, function () { });
    }
}*/

/*function LoadSection(url, callback)
{
    DebugLog("LoadSection", url);

    ShowLoader();

    ApiMethod("engine.sections.get", { url: url }, function (data) {
        console.log(data);

        HideLoader();

        if(data.response) {
            callback();
            //
            document.title = data.response.title;
            window.scrollTo(0, 0);
            //
            var data_blocks = ["default_title", "default_box", "wide_title", "wide_box", "narrow_title", "narrow_box", "huge_title", "huge_box", "full_box", "semi_full_box"];
            for (var i = 0; i < data_blocks.length; i++) {
                Clear(data_blocks[i]);
            }
            //
            var markup = data.response.markup;

            var layout = data.response.layout;

            if(layout == 0) {
                Write("default_title", data.response.header);
                Write("default_box", markup);
            }

            if (layout == 1) {
                Write("wide_title", data.response.header);
                Write("wide_box", markup);
            }

            if (layout == 2) {
                Write("narrow_title", data.response.header);
                Write("narrow_box", markup);
            }

            if (layout == 3) {
                Write("huge_title", data.response.header);
                Write("huge_box", markup);
            }

            if (layout == 4) {
                //Write("full_title", data.response.header);
                Write("full_box", markup);
            }

            if (layout == 5) {
                //Write("semi_full_title", data.response.header);
                Write("semi_full_box", markup);
            }

            ExecuteJS(markup);

            //
            SetVisibility("default_layout", layout == 0);
            SetVisibility("wide_layout", layout == 1);
            SetVisibility("narrow_layout", layout == 2);
            SetVisibility("huge_layout", layout == 3);
            SetVisibility("full_layout", layout == 4);
            SetVisibility("semi_full_layout", layout == 5);
            //
            SetVisibility("scroll_top", layout != 3);
            //
            var title_enabled = data.response.title_wrap_enabled;
            SetVisibility("default_title_wrap", title_enabled);
            SetVisibility("wide_title_wrap", title_enabled);
            SetVisibility("narrow_title_wrap", title_enabled);
            SetVisibility("hige_title_wrap", title_enabled);
            //
            var header_wrap = data.response.header_wrap_type;
            SetVisibility("empty_header_wrap", header_wrap == -1);
            SetVisibility("no_header_wrap", header_wrap == 0);
            SetVisibility("header_wrap", header_wrap == 1);
            SetVisibility("admin_header_wrap", header_wrap == 2);
        }

        if(data.error) {

        }
    });
}*/