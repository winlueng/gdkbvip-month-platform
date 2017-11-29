<?php
// +----------------------------------------------------------------------
// | Author: winleung <393857054@qq.com>
// +----------------------------------------------------------------------

return [
    '__file__' => ['common.php', 'config.php', 'database.php', 'route.php'],

    'admin'     => [
        '__file__'   => ['config.php'],
        '__dir__'    => ['behavior', 'controller', 'model', 'validate'],
        'controller' => ['Admin', 'Article', 'ArticleStatis', 'Banner', 'BannerStatis', 'Business', 'BusinessStatis', 'DoctorInfo', 'GroupAccess', 'NewsRecord', 'Organization', 'OrganizationComment', 'OrganizationDetail', 'OrganizationService', 'OrganizationServiceStatis', 'OrganizationStatis', 'QuestionArticle', 'QuestionArticleStatis', 'Rule', 'RuleGroup', 'SubscribeOrder', 'Tag', 'TagClassify', 'User', 'UserAfterPregnancyInfo', 'UserDetailInfo', 'UserFromHelp', 'UserPregnancyInfo', 'UserQuesitionRecord', 'UserReadyPregnancyInfo', 'UserVisitTrack'],
        'model'      => ['Admin', 'Article', 'ArticleStatis', 'Banner', 'BannerStatis', 'Business', 'BusinessStatis', 'DoctorInfo', 'GroupAccess', 'NewsRecord', 'Organization', 'OrganizationComment', 'OrganizationDetail', 'OrganizationService', 'OrganizationServiceStatis', 'OrganizationStatis', 'QuestionArticle', 'QuestionArticleStatis', 'Rule', 'RuleGroup', 'SubscribeOrder', 'Tag', 'TagClassify', 'User', 'UserAfterPregnancyInfo', 'UserDetailInfo', 'UserFromHelp', 'UserPregnancyInfo', 'UserQuesitionRecord', 'UserReadyPregnancyInfo', 'UserVisitTrack'],
        'validate'    => ['Admin', 'Article', 'Banner','Business', 'BusinessStatis', 'DoctorInfo', 'GroupAccess', 'NewsRecord', 'Organization', 'OrganizationComment', 'OrganizationDetail', 'OrganizationService', 'QuestionArticle', 'Rule', 'SubscribeOrder', 'Tag', 'TagClassify']
    ],
    'user'    => [
        '__file__'   => ['config.php'],
        '__dir__'    => ['behavior', 'controller', 'model', 'validate'],
        'controller' => ['Article', 'ArticleStatis', 'Banner', 'BannerStatis', 'Business', 'BusinessStatis', 'DoctorInfo', 'NewsRecord', 'Organization', 'OrganizationComment', 'OrganizationDetail', 'OrganizationService', 'OrganizationServiceStatis', 'OrganizationStatis', 'QuestionArticle', 'QuestionArticleStatis','SubscribeOrder', 'Tag', 'TagClassify', 'User', 'UserAfterPregnancyInfo', 'UserDetailInfo', 'UserFromHelp', 'UserPregnancyInfo', 'UserQuesitionRecord', 'UserReadyPregnancyInfo', 'UserVisitTrack'],
        'model'      => ['Article', 'ArticleStatis', 'Banner', 'BannerStatis', 'Business', 'BusinessStatis', 'DoctorInfo', 'NewsRecord', 'Organization', 'OrganizationComment', 'OrganizationDetail', 'OrganizationService', 'OrganizationServiceStatis', 'OrganizationStatis', 'QuestionArticle', 'QuestionArticleStatis','SubscribeOrder', 'Tag', 'TagClassify', 'User', 'UserAfterPregnancyInfo', 'UserDetailInfo', 'UserFromHelp', 'UserPregnancyInfo', 'UserQuesitionRecord', 'UserReadyPregnancyInfo', 'UserVisitTrack'],
        'validate'    => ['Article', 'Banner','Business', 'BusinessStatis', 'DoctorInfo', 'NewsRecord', 'Organization', 'OrganizationComment', 'OrganizationDetail', 'OrganizationService', 'QuestionArticle', 'SubscribeOrder', 'Tag', 'TagClassify']
    ],
    'doctor'   => [
        '__file__'   => ['config.php'],
        '__dir__'    => ['behavior', 'controller', 'model', 'validate'],
        'controller' => ['Article', 'ArticleStatis', 'DoctorInfo', 'NewsRecord', 'Organization', 'OrganizationComment', 'OrganizationDetail', 'OrganizationService', 'OrganizationServiceStatis', 'OrganizationStatis', 'QuestionArticle', 'QuestionArticleStatis', 'Tag', 'TagClassify'],
        'model'      => ['Article', 'ArticleStatis', 'DoctorInfo', 'NewsRecord', 'Organization', 'OrganizationComment', 'OrganizationDetail', 'OrganizationService', 'OrganizationServiceStatis', 'OrganizationStatis', 'QuestionArticle', 'QuestionArticleStatis', 'Tag', 'TagClassify'],
        'validate'    => ['Article', 'DoctorInfo', 'NewsRecord', 'Organization', 'OrganizationComment', 'OrganizationDetail', 'OrganizationService', 'QuestionArticle', 'Tag', 'TagClassify']
    ],
    // 其他更多的模块定义
];
