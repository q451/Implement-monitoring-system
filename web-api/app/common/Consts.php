<?php
 
namespace app\common;
 
use app\modules\common\service\CollegeInfoService;
use Yii;
 
class Consts
{
    const ROLE_ID_FUDAOYUAN = 1; //辅导员
    const ROLE_ID_BANZHUREN = 2; //班主任
    const ROLE_ID_RENKEJIAOSHI = 3; //任课教师
    const ROLE_ID_JIGUANJIAOSHI = 4; //机关教师
    const ROLE_ID_XIAOJIJIAOSHI = 5; //校级教师
    const ROLE_ID_XUEYUANLINGDAO = 901; //学院领导
    const ROLE_ID_XUEXIAOLINGDAO = 904; //学校领导
    const ROLE_ID_XUEGONGCHU = 907; //学工处

    const ROLE_XUEYUAN = [
        self::ROLE_ID_XUEYUANLINGDAO,
    ];
    const ROLE_XUEXIAO = [
        self::ROLE_ID_XUEXIAOLINGDAO,
        self::ROLE_ID_XUEGONGCHU,
    ];

    const IDENTITY_ID_XIAOJIGUANLIYUAN = 1; //校级管理员
    const IDENTITY_ID_YUANJIGUANLIYUAN = 2; //院级管理员
    const IDENTITY_ID_FUDAOYUAN = 3; //辅导员
    const IDENTITY_ID_STUDENT = 4; //学生

    const IDENTITY_NAME = [
        self::IDENTITY_ID_XIAOJIGUANLIYUAN => '校级管理员',
        self::IDENTITY_ID_YUANJIGUANLIYUAN => '院级管理员',
        self::IDENTITY_ID_FUDAOYUAN => '辅导员',
    ];

    const STUDENT_RECORD_TYPE_COUNSEL = 1; //辅导记录
    const STUDENT_RECORD_TYPE_DIARY = 2; //成长日记

    const MOVE_INFO_OPERATION_STATUS_ON = 0; //调动信息调整中
    const MOVE_INFO_OPERATION_STATUS_ACCESS = 1; //调动信息通过
    const MOVE_INFO_OPERATION_STATUS_REJECT = 2; //调动信息拒绝

    const MOVE_INFO_OPERATION_TYPE = [
        self::MOVE_INFO_OPERATION_STATUS_ON,
        self::MOVE_INFO_OPERATION_STATUS_ACCESS,
        self::MOVE_INFO_OPERATION_STATUS_REJECT,
    ];

    const MOVE_INFO_TYPE_IN = 0; //调动信息调入
    const MOVE_INFO_TYPE_OUT = 1; //调动信息调出

    const MOVE_INFO_TYPE = [
        self::MOVE_INFO_TYPE_IN,
        self::MOVE_INFO_TYPE_OUT,
    ];

    // 学生信息 字段类型
    const STUDENT_XINXI_COLUMN_TYPE = [
        0,
        self::STUDENT_XINXI_COLUMN_JIBEN,
        self::STUDENT_XINXI_COLUMN_XUEJI,
        self::STUDENT_XINXI_COLUMN_XUEYE,
        self::STUDENT_XINXI_COLUMN_SUSHE,
        self::STUDENT_XINXI_COLUMN_BIYE,
        self::STUDENT_XINXI_COLUMN_GUOJI,
        self::STUDENT_XINXI_COLUMN_WENYI,
    ];

