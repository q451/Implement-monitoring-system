<template>
    <section class="main">
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-video-camera-solid"></i> 实时监护</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="container">
            <el-row :gutter="40">
                <el-col :span="12">
                    <span>请选择检测功能：</span>
                    <el-select v-model="value" clearable placeholder="请选择" 
                    @click="openFullScreen1()"
                    v-loading.fullscreen.lock="fullscreenLoading">
                        <el-option
                            v-for="item in options"
                            :key="item.value"
                            :label="item.label"
                            :value="item.value"
                        >
                        </el-option>
                    </el-select>
                </el-col>
            </el-row>
            <el-row type="flex" justify="center">
                <el-col :offset="6">
                    <img :src="value" alt="">
                </el-col>
            </el-row>
        </div>
    </section>
</template>

<script>
import { videoPlayer } from 'vue-video-player'
import 'video.js/dist/video-js.css'
import 'videojs-flash'
export default {
    components: {
        videoPlayer
    },
    data() {
        return {
            fullscreenLoading: false,
            playerOptions: {
                playbackRates: [0.5, 1.0, 1.5, 2.0], // 可选的播放速度
                autoplay: true, // 如果为true,浏览器准备好时开始回放。
                muted: false, // 默认情况下将会消除任何音频。
                loop: false, // 是否视频一结束就重新开始。
                preload: 'auto', // 建议浏览器在<video>加载元素后是否应该开始下载视频数据。auto浏览器选择最佳行为,立即开始加载视频（如果浏览器支持）
                language: 'zh-CN',
                live:true,
                aspectRatio: '16:9', // 将播放器置于流畅模式，并在计算播放器的动态大小时使用该值。值应该代表一个比例 - 用冒号分隔的两个数字（例如"16:9"或"4:3"）
                fluid: true, // 当true时，Video.js player将拥有流体大小。换句话说，它将按比例缩放以适应其容器。
                
                poster: require('../../assets/img/img.jpg'), // 封面地址
                // poster: 'http://127.0.0.1:8000/video/',
                // techOrder: ['flash'],
                sources: [{
                    type: 'rtmp/flv',
                    // type: 'video/mp4',
                    src: ' rtmp://localhost:1935/live/home1'
                }],
                notSupportedMessage: '此视频暂无法播放，请稍后再试', // 允许覆盖Video.js无法播放媒体源时显示的默认信息。
                controlBar: {
                    timeDivider: false, // 当前时间和持续时间的分隔符
                    durationDisplay: false, // 显示持续时间
                    remainingTimeDisplay: false, // 是否显示剩余时间功能
                    fullscreenToggle: true // 是否显示全屏按钮
                }
            },
            options: [
                {
                    value: 'http://127.0.0.1:8000/api/chack/',
                    label: '人脸检测',
                    value1:''

                }, 
                {
                    value: 'http://127.0.0.1:8000/api/webchack/',
                    label: '远程人脸检测',
                }, 
                {
                    value: 'http://192.168.31.154:5001/',
                    label: '入侵检测'
                }, 
                {
                    value: '741',
                    label: '人脸识别'
                }, 
                {
                    value: 'http://127.0.0.1:8000/api/quyu/',
                    label: '区域监测'
                }, 
                {
                    value: 'http://127.0.0.1:8000/api/shouji/',
                    label: '表情分析'
                }
            ],
            value: ''
        }
    },
    computed: {
        player() {
            return this.$refs.videoPlayer.player
        }
    },
    mounted() {
        // console.log('this is current player instance object', this.player)
        setTimeout(() => {
            console.log('dynamic change options', this.player)
            this.player.muted(false)
        }, 5000)
    },
    methods: {
        openFullScreen1() {
            this.fullscreenLoading = true;
            setTimeout(() => {
                this.fullscreenLoading = false;
            }, 2000);
        },
        // listen event
        onPlayerPlay(player) {
        // console.log('player play!', player)
        },
        onPlayerPause(player) {
        // console.log('player pause!', player)
        },
        onPlayerEnded(player) {
        // console.log('player ended!', player)
        },
        onPlayerLoadeddata(player) {
        // console.log('player Loadeddata!', player)
        },
        onPlayerWaiting(player) {
        // console.log('player Waiting!', player)
        },
        onPlayerPlaying(player) {
        // console.log('player Playing!', player)
        },
        onPlayerTimeupdate(player) {
        // console.log('player Timeupdate!', player.currentTime())
        },
        onPlayerCanplay(player) {
        // console.log('player Canplay!', player)
        },
        onPlayerCanplaythrough(player) {
        // console.log('player Canplaythrough!', player)
        },
        // or listen state event
        playerStateChanged(playerCurrentState) {
        // console.log('player current update state', playerCurrentState)
        },
        // player is ready
        playerReadied(player) {
        // seek to 10s
        console.log('example player 1 readied', player)
        // player.currentTime(10)
        // console.log('example 01: the player is readied', player)
        }
  }
}
</script>


<style scoped>
.list{
    padding: 30px 0;
}
.list p{
    margin-bottom: 20px;
}
a{
    color: #409eff;
}
.el-row {
    margin-bottom: 20px;
}

</style>
