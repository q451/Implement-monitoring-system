<template>
    <div class="login-wrap">
        <div class="ms-login">
            <div class="ms-title">老人监护系统</div>
            <el-form label-width="0px" class="ms-content">
                <el-form-item prop="username">
                    <el-input v-model="formR.email" placeholder="email">
                        <el-button slot="prepend" icon="el-icon-lx-people"></el-button>
                    </el-input>
                </el-form-item>
                <el-form-item prop="password">
                    <el-input type="password" placeholder="password" v-model="formR.password" @keyup.enter.native="submitForm()">
                        <el-button slot="prepend" icon="el-icon-lx-lock"></el-button>
                    </el-input>
                </el-form-item>
                <el-form-item prop="password_again">
                    <el-input type="password" placeholder="password again" v-model="formR.password_again">
                        <el-button slot="prepend" icon="el-icon-lx-lock"></el-button>
                    </el-input>
                </el-form-item>
                <div class="login-btn">
                    <el-button type="primary" @click="register()">立即注册</el-button>
                </div>
                <div class="login-btn">
                    <el-button type="primary" @click="registerjump()">登录</el-button>
                </div>
            </el-form>
        </div>
    </div>
</template>

<script>
import { registerForm } from '@/api/index'
export default {
    data: function() {
        return { 
            formR:{
                email: '',
                password: '',
                password_again: '',
            },
            
        };
    },
    mounted() {

    },
    methods: {
        registerjump(){
            this.$router.push('/');
        },
        register() {
            if(this.formR.email ==''||this.formR.password ==''){
                alert("请输入账号或密码")
            }else{
                if(this.formR.password_again != this.formR.password ){
                    alert("两次输入密码不一致")
                }else{
                    registerForm(this.formR).then(response => {
                        console.log(response);
                        if(response.code === 0){
                            this.$router.push('/');
                        }else{
                            alert(response.message)
                        }
                    })
                }
            }
        },
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