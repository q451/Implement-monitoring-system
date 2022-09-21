<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>
                    <i class="el-icon-pie-chart"></i> 人员录入
                </el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="container">
            <el-row>
                <el-col :span="24">
                    <div class="tool-box">
                        <el-button type="primary" icon="el-icon-circle-plus-outline" size="small" @click="dialogFormVisible = true">新增老人</el-button>
                        <el-button type="primary" icon="el-icon-circle-plus-outline" size="small" @click="dialogVisible = true">新增员工或义工</el-button>
                    </div>
                </el-col>
            </el-row>
            
            <el-dialog title="老人信息录入" :visible.sync="dialogFormVisible">
                <el-form :model="form">
                    <el-form-item label="姓名" :label-width="formLabelWidth">
                        <el-input v-model="form.name" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="性别" :label-width="formLabelWidth">
                        <el-select v-model="form.gender" placeholder="请选择性别">
                            <el-option label="男" value="男"></el-option>
                            <el-option label="女" value="女"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="电话" :label-width="formLabelWidth">
                        <el-input v-model="form.phone" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="身份证" :label-width="formLabelWidth">
                        <el-input v-model="form.card" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="第一监护人姓名" :label-width="formLabelWidth">
                        <el-input v-model="form.firstguardian_name" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="第一监护人邮箱" :label-width="formLabelWidth">
                        <el-input v-model="form.email" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="创建时间" :label-width="formLabelWidth">
                        <el-col :span="11">
                            <el-date-picker type="date" placeholder="选择日期" v-model="time.date1" style="width: 100%;"></el-date-picker>
                            </el-col>
                            <el-col :span="11">
                            <el-time-picker placeholder="选择时间" v-model="time.date2" style="width: 100%;"></el-time-picker>
                         </el-col>
                    </el-form-item>
                </el-form>
                <div slot="footer" class="dialog-footer">
                    <el-button @click="dialogFormVisible = false">取 消</el-button>
                    <el-button type="primary" @click="manageOldPeople()">确 定</el-button>
                </div>
            </el-dialog>

            <el-dialog title="工作人员或义工信息录入" :visible.sync="dialogVisible">
                <el-form :model="form">
                    <el-form-item label="录入人员类型" :label-width="formLabelWidth">
                        <el-select v-model="form1.type" placeholder="请选择类型">
                            <el-option label="义工" value=1></el-option>
                            <el-option label="工作人员" value=2></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="姓名" :label-width="formLabelWidth">
                        <el-input v-model="form1.name" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="性别" :label-width="formLabelWidth">
                        <el-select v-model="form1.gender" placeholder="请选择性别">
                            <el-option label="男" value="男"></el-option>
                            <el-option label="女" value="女"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="电话" :label-width="formLabelWidth">
                        <el-input v-model="form1.phone" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="身份证" :label-width="formLabelWidth">
                        <el-input v-model="form1.card" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item label="创建时间" :label-width="formLabelWidth">
                        <el-col :span="11">
                            <el-date-picker type="date" placeholder="选择日期" v-model="time.date1" style="width: 100%;"></el-date-picker>
                            </el-col>
                            <el-col :span="11">
                            <el-time-picker placeholder="选择时间" v-model="time.date2" style="width: 100%;"></el-time-picker>
                         </el-col>
                    </el-form-item>
                </el-form>
                <div slot="footer" class="dialog-footer">
                    <el-button @click="dialogVisible = false">取 消</el-button>
                    <el-button type="primary" @click="managePeople()">确 定</el-button>
                </div>
            </el-dialog>

             <el-tabs v-model="activeName" @tab-click="handleClick">
                <el-tab-pane label="老人管理" name="first">
                    <el-table
                        :data="oldPeopleData.filter(data => !search || data.name.toLowerCase().includes(search.toLowerCase()))"
                        style="width: 100%" height="400" :row-class-name="tableRowClassName">
                        <el-table-column label="姓名" prop="username"> </el-table-column>
                        <el-table-column label="性别" prop="gender"> </el-table-column>
                        <el-table-column label="电话" prop="phone"> </el-table-column>
                        <el-table-column label="身份证号" prop="id_card"> </el-table-column>
                        <el-table-column label="监护人姓名" prop="firstguardian_name"> </el-table-column>
                        <el-table-column label="监护人邮箱" prop="firstguardian_email"> </el-table-column>
                        <el-table-column label="创建时间" prop="create_time"> </el-table-column>
                        <el-table-column align="right">
                            <template slot="header" slot-scope="scope">
                                <el-input
                                v-model="search"
                                size="mini"
                                placeholder="输入关键字搜索"/>
                            </template>
                        <template slot-scope="scope">
                            <el-button size="mini" @click="oldPeopleDelete(scope.row.id)"><i class="el-icon-delete"></i></el-button>
                        </template>
                        </el-table-column>
                    </el-table>
                </el-tab-pane>
                <el-tab-pane label="工作人员管理" name="second">
                    <el-table
                        :data="employData.filter(data => !search || data.name.toLowerCase().includes(search.toLowerCase()))"
                        style="width: 100%" height="400" :row-class-name="tableRowClassName">
                        <el-table-column label="姓名" prop="username"> </el-table-column>
                        <el-table-column label="性别" prop="gender"> </el-table-column>
                        <el-table-column label="电话" prop="phone"> </el-table-column>
                        <el-table-column label="身份证号" prop="id_card"> </el-table-column>
                        <el-table-column label="创建时间" prop="create_time"> </el-table-column>
                        <el-table-column align="right">
                            <template slot="header" slot-scope="scope">
                                <el-input
                                v-model="search"
                                size="mini"
                                placeholder="输入关键字搜索"/>
                            </template>
                        <template slot-scope="scope">
                            <el-button size="mini" @click="deployDelete(scope.row.id)"><i class="el-icon-delete"></i></el-button>
                        </template>
                        </el-table-column>
                    </el-table>
                </el-tab-pane>
                <el-tab-pane label="义工管理" name="third">
                    <el-table
                        :data="volunteerData.filter(data => !search || data.name.toLowerCase().includes(search.toLowerCase()))"
                        style="width: 100%" height="400" :row-class-name="tableRowClassName">
                       <el-table-column label="姓名" prop="username"> </el-table-column>
                        <el-table-column label="性别" prop="gender"> </el-table-column>
                        <el-table-column label="电话" prop="phone"> </el-table-column>
                        <el-table-column label="身份证号" prop="id_card"> </el-table-column>
                        <el-table-column label="创建时间" prop="create_time"> </el-table-column>
                        <el-table-column align="right">
                            <template slot="header" slot-scope="scope">
                                <el-input
                                v-model="search"
                                size="mini"
                                placeholder="输入关键字搜索"/>
                            </template>
                        <template slot-scope="scope">
                            <el-button size="mini" @click="volunteerDelete(scope.row.id)"><i class="el-icon-delete"></i></el-button>
                        </template>
                        </el-table-column>
                    </el-table>
                </el-tab-pane>
            </el-tabs>
             
        </div>
    </div>
