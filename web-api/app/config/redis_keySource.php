<?php
/**
 * redis key 分布列表
 * 按照业务分类
 *
 * 命名规则:
 * 1. KEY 全部大写 采用常量命名方式
 * 2. VALUE 要有能够进行业务区分的唯一前缀 后面跟上具体的变量 中间用半角冒号连接
 * 3. 数据表的缓存使用 model: 前缀，其他业务禁用
 *
 */

return [
    /**
     * 业务数据缓存
     */
    // 辅导模块
        // 批量下载消息触发
        'ZHIHUISIZHENG_COUNSELING_BATCH_DOWNLOD' => [
            'key'    => 'zhihuixuegong_counseling_record_batch_downlod_task', // 批量下载任务
            'expire' => false,
        ],
    // 征集模块
        // 批量下载消息触发
        'ZHIHUISIZHENG_SURVEY_BATCH_DOWNLOD' => [
            'key'    => 'zhihuixuegong_survey_answer_batch_downlod_task', // 批量下载任务
            'expire' => false,
        ],
];