    // 学生信息 基本类
    const STUDENT_XINXI_COLUMN_JIBEN = [
        ['column_string' => 'xh', 'type' => 'string', 'meaning' => '学号'],
        ['column_string' => 'xm', 'type' => 'string', 'meaning' => '姓名'],
        ['column_string' => 'xjlx', 'type' => 'string', 'meaning' => '学籍类型'],
        ['column_string' => 'xb', 'type' => 'string', 'meaning' => '性别', 'range' => ['男', '女']],
        ['column_string' => 'xy', 'type' => 'string', 'meaning' => '学院', 'range' => []],
        ['column_string' => 'grade_now', 'type' => 'string', 'meaning' => '当前年级', 'range' => []],
        ['column_string' => 'nj', 'type' => 'string', 'meaning' => '入学年份'],
        ['column_string' => 'zy', 'type' => 'string', 'meaning' => '专业', 'range' => []],
        ['column_string' => 'zrb', 'type' => 'string', 'meaning' => '自然班', 'range' => []],
        ['column_string' => 'jxb', 'type' => 'string', 'meaning' => '教学班'],
        ['column_string' => 'nl', 'type' => 'int', 'meaning' => '年龄'],
        ['column_string' => 'csny', 'type' => 'string', 'meaning' => '出生日期'],
        ['column_string' => 'jg', 'type' => 'string', 'meaning' => '籍贯'],
        ['column_string' => 'syd', 'type' => 'string', 'meaning' => '生源地'],
        ['column_string' => 'sfzh', 'type' => 'string', 'meaning' => '身份证号'],
        ['column_string' => 'email', 'type' => 'string', 'meaning' => '邮箱'],
        ['column_string' => 'lxfs', 'type' => 'string', 'meaning' => '联系方式'],
        ['column_string' => 'zzmm', 'type' => 'string', 'meaning' => '政治面貌', 'range' => ['中共党员', '中共预备党员', '共青团员', '群众', '']],
        ['column_string' => 'mz', 'type' => 'string', 'meaning' => '民族', 'range' => [
            '汉族', '蒙古族', '回族', '藏族', '维吾尔族', '苗族', '彝族', '壮族', '布依族', '朝鲜族',
            '满族', '侗族', '瑶族', '白族', '土家族', '哈尼族', '哈萨克族', '傣族', '黎族', '僳僳族',
            '佤族', '畲族', '高山族', '拉祜族', '水族', '东乡族', '纳西族', '景颇族', '柯尔克孜族', '土族',
            '达斡尔族', '仫佬族', '羌族', '布朗族', '撒拉族', '毛南族', '仡佬族', '锡伯族', '阿昌族',
            '普米族', '塔吉克族', '怒族', '乌孜别克族', '俄罗斯族', '鄂温克族', '德昂族', '保安族', '裕固族',
            '京族', '塔塔尔族', '独龙族', '鄂伦春族', '赫哲族', '门巴族', '珞巴族', '基诺族']
        ],
        ['column_string' => 'zjxy', 'type' => 'string', 'meaning' => '宗教信仰', 'range' => ['无', '佛教', '伊斯兰教', '基督教', '其他']],
        ['column_string' => 'jjkn', 'type' => 'int', 'meaning' => '是否经济困难', 'range' => [0 => '否', 1 => '是']],
        ['column_string' => 'jjlxr1_name', 'type' => 'string', 'meaning' => '紧急联系人一姓名'],
        ['column_string' => 'jjlxr1_lxfs', 'type' => 'string', 'meaning' => '紧急联系人一联系方式'],
        ['column_string' => 'jjlxr1_gx', 'type' => 'string', 'meaning' => '紧急联系人一关系'],
        ['column_string' => 'jjlxr2_name', 'type' => 'string', 'meaning' => '紧急联系人二姓名'],
        ['column_string' => 'jjlxr2_lxfs', 'type' => 'string', 'meaning' => '紧急联系人二联系方式'],
        ['column_string' => 'jjlxr2_gx', 'type' => 'string', 'meaning' => '紧急联系人二关系'],
        ['column_string' => 'jjlxr3_name', 'type' => 'string', 'meaning' => '紧急联系人三姓名'],
        ['column_string' => 'jjlxr3_lxfs', 'type' => 'string', 'meaning' => '紧急联系人三联系方式'],
        ['column_string' => 'jjlxr3_gx', 'type' => 'string', 'meaning' => '紧急联系人三关系'],
        ['column_string' => 'bjzw', 'type' => 'string', 'meaning' => '班级职务', 'range' => self::STUDENT_BANJIZHIWU_RANGE],
    ];
    // 学生信息 学籍类
    const STUDENT_XINXI_COLUMN_XUEJI = [
        ['column_string' => 'xh', 'type' => 'string', 'meaning' => '学号'],
        ['column_string' => 'xm', 'type' => 'string', 'meaning' => '姓名'],
        ['column_string' => 'zzy', 'type' => 'int', 'meaning' => '是否转专业', 'range' => [0 => '否', 1 => '是']],
        ['column_string' => 'zzyqxy', 'type' => 'string', 'meaning' => '转专业前学院'],
        ['column_string' => 'zzyqzy', 'type' => 'string', 'meaning' => '转专业前专业'],
        ['column_string' => 'ljs', 'type' => 'int', 'meaning' => '是否留级生', 'range' => [0 => '否', 1 => '是']],
        ['column_string' => 'sps', 'type' => 'int', 'meaning' => '是否双培生', 'range' => [0 => '否', 1 => '是']],
        ['column_string' => 'spsyxx', 'type' => 'string', 'meaning' => '双培原学校'],
        ['column_string' => 'lxs', 'type' => 'int', 'meaning' => '是否留学生', 'range' => [0 => '否', 1 => '是']],
    ];
    // 学生信息 学业类
    const STUDENT_XINXI_COLUMN_XUEYE = [
        ['column_string' => 'xh', 'type' => 'string', 'meaning' => '学号'],
        ['column_string' => 'xm', 'type' => 'string', 'meaning' => '姓名'],
        ['column_string' => 'yxdxf', 'type' => 'float', 'meaning' => '已修读学分'],
        ['column_string' => 'xyqk', 'type' => 'string', 'meaning' => '学业情况'],
        ['column_string' => 'xyjsqk', 'type' => 'string', 'meaning' => '学业警示情况'],
        ['column_string' => 'zyrs', 'type' => 'int', 'meaning' => '专业人数'],
        ['column_string' => 'xxpm', 'type' => 'int', 'meaning' => '学习排名'],
        ['column_string' => 'jd', 'type' => 'float', 'meaning' => '绩点'],
        ['column_string' => 'bjgmc', 'type' => 'int', 'meaning' => '不及格门次'],
        ['column_string' => 'qsxnxxpm', 'type' => 'int', 'meaning' => '前三学年学习排名'],
        ['column_string' => 'qsxnzyrs', 'type' => 'int', 'meaning' => '前三学年专业人数'],
        ['column_string' => 'qsxn', 'type' => 'float', 'meaning' => '前三学年绩点'],
        ['column_string' => 'qsxnbjgmc', 'type' => 'int', 'meaning' => '前三学年不及格门次'],
        ['column_string' => 'dxzzxxpm', 'type' => 'int', 'meaning' => '大学最终学习排名'],
        ['column_string' => 'dxzzzyrs', 'type' => 'int', 'meaning' => '大学最终专业人数'],
        ['column_string' => 'dxzzbjgmc', 'type' => 'int', 'meaning' => '大学最终不及格门次'],
    ];
    // 学生信息 宿舍类
    const STUDENT_XINXI_COLUMN_SUSHE = [
        ['column_string' => 'xh', 'type' => 'string', 'meaning' => '学号'],
        ['column_string' => 'xm', 'type' => 'string', 'meaning' => '姓名'],
        ['column_string' => 'ssl', 'type' => 'string', 'meaning' => '宿舍楼'],
        ['column_string' => 'ssh', 'type' => 'string', 'meaning' => '宿舍号'],
        ['column_string' => 'ch', 'type' => 'string', 'meaning' => '床号'],
        ['column_string' => 'sswmdj', 'type' => 'string', 'meaning' => '宿舍文明等级'],
    ];
    // 学生信息 毕业类
    const STUDENT_XINXI_COLUMN_BIYE = [
        ['column_string' => 'xh', 'type' => 'string', 'meaning' => '学号'],
        ['column_string' => 'xm', 'type' => 'string', 'meaning' => '姓名'],
        ['column_string' => 'bymb', 'type' => 'string', 'meaning' => '毕业目标'],
        ['column_string' => 'byzt', 'type' => 'string', 'meaning' => '毕业状态'],
    ];
    // 学生信息 国际类
    const STUDENT_XINXI_COLUMN_GUOJI = [
        ['column_string' => 'xh', 'type' => 'string', 'meaning' => '学号'],
        ['column_string' => 'xm', 'type' => 'string', 'meaning' => '姓名'],
        ['column_string' => 'cgyx', 'type' => 'int', 'meaning' => '出国意向', 'range' => [0 => '无', 1 => '有']],
        ['column_string' => 'jlxm', 'type' => 'string', 'meaning' => '交流项目'],
        ['column_string' => 'gjb', 'type' => 'string', 'meaning' => '国际班'],
        ['column_string' => 'lxxn', 'type' => 'string', 'meaning' => '离校学年'],
    ];
    // 学生信息 文艺类
    const STUDENT_XINXI_COLUMN_WENYI = [
        ['column_string' => 'xh', 'type' => 'string', 'meaning' => '学号'],
        ['column_string' => 'xm', 'type' => 'string', 'meaning' => '姓名'],
        ['column_string' => 'wytc', 'type' => 'int', 'meaning' => '文艺特长', 'range' => [0 => '无', 1 => '有']],
        ['column_string' => 'tytc', 'type' => 'int', 'meaning' => '体育特长', 'range' => [0 => '无', 1 => '有']],
        ['column_string' => 'qttc', 'type' => 'int', 'meaning' => '其他特长', 'range' => [0 => '无', 1 => '有']],
    ];

