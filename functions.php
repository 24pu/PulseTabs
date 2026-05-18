<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

use Typecho\Widget\Helper\Form\Element\Text;
use Typecho\Widget\Helper\Form\Element\Textarea;
use Typecho\Widget\Helper\Layout;

/**
 * 主题后台配置 (themeConfig)
 */
function themeConfig($form)
{
    echo '<p style="margin-bottom: 15px;">PulseTabs 主题设置</p>';

    // 新增 Logo
    $logoUrl = new Text('logoUrl', NULL, NULL, _t('站点 Logo 图片地址'), _t('输入图片 URL，留空则显示默认文字 Logo。建议尺寸：宽高比 2:1'));
    $form->addInput($logoUrl);

    // 新增 Favicon
    $faviconUrl = new Text('faviconUrl', NULL, NULL, _t('网站 Favicon 地址'), _t('输入 .ico 或 .png 图片 URL，留空则不显示。'));
    $form->addInput($faviconUrl);

    // 原有配置
    $siteNotice = new Text('siteNotice', NULL, NULL, _t('站点公告'), _t('显示在首页 Banner 上方的短公告文字'));
    $form->addInput($siteNotice);

    $footerCopyright = new Text('footerCopyright', NULL, '© 2026 PulseTabs. All rights reserved.', _t('页脚版权'), _t('自定义页脚版权信息'));
    $form->addInput($footerCopyright);

    $friendLinks = new Textarea('friendLinks', NULL, NULL, _t('友情链接'), _t('每行一个，格式：名称|链接'));
    $form->addInput($friendLinks);

    $socialGithub = new Text('socialGithub', NULL, NULL, _t('GitHub'), _t('GitHub 主页链接'));
    $form->addInput($socialGithub);

    $socialTwitter = new Text('socialTwitter', NULL, NULL, _t('Twitter'), _t('Twitter 主页链接'));
    $form->addInput($socialTwitter);
}


/**
 * 获取导航链接的样式类（支持桌面端和移动端）
 * @param string $type 页面类型：'index', 'page'
 * @param string|null $slug 页面缩略名（页面类型时必填）
 * @param bool $isMobile 是否为移动端（默认false，桌面端）
 * @return string
 */
function get_nav_class($type, $slug = null, $isMobile = false) {
    $archive = Typecho_Widget::widget('Widget_Archive');
    $isActive = false;
    
    if ($type === 'index') {
        $isActive = $archive->is('index');
    } elseif ($type === 'page' && $slug) {
        $isActive = ($archive->is('page') && $archive->slug == $slug);
    }
    
    if ($isMobile) {
        // 移动端：左边框高亮（无背景色）
        return $isActive 
            ? 'block border-l-4 border-accent pl-3 py-1 text-accent font-medium' 
            : 'block text-gray-600 hover:text-accent';
    } else {
        // 桌面端：下划线高亮
        return $isActive 
            ? 'text-dark border-b-2 border-accent pb-0.5 font-medium' 
            : 'text-gray-600 hover:text-accent';
    }
}

/**
 * 获取用户头像（基于邮箱的 Gravatar）
 * @param string $email 用户邮箱
 * @param int $size 头像尺寸
 * @return string 头像 URL
 */
function getGravatar($email, $size = 48) {
    $hash = md5(strtolower(trim($email)));
    $default = urlencode('https://24pu.com/usr/themes/PulseTabs/assets/images/default-avatar.png');
    return "https://gravatar.loli.net/avatar/$hash?s=$size&d=$default&r=g";
}

/**
 * 文章/页面自定义字段 (themeFields)
 */
function themeFields(Layout $layout)
{
    // 置顶文章
    $sticky = new Typecho\Widget\Helper\Form\Element\Select('sticky', ['0' => '普通', '1' => '置顶'], '0', _t('文章置顶'), _t('是否将此文章置顶显示在列表顶部'));
    $layout->addItem($sticky);

    // 推荐标记
    $recommend = new Typecho\Widget\Helper\Form\Element\Select('recommend', ['0' => '普通', '1' => '推荐'], '0', _t('推荐标记'), _t('是否在文章标题旁显示推荐图标'));
    $layout->addItem($recommend);
}

// 获取文章浏览量
function getPostViews($archive)
{
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();
    // 确保 views 字段存在
    try {
        $db->query('SELECT views FROM `' . $prefix . 'contents` LIMIT 1');
    } catch (Typecho_Db_Exception $e) {
        // 字段不存在，自动添加
        $db->query('ALTER TABLE `' . $prefix . 'contents` ADD `views` INT(10) DEFAULT 0');
    }
    $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $archive->cid));
    return $row ? intval($row['views']) : 0;
}

// 更新文章浏览量
function getPostView($archive)
{
    $cid = $archive->cid;
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();
    // 确保 views 字段存在
    try {
        $db->query('SELECT views FROM `' . $prefix . 'contents` LIMIT 1');
    } catch (Typecho_Db_Exception $e) {
        $db->query('ALTER TABLE `' . $prefix . 'contents` ADD `views` INT(10) DEFAULT 0');
    }
    $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid));
    $views = intval($row['views']) + 1;
    $db->query($db->update('table.contents')->rows(array('views' => $views))->where('cid = ?', $cid));
}