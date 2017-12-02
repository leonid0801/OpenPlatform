

Page({

  data: { 
    reqs: [],
  },

  onLoad: function () {
    var that = this;
    that.getTopData();
  },  

  // 获取数据  pageIndex：页码参数
  getTopData: function () {
    var self = this;
    var pageIndex = 0;
    var client_session = wx.getStorageSync("client_session");
    wx.request({
      url: 'https://bjwob.top/index.php/api/wxapp/get_user_items',
      data: {
        "page": pageIndex,
        "page_size": 100,
        "client_session": client_session,
      },
      success: function (res) {
        console.log('get top data res:', res);
        if (res.data.code == 0) {
          if (pageIndex == 0) { // 下拉刷新
            let list = []
            for (let i = 0; i < res.data.data.length; i++) {
              list.push(res.data.data[i])
            }
            console.log('list:', list);
            self.setData({
              reqs: list,
            })
          }
        }
      },
    })
  },

  toast: function (e) {
    var f_id = e.currentTarget.id;
    wx.navigateTo({
      url: '../detail/detail?f_id=' + f_id
    })
  },

  backTo: function () {
    wx.navigateBack({
      url: '../user/user'
    })
  },



})