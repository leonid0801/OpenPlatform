


Page({
  data: {
    f_id: 0,
    textarea: "",
    item:"",
    hideDel: false
  },
  onLoad: function (res) {
    var that = this;
    var item_id = res.f_id;
    var client_session = wx.getStorageSync('client_session');
    wx.request({
      url: 'https://bjwob.top/index.php/api/wxapp/get_item_detail',
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
          var can_modify = res.data.data.can_modify;
          that.setData({
            f_id: item_id,
            textarea: f_textarea,
            item:item_data,
            hideDel: !can_modify
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

  delItem: function () {
    var that = this;
    var client_session = wx.getStorageSync('client_session');
    wx.request({
      url: 'https://bjwob.top/index.php/api/wxapp/del_item',
      data: {
        "f_id": that.data.f_id,
        "client_session": client_session
      },
      method: 'GET',
      header: { 'content-type': 'application/json' },
      success: function (res) {
        console.log(res)
        if (res.data.code == 0) {

          wx.showToast({
            title: '已删除',
            icon: 'success',
            duration: 10000
          })

          setTimeout(function () {
            wx.switchTab({
              url: '../index/index',
              success: function (e) {
                var page = getCurrentPages().pop();
                if (page == undefined || page == null) {
                  return;
                }
                page.onLoad();
              }
            });
          }, 1500)

        }
      }


    })

  },

  

})