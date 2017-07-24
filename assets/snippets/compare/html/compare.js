var defaultConfig = {
    'active': 'active', //клас елемента который находиться в сравнении
    'compareSelector':'.to-compare', // селектор елементов для сравнения
    'compareCount':'#compare-count', // блок с количеством элементов в сравнении
};
config = $.extend( defaultConfig, c_config );
var compare_top_id = 1; // если нет категорий, то всё будет писаться для parent=1
var cookieExpTime = 2592000;
var cookieName = 'compare_ids';

var compareCount = [];    // счётчик количества элементов в сравнении (в категории)
var compareCountFull = 0; // счётчик общего количества элементов в сравнении (по всем категориям)

// возвращает cookie с именем name, если есть, если нет, то undefined
function getCookie(name) {
  var matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  matches = matches ? decodeURIComponent(matches[1]) : null;
  if( matches === null){
      return {};
  }
  matches = matches.replace(/\+/g, ' ');
  var cookie = matches.replace("} }", "}}");
  cookie = JSON.parse(cookie);
  return cookie;
}

function setCookie(name, valueObj, options) {
  options = options || {};
  value = JSON.stringify(valueObj).replace("}}", "} }");

  var expires = options.expires;

  if (typeof expires == "number" && expires) {
    var d = new Date();
    d.setTime(d.getTime() + expires * 1000);
    expires = options.expires = d;
  }
  if (expires && expires.toUTCString) {
    options.expires = expires.toUTCString();
  }

  value = encodeURIComponent(value);

  var updatedCookie = name + "=" + value;

  for (var propName in options) {
    updatedCookie += "; " + propName;
    var propValue = options[propName];
    if (propValue !== true) {
      updatedCookie += "=" + propValue;
    }
  }

  document.cookie = updatedCookie;
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
function showHelper(elem,msg){
    if(typeof($(elem).get(0))=='undefined') return;
    $(elem).popover({
      placement: 'top',
      trigger: 'manual',
      container: 'body',
      content: msg
    });
    $(elem).popover('show');
    setTimeout(function () {
        $(elem).popover('hide');
    },1500);
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
        setActive();
        setCount(compareCountFull);
    })
}

function setActive() {
  var cookie = getCookie(cookieName);
  var _compareCountFull = 0,
      _compareCount = [];
    for (var parent in cookie) {
        for (var id in cookie[parent]) {
            if (cookie[parent][id] === true) {
                var elem = config['compareSelector'] + '[data-id="' + id + '"]';
                if ($(elem).length) {
                    $(elem).addClass('active')
                    if (typeof afterSetDefault == 'function') {
                        afterSetDefault(id, elem, true);
                    }
                    if(typeof _compareCount[parent] === 'undefined'){
                        _compareCount[parent] = 0;
                    }
                }
                _compareCountFull++;
                _compareCount[parent]++;
            }
        }
    }
    if (parent === undefined) {
      $(config['compareSelector']).each(function(indx, elem){
        $(elem).removeClass('active');
      });
    }
    compareCount = []; // обнуляем глобальный compareCount, на случай если кто-то вызовет setActive() 2 раза подряд.
    for (var parent in _compareCount) {
      compareCount[parent] = _compareCount[parent];
    }
    compareCountFull = _compareCountFull;
}

//устанавливает количество элементов в сравнении
function setCount(count) {
    $(config['compareCount']).text(count)

    if (typeof afterSetCount == 'function') {
        afterSetCount(count);
    }

}

compare_parent();

//очистка списка сравнения
function clearCompare() {
  var cookie = {};
    setCookie(cookieName, cookie, cookieExpTime);
    $.get('ajax-compare-clear');
    localStorage.removeItem(cookieName); // fix для старой версии
    for (var parent in compareCount) {
      compareCount[parent] = 0;
    }
    compareCountFull = 0;
    setActive(); // чтобы обновить данные на странице
    setCount(0);
}

function deleteFromCompare(id) {
    var cookie = getCookie(cookieName);
    var _parent = false;
    for (var parent in cookie) {
      if (cookie[parent][id] === true) {
        delete cookie[parent][id];
        _parent = parent;
      }
    }
    setCookie(cookieName, cookie, cookieExpTime);
    $.get('ajax-compare-delete',{id:id,parent:parent}); // чтобы работало при ajax
}
function addInCompare(id, parent = compare_top_id) {
    var cookie = getCookie(cookieName);
    if(typeof cookie[parent] === 'undefined' || cookie[parent] === null){
        cookie[parent] = {};
    }
    cookie[parent][id] = true;
    setCookie(cookieName, cookie, cookieExpTime);
    $.get('ajax-compare-add',{id:id,parent:parent}); // чтобы работало при ajax
}


$('body').on('click',config['compareSelector'],function (e) {
    e.preventDefault(e); // (e) - чтобы точно работало в firefox
    elem = $(this);

    var id = elem.attr('data-id');
    var parent = elem.attr('data-parent');
    var group = elem.attr('data-parent-title');

    if(typeof compareCount[parent] === 'undefined'){
        compareCount[parent] = 0;
    }

    if(elem.hasClass(config['active'])){// убираем из сравнения
        elem.removeClass(config['active'])
        compareCountFull --;
        compareCount[parent]--;

        deleteFromCompare(id);

        if (typeof afterDeleteFormCompare == 'function') {
            afterDeleteFormCompare(id,elem);
        }

    }
    else {   //добавляем в сравнение

        //проверяем количество
        if(compareCount[parent]>=c_config['max']){
            var message = c_message['maxMessage'];
            message = message.replace('(current)', compareCount[parent]);
            message = message.replace('(total)', c_config['max']);
            message = message.replace('(group)', group);
            c_helper(elem, message);
            //showHelper(elem, message); // вариант хелпера для bootstrap
            return;
        }

        elem.addClass(config['active'])
        compareCountFull ++;
        compareCount[parent]++;

        addInCompare(id,parent);

        if (typeof afterAddToCompare == 'function') {
            afterAddToCompare(id,elem);
        }

    }
    setCount(compareCountFull)
})
