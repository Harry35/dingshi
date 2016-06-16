/*=== menu popup ===*/
var loadMenuPopup = function () {
    if (typeof arrayMenus == 'undefined') {
        return;
    }
    
    for (var i = 0, len = arrayMenus.length; i < len; i++) {
        arrayMenus[i] = baseUrl.concat("/assets/img/menus/", arrayMenus[i]);
     }

     $(document).on('click','.menu-popup',function () {
        var index = $(this).attr('data-index');
        var myPhotoBrowserPopup = $.photoBrowser({
            photos : arrayMenus,
            type: 'popup',
            initialSlide: index,
            navbar: true,
            toolbar: true,
        });
         myPhotoBrowserPopup.open();
     });
}


/*=== food popup ===*/

var loadFoodPopup = function () {
    if (typeof arrayMenus == 'undefined') {
        return;
    }
    
    var arrayFood = [];

    for (var i = 0, len = foods.length; i < len; i++) {
        var obj = {
            url : baseUrl.concat("/assets/img/foods/", foods[i]['pic']),
            caption : foods[i]['name'].concat(" ", foods[i]['price'])
        }
        arrayFood.push(obj);
    }

    $(document).on('click','.food-popup',function () {
        var index = $(this).attr('data-index');
        var myPhotoBrowserCaptions = $.photoBrowser({
            photos : arrayFood,
            type: 'popup',
            initialSlide: index,
            navbar: true,
            toolbar: true,
        });
        myPhotoBrowserCaptions.open();
    });
}

/*==== 微信二维码 ====*/

var loadWechatPopup = function () {
    $(document).on('click','.confirm-wechat', function () {
        var myQRBrowserCaptions = $.photoBrowser({
            photos : [
                {
                    url : baseUrl.concat("/assets/img/qr/", wechatQR),
                    caption : "长按点击《识别图中二维码》"
                }
            ],
            type: 'popup'
        });
        myQRBrowserCaptions.open();
    });
}