    const STUDENT_XSLX_TYPE_LEAVE_SCHOLL = [
        '双培生', '借读生'
    ];

    // 在校状态 对应离校
    const STUDENT_XJLX_TYPE_LEAVE_SCHOLL = [
        '永久离校生-毕业', '永久离校生-结业', '永久离校生-肄业-主动退学', '永久离校生-肄业-勒令退学', '永久离校生-未报到入学', '永久离校生-转学'
    ];

    // 班级职务
    const STUDENT_BANJIZHIWU_RANGE = [
        '班长', '团支部书记', '学习委员', '组织委员', '宣传委员', '纪律委员', '体育委员', '其他'
    ];

    const COUNSELING_CATEGORY_TAG_TYPE_0 = 0;
    const COUNSELING_CATEGORY_TAG_TYPE_1 = 1;
    const COUNSELING_CATEGORY_TAG_TYPE_2 = 2;
    const COUNSELING_CATEGORY_TAG_TYPE_3 = 3;
    const COUNSELING_CATEGORY_TAG_TYPE_4 = 4;
    const COUNSELING_CATEGORY_TAG_TYPE = [
        0 => '深度辅导问题标签',
        1 => '数据同步标签',
        2 => '校级标签',
        3 => '院级标签',
        4 => '自定义标签',
    ];