</template>

<script>
import Schart from 'vue-schart';
import { getOldPeopleInfo, getVolunteerInfo, getEmployInfo, delOldPeople,delVolunteer,delEmploy, oldPeopleManage, peopleManage } from '@/api/index'
export default {
    name: 'basecharts',
    components: {
        Schart
    },
    data() {
        return {
           activeName: 'first',
           dialogFormVisible: false,
           dialogVisible:false,
           time:{
                data1:'',
                data2:'',
           },
           form: {
                name: '',
                gender: '',
                phone:'',
                card:'',
                firstguardian_name:'',
                email: '',
            },
            form1:{
                type:'',
                name: '',
                gender: '',
                phone:'',
                card:'',
            },
            formLabelWidth: '120px',
            search: '',
            oldPeopleData: [],  
            volunteerData: [],  
            employData: [],  
        };
        
    },
    mounted() {
        this.getOldPeopleData();
        this.getEmployData();
        this.getVolunteerData();
    },
    methods: {
        tableRowClassName({row, rowIndex}) {
            if (rowIndex%2 === 0) {
                return 'warning-row';
            }
        },
        handleClick(tab, event) {
            console.log(tab, event);
        },
        manageOldPeople(){
            this.$confirm('请核对人员信息, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
                }).then(() => {
                    oldPeopleManage(this.form).then(response => {
                        if(response.code === 0){
                            this.dialogFormVisible = false
                            this.$message({
                                type: 'success',
                                message: '录入成功!'
                            });
                        }
                        else{
                            this.$message({
                                type: 'info',
                                message: response.message
                            });
                        }
                    
                    })
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '已取消录入'
                });          
            });
        },
        managePeople(){
            this.$confirm('请核对人员信息, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
                }).then(() => {
                    peopleManage(this.form1).then(response => {
                        if(response.code === 0){
                            this.dialogVisible = false
                            this.$message({
                                type: 'success',
                                message: '录入成功!'
                            });
                        }
                        else{
                            this.$message({
                                type: 'info',
                                message: response.message
                            });
                        }
                    
                    })
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '已取消录入'
                });          
            });
        },
        // 获取数据
        getOldPeopleData(){
            getOldPeopleInfo().then(response => {
                console.log(response);
                this.oldPeopleData = response.data; 
            }).catch(() => {
                this.errors.push(e)         
            });
        },
        getVolunteerData(){
            getVolunteerInfo().then(response => {
                console.log(response);
                this.volunteerData = response.data; 
            }).catch(() => {
                this.errors.push(e)         
            });
        },
        getEmployData(){
            getEmployInfo().then(response => {
                console.log(response);
                this.employData = response.data; 
            }).catch(() => {
                this.errors.push(e)         
            });
        },
        oldPeopleDelete(index){
            console.log(index);
            this.$confirm('此操作将永久删除这条信息, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
                }).then(() => {
                    delOldPeople({id:index}).then(response => {
                        console.log(response.data);
                        if(response.code === 0){
                            this.$message({
                                type: 'success',
                                message: '删除成功!'
                            });
                        }
                        else{
                            this.$message({
                                type: 'info',
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
        volunteerDelete(index){
            console.log(index);
            this.$confirm('此操作将永久删除这条信息, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
                }).then(() => {
                    delVolunteer({id:index}).then(response => {
                        console.log(response.data);
                        if(response.code === 0){
                            this.$message({
                                type: 'success',
                                message: '删除成功!'
                            });
                        }
                        else{
                            this.$message({
                                type: 'info',
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
        deployDelete(index){
            console.log(index);
            this.$confirm('此操作将永久删除这条信息, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
                }).then(() => {
                    delEmploy({id:index}).then(response => {
                        console.log(response.data);
                        if(response.code === 0){
                            this.$message({
                                type: 'success',
                                message: '删除成功!'
                            });
                        }
                        else{
                            this.$message({
                                type: 'info',
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
    },
};
</script>

<style scoped>
  .el-table .warning-row {
    background: oldlace;
  }

  .el-table .success-row {
    background: #f0f9eb;
  }
</style>