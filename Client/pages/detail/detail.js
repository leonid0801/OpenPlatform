


Page({
  data: {
    logs: [],
    textarea: "",
    item:""
  },
  onLoad: function (options) {
    var that = this;
    //console.log("options", options)
    var item_id = options.f_id;
    var client_session = wx.getStorageSync('client_session');
    wx.request({
      url: 'https://wtmb.online/index.php/api/wxapp/get_item_detail',
      data: {
        f_id: item_id,
        client_session: client_session
      },
      method: 'GET',
      header: { 'content-type': 'application/json' },
      success: function (res) {
        if (res.data.code == 0) {
          var item_data = res.data.data;
          var f_textarea = res.data.data.f_textarea;
          that.setData({
            textarea: f_textarea,
            item:item_data,
          })
        }
        console.log(res)
      }
    })
  },

  backTo: function () {
    wx.navigateBack({
      url: '../index/index'
    })
  },

})