    const STUDENT_XINXI_CONFLICT_ORIGIN_TYPE_SHOUDONG = 1;
    const STUDENT_XINXI_CONFLICT_ORIGIN_TYPE_ZHENGJI = 2;
    const STUDENT_XINXI_CONFLICT_ORIGIN_TYPE_YEWU = 3;
    const STUDENT_XINXI_CONFLICT_ORIGIN_TYPE_TONGBU = 4;
    const STUDENT_XINXI_CONFLICT_ORIGIN_TYPE_DAORU = 5;
    const STUDENT_XINXI_CONFLICT_ORIGIN_TYPE_LIST = [
        self::STUDENT_XINXI_CONFLICT_ORIGIN_TYPE_SHOUDONG => '数据纠错',
        self::STUDENT_XINXI_CONFLICT_ORIGIN_TYPE_ZHENGJI => '信息征集',
        self::STUDENT_XINXI_CONFLICT_ORIGIN_TYPE_YEWU => '业务融入',
        self::STUDENT_XINXI_CONFLICT_ORIGIN_TYPE_TONGBU => '系统同步',
        self::STUDENT_XINXI_CONFLICT_ORIGIN_TYPE_DAORU => '批量导入',
    ];

    const COMMON_CLASS_GUIDANG_REASON = [
        1 => '毕业班级',
        2 => '专业分流',
    ];

    const STUDENT_COLUMN_DISPALY_TYPE_ALL = 1;
    const STUDENT_COLUMN_DISPALY_TYPE_ADMIN = 2;
    const STUDENT_COLUMN_DISPALY_TYPE_TEACHER = 3;

    // 权限类型
    const AUTHORITY_FEATURE_NOTIFY = 1;
    const AUTHORITY_FEATURE_COUNSEL = 2;
    const AUTHORITY_FEATURE_ZIXUN = 3;
    const AUTHORITY_FEATURE_DATA = 4;
    const AUTHORITY_FEATURE_XIAOYOU = 5;

    const IS_SCHOOL = [
        'IS' => 1,
        'NOT' => 0
    ];

    CONST XUEJI_COLUMN = ['xy', 'zy', 'grade_now', 'zrb'];
    CONST SUSHE_COLUMN = ['ssl', 'sslch', 'ssh', 'ch'];

