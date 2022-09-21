<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>
                    <i class="el-icon-lx-home"></i> 系统首页
                </el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <el-row :gutter="20">
            <el-col :span="8">
                <el-card shadow="hover" class="mgb20">
                    <div class="user-info">
                        <img src="../../assets/img/img.jpg" class="user-avator" alt />
                        <div class="user-info-cont">
                            <div class="user-info-name">{{email}}</div>
                            <div>欢迎登录</div>
                        </div>
                    </div>
                </el-card>
                <el-row :gutter="20" class="mgb20">
                     <el-col :span="24">
                        <el-card shadow="hover" :body-style="{padding: '0px'}">
                            <div class="grid-content grid-con-1">
                                <i class="el-icon-monitor grid-con-icon"></i>
                                <div class="grid-cont-right">
                                    <div class="grid-num">老人监护系统</div>
                                </div>
                            </div>
                        </el-card>
                    </el-col>
                    <el-col :span="24">
                        <el-card shadow="hover" :body-style="{padding: '0px'}">
                            <div class="grid-content grid-con-2">
                                <i class="el-icon-lx-notice grid-con-icon"></i>
                                <div class="grid-cont-right">
                                    <div class="grid-num">{{todoList.length}}</div>
                                    <div>监护通知未处理警报</div>
                                </div>
                            </div>
                        </el-card>
                    </el-col>
                 </el-row>
            </el-col>
            <el-col :span="16">
                <el-card shadow="hover" style="height:403px;">
                    <div slot="header" class="clearfix">
                        <span>监护待处理通知</span>
                    </div>
                    <el-table 
                    :show-header="true" 
                    :data="todoList.slice((currentPage-1)*pageSize,currentPage*pageSize)" 
                    style="width:100%;" height="290"
                    >
                        <!-- -->
                        <!-- <el-table-column fixed label="老人id">
                            <template slot-scope="scope">
                                <div
                                    class="todo-item"
                                >{{scope.row.oldperson_id}}</div>
                            </template>
                        </el-table-column> -->
                        <el-table-column width="150" fixed label="检测类型">
                            <template slot-scope="scope">
                                <div
                                    class="todo-item"
                                >{{scope.row.type}}</div>
                            </template>
                        </el-table-column>

                        <el-table-column width="100" label="位置">
                            <template slot-scope="scope">
                                <div
                                    class="todo-item"
                                >{{scope.row.event_location}}</div>
                            </template>
                        </el-table-column>
                        <el-table-column width="500" label="内容">
                            <template slot-scope="scope">
                                <div
                                    class="todo-item"
                                >{{scope.row.event_desc}}</div>
                            </template>
                        </el-table-column>
                        <el-table-column width="200" label="时间">
                            <template slot-scope="scope">
                                <div
                                    class="todo-item"
                                >{{scope.row.event_date}}</div>
                            </template>
                        </el-table-column>
                        <el-table-column width="60" fixed="right" label="操作">
                            <template slot-scope="scope">
                                <i class="el-icon-chat-line-square"@click="NotifyRead(scope.row.id)"></i>
                                <i class="el-icon-delete" @click="NotifyDelete(scope.row.id)"></i>
                            </template>
                        </el-table-column>
                        
                    </el-table>
                    <el-row type="flex" justify="end">
                        <!-- <el-pagination layout="prev, pager, next" :total="1000"></el-pagination> -->
                        <el-pagination
                            @size-change="handleSizeChange"
                            @current-change="handleCurrentChange"
                            :current-page="currentPage"
                            :page-size.sync="pageSize"
                            layout="total, prev, pager, next, jumper"
                            :total=todoList.length>
                        </el-pagination>
                    </el-row>
                </el-card>
            </el-col>
        </el-row>
        <el-row :gutter="20">
            <el-col :span="12">
                <el-card shadow="hover">
                    <schart ref="bar" class="schart" canvasId="bar" :options="options"></schart>
                </el-card>
            </el-col>
            <el-col :span="12">
                <el-card shadow="hover">
                    <schart ref="line" class="schart" canvasId="line" :options="options2"></schart>
                </el-card>
            </el-col>
        </el-row>
    </div>
</template>

<script>
import Schart from 'vue-schart';

import { deleteNotify, getDataInfo, readNotify } from '@/api/index'

