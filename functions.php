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

    // 首页风格选择
    $homeLayout = new Typecho_Widget_Helper_Form_Element_Select(
        'homeLayout',
        array(
            'style_a' => _t('风格 A（传统博客列表）'),
            'style_b' => _t('风格 B（企业风格卡片）')
        ),
        'style_a',
        _t('首页布局风格'),
        _t('选择首页的整体展示风格')
    );
    $form->addInput($homeLayout);
    $notice = new Typecho_Widget_Helper_Form_Element_Text(
        'notice',
        null,
        null,
        _t('配色说明'),
        _t('蓝色系使用 Tailwind Blue-500，翠绿色系使用 Emerald-500，青色系使用 Cyan-500。您也可以自定义任意十六进制颜色。')
    );
    $notice->input->setAttribute('disabled', 'disabled');
    $form->addInput($notice);

    // 配色方案选择
    $colorScheme = new Typecho_Widget_Helper_Form_Element_Select(
        'colorScheme',
        array(
            'orange' => _t('橙色系（默认）'),
            'blue'   => _t('蓝色系'),
            'emerald' => _t('翠绿色系'),
            'cyan'   => _t('青色系'),
            'custom' => _t('自定义')
        ),
        'orange',
        _t('主题配色'),
        _t('选择预设配色或自定义')
    );
    $form->addInput($colorScheme);

    // 自定义主色（始终显示，但用户需先选择“自定义”）
    $customAccent = new Typecho_Widget_Helper_Form_Element_Text(
        'customAccent',
        null,
        '#FF5E00',
        _t('自定义主色'),
        _t('输入十六进制颜色值，如 #3B82F6（蓝色）、#10B981（翠绿）、#06B6D4（青色）。需先选择“自定义”')
    );
    $form->addInput($customAccent);   // 关键：添加到表单

    // 在表单底部添加“恢复默认”按钮（通过 JavaScript 重置）
    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        var resetBtn = document.createElement("button");
        resetBtn.type = "button";
        resetBtn.className = "btn btn-xs";
        resetBtn.innerText = "恢复默认配色";
        resetBtn.style.marginTop = "15px";
        resetBtn.onclick = function() {
            if(confirm("确定恢复默认配色吗？")) {
                document.querySelector("[name=colorScheme]").value = "orange";
                document.querySelector("[name=customAccent]").value = "#FF5E00";
                document.querySelector("[type=submit]").click();
            }
        };
        // 找到合适的位置插入按钮，比如配色说明之后，或直接追加到表单末尾
        var lastOption = document.querySelector(".typecho-option:last-of-type");
        if (lastOption) lastOption.after(resetBtn);
    });
    </script>';

    // Banner 独立风格（可覆盖主题配色）
    $bannerStyle = new Typecho_Widget_Helper_Form_Element_Select(
        'bannerStyle',
        array(
            'follow_theme' => _t('跟随主题配色'),
            'teal_cyan_emerald' => _t('固定青绿渐变（风格A原版）'),
            'custom' => _t('自定义颜色渐变')
        ),
        'teal_cyan_emerald',   // 默认让 Banner 保持原青绿风格
        _t('Banner 背景风格'),
        _t('可选择独立于主题配色的 Banner 样式，或自定义渐变。')
    );
    $form->addInput($bannerStyle);

    // 自定义 Banner 起始色（仅当 bannerStyle 为 custom 时生效）
    $bannerStart = new Typecho_Widget_Helper_Form_Element_Text(
        'bannerStart',
        null,
        '#2dd4bf',
        _t('Banner 渐变起始色'),
        _t('十六进制颜色值，如 #2dd4bf (teal-400)')
    );
    $form->addInput($bannerStart);

    // 自定义 Banner 中间色
    $bannerVia = new Typecho_Widget_Helper_Form_Element_Text(
        'bannerVia',
        null,
        '#67e8f9',
        _t('Banner 渐变中间色'),
        _t('十六进制颜色值，如 #67e8f9 (cyan-300)')
    );
    $form->addInput($bannerVia);

    // 自定义 Banner 结束色
    $bannerTo = new Typecho_Widget_Helper_Form_Element_Text(
        'bannerTo',
        null,
        '#6ee7b7',
        _t('Banner 渐变结束色'),
        _t('十六进制颜色值，如 #6ee7b7 (emerald-300)')
    );
    $form->addInput($bannerTo);

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

    // 统计代码（新增）
    $analyticsCode = new Textarea('analyticsCode', NULL, NULL, _t('统计代码'), _t('输入统计 JS 代码，如 Google Analytics、百度统计等，将自动添加到页面 </head> 之前。'));
    $form->addInput($analyticsCode);
}


/**
 * 获取导航链接的样式类（支持桌面端和移动端）
 * @param string $type 页面类型：'index', 'page'
 * @param string|null $slug 页面缩略名（页面类型时必填）
 * @param bool $isMobile 是否为移动端（默认false，桌面端）
 * @return string
 */
function get_nav_class($type, $slug = null, $isMobile = false, $parentSlug = null)
{
    $archive = Typecho_Widget::widget('Widget_Archive');
    $isActive = false;

    if ($type === 'index') {
        $isActive = $archive->is('index');
    } elseif ($type === 'page' && $slug) {
        // 当前页面是目标页面，或者当前页面的父级 slug 等于目标 slug
        $isActive = ($archive->is('page') && $archive->slug == $slug)
                    || ($parentSlug && $parentSlug == $slug);
    }

    if ($isMobile) {
        return $isActive
            ? 'block border-l-4 border-accent pl-3 py-1 text-accent font-medium'
            : 'block text-gray-600 hover:text-accent';
    } else {
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



/**
 * 返回 Banner 区域的渐变样式（类或内联样式）
 * @return string
 */
function get_banner_gradient_style() {
    $bannerStyle = Typecho_Widget::widget('Widget_Options')->bannerStyle ?: 'teal_cyan_emerald';
    
    if ($bannerStyle === 'teal_cyan_emerald') {
        // 使用 Tailwind 预设类
        return 'bg-gradient-to-r from-teal-400 via-cyan-300 to-emerald-300';
    } elseif ($bannerStyle === 'custom') {
        $start = Typecho_Widget::widget('Widget_Options')->bannerStart ?: '#2dd4bf';
        $via = Typecho_Widget::widget('Widget_Options')->bannerVia ?: '#67e8f9';
        $to = Typecho_Widget::widget('Widget_Options')->bannerTo ?: '#6ee7b7';
        // 由于 Tailwind 的动态类不支持任意十六进制值，使用内联样式
        return 'style="background-image: linear-gradient(90deg, ' . $start . ', ' . $via . ', ' . $to . ');"';
    }
    // 跟随主题配色（使用主题的 accent 变量）
    return 'bg-gradient-to-r from-accent via-accent-light to-accent/20';
}