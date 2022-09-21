<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>
                    <i class="el-icon-lx-calendar"></i> 个人
                </el-breadcrumb-item>
                <el-breadcrumb-item>修改信息</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="container">
            <el-row>
                <el-col :span="12" :offset="6">
                    <el-form :model="form" label-width="80px">
                        <el-form-item label="邮箱">
                            <el-input v-model="form.email" :placeholder="form.email" :disabled="true" autocomplete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="昵称" >
                            <el-input v-model="form.UserName" :placeholder="users.UserName" autocomplete="on"></el-input>
                        </el-form-item>
                        <el-form-item label="姓名">
                            <el-input v-model="form.REAL_NAME" :placeholder="users.REAL_NAME" autocomplete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="性别">
                            <el-select v-model="form.SEX" :placeholder="users.SEX" >
                                <el-option label="男" value="男"></el-option>
                                <el-option label="女" value="女"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="电话">
                            <el-input v-model="form.PHONE" :placeholder="users.PHONE" autocomplete="off"></el-input>
                        </el-form-item>
                        <el-form-item label="个人描述">
                            <el-input type="textarea" v-model="form.DESCRIPTION" :placeholder="users.DESCRIPTION" autocomplete="off"></el-input>
                        </el-form-item>

                        <el-form-item>
                            <el-button type="primary" @click="changeUserContext()">确认修改</el-button>
                        </el-form-item>
                    </el-form>
                    
                </el-col>
            </el-row>
        </div>
    </div>
</template>

<script>
import { changeUserInfo, userInfo } from '@/api/index'
export default {
    data() {
      return {
        
        form: {
            UserName: '',
            REAL_NAME: '',
            SEX:'',
            PHONE:'',
            DESCRIPTION:'',
            email: sessionStorage.getItem('email'),
        },
        users:[],
        
      };
    },
    mounted() {
        this.getUser();
    },
    methods:{
        changeUserContext(){
            this.$confirm('此操作将修改个人信息, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                changeUserInfo(this.form).then(response => {
                    console.log(response);
                    if(response.code === 0){
                        this.$message({
                            type: 'success',
                            message: '修改信息成功!'
                        });
                    }
                    else{
                        this.$message({
                            type: 'success',
                            message: response.message
                        });
                    }
                })
            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: '已取消删除'
                });          
            });
        },
        getUser(){
            userInfo({email:this.form.email}).then(response => {
                console.log(response.data);
                this.users = response.data;
                sessionStorage.setItem("name",this.users.UserName);
            });
        }
    }
}
</script>