export default {
    name: 'dashboard',
    data() {
        return {
            name: localStorage.getItem('ms_username'),  
            pageSize:6,
            currentPage: 1,
            options: {
                type: 'bar',
                title: {
                    text: '最近一周监护监测'
                },
                xRorate: 25,
                labels: ['周一', '周二', '周三', '周四', '周五'],
                datasets: [
                    {
                        label: '入侵检测',
                        data: [1, 1, 2, 5, 6]
                    },
                    {
                        label: '摔倒',
                        data: [0, 0, 0, 3, 4]
                    },
                    {
                        label: '陌生人',
                        data: [0, 0, 1, 1, 2]
                    }
                ]
            },
            options2: {
                type: 'line',
                title: {
                    text: '最近几天监护监测'
                },
                labels: ['周一', '周二', '周三', '周四', '周五'],
                datasets: [
                    {
                        label: '入侵',
                        data: [1, 1, 2, 5, 6]
                    },
                    {
                        label: '摔倒',
                        data: [0, 0, 0, 3, 4]
                    },
                    {
                        label: '陌生人',
                        data: [0, 0, 1, 1, 2]
                    }
                ]
            },
            todoList:[],
            // length: todoList.length,
            email:sessionStorage.getItem('email'),
            userName:sessionStorage.getItem('name'),
        };
    },
    components: {
        Schart
    },
    mounted() {
        this.getdata();
    },
    methods:{
        // 获取数据
        getdata(){
            getDataInfo().then(response => {
                console.log(response);
                this.todoList = response.data; 
                alter(response);
            }).catch(() => {
                this.errors.push(e)         
            });
        },
        // 分页自带的函数，当pageSize变化时会触发此函数
        handleSizeChange(val) {
          this.pageSize = val;
        },
        // 分页自带函数，当currentPage变化时会触发此函数
        handleCurrentChange(val) {
          this.currentPage = val;
        },
        NotifyDelete(index){
            this.$confirm('此操作将永久删除这条警报, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
                }).then(() => {
                    deleteNotify({id:index}).then(response => {
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
        NotifyRead(index){
            this.$confirm('此操作将处理警报, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
                }).then(() => {
                    readNotify({id:index}).then(response => {
                        console.log(response.data);
                        if(response.code === 0){
                            this.$message({
                                type: 'success',
                                message: '处理成功!'
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
                        message: '已取消处理'
                    });          
            });
        },
    }
};
</script>


<style scoped>
.el-row {
    margin-bottom: 20px;
}

.grid-content {
    display: flex;
    align-items: center;
    height: 100px;
}

.grid-cont-right {
    flex: 1;
    text-align: center;
    font-size: 14px;
    color: #999;
}

.grid-num {
    font-size: 30px;
    font-weight: bold;
}

.grid-con-icon {
    font-size: 50px;
    width: 100px;
    height: 100px;
    text-align: center;
    line-height: 100px;
    color: #fff;
}

.grid-con-1 .grid-con-icon {
    background: rgb(45, 140, 240);
}

.grid-con-1 .grid-num {
    color: rgb(45, 140, 240);
}

.grid-con-2 .grid-con-icon {
    background: rgb(100, 213, 114);
}

.grid-con-2 .grid-num {
    color: rgb(45, 140, 240);
}

.grid-con-3 .grid-con-icon {
    background: rgb(242, 94, 67);
}

.grid-con-3 .grid-num {
    color: rgb(242, 94, 67);
}

.user-info {
    display: flex;
    align-items: center;
    /* border-bottom: 2px solid #ccc; */
    margin-bottom: 20px;
}

.user-avator {
    width: 120px;
    height: 120px;
    border-radius: 50%;
}

.user-info-cont {
    padding-left: 50px;
    flex: 1;
    font-size: 14px;
    color: #999;
}

.user-info-cont div:first-child {
    font-size: 30px;
    color: #222;
}

.user-info-list {
    font-size: 14px;
    color: #999;
    line-height: 25px;
}

.user-info-list span {
    margin-left: 70px;
}

.mgb20 {
    margin-bottom: 20px;    
}

.todo-item {
    font-size: 14px;
}

.todo-item-del {
    text-decoration: line-through;
    color: #999;
}

.schart {
    width: 100%;
    height: 300px;
}

</style>
