<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-lx-calendar"></i> 个人</el-breadcrumb-item>
                <el-breadcrumb-item>修改密码</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="container">
            <el-row>
                <el-col :span="12" :offset="6">
                    <el-card shadow="hover" class="mgb20">
                        <div>
                            <el-radio-group v-model="labelPosition" size="small">
                                <el-radio-button label="left">左对齐</el-radio-button>
                                <el-radio-button label="right">右对齐</el-radio-button>
                                <el-radio-button label="top">顶部对齐</el-radio-button>
                            </el-radio-group>
                            <div style="margin: 20px;"></div>
                            <el-form :label-position="labelPosition" label-width="80px" :model="formLabelAlign">
                                <el-form-item label="email">
                                    <el-input v-model="formLabelAlign.email" :placeholder="email" :disabled="true"></el-input>
                                </el-form-item>
                                <el-form-item label="password">
                                    <el-input type="password" v-model="formLabelAlign.again_pw"></el-input>
                                </el-form-item>
                                <el-form-item label="password again">
                                    <el-input type="password" v-model="formLabelAlign.asset_pw"></el-input>
                                </el-form-item>
                                <el-form-item>
                                    <el-button type="primary" @click="change()">确认修改</el-button>
                                </el-form-item>
                            </el-form>
                        </div>
                    </el-card>
                </el-col>
            </el-row>
        </div>
    </div>
</template>

<script>
import { ChangePassword } from '@/api/index'

export default {
    data() {
      return {
        labelPosition: 'top',
        formLabelAlign: {
          email:sessionStorage.getItem('email'),
          again_pw: '',
          asset_pw: ''
        },
        email:sessionStorage.getItem('email'),
      };
    },
    methods:{
        change(){
            this.$confirm('此操作将修改密码, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                ChangePassword(this.formLabelAlign).then(response => {
                    console.log(response);
                    if(response.code === 0){
                        this.$message({
                            type: 'success',
                            message: '修改密码成功!'
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
    }
}
</script>

<style scoped>
  .el-row {
    margin-bottom: 20px;
}

</style>