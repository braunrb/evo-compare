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
function compare_parent() {
    var items = [];
    $(config['compareSelector']).each(function (ind,elem) {
        var id = $(elem).data('id');
        items.push(id)
    })
    $.get('compare_parent',{data:items},function (data) {
        data = JSON.parse(data);

            for (var key in data) {
                var value = data[key];
                $('.to-compare[data-id="'+key+'"]').attr('data-parent',value['parent']);
                $('.to-compare[data-id="'+key+'"]').attr('data-parent-title',value['title']);

            }
        setActive()
        setCount(compareCountFull)
    })
}
function setActive() {

    for (parent = 0, len = cookie.length; parent < len; ++parent) {
        if (cookie[parent] !== null) {
            for (var id = 0, length = cookie[parent].length; id < length; ++id) {
                if (cookie[parent][id] === true) {
                    var elem = config['compareSelector'] + '[data-id="' + id + '"]';
                    if ($(elem).length) {
                        $(elem).addClass('active')
                        if (typeof afterSetDefault == 'function') {
                            afterSetDefault(key, elem, true);
                        }
                        if(typeof compareCount[parent] === 'undefined'){
                            compareCount[parent] = 0;
                        }

                    }
                    compareCount[parent]++;
                    compareCountFull++;
                }
            }
        }
    }
}

var defaultConfig = {
    'active': 'active', //клас елемента который находиться в сравнении
    'compareSelector':'.to-compare', // селектор елементов для сравнения
    'compareCount':'#compare-count', // блок с количеством елементов с равнении
};
config = $.extend( defaultConfig, c_config );

var cookie = localStorage.getItem('compare_ids');

if(cookie === null){

    cookie = []
}
else{
    var json = cookie.replace("} }", "}}");

    cookie = JSON.parse(json);

}

var compareCount = [];
var compareCountFull = 0;
compare_parent();


//устанавливает количество елментов в сравнении
function setCount(count) {
    $(config['compareCount']).text(count)

    if (typeof afterSetCount == 'function') {
        afterSetCount(count);
    }

}


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

    var id = elem.attr('data-id');
    var parent = elem.attr('data-parent');
    var group = elem.attr('data-parent-title');

    if(typeof compareCount[parent] === 'undefined'){
        compareCount[parent] = 0;
    }


    if(elem.hasClass(config['active'])){// убираем из сравнения
        delete   cookie[parent][id] ;
        elem.removeClass(config['active'])
        compareCountFull --;
        compareCount[parent]--;

        if (typeof afterDeleteFormCompare == 'function') {
            afterDeleteFormCompare(id,elem);
        }
        $.get('ajax-compare-delete',{id:id,parent:parent});
    }
    else{   //добавляем в сравнение

        //проверяем количество

        if(compareCount[parent]>=c_config['max']){
            var message = c_message['maxMessage'];
            message = message.replace('(current)', compareCount[parent]);
            message = message.replace('(total)', c_config['max']);
            message = message.replace('(group)', group);
            c_helper(elem,  message)
            return;
        }

        if(typeof cookie[parent] === 'undefined' || cookie[parent] === null){
            cookie[parent] = [];
        }

        cookie[parent][id] = true;

        elem.addClass(config['active'])
        compareCountFull ++;
        compareCount[parent]++;

        if (typeof afterAddToCompare == 'function') {
            afterAddToCompare(id,elem);
        }
        $.get('ajax-compare-add',{id:id,parent:parent})
    }
    var json = JSON.stringify(cookie).replace("]]", "] ]");
    localStorage.setItem('compare_ids', json);

     setCount(compareCountFull)
})