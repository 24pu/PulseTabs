<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<main class="min-h-[60vh] flex items-center justify-center px-4">
    <div class="text-center">
        <div class="text-6xl md:text-8xl font-bold text-dark mb-4">404</div>
        <p class="text-gray-500 text-lg mb-6">啊哦！您访问的页面不存在或已被移除。</p>
        <a href="<?php $this->options->siteUrl(); ?>" class="inline-flex items-center px-6 py-3 rounded-full bg-accent text-white font-semibold hover:bg-accent-dark transition">
            <i class="fas fa-home mr-2"></i> 返回首页
        </a>
    </div>
</main>

<?php $this->need('footer.php'); ?>