    CONST FILE_RECORD_TYPE_RECORD = 1;
    CONST FILE_RECORD_TYPE_SPECIAL_EXCEL = 2;
    CONST FILE_RECORD_TYPE = [
        self::FILE_RECORD_TYPE_RECORD => '辅导记录',
        self::FILE_RECORD_TYPE_SPECIAL_EXCEL => '个性通知原始excel'
    ];

    CONST SURVEY_TYPE_COMMON = 1;
    CONST SURVEY_TYPE_YEWU = 2;
    CONST SURVEY_TYPE_YUJING = 3;
    CONST SURVEY_TYPE_SYSTEM = 4;
    CONST SURVEY_TYPE = [
        self::SURVEY_TYPE_COMMON => '工作通知',
        self::SURVEY_TYPE_YEWU => '业务通知',
        self::SURVEY_TYPE_YUJING => '预警通知',
        self::SURVEY_TYPE_SYSTEM => '系统通知',
    ];

    CONST XINXI_COLUMN_INFO_CHILD_TYPE_COMMON = 1;
    CONST XINXI_COLUMN_INFO_CHILD_TYPE_YEAR = 2;
    CONST XINXI_COLUMN_INFO_CHILD_TYPE_STUDY_YEAR = 3;
    CONST XINXI_COLUMN_INFO_CHILD_TYPE_TERM = 4;

    CONST XINXI_COLUMN_INFO_IF_SCHOOLFELLOW_ALL = 1;
    CONST XINXI_COLUMN_INFO_IF_SCHOOLFELLOW_FELLOW = 2;
    CONST XINXI_COLUMN_INFO_IF_SCHOOLFELLOW_COMMON = 3;

    CONST STUDENT_FIELDS = [
        'xm', 'xb', 'zzmm', 'csny', 'nl', 'jg', 'mz', 'sfzh', 'email', 'lxfs', 'zjxy',
        'xy', 'grade_now', 'zy', 'zrb', 'jxb', 'lxxn', 'xjlx', 'xslx', 'zdxq', 'xllx', 'xjzt'
    ];
    CONST TEACHER_FIELDS = [
        'name' => 'xm',
        'second_unit_id' => 'ejdw',
        'third_unit_id' => 'sjdw',
        'position_id' => 'zw',
        'job' => 'zc',
        'email' => 'dzyx',
        'office' => 'bgdz',
        'office_phone' => 'bgdh'
    ];

    CONST REDIS_KEY_SURVEY_ID_LIST_QUEUE_PUBLISH = 'survey_id_list_queue_publish';
    CONST REDIS_KEY_INSPECT_SURVEY_ID_LIST_QUEUE_PUBLISH = 'inspect_survey_id_list_queue_publish';
    CONST REDIS_KEY_SURVEY_ID_LIST_QUEUE_RECEIVE = 'survey_id_list_queue_receive';

    CONST SURVEY_RANGE_SURVEY_STATUS_1 = 1;
    CONST SURVEY_RANGE_SURVEY_STATUS_2 = 2;
    CONST SURVEY_RANGE_SURVEY_STATUS_3 = 3;
    CONST SURVEY_RANGE_SURVEY_STATUS_4 = 4;
    CONST SURVEY_RANGE_SURVEY_STATUS_5 = 5;
    CONST SURVEY_RANGE_SURVEY_STATUS_6 = 6;
    CONST SURVEY_RANGE_SURVEY_STATUS_7 = 7;
    CONST SURVEY_RANGE_SURVEY_STATUS_8 = 8;
    CONST SURVEY_RANGE_SURVEY_STATUS_9 = 9;
    CONST SURVEY_RANGE_SURVEY_STATUS_10 = 10;

    CONST XINXI_COLUMN_TYPE = [
        'select' => 1,
        'common' => 2,
        'date' => 3,
        'city' => 4,
        'number' => 5
    ];

    //业务状态信息
    CONST BUSINESS_APPLY_NOW_STATUS_0=0;//已删除
    CONST BUSINESS_APPLY_NOW_STATUS_1=1;//正在进行
    CONST BUSINESS_APPLY_NOW_STATUS_2=2;//已完成
    CONST BUSINESS_APPLY_NOW_STATUS_3=3;//由发起人取消
    CONST BUSINESS_APPLY_NOW_STATUS_4=4;//由审批人终止

}