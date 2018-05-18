/** layuiAdmin.pro-v1.0.0-beta7 LPPL License By http://www.layui.com/admin/ */
;layui.define(["form", "upload", "laydate"], function(t) {
    var i = layui.$
        , e = layui.layer
        , n = (layui.laytpl,
        layui.setter,
        layui.view,
        layui.admin)
        , a = layui.form
        , s = layui.upload
        , laydate = layui.laydate;
    i("body");
    a.render(),
        a.verify({
            nickname: function(t, i) {
                return new RegExp("^[a-zA-Z0-9_一-龥\\s·]+$").test(t) ? /(^\_)|(\__)|(\_+$)/.test(t) ? "用户名首尾不能出现下划线'_'" : /^\d+\d+\d$/.test(t) ? "用户名不能全为数字" : void 0 : "用户名不能有特殊字符"
            },
            pass: [/^[\S]{6,12}$/, "密码必须6到12位，且不能出现空格"],
            repass: function(t) {
                if (t !== i("#LAY_password").val())
                    return "两次密码输入不一致"
            }
        }),
        a.on("submit(set_setting)", function(t) {
            n.req({
                url: '/setting/saveSetting/'+i(this).attr('lay-type')
                ,data: t.field
                ,type: 'post'
                ,done: function(res){
                    //登入成功的提示与跳转
                    layer.msg(res.msg, {
                        offset: '15px'
                        ,icon: 1
                        ,time: 1000
                    });
                }
                ,fail: function(res){
                    layer.msg(res.msg, {
                        offset: '15px'
                        ,icon: 2
                    });
                }
            });
        });
    i('[lay-click]').on('click', function(){
        n.req({
            url: i(this).attr('lay-link')
            ,done: function(res){
                layer.msg(res.msg, {
                    offset: '15px'
                    ,icon: 1
                    ,time: 1000
                });
            }
        });
    });

    var r = i("#LAY_avatarSrc");
    s.render({
        url: "/api/upload/",
        elem: "#LAY_avatarUpload",
        done: function(t) {
            0 == t.status ? r.val(t.url) : e.msg(t.msg, {
                icon: 5
            })
        }
    }),
        n.events.avartatPreview = function(t) {
            var i = r.val();
            e.photos({
                photos: {
                    title: "查看头像",
                    data: [{
                        src: i
                    }]
                },
                shade: .01,
                closeBtn: 1,
                anim: 5
            })
        }
        ,
        a.on("submit(setmypass)", function(t) {
            n.req({
                url: '/user/changePassword'
                ,data: t.field
                ,type: 'post'
                ,done: function(res){
                    //登入成功的提示与跳转
                    layer.msg(res.msg, {
                        offset: '15px'
                        ,icon: 1
                        ,time: 1000
                    });
                }
                ,fail: function(res){
                    layer.msg(res.msg, {
                        offset: '15px'
                        ,icon: 2
                    });
                }
            });
        }),
        t("set", {})
});