$('document').ready(function(){
    $.init();
    loadMenuPopup();
    loadFoodPopup();
    loadWechatPopup();

    $(document).on('click','.edit-review', function () {
        var resto_id = $('#resto-id').val();
        var user_id = $('#user-id').val();
        $.ajax({
            url : baseUrl + '/resto/getUserReview',
            type : 'POST',
            dataType : 'json',
            data : {
                resto_id : resto_id,
                user_id : user_id,
            },
            success: function(result){
                if (!result) {
                    $.popup('.popup-edit-review');
                } else {
                    $.alert('已经评论过这家外卖啦～');
                }
            }
        });
        
    });

    $(document).on('click','.pic-menu', function () {
        $.popup('.popup-pic-menu');
    });

    $(document).on('click','.open-vertical-modal', function () {
        var arrayText = [];
        for (i = 0; i < arrayTels.length; i++) {
            arrayText[i] = {
                text: '<a href="tel:'+ arrayTels[i] +'">'+ arrayTels[i] +'</a>'
            }
        }
        arrayText.push({
            text : '关闭'
        })

        $.modal({
          text: '点击下列电话号码联系卖家',
          verticalButtons: true,
          buttons: arrayText
        })
    });
    
    
    
    var list_review = $('#list-reviews');
    var loadDoLikeReview =  function () {
        var target_id = $(this).attr('data-review');
        var user_id = $('#user-id').val();
        var liked = $(this).attr('data-like');
        var likes = $('#review-likes-'+target_id).html();
        if (likes == null || likes == '') {
            likes = 0;
        } else {
            likes = parseInt(likes);
        }
        if (liked != 0) {
            return;
        }
//        点赞＋1动画
//        $('#do-like-review-'+target_id).removeAttr('href');
        $('#do-like-review-'+target_id).attr('data-like', 1);
        likes++;
        $('#do-like-review-'+target_id).html('<span class="fa fa-thumbs-up" aria-hidden="true" ><span id="review-likes-'+target_id+'" class="cpt-likes" style="margin-left: 5px;">'+likes+'</span></span>');
        $.toast("已点赞");
        
        $.ajax({
            url : baseUrl + '/resto/addLike',
            type : 'POST',
            dataType : 'json',
            data : {
                target_id : target_id,
                user_id : user_id,
                type : 'review'
            },
            success: function(result){
                if (!result) {
//                    $('#do-like-review-'+target_id).removeAttr('href');
                    $('#do-like-review-'+target_id).attr('data-like', 0);
                    likes--;
                    $('#do-like-review-'+target_id).html('<span class="fa fa-thumbs-up" aria-hidden="true" ><span id="review-likes-'+target_id+'" class="cpt-likes" style="margin-left: 5px;">'+likes+'</span></span>');
                }
            }
        });
    };
    list_review.find('.do-like-review').on('click',loadDoLikeReview);
    
    
    
    $('#valide-review').on('click', function () {
        var resto_id = $('#resto-id').val();
        var text = $('#text').val();
        var username = $('#username').val();
        var user_id = $('#user-id').val();
        
        if (username == '') {
            alert('请添加用户名');
            return;
        }
        
        if (text == '') {
            alert('请添加评论');
            return;
        }
        
        //加载
        $.showIndicator();
        $.ajax({
            url : baseUrl + '/resto/addReview',
            type : 'POST',
            dataType : 'json',
            data : {
                resto_id : resto_id,
                user_id : user_id,
                text : text,
                username : username
            },
            success: function(result){
                if (result) {
                    $.hideIndicator();
                    $('#text').val('');
                    $('#username').val('');
                    $.closeModal('.popup-edit-review');
                    $.toast("已添加评论啦");
                    var html = list_review.html();
                    if (haveReview !== "1") {
                        html = '';
                    }
                    var htmlReview = '<li class="item-content">' +
                        '<div class="item-media">' +
                            '<img src="'+baseUrl+'/assets/img/profils/'+ result.pic +'" width="44">' + 
                        '</div>' +
                        '<div class="item-inner">' + 
                            '<div class="item-title-row">' + 
                                '<div class="item-title">' + result.username + '</div>' +
                                '<span style="float:right">' +
                                    '<a href="#" id="do-like-review-'+ result.id +'" class="do-like-review" data-review="'+ result.id +'" data-like="0">' + 
                                        '<span class="fa fa-thumbs-o-up" aria-hidden="true" >' +
                                            '<span id="review-likes-' + result.id + '" class="cpt-likes"></span>' +
                                        '</span>' +
                                    '</a>' +
                                '</span>' +
                            '</div>' +
                            '<div class="item-subtitle">' + result.text + '</div>' +
                            '<div class="item-subtitle color-gray">'+ result.created.substring(0, 10) +'</div>' +
                        '</div>' +
                    '</li>'; 

                    html = htmlReview + html;
                    list_review.html(html);
                    list_review.find('.do-like-review').on('click',loadDoLikeReview);
                    
                }
            }
        });
    });
        
    $('.do-like-food').on('click', function () {
        var target_id = $(this).attr('data-food');
        var user_id = $('#user-id').val();
        var liked = $(this).attr('data-like');
        var likes = $('#food-likes-'+target_id).html();
        if (likes == null || likes == '') {
            likes = 0;
        } else {
            likes = parseInt(likes);
        }
        if (liked != 0) {
            return;
        }
        
        //先点赞＋1，不成功－1
//        $('#do-like-food-'+target_id).removeAttr('href');
        $('#do-like-food-'+target_id).attr('data-like', 1);
        likes++;
        $('#do-like-food-'+target_id).html('<span class="fa fa-thumbs-up" aria-hidden="true" ><span id="food-likes-'+target_id+'" class="cpt-likes" style="margin-left: 5px;">'+likes+'</span></span>');
        $.toast("已点赞");

        $.ajax({
            url : baseUrl + '/resto/addLike',
            type : 'POST',
            dataType : 'json',
            data : {
                target_id : target_id,
                user_id : user_id,
                type : 'food'
            },
            success: function(result){
                if (!result) {
//                    $('#do-like-food-'+target_id).removeAttr('href');
                    $('#do-like-food-'+target_id).attr('data-like', 0);
                    likes--;
                    $('#do-like-food-'+target_id).html('<span class="fa fa-thumbs-up" aria-hidden="true" ><span id="food-likes-'+target_id+'" class="cpt-likes" style="margin-left: 5px;">'+likes+'</span></span>');
                }
            }
        });
    });
    
    
});