<template>
    <div class="login-wrap">
        <div class="ms-login">
            <div class="ms-title">老人监护系统</div>
            <el-form label-width="0px" class="ms-content">
                <el-form-item prop="username">
                    <el-input v-model="loginForm.email" placeholder="email">
                        <el-button slot="prepend" icon="el-icon-lx-people"></el-button>
                    </el-input>
                </el-form-item>
                <el-form-item prop="password">
                    <el-input type="password" placeholder="password" v-model="loginForm.password" @keyup.enter.native="submitForm()">
                        <el-button slot="prepend" icon="el-icon-lx-lock"></el-button>
                    </el-input>
                </el-form-item>
                <div class="login-btn">
                    <el-button type="primary" @click="submitForm()">登录</el-button>
                </div>
                 <div class="login-btn">
                    <el-button type="primary" @click="jump()">立即注册</el-button>
                </div>
                <p class="login-tips">Tips : email为本人的邮箱账号</p>
            </el-form>
        </div>
    </div>
</template>

<script>
import { login } from '@/api/index'
export default {
    data: function() {
        return {
            loginForm:{
                email: '',
                password: '',
            }, 
        };
    },
    mounted() {
    },
    methods: {
        submitForm() {
            if(this.loginForm.email ==''||this.loginForm.password ==''){
                alert('请输入账号或密码')
            }else{
                login(this.loginForm).then(response => {
                    console.log(response);
                    if(response.code === 0){
                        sessionStorage.setItem("email",this.loginForm.email);
                        this.$router.push('/dashboard');
                    }else{
                        alert(response.message)
                    }
                })
            }  
        },
        jump(){
            this.$router.push('/register');
        }
    },
};
</script>

<style scoped>
.login-wrap {
    position: relative;
    width: 100%;
    height: 100%;
    background-image: url(../../assets/img/login-bg.jpg);
    background-size: 100%;
}
.ms-title {
    width: 100%;
    line-height: 50px;
    text-align: center;
    font-size: 20px;
    color: #fff;
    border-bottom: 1px solid #ddd;
}
.ms-login {
    position: absolute;
    left: 50%;
    top: 50%;
    width: 350px;
    margin: -190px 0 0 -175px;
    border-radius: 5px;
    background: rgba(255, 255, 255, 0.3);
    overflow: hidden;
}
.ms-content {
    padding: 30px 30px;
}
.login-btn {
    text-align: center;
}
.login-btn button {
    width: 100%;
    height: 36px;
    margin-bottom: 10px;
}
.login-tips {
    font-size: 12px;
    line-height: 30px;
    color: #fff;
}
</style>