var app = getApp();

var initData = 'this is first line\nthis is second line';

Page({
  data: {
    text: initData,
    array: ['旅游', '穿越', '单身交友', '婚恋服务', 'K歌', '其它'],
    index: 0,
    inputContent: "",
    date: '2016-09-26',

    latitude: '',
    longitude: '',

    /*type*/
    main_types: [],
    objectArray: [
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
    object: [],
    main_type_id: 0,
    sub_type_id: 0  
  },

  onLoad: function () {
    var objectArray = this.data.objectArray
    var main_types = []
    for (var i = 0; i < objectArray.length; i++) {
      main_types.push(objectArray[i].main_type, )
    }
    this.setData({ main_types: main_types, object: objectArray[this.data.main_type_id].array })

    var that = this;
    wx.getLocation({
      type: 'wgs84',
      success: function (res) {
        that.setData({
          latitude: res.latitude,
          longitude: res.longitude
        })
      }
    })
  },

  pickChange: function (e) {
    this.setData({
      index: e.detail.value
    });
    
  },
  textChange: function (e) {
    this.setData({
      inputContent: e.detail.value
    }) 
  },
  setPrimary: function () {
    console.log('setPrimary....')
    console.log('inputContent....', this.data.inputContent)
  },
  bindFormSubmit: function (e) {
    var client_session = wx.getStorageSync('client_session')
    console.log('main_type_id....', this.data.main_type_id)
    console.log('sub_type_id....', this.data.sub_type_id)
    console.log('textarea....', e.detail.value.textarea)
    console.log('date....', this.data.date)
    console.log('latitude....', this.data.latitude)
    console.log('longitude....', this.data.longitude)
    console.log('client_session....', client_session)

    wx.request({
      url: 'https://wtmb.online/index.php/api/wxapp/add_item',
      data: {
        "main_type_id": this.data.main_type_id,
        "sub_type_id": this.data.sub_type_id,
        "textarea": e.detail.value.textarea,
        "date": this.data.date,
        "latitude": this.data.latitude,
        "longitude": this.data.longitude,
        "client_session": client_session
      },
      method: 'POST', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
      header: {"Content-Type":"application/x-www-form-urlencoded"}, // 设置请求的 header
      success: function (res) {


        wx.showToast({
          title: '成功',
          icon: 'success',
          duration: 10000
        })

        setTimeout(function () {
          wx.hideToast()
          wx.switchTab({
            url: '../index/index',
            success: function (e) {
              var page = getCurrentPages().pop();
              if (page == undefined || page == null) {
                console.log("pageeeeeeeee");
                return;
              }
              page.onLoad();
            }
          });
        }, 1500)

      },
      fail: function (res) {
        //console.log(JSON.stringify(res));
      },
      complete: function (e) {
        // complete       
      }
    })
  },

  /**
  * 监听日期picker选择器
  */
  bindDateChange: function (e) {
    this.setData({
      date: e.detail.value
    })
  },


  bindPickerChange0: function (e) {
    this.setData({ main_type_id: e.detail.value, sub_type_id: 0 })
    var objectArray = this.data.objectArray
    this.setData({ object: objectArray[this.data.main_type_id].array })
  },
  bindPickerChange1: function (e) {
    this.setData({
      sub_type_id: e.detail.value
    })
  },  


/**
 * 选择相册或者相机 配合上传图片接口用
 */
  listenerButtonChooseImage1: function () {
    var that = this;
    wx.chooseImage({
      success: function (res) {
        var tempFilePaths = res.tempFilePaths
        wx.uploadFile({
          url: 'http://example.weixin.qq.com/upload', //仅为示例，非真实的接口地址  
          filePath: tempFilePaths[0],
          name: 'file',
          formData: {
            'user': 'test'
          },
          success: function (res) {
            var data = res.data
            //do something  
          }
        })
      }
      })  
  },
  listenerButtonChooseImage: function () {
    var that = this;
    wx.chooseImage({
      count: 1,
      //original原图，compressed压缩图
      sizeType: ['compressed'],
      //album来源相册 camera相机 
      sourceType: ['album', 'camera'],
      //成功时会回调
      success: function (res) {
        //重绘视图
        that.setData({
          source: res.tempFilePaths
        })
      }
    })
  },

  

})