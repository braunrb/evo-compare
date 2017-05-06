function c_getCookie(name) {
    var cookie = " " + document.cookie;
    var search = " " + name + "=";
    var setStr = null;
    var offset = 0;
    var end = 0;
    if (cookie.length > 0) {
        offset = cookie.indexOf(search);
        if (offset != -1) {
            offset += search.length;
            end = cookie.indexOf(";", offset)
            if (end == -1) {
                end = cookie.length;
            }
            setStr = unescape(cookie.substring(offset, end));
        }
    }
    return(setStr);
}
function c_getPosition(elem){
    var el = $(elem).get(0);
    var p = {x: el.offsetLeft, y: el.offsetTop}
    while (el.offsetParent){
        el = el.offsetParent;
        p.x += el.offsetLeft;
        p.y += el.offsetTop;
        if (el != document.body && el != document.documentElement){
            p.x -= el.scrollLeft;
            p.y -= el.scrollTop;
        }
    }
    return p;
}
function c_getCenterPos(elA,elB,Awidth,Aheight){
    if(typeof(Awidth)=='undefined') Awidth = $(elA).outerWidth();
    if(typeof(Aheight)=='undefined') Aheight = $(elA).outerHeight();
    posB = new Object();
    cntPos = new Object();
    posB = c_getPosition(elB);
    var correct;
    cntPos.x = Math.round(($(elB).outerWidth()-Awidth)/2)+posB.x;
    cntPos.y = Math.round(($(elB).outerHeight()-Aheight)/2)+posB.y;
    if(cntPos.x+Awidth>$(window).width()){
        cntPos.x = Math.round($(window).width()-$(elA).outerWidth())-2;
    }
    if(cntPos.x<0){
        cntPos.x = 2;
    }
    return cntPos;
}
function c_helper(elem,name){
    if(typeof($(elem).get(0))=='undefined') return;
    shkHelper = '<div id="stuffHelper"><div class="title"></div><div><span id="stuffHelperName"></span></div></div>'
    $('#stuffHelper').remove();
    $('body').append(shkHelper);
    var elHelper = $('#stuffHelper');
    var btPos = c_getCenterPos(elHelper,elem);
    if(name){
        $('#stuffHelperName').html(name);
    }else{
        $('#stuffHelperName').remove();
    }
    $('#stuffHelper').css({'top':btPos.y+'px','left':btPos.x+'px'}).fadeIn(500);
    setTimeout(function () {
        $('#stuffHelper').fadeOut(500);
    },500)
}

function c_setCookie (name, value, expires, path, domain, secure) {
    path = '/'
    document.cookie = name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires : "") +
        ((path) ? "; path=" + path : "/") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}

var defaultConfig = {
    'active': 'active', //клас елемента который находиться в сравнении
    'compareSelector':'.to-compare', // селектор елементов для сравнения
    'compareCount':'#compare-count', // блок с количеством елементов с равнении
};
config = $.extend( defaultConfig, c_config );

var cookie = c_getCookie('compare_ids');
if(cookie==null){
    cookie = {};
}
else{
    cookie = JSON.parse(cookie)
}
var itemCount;
//разстановка активного класа для елементов которие уже в сравнении
var tmp = 0;
function setActive() {
    for (var key in cookie) {
        var elem = config['compareSelector']+'[data-id="'+key+'"]';
        if($(elem).length){
            if(cookie[key] !== undefined){
                $(elem).addClass('active')
                if (typeof afterSetDefault == 'function') {
                    afterSetDefault(key,elem,true);
                }

            }
            else{
                if (typeof afterSetDefault == 'function') {
                    afterSetDefault(key,elem,false);
                }
            }
        }
        tmp ++;
    }
    return tmp;
}
itemCount = setActive()


//устанавливает количество елментов в сравнении
function setCount(count) {
    $(config['compareCount']).text(count)

    if (typeof afterSetCount == 'function') {
        afterSetCount(count);
    }

}
setCount(itemCount)

function deleteFromCompare(id) {
    var cookie = c_getCookie('compare_ids');
    if(cookie==null){
        cookie = {};
    }
    else{
        cookie = JSON.parse(cookie)
    }
    delete   cookie[id] ;
    c_setCookie('compare_ids',JSON.stringify(cookie),2592000)
}
function addInCompare(id) {
    var cookie = c_getCookie('compare_ids');
    if(cookie==null){
        cookie = {};
    }
    else{
        cookie = JSON.parse(cookie)
    }
    cookie[id] = true;
    c_setCookie('compare_ids',JSON.stringify(cookie),2592000)
}
$('body').on('click',config['compareSelector'],function (e) {
    e.preventDefault()

    elem = $(this);

    id = elem.attr('data-id')
    if(elem.hasClass(config['active'])){// убираем из сравнения
        delete   cookie[id] ;
        elem.removeClass(config['active'])
        itemCount --;

        if (typeof afterDeleteFormCompare == 'function') {
            afterDeleteFormCompare(id,elem);
        }
    }
    else{   //добавляем в сравнение

        //проверяем количество
        if(itemCount>=c_config['max']){
            var message = c_message['maxMessage'];
            message = message.replace('(current)', itemCount);
            message = message.replace('(total)', c_config['max']);
            c_helper(elem,  message)
            return;
        }
        cookie[id] = true
        elem.addClass(config['active'])
        itemCount ++;
        if (typeof afterAddToCompare == 'function') {
            afterAddToCompare(id,elem);
        }
    }
    c_setCookie('compare_ids',JSON.stringify(cookie),2592000)
     setCount(itemCount)
})