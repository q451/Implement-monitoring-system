<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>
                    <i class="el-icon-lx-cascades"></i> 人脸采集
                </el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="container">
            <el-row :gutter="40">
                <el-col :span="6" :offset="5">
                    <h1 v-if="photoStep===1" class="pageTitle">1.请点击拍摄后眨眨眼</h1>
                    <h1 v-if="photoStep===2" class="pageTitle">2.请点击拍摄后微笑</h1>
                    <h1 v-if="photoStep===3" class="pageTitle">3.请点击拍摄后张嘴</h1>
                    <h1 v-if="photoStep===4" class="pageTitle">4.请点击拍摄后抬头</h1>
                    <h1 v-if="photoStep===5" class="pageTitle">5.请点击拍摄后低头</h1>
                    <h1 v-if="photoStep===6" class="pageTitle">6.请点击拍摄后向左看</h1>
                    <h1 v-if="photoStep===7" class="pageTitle">7.请点击拍摄后向右看</h1>
                    <h1 v-if="photoStep===8" class="pageTitle">8.恭喜您！录入完成</h1>
                    <br>
                    <el-tag>录入人： {{ email }}</el-tag>
                    <br>
                    <br>
                    <el-tag>当前照片数 {{ imageIndex }}</el-tag>
                    <br>
                    <br>
                    <div class="button_group">
                        <el-button v-if="!cameraState" type="primary" title="打开摄像头" @click.native="openCamera()" >打开摄像头 </el-button>
                        <el-button v-else :disabled="photoing" type="primary" title="关闭摄像头" @click="closeCamera()" >关闭摄像头 </el-button>
                    </div>
                    <br>
                    <el-button v-if="photoStep===8" type="primary" @click="baoCun()">  录入成功,保存录入信息 </el-button>
                </el-col>
                <el-col :span="12">
                    <div>
                        <div class="video_box">
                            <video ref="video"/>
                            <img v-show="false" ref="img" class="img">
                        </div>
                        <canvas v-show="false" ref="canvas" class="canvas"/>
                    </div>
                    <br>
                    <div v-if="confirmState" class="button_group">
                        <el-button :disabled="photoing" type="primary" icon="el-icon-camera" title="拍照" @click.native="photo" />
                        <el-button type="primary" icon="el-icon-back" title="返回" @click.native="backButton" />
                    </div>
                    <div v-else class="button_group">
                        <el-button :disabled="photoing" type="primary" icon="el-icon-check" title="确定上传" @click.native="confirmClick" />
                        <el-button :disabled="photoing" type="primary" icon="el-icon-close" title="删除" @click.native="deletePhoto" />
                    </div>
                    <br>
                </el-col>
            </el-row>
            <el-row type="flex" class="row-bg" justify="center">
                <el-col :span="16">
                    <div>
                    <viewer :images="images" style="height: 800px;">
                        <img v-for="item in images" :src="item.src" :key="item.index" height="100">
                    </viewer>
                </div>
                </el-col>
            </el-row>
        </div>
            
    </div>
</template>

<script>
export default {
    name: 'basetable',
    data() {
        return {
            images: [],
            confirmState: true,
            imageIndex: 0,
            video: null,
            cameraState: true,
            dataURL: null,
            track: null,
            username: '',
            userType: '',
            id: null,
            photoing: false,
            photoStep: 1,
            uploadPhotoNum: 0,
            fileForm: {
                action: 'z0',
                token: '',
                file: null
            },
            fileList: [],
            email:sessionStorage.getItem('email'),
        };
    },
    mounted() {
    this.initPhoto()
  },
  created() {
    this.username = this.$route.query.username
    this.userType = this.$route.query.userType
    this.id = this.$route.query.id
  },
  destroyed() {
    const vm = this
    vm.stopPhoto()
  },
  methods: {
    initPhoto() {
      const vm = this
      // 浏览器兼容
      const mediaDevices = navigator.mediaDevices.getUserMedia({ audio: false, video: { width: 300, height: 400 }})
      mediaDevices.then(mediaStream => {
        var binaryData = []
        binaryData.push(mediaStream)
        const video = vm.$refs.video
        video.srcObject = mediaStream
        this.cameraState = true
        video.onloadedmetadata = (e) => {
          video.play()
        }
        vm.video = video
        vm.track = mediaStream.getTracks()[0]
      }).catch(err => {
        this.cameraState = false
        this.$message({
          message: '调用摄像头失败',
          type: 'error',
          duration: 5 * 1000
        })
        console.log('err.message' + err.message)
      })
    },
    photo() {
      this.photoing = true
      if (this.cameraState) {
        for (let index = 0; index < 10; index++) {
          setTimeout(() => {
            this.t_photo()
          }, index * 500)
        }
      } else {
        this.$notify.error({
          title: '错误',
          message: '请先打开摄像头!',
          duration: 3000
        })
      }
      setTimeout(() => {
        this.photoing = false
      }, 10 * 500)
      // this.photoing = false
      this.confirmState = false
    },
    t_photo() {
      const vm = this
      let dataURL = null
      const img = vm.$refs.img
      const canvas = vm.$refs.canvas
      const context = canvas.getContext('2d')
      const width = 180
      const height = 240
      canvas.width = width
      canvas.height = height
      img.height = height
      context.drawImage(vm.video, 0, 0, width, height)
      dataURL = canvas.toDataURL('image/png')
      img.src = dataURL
      // 记录dataURL，并且改变按钮的状态
      vm.dataURL = dataURL
      this.images.push({
        src: dataURL,
        index: this.imageIndex
      })
      this.imageIndex++
      this.uploadPhoto(img.src, this.imageIndex)
    },
    deletePhoto() {
      if (this.imageIndex === 0) {
        this.$notify.error({
          title: '错误',
          message: '照片已经被清空了!!',
          duration: 3000
        })
      } else {
        for (let index = 0; index < 10; index++) {
          this.images.pop()
        }
        this.imageIndex = this.imageIndex - 10
        this.photoStep--
      }
    },
    backButton() {
      this.$router.go(-1)
    },
    stopPhoto() {
      this.track.stop()
    },
    closeCamera() {
      const video = this.$refs.video
      video.pause()
      this.cameraState = false
      this.track.stop()
      this.photoing = true
    },
    openCamera() {
      this.cameraState = true
      this.initPhoto()
      this.photoing = false
    },
    confirmClick() {
      this.confirmState = true
      this.photoStep++
      this.uploadPhotoNum = this.imageIndex
    },
    uploadPhoto(fileCode, index) {
      var postForm = {
        type: 'takephoto',
        id: this.id,
        username: this.username,
        userType: this.userType,
        Index: index,
        base: fileCode
      }
    //   ws.send(JSON.stringify(postForm))
    },
    baoCun(){
        this.cameraState =  false,
        this.$confirm('人脸录入已完成, 是否保存?', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning'
            }).then(() => { 
                this.$message({
                    type: 'success',
                    message: '人脸数据保存成功!'
                });     
            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: '已取消删除'
                });          
        });
    },
  }
};
</script>
<style>
 .photograph_page {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;

}
.video_box {
  width: 300px;
  height:400px;
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  box-shadow: 1px 3px 10px #000;
  border: 10px solid #fff;
  background: #fff;
  border-radius: 2px;
  box-sizing: content-box;

}
</style>