
Page({
  
  onLoad: function () {
      var that = this;
      wx.getStorage({
        //获取数据的key
        key: 'user_info',
        success: function (res) {
          console.log(res)
          that.setData({
            //
            userInfo: res.data
          })
        },
        /**
         * 失败会调用
         */
        fail: function (res) {
          console.log(res)
        }
      })
    },


      
})