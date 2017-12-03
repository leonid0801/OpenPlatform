//index.js
//获取应用实例
const app = getApp()

Page({
  data: {
    motto: 'Hello World',
    userInfo: {},
    hasUserInfo: false,
    //canIUse: wx.canIUse('button.open-type.getUserInfo')
    canIUse: false,
    scrollTop: 0,
    srollHeight: 300, //随便给的一个初始高度
    hideHeader: true,
    hideBottom: true,
    contentlist: [], // 列表显示的数据源
    allPages: '',    // 总页数
    currentPage: 0,  // 当前页数  默认是0
    page_size: 10,
    loadMoreData: '加载更多……' ,
    reqs:[],
    topLoad: true,
    bottomLoad: true,

  },
  
  //事件处理函数
  bindViewTap: function() {
    wx.navigateTo({
      url: '../logs/logs'
    })
  },

  toast: function (e) {
    var f_id = e.currentTarget.id;
    wx.navigateTo({
      url: '../detail/detail?f_id=' + f_id
    })
  },


  onLoad: function () {
    if (app.globalData.userInfo) {
      this.setData({
        userInfo: app.globalData.userInfo,
        hasUserInfo: true
      })
    } 
    /*
    else if (this.data.canIUse){
      // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
      // 所以此处加入 callback 以防止这种情况
      app.userInfoReadyCallback = res => {
        this.setData({
          userInfo: res.userInfo,
          hasUserInfo: true
        })
      }
    } else {
      // 在没有 open-type=getUserInfo 版本的兼容处理
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
      
    }
    */

    wx.setStorageSync('max_item_index', 0)
    // bug fix
    this.setData({
      currentPage: 0
    })

    this.getTopData();
  },

onShow: function () {  //在onShow中根据屏幕窗口宽度动态设置scroll-view的高度
    //console.log('onShowed')
    var that = this;
    wx.getSystemInfo({
      success: function (res) {
        var height = res.windowHeight-50;   //footerpannelheight为底部组件的高度
        that.setData({
          srollHeight: height
        });
      }
    })
},

topLoad: function (e) {
  var self = this;
  if (self.data.topLoad){
      console.log('Top');
      self.setData({
        currentPage: 0,
        topLoad: false,
        hideHeader: false,   
      })
      
      setTimeout(function () {
        self.getTopData();
        self.setData({
          topLoad: true
        })
      }, 800);
  }
},

bottomLoad: function (e) {
  var self = this;
  if (self.data.bottomLoad) {
    console.log('Bottom');
    var tempCurrentPage = self.data.currentPage;
    tempCurrentPage = tempCurrentPage + 1;
    self.setData({
      currentPage: tempCurrentPage,
      bottomLoad: false,
    })

    setTimeout(function () {
      self.getTopData();
      self.setData({
        bottomLoad: true
      })
    }, 800);
  }
},

// 获取数据  pageIndex：页码参数
getTopData: function () {
  var self = this;
  //var pageIndex = self.data.currentPage;
  var pageIndex = 0;
  var cur_max_item_index = wx.getStorageSync("max_item_index");
  wx.request({
    url: 'https://bjwob.top/index.php/api/wxapp/get_top_more',
    data: {
      page: pageIndex,
      page_size: self.data.page_size,
      max_item_index: cur_max_item_index,
    },
    success: function (res) {
      console.log('get top data res:', res);
      var dataModel = res.data;
      if (res.data.code == 0) {
        if (pageIndex == 0) { // 下拉刷新
          let list = []
          for (let i = 0; i < res.data.data.length; i++) {
            list.push(res.data.data[i])
          }
          /*
          for (let i = 0; i < self.data.reqs.length; i++) {
            list.push(self.data.reqs[i])
          }*/
          console.log('list:', list);
          self.setData({
            reqs: list,
            hideHeader: true,
          })
          wx.setStorageSync("max_item_index", res.data.max_item_index);
        } 
      }
    },
    fail: function () {
    },
    complete: function(){
      self.setData({
        hideHeader: true,
      })


    }
  })
},


// 获取数据  pageIndex：页码参数
getMoreData: function () {
  var self = this;
  var pageIndex = self.data.currentPage;
  console.log('pageIndex:', pageIndex);
  pageIndex++;
  wx.request({
    url: 'https://bjwob.top/index.php/api/wxapp/get_bottom_data',
    data: {
      page: pageIndex,
      page_size: self.data.page_size,
    },
    success: function (res) {
      console.log('res:', res);
      var dataModel = res.data;
      if (res.data.code == 0) {
        
          let list = []
          for (let i = 0; i < self.data.reqs.length; i++) {
            list.push(self.data.reqs[i])
          }
          for (let i = 0; i < res.data.data.length; i++) {
            list.push(res.data.data[i])
          }
          console.log('list:', self.data.reqs);
          self.setData({
            reqs: list,
            hideHeader: true,
            currentPage: pageIndex,
          })

      } else {
        var curTitle='';
        if (res.data.code=1){
          curTitle='No more'
        }

        wx.showToast({
          title: curTitle,
          icon: 'loading',
          duration: 10000
        })
        setTimeout(function () {
          wx.hideToast()
        }, 1500)

      }
    },
  })
},



  //事件处理函数
  clickButton: function () {
    wx.navigateTo({
      url: '../publish/publish'
    })
  },

  onPullDownRefresh: function () {
    var that = this;
    console.log('top')
    setTimeout(function () {
      that.getTopData();
      wx.stopPullDownRefresh() //停止下拉刷新
    }, 1200);  
  },

  onReachBottom: function () {
    var that = this;
    console.log('bottom')
    wx.showNavigationBarLoading() //在标题栏中显示加载
    
    setTimeout(function () {
      that.getMoreData();
      wx.hideNavigationBarLoading() //完成停止加载
    }, 1200);
    
  },

  saveUserInfo: function () {
    
    var that = this;
    var client_session = wx.getStorageSync("client_session");
    var user_info = wx.getStorageSync("user_info");
    console.log('!!!!!!!!!!!client_session', client_session)
    

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
