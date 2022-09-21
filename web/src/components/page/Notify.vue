<template>
    <div class="">
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item><i class="el-icon-message-solid"></i> 通知信息</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="container">
            <el-tabs v-model="message">
                <el-tab-pane :label="`未读消息(${unread.length})`" name="first">
                    <el-table :data="unread" :show-header="false" style="width: 100%">
                        <el-table-column>
                            <template slot-scope="scope">
                                <span class="message-title">{{scope.row.oldperson_id}}</span>
                            </template>
                        </el-table-column>

                        <el-table-column >
                            <template slot-scope="scope">
                                <span class="message-title">{{scope.row.type}}</span>
                            </template>
                        </el-table-column>

                        <el-table-column>
                            <template slot-scope="scope">
                                <span class="message-title">{{scope.row.event_location}}</span>
                            </template>
                        </el-table-column>

                        <el-table-column width="500">
                            <template slot-scope="scope">
                                <span class="message-title">{{scope.row.event_desc}}</span>
                            </template>
                        </el-table-column>

                        <el-table-column width="200">
                            <template slot-scope="scope">
                                <span class="message-title">{{scope.row.event_date}}</span>
                            </template>
                        </el-table-column>

                        <el-table-column prop="date" width="80"></el-table-column>
                        <el-table-column width="120">
                            <template slot-scope="scope">
                                <el-button size="small" @click="handleRead(scope.$index, scope.row.id)">标为已读</el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                    <!-- <div class="handle-row">
                        <el-button type="primary">全部标为已读</el-button>
                    </div> -->
                </el-tab-pane>
                <el-tab-pane :label="`已读消息(${read.length})`" name="second">
                    <template v-if="message === 'second'">
                        <el-table :data="read" :show-header="false" style="width: 100%">
                            <el-table-column>
                                <template slot-scope="scope">
                                    <span class="message-title">{{scope.row.oldperson_id}}</span>
                                </template>
                            </el-table-column>

                            <el-table-column >
                                <template slot-scope="scope">
                                    <span class="message-title">{{scope.row.type}}</span>
                                </template>
                            </el-table-column>

                            <el-table-column>
                                <template slot-scope="scope">
                                    <span class="message-title">{{scope.row.event_location}}</span>
                                </template>
                            </el-table-column>

                            <el-table-column width="500">
                                <template slot-scope="scope">
                                    <span class="message-title">{{scope.row.event_desc}}</span>
                                </template>
                            </el-table-column>

                            <el-table-column width="200">
                                <template slot-scope="scope">
                                    <span class="message-title">{{scope.row.event_date}}</span>
                                </template>
                            </el-table-column>
                            <el-table-column prop="date" width="80"></el-table-column>
                            <el-table-column width="120">
                                <template slot-scope="scope">
                                    <el-button type="danger" @click="handleDel(scope.$index, scope.row.id)">删除</el-button>
                                </template>
                            </el-table-column>
                        </el-table>
                        <!-- <div class="handle-row">
                            <el-button type="danger">删除全部</el-button>
                        </div> -->
                    </template>
                </el-tab-pane>
                <el-tab-pane :label="`回收站(${recycle.length})`" name="third">
                    <template v-if="message === 'third'">
                        <el-table :data="recycle" :show-header="false" style="width: 100%">
                            <el-table-column>
                                <template slot-scope="scope">
                                    <span class="message-title">{{scope.row.oldperson_id}}</span>
                                </template>
                            </el-table-column>
                            
                            <el-table-column >
                                <template slot-scope="scope">
                                    <span class="message-title">{{scope.row.type}}</span>
                                </template>
                            </el-table-column>

                            <el-table-column>
                                <template slot-scope="scope">
                                    <span class="message-title">{{scope.row.event_location}}</span>
                                </template>
                            </el-table-column>

                            <el-table-column width="500">
                                <template slot-scope="scope">
                                    <span class="message-title">{{scope.row.event_desc}}</span>
                                </template>
                            </el-table-column>

                            <el-table-column width="200">
                                <template slot-scope="scope">
                                    <span class="message-title">{{scope.row.event_date}}</span>
                                </template>
                            </el-table-column>
                            <el-table-column prop="date" width="80"></el-table-column>
                            <el-table-column width="120">
                                <template slot-scope="scope">
                                    <el-button @click="handleRestore(scope.$index, scope.row.id)">还原</el-button>
                                </template>
                            </el-table-column>
                        </el-table>
                        <!-- <div class="handle-row">
                            <el-button type="danger">清空回收站</el-button>
                        </div> -->
                    </template>
                </el-tab-pane>
            </el-tabs>
        </div>
        <!-- <h1>{{read}}</h1> -->
    </div>
</template>

<script>
import { detailNotify, deleteNotify, readNotify, recoryNotify } from '@/api/index'
    export default {
        name: 'tabs',
        data() {
            return {
                message: 'first',
                showHeader: false,
                unread: [],
                read: [],
                recycle: []
            }
        },
        mounted() {
            this.getdata();
        },
        methods: {
            handleRead(index,notifyId) {
                this.$confirm('此操作将处理警报, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                    }).then(() => {
                        readNotify({id:notifyId}).then(response => {
                            console.log(response.data);
                            if(response.code === 0){
                                const item = this.unread.splice(index, 1);
                                console.log(item);
                                this.read = item.concat(this.read);
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
            handleDel(index,notifyId) {
                this.$confirm('此操作删除警报, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                    }).then(() => {
                        deleteNotify({id:notifyId}).then(response => {
                            console.log(response.data);
                            if(response.code === 0){
                                const item = this.read.splice(index, 1);
                                this.recycle = item.concat(this.recycle);
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
            handleRestore(index, notifyId) {
                this.$confirm('此操作将恢复已删除警报, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                    }).then(() => {
                        recoryNotify({id:notifyId}).then(response => {
                            console.log(response.data);
                            if(response.code === 0){
                                const item = this.recycle.splice(index, 1);
                                this.read = item.concat(this.read);
                                this.$message({
                                    type: 'success',
                                    message: '恢复成功!'
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
                            message: '已取消恢复'
                        });          
                });
                
            },
            getdata(){
                detailNotify().then(response => {
                    console.log(response);
                    this.read = response.data.read; 
                    this.unread = response.data.unread; 
                    this.recycle = response.data.recycle; 
                }).catch(() => {
                    this.errors.push(e)         
                });
            },
        },
        computed: {
            unreadNum(){
                return this.unread.length;
            }
        }
    }

</script>

<style>
.message-title{
    cursor: pointer;
}
.handle-row{
    margin-top: 30px;
}
</style>

