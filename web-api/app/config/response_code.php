<?php
/**
 * 定义返回码，规则如下
 *      1. 返回码类型有：系统公用、数据库专用、模块专用
 *      2. 为了避免与框架本身的错误码以及HTTP协议的状态码冲突，长度统一定义为6位
 *      3. 特别补充：正常返回码为0
 *
 * @author lunixy<lunixy@juzigo.com>
 * @date 2017-05-23 17:38:30
 */

return [
    // 系统公用 [100100-199999]
        // 正常 [100100-100199]
        'NORMAL_SUCCESS'       => 0,         // 正常响应

        // 警告 [100200-100299]

        // 异常 [100300-100399]
        'ERROR_REQUEST_METHOD'      => 100300,    // 请求方法错误

        'ERROR_TOKEN_NOT_EXISTS'    => 100301,    // token 不存在
        'ERROR_TOKEN_FORMAT'        => 100302,    // token 格式错误
        'ERROR_TOKEN_PARSE_FAILED'  => 100303,    // token 解析失败
        'ERROR_TOKEN_SIGNATURE'     => 100304,    // token 签名错误
        'ERROR_TOKEN_EXPIRED'       => 100305,    // token 已经过期
        'ERROR_TOKEN_VERIFY_FAILED' => 100306,    // token 已经失效

        'ERROR_REQUEST_PARAMS'      => 100310,    // 请求参数错误

        'ERROR_HTTP_STATUS_NOT_200' => 100320,    // http状态码非200

        'ERROR_QINIU_UPLOAD_FAILED' => 100400,    // 上传文件到七牛失败
        'ERROR_QINIU_DELETE_AFTER_DAYS_FAILED' => 100401,    // 设置文件生存时间失败
        'ERROR_QINIU_DELETE_FAILED' => 100402,    // 文件删除失败
		
        'ERROR_SETTING_NOT_OK' => 100500,    // 系统数据刷新不可用

    // 数据库专用 [200100-299999]
        // MySQL [200100-200199]
        'ERROR_NO_CONDITION_DELETE' => 200100,    // 删除没有加条件语句
        'ERROR_NO_CONDITION_UPDATE' => 200101,    // 更新没有加条件语句


    // 模块专用 [300100-399999]
        // 用户模块，范围[300100-300199]
        'ERROR_INVALID_USERID'   => 300100,      // 用户名不存在
        'ERROR_INVALID_PASSWORD' => 300101,      // 密码不正确
        'ERROR_INVALID_PASSWORD_AGAIN'   => 300102,      // 重复的新密码不正确
        'ERROR_INVALID_EMAIL'    => 300103,      // 没有邮箱信息
        'ERROR_INVALID_TEACHER'  => 300104,      // 老师账户在职状态已是无效
        'ERROR_PERMISSION_DENY'  => 300105,      // 没有操作权限
        'ERROR_IMPORT_INSTRUCTOR_TEMPLATE' => 300106, // 导入的人员信息模版不正确
        'ERROR_IMPOART_UNCORRECT_DATA' => 300107, // 导入的人员信息中学院或是职称不匹配
        'ERROR_INVALID_ADMIN'  => 300108,      // 此通道不允许非管理员账号登录
        'ERROR_INVALID_USER_TYPE'  => 300109,      // 不允许此账号类型登录系统
        'ERROR_OPENID_NOT_EXIST'  => 300110,      // 该微信账户未与系统绑定
        'ERROR_INVALID_WXCODE'  => 300111,      // 该微信code有问题
        'ERROR_LACK_OF_SPACE'  => 300112,      // 个人空间不足
        'ERROR_CLASS_NOT_EXIST'  => 300113,      // 班级不存在
        'ERROR_MUCH_INVALID_LOGIN'   => 300114,      // 请5分钟后再尝试登录

        // 深度辅导模块, 范围[300200-300299]
        'ERROR_INVALID_TAG_EXCEL' => 300200, // 导入的标签表格模版不正确
        'ERROR_INVALID_QUESTION_EXCEL' => 300201, // 导入的问题表格模版不正确
        'ERROR_INVALID_ALLOCATE_SUBMIT' => 300202, // 无效的提交，必须包含所有辅导员
        'ERROR_ALLOCATE_SUBMIT_LAST_NOT_AUDITOR' => 300203, //上一次提交未被审核，不能再提交
        'ERROR_ALLOCATE_REPEAT_CLASS' => 30204, // 分配班中有重复的
        'ERROR_NOT_EXIST_RECORD' => 300205, // 辅导记录不存在
        'ERROR_ALLOCATE_NOT_ALLCLASS' => 300206, // 无效的提交，必须包含所有班级
        'ERROR_INVALID_QUESTION_DATA' => 300207, // 导入问题数据中有没有回答或建议的内容 
        'ERROR_NOT_EXIST_RECORD_AT_ALL' => 300208, // 当前系统暂无辅导记录可供下载
        'ERROR_CATAGORY_DENY_DELETE' => 300209, // 分类中有不可删除的标签
        'ERROR_SAVE_RECORD_FILE_FAIL' => 300210, // 辅导录音文件保存失败
        'ERROR_DOWNLOAD_RECORD_FILE_FAIL' => 300211, // 本地文件不存在
        'ERROR_DOWNLOAD_RECORD_FILE_FAIL_PERMISSION' => 300212, // 非文件上传人不可下载该文件
        'ERROR_Tag_NOT_EXIST' => 300213, // 该标签不存在
        
        // 通知模块, 范围[300300-300399]
        'ERROE_NOTIFY_NOT_EXIST' => 300300, // 通知不存在
        'ERROE_QUESTION_NOT_EXIST' => 300301, // 问题不存在
        
        //问题模块, 范围[300400-300499]
        'ERROE_CATAGORY_HAS_EXIST' => 300400, // 类别已存在
        'ERROE_CATAGORY_PERMISSION_HAS_EXIST' => 300401, // 该用户已经有此权限
        
        //群组模块, 范围[300500-300599]
        'ERROR_GROUP_MAX_NEED_EMAIL' => 300500, // 群组人数较多需设置邮箱
        'ERROR_GROUP_NAME_EXIST' => 300501, // 同名群组已存在
        'ERROR_GROUP_EMPTY' => 300502, // 群组不存在
        'ERROR_GROUP_IN_ROLE' => 300503, // 该小组为角色范围小组，不可删除
        
        //征集模块, 范围[300600-300699]
        'ERROR_ANSWER_REVIEWED' => 300600, // 征集结果已审核，无需再次提交
        'ERROR_ANSWER_EMPTY' => 300601, // 答卷不存在
        'ERROR_ANSWER_SUBMITTED' => 300602, // 征集结果已提交，无需再次提交
        'ERROE_SURVEY_NOT_EXIST' => 300603, // 征集不存在
        'ERROE_SURVEY_IS_CLOSED' => 300604, // 征集已关闭
        'ERROE_SURVEY_UPPER_LIMIT' => 300605, // 选项人数达到上限
        'ERROE_OPTION_STATISTICS' => 300606,    //选项统计错误
        'ERROE_INVALID_FIELD_FORMAT' => 300607,    //字段类型填写错误
        'ERROE_INVALID_FIELD_NAME' => 300608,    //字段名称填写错误
        'ERROE_INVALID_OPTION' => 300609,           //选项不合法
        'ERROR_SEND_TIME' => 300610,           //定时发送征集次数错误
        'ERROR_TIMED_SURVEY_TYPE' => 300611,//关于定时发送的征集的选择错误
        
        //权限模块, 范围[300700-300799]
        'ERROR_ROLE_EMPTY' => 300700, // 角色不存在
        'ERROR_ROLE_NOT_DELETE' => 300701, // 系统角色，不可删除
        
        //数据统计模块, 范围[300800-300899]
        'ERROR_INVALID_GRADE_LINE' => 300800, // 入学年级字段错误
        'ERROR_INVALID_JJKN_LINE' => 300801, // 是否经济困难字段错误 在  基本信息模板
        'ERROR_INVALID_ZZY_LINE' => 300802, // 是否转专业字段错误  在  学籍信息模板
        'ERROR_INVALID_LJS_LINE' => 300803, // 是否留级生字段错误  在  学籍信息模板
        'ERROR_INVALID_SPS_LINE' => 300804, // 是否双培生字段错误  在  学籍信息模板
        'ERROR_INVALID_CGYX_LINE' => 300805, // 出国意向          在  国际交流模板
        'ERROR_INVALID_WYTC_LINE' => 300806, // 文艺特长字段错误  在  文体活动模板
        'ERROR_INVALID_TYTC_LINE' => 300807, // 体育特长字段错误  在  文体活动模板
        'ERROR_INVALID_QTTC_LINE' => 300808, // 其他特长字段错误  在  文体活动模板
        'ERROR_INVALID_LXS_LINE' => 300809, // 留学生字段错误  在  学籍信息模板
        'ERROR_INVALID_ZZMM_LINE' => 300810, // 政治面貌字段错误  在  基本信息模板
        'ERROR_INVALID_XYJSQK_LINE' => 300811, // 学业警示字段错误  在  学籍信息模板
        'ERROR_INVALID_XB_LINE' => 300812, // 性别字段错误  在  基本信息模板
        'ERROR_INVALID_BYMB_LINE' => 300813, // 毕业目标字段错误  在  毕业就业模板
        'ERROR_INVALID_BYZT_LINE' => 300814, // 毕业状态字段错误  在  毕业就业模板
        'ERROR_INVALID_GJB_LINE' => 300815, // 国际班字段错误  在  国际交流模板
        'ERROR_INVALID_XYQK_LINE' => 300816, // 学业警示字段错误  在  学籍信息模板
        'ERROR_EXPORT_EMPTY' => 300817, // 导出内容为空
        
        //协同督办模块, 范围[300900-300999]
        'ERROR_INSPECT_MUCH_GROUP' => 300900, // 同一通知/征集不允许同一个人被分配多个督办任务
        'ERROR_INSPECT_MUCH_UNIT' => 300901, // 同一通知/征集不允许同一个人被分配督办多个单位
		
        //成长日记模块, 范围[301000-301099]
        'ERROR_RECORD_NOT_EXIST' => 301001, // 该记录不存在
        'ERROR_RECORD_EXCEL_CONTENT' => 301002, // 导入的数据表内容有误
        'ERROR_RECORD_EXCEL_ERROR_USERID' => 301003, // 导入的数据表内容工号错误有误
        'ERROR_RECORD_EXCEL_ERROR_USERID_NAME' => 301004, // 导入的数据表内容工号/学号与姓名不匹配

        //学生数据留痕模块, 范围[301100-301199]
        'ERROR_COLUMN_NOT_EXIST' => 301001, // 该字段不存在
        'ERROR_CLASSIFICATION_NOT_EXIST' => 301002, // 该类别不存在
        'ERROR_CLASSIFICATION_NOT_UPDATE' => 301003, // 该类别不可修改
        'ERROR_COLUMN_NOT_UPDATE' => 301004, // 该字段不可修改
];
