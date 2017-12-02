//app.js
App({
  globalData: {
    userInfo: null,
    typeObjectArray: [
      {
        main_type: "旅游",
        id: 0,
        array: ["周边游", "国内游", "境外游", "自驾游", "穿越", "其它"]
      },
      {
        main_type: "活动",
        id: 1,
        array: ["婚恋服务", "单身交友", "游泳", "K歌", "桌游", "聚餐", "其它"]
      },
      {
        main_type: "户外",
        id: 2,
        array: ["爬山", "撕名牌", "景点参观", "钓鱼", "其它"]
      },
      {
        main_type: "摄影",
        id: 3,
        array: ["自然风景", "人像写真", "其它"]
      }
    ],
  },
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
              console.log("[js_code]", res.code)
              //发起网络请求
              wx.request({
                url: 'https://bjwob.top/index.php/api/wxapp/get_seq',
                //url: 'https://wxapi.hotapp.cn/proxy/?appkey=hotapp11377340&url=https://www.bjwob.top/wx.php',
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

                    wx.getUserInfo({
                      success: res => {
                        console.log('userInfo....', res.userInfo)
                        wx.setStorageSync('user_info', res.userInfo)
                        wx.request({
                          url: 'https://bjwob.top/index.php/api/wxapp/upt_user_info',
                          data: {
                            "client_session": client_session,
                            "nickname": res.userInfo.nickName,
                            "avatar_url": res.userInfo.avatarUrl,
                            "city": res.userInfo.city,
                            "gender": res.userInfo.gender,
                            "language": res.userInfo.language,
                            "province": res.userInfo.province,
                            "country": res.userInfo.country,
                          },
                          method: 'POST', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
                          header: { "Content-Type": "application/x-www-form-urlencoded" }, // 设置请求的 header
                          success: function (res) {
                            console.log('saveUserInfo……', res)
                          }
                        })
                      }
                    })
                  }
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



  saveUserInfo: function (client_session) {

    wx.getUserInfo({
      success: res => {
        app.globalData.userInfo = res.userInfo
        console.log('userInfo....', res.userInfo)
        wx.setStorageSync('user_info', res.userInfo)
        this.setData({
          userInfo: res.userInfo,
          hasUserInfo: true
        })
      }
    })

    var user_info = res.userInfo
    console.log('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!client_session', client_session)
    wx.request({
      url: 'https://bjwob.top/index.php/api/wxapp/upt_user_info',
      data: {
        "client_session": client_session,
        "nickname": user_info.nickName,
        "avatar_url": user_info.avatarUrl,
        "city": user_info.city,
        "gender": user_info.gender,
        "language": user_info.language,
        "province": user_info.province,
        "country": user_info.country,
      },
      method: 'POST', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
      header: { "Content-Type": "application/x-www-form-urlencoded" }, // 设置请求的 header
      success: function (res) {
        console.log('saveUserInfo……', res)
      }
    })
  }
})