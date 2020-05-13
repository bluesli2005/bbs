var document_title = document.title;

$(document).ready(function () {

    // 检测首页动态更新
    if (G_USER_ID.length > 0) {
        var checkactionsnew_handle = setInterval(function () {
            check_actions_new(new Date().getTime());
        }, 60000);
    }

    // 滚动指定位置
    if (window.location.hash.indexOf('#!') != -1) {
        if ($('a[name=' + window.location.hash.replace('#!', '') + ']').length) {
            $.scrollTo($('a[name=' + window.location.hash.replace('#!', '') + ']').offset()['top'] - 20, 600, {queue: true});
        }
    }

    // 验证码自动点击
    $('#captcha').click();

    // 导航条小箭头位置修复
    $('.nav .triangle').css('left', $('.nav li').innerWidth() / 2 - 8);

    // 导航条按钮
    $('.nav ul li .user').click(function () {
        if (G_USER_ID.length<=0) {
            window.location.href = G_LOGIN_URL;
            return false;
        }
        if ($(this).parents('li').find('.triangle').css('display') == 'none') {
            $(this).parents('li').find('.triangle').show();
            $(this).parents('.nav').find('.aw-popover.user').show();
            $(this).parents().siblings().children('a').removeClass("active");
            $(this).addClass('active');
        }
        else {
            $(this).parents('li').find('.triangle').hide();
            $(this).parents('.nav').find('.aw-popover.user').hide();
            $(this).removeClass('active');
        }
    });


    // textarea自动增加高度
    $('.autosize').autosize();

    // 问题评论box
    AWS.Init.init_comment_box('.aw-add-comment');

    // 文章评论box
    AWS.Init.init_article_comment_box('.aw-article-comment');

    // 话题编辑box
    AWS.Init.init_topic_edit_box('.aw-topic-bar .icon-inverse');
    AWS.Init.init_topic_edit_box_new('.topic_tab .edit');

    // 搜索下拉菜单
    AWS.Dropdown.bind_dropdown_list('.aw-search-bar input', 'search');

    // 私信搜索下拉菜单
    AWS.Dropdown.bind_dropdown_list('.aw-inbox-search-bar input', 'message');

    // 邀请下拉菜单
    AWS.Dropdown.bind_dropdown_list('.aw-invite-box input', 'invite');

    // 话题编辑删除按钮
    $(document).on('click', '.aw-topic-bar .tag-bar .topic-tag i', function () {
        var _this = $(this);
        $.post(G_BASE_URL + '/topic/ajax/remove_topic_relation/', {
            'type': $(this).parents('.aw-topic-bar').attr('data-type'),
            'item_id': $(this).parents('.aw-topic-bar').attr('data-id'),
            'topic_id': $(this).parents('.topic-tag').attr('data-id')
        }, function (result) {
            if (result.errno == 1) {
                _this.parents('.topic-tag').detach();
            } else {
                AWS.DingTalk.showToast("error");
            }
        }, 'json');
        return false;
    });

    //邀请回答按钮
    $('.aw-invite-replay').click(function () {
        if ($(this).parents('.aw-question-detail').find('.aw-invite-box').is(':visible')) {
            $(this).parents('.aw-question-detail').find('.aw-invite-box').hide();
            $(this).removeClass('active');
        } else {
            $(this).parents('.aw-question-detail').find('.aw-invite-box').show();
            $(this).addClass('active');
        }
    });
  
  　$('.aw-invite-replay-box').click(function () {
        $('html, body').animate({
            scrollTop: $("#editorContainer").offset().top
        }, 1000);
    });

    function check_actions_new(time) {
        $.get(G_BASE_URL + '/home/ajax/check_actions_new/time-' + time, function (result) {
            if (result.errno == 1) {
                if (result.rsm.new_count > 0) {
                    if ($('#new_actions_tip').is(':hidden')) {
                        $('#new_actions_tip').css('display', 'block');
                    }

                    $('#new_action_num').html(result.rsm.new_count);

                    $('.nav .new-action').show();
                }
            }
        }, 'json');
    }

    // textarea获取焦点时兼容导航溢出
    $('textarea').bind({
        focus: function () {
            $('.nav').css({
                'position': 'relative',
                'bottom': -(parseInt($(document).height()) - parseInt($('.nav').offset().top))
            });

            $('body').addClass('focus');
        },

        blur: function () {
            $('.nav').css({
                'position': 'fixed',
                'bottom': 0
            });

            $('body').removeClass('focus');
        }
    })

});

function encrypt_pass(pass) {
    key = CryptoJS.enc.Utf8.parse(G_PRIVATEKEY);// 秘钥
    var iv= CryptoJS.enc.Utf8.parse(G_IV);//向量iv
    var encrypted = CryptoJS.AES.encrypt(pass, key, { iv: iv, mode: CryptoJS.mode.CBC, padding: CryptoJS.pad.Pkcs7});
    return encrypted.toString();
}