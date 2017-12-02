
var appInstance = getApp();
var globalData = appInstance.globalData;

var initData = 'this is first line\nthis is second line';
var sys_date = new Date();
var cur_date = sys_date.getFullYear() + '-' + (sys_date.getMonth()+1) + '-' + sys_date.getDate();
Page({
  data: {
    text: initData,
    index: 0,
    inputContent: "",
    date: cur_date,
    fin_date: cur_date,

    latitude: '',
    longitude: '',

    /*type*/
    object: [],
    main_type_id: 0,
    sub_type_id: 0  
  },

  onLoad: function () {
    var objectArray = globalData.typeObjectArray
    var main_types = []
    for (var i = 0; i < objectArray.length; i++) {
      main_types.push(objectArray[i].main_type, )
    }
    this.setData({ 
      main_types: main_types, 
      object: objectArray[this.data.main_type_id].array 
    })

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

  bindPickerChange0: function (e) {
    this.setData({ main_type_id: e.detail.value, sub_type_id: 0 })
    var objectArray = globalData.typeObjectArray
    this.setData({ object: objectArray[this.data.main_type_id].array })
  },
  bindPickerChange1: function (e) {
    this.setData({
      sub_type_id: e.detail.value
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
      url: 'https://bjwob.top/index.php/api/wxapp/add_item',
      data: {
        "main_type_id": this.data.main_type_id,
        "sub_type_id": this.data.sub_type_id,
        "textarea": e.detail.value.textarea,
        "date": this.data.date,
        "fin_date": this.data.fin_date,
        "latitude": this.data.latitude,
        "longitude": this.data.longitude,
        "client_session": client_session
      },
      method: 'POST', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
      header: {"Content-Type":"application/x-www-form-urlencoded"}, // 设置请求的 header
      success: function (res) {
        console.log('***********', res)

        if (res.data.code==0){
          wx.showToast({
            title: '成功',
            icon: 'success',
            duration: 10000
          })
        }else{
          wx.showToast({
            title: "Error: "+res.data.code,
            icon: 'loading',
            duration: 10000
          })
        }

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
      date: e.detail.value,
      fin_date: e.detail.value
    })
  },
  bindFinDateChange: function (e) {
    this.setData({
      fin_date: e.detail.value
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