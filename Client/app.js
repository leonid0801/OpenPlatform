//app.js
App({
  onLaunch: function () {
    // 展示本地存储能力
    var logs = wx.getStorageSync('logs') || []
    logs.unshift(Date.now())
    wx.setStorageSync('logs', logs)

    //检查登录是否过期
    wx.checkSession({
      success: function (e) {   //登录态未过期
        console.log("没过期");
      },
      fail: function () {   //登录态过期了
        console.log("过期了");
        wx.login({
          success: function (res) {
            //用户登录凭证（有效期五分钟）
            var js_code = res.code
            if (js_code) {
              console.log("ccccccccccccc", res.code)
              //发起网络请求
              wx.request({
                url: 'https://wtmb.online/index.php/api/wxapp/get_seq',
                //url: 'https://wxapi.hotapp.cn/proxy/?appkey=hotapp11377340&url=https://www.wtmb.online/wx.php',
                data: {
                  code: js_code
                },
                method: 'GET',
                header: { 'content-type': 'application/json' },
                success: function (openIdRes) {
                  if (openIdRes.data.code==0){
                    var client_session = openIdRes.data.data.client_session
                    console.log("[client_session]", client_session)
                    // 写入
                    wx.setStorageSync('client_session', client_session)
                  }
                  //weChatUserInfo.openId = openIdRes.data.openid;
                  console.log(openIdRes)
                }
              })
            } else {
              console.log('获取用户登录态失败！' + res.errMsg)
            }
          }
        });
      }
    });    



    // 获取用户信息
    wx.getSetting({
      success: res => {
        if (res.authSetting['scope.userInfo']) {
          // 已经授权，可以直接调用 getUserInfo 获取头像昵称，不会弹框
          wx.getUserInfo({
            success: res => {
              // 可以将 res 发送给后台解码出 unionId
              this.globalData.userInfo = res.userInfo

              // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
              // 所以此处加入 callback 以防止这种情况
              if (this.userInfoReadyCallback) {
                this.userInfoReadyCallback(res)
              }
            }
          })
        }
      }
    })
  },

  globalData: {
    userInfo: null
  }
})