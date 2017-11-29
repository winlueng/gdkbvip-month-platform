<?php
// +----------------------------------------------------------------------
// | Author: winson <393857054@qq.com>
// +----------------------------------------------------------------------
use think\Route;
// 域名路由绑定
Route::domain('img.month.gdkbvip.com','/public/static');// 绑定后台

// 管理后台接口
Route::controller('/v1/admin/rule', 'admin/rule');
Route::controller('/v1/admin/rule_group', 'admin/RuleGroup');
Route::controller('/v1/admin/rule_access', 'admin/GroupAccess');
Route::controller('/v1/admin/common', 'admin/common');
Route::controller('/v1/admin/admin', 'admin/admin');
Route::controller('/v1/admin/tag_classify', 'admin/TagClassify');
Route::controller('/v1/admin/tag', 'admin/Tag');
Route::controller('/v1/admin/classify', 'admin/classify');
Route::controller('/v1/admin/article', 'admin/article');
Route::controller('/v1/admin/art_comment', 'admin/ArticleComment');
Route::controller('/v1/admin/banner', 'admin/banner');
Route::controller('/v1/admin/business', 'admin/business');
Route::controller('/v1/admin/address', 'admin/district');
Route::controller('/v1/admin/organization', 'admin/organization');
Route::controller('/v1/admin/org_comment', 'admin/organizationComment');
Route::controller('/v1/admin/organization_detail', 'admin/OrganizationDetail');
Route::controller('/v1/admin/service', 'admin/OrganizationService');
Route::controller('/v1/admin/question_article', 'admin/QuestionArticle');
Route::controller('/v1/admin/user', 'admin/user');
Route::controller('/v1/admin/system_news', 'admin/SystemNews');
Route::controller('/v1/admin/doctor', 'admin/DoctorInfo');
Route::controller('/v1/admin/order', 'admin/SubscribeOrder');
Route::controller('/v1/admin/d_comment', 'admin/DoctorComment');
Route::controller('/v1/admin/q_order', 'admin/DoctorQuestionOrder');
Route::controller('/v1/admin/news_record', 'admin/NewsRecord');
Route::controller('/v1/admin/announcement', 'admin/Announcement');

// 医生手机端接口
Route::controller('/v1/doctor/article', 'doctor/article');
Route::controller('/v1/doctor/common', 'doctor/common');
Route::controller('/v1/doctor/doctor', 'doctor/DoctorInfo');
Route::controller('/v1/doctor/article', 'doctor/article');
Route::controller('/v1/doctor/tag_classify', 'doctor/TagClassify');
Route::controller('/v1/doctor/tag', 'doctor/tag');
Route::controller('/v1/doctor/comment', 'doctor/DoctorComment');
Route::controller('/v1/doctor/announcement', 'doctor/Announcement');

// 用户手机端接口
Route::controller('/v1/user/user', 'user/user');
Route::controller('/v1/user/detail', 'user/UserDetailInfo');
Route::controller('/v1/user/ready_pregnancy', 'user/UserReadyPregnancyInfo');
Route::controller('/v1/user/pregnancy', 'user/UserPregnancyInfo');
Route::controller('/v1/user/after_pregnancy', 'user/UserAfterPregnancyInfo');
Route::controller('/v1/user/communication', 'user/Communication');
Route::controller('/v1/user/search', 'user/search');
Route::controller('/v1/user/question_article', 'user/QuestionArticle');
Route::controller('/v1/user/article', 'user/Article');
Route::controller('/v1/user/art_behavior', 'user/ArticleBehavior');
Route::controller('/v1/user/art_comment', 'user/ArticleComment');
Route::controller('/v1/user/classify', 'user/classify');
Route::controller('/v1/user/organization', 'user/organization');
Route::controller('/v1/user/org_behavior', 'user/OrganizationBehavior');
Route::controller('/v1/user/organization_comment', 'user/OrganizationComment');
Route::controller('/v1/user/service', 'user/OrganizationService');
Route::controller('/v1/user/service_comment', 'user/OrganizationServiceComment');
Route::controller('/v1/user/order', 'user/SubscribeOrder');
Route::controller('/v1/user/doctor', 'user/DoctorInfo');
Route::controller('/v1/user/doctor_comment', 'user/DoctorComment');
Route::controller('/v1/user/doc_behavior', 'user/DoctorBehavior');
Route::controller('/v1/user/symptomatography', 'user/UserSymptomatography');
Route::controller('/v1/user/news_record', 'user/NewsRecord');
Route::controller('/v1/user/common', 'user/common');
Route::controller('/v1/user/q_order', 'user/DoctorQuestionOrder');
Route::controller('/v1/user/announcement', 'user/Announcement');

Route::miss('');