<?php
/**
 * Tailwindcss响应式的 Typecho 主题，支持1.3<br/>
 * 包含有情链接 置顶 在主题设置里 主题设置可以修改风格自定义色系
 *
 * @package PulseTabs
 * @author 24pu.com
 * @version 1.0.1
 * @link https://24pu.com/
 */
 ?>
<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<?php
$layout = $this->options->homeLayout ?: 'style_a';
if ($layout === 'style_a') {
    $this->need('home-style-a.php');
} elseif ($layout === 'style_b') {
    $this->need('home-style-b.php');
} else {
    $this->need('home-style-a.php');
}
?>

<?php $this->need('footer.php'); ?>