<?php
/**
 * Tailwindcss响应式的 Typecho 主题，支持1.3<br/>
 * 包含有情链接 置顶 在主题设置里
 *
 * @package PulseTabs
 * @author 24pu.com
 * @version 1.0.0
 * @link https://24pu.com/
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<main>
    <!-- Banner 区域 -->
    <section class="relative bg-gradient-to-r from-teal-400 via-cyan-300 to-emerald-300 text-dark">
        <div class="absolute inset-0 bg-black/5"></div>
        <div class="relative max-w-7xl mx-auto px-4 py-16 md:py-24 text-center">
            <?php if ($this->options->siteNotice): ?>
                <div class="inline-flex items-center bg-white/30 backdrop-blur-sm rounded-full px-4 py-1.5 mb-6 text-sm font-medium text-dark">
                    <i class="fas fa-pulse mr-2 text-accent"></i> <?php $this->options->siteNotice(); ?>
                </div>
            <?php endif; ?>
            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-6 animate-fade-in">
                <?php $this->options->title() ?>
            </h1>
            <p class="text-lg md:text-xl text-gray-700 max-w-2xl mx-auto mb-8">
                <?php $this->options->description() ?>
            </p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="#content" class="inline-flex items-center px-6 py-3 rounded-full bg-dark text-white font-semibold shadow-md hover:bg-gray-800 transition">
                    开始探索 <i class="fas fa-arrow-down ml-2"></i>
                </a>
                <a href="<?php $this->options->siteUrl(); ?>archives.html" class="inline-flex items-center px-6 py-3 rounded-full bg-transparent border-2 border-dark text-dark font-semibold hover:bg-gray-100 transition">
                    查看归档 <i class="fas fa-archive ml-2"></i>
                </a>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 80" fill="none" class="w-full h-10 md:h-14">
                <path d="M0 64L80 69.3C160 75 320 85 480 80C640 75 800 53 960 48C1120 43 1280 53 1360 58.7L1440 64V120H1360C1280 120 1120 120 960 120C800 120 640 120 480 120C320 120 160 120 80 120H0V64Z" fill="#FFFFFF" />
            </svg>
        </div>
    </section>

    <!-- 搜索区域 -->
    <section class="relative z-10 px-4 -mt-8 mb-12 md:mb-20">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl p-2 md:p-3 flex flex-col md:flex-row gap-2 border border-gray-200">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="searchInput" placeholder="搜索吉他谱..." class="w-full py-4 pl-12 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent text-gray-700 placeholder-gray-400 bg-gray-50">
                </div>
                <button id="searchBtn" class="bg-accent hover:bg-accent-dark text-white font-semibold py-4 px-8 rounded-xl transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="fas fa-search"></i> 搜索
                </button>
            </div>
        </div>
    </section>

    <!-- 文章列表 + 侧边栏 -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-12" id="content">
        <div class="flex flex-col lg:flex-row gap-10">
            <!-- 左侧文章列表 -->
            <div class="flex-1 space-y-6">
                <?php if ($this->have()): ?>
                    <?php while($this->next()): ?>
                        <article class="bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start flex-wrap gap-2 mb-2">
                                <h2 class="text-base font-normal text-dark hover:text-accent transition">
                                    <a href="<?php $this->permalink() ?>"><?php $this->title() ?></a>
                                </h2>
                                <span class="text-sm text-gray-400 whitespace-nowrap"><i class="far fa-calendar-alt mr-1"></i><?php $this->date('Y-m-d'); ?></span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-500 mb-3">
                                <span><i class="fas fa-user mr-1"></i><?php $this->author(); ?></span>
                                <span><i class="fas fa-eye mr-1"></i><?php echo getPostViews($this); ?> 阅读</span>
                                <span><i class="fas fa-tag mr-1"></i><?php $this->category(','); ?></span>
                            </div>
                            <p class="text-gray-600 leading-relaxed mb-4"><?php $this->excerpt(120, '...'); ?></p>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <?php $tags = $this->tags; ?>
                                <?php if($tags): ?>
                                    <?php foreach ($tags as $tag): ?>
                                        <a href="<?php $tag['permalink']; ?>" class="inline-block px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full hover:bg-accent hover:text-white transition"><?php $tag['name']; ?></a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <a href="<?php $this->permalink() ?>" class="text-accent hover:text-accent-dark text-sm font-medium inline-flex items-center gap-1">
                                阅读全文 <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </article>
                    <?php endwhile; ?>
                    <!-- 分页 -->
                    <div class="flex justify-center mt-8">
                        <nav class="flex items-center space-x-2">
                            <?php $this->pageLink('上一页', 'prev'); ?>
                            <?php $this->pageLink('下一页', 'next'); ?>
                        </nav>
                    </div>
                <?php else: ?>
                    <div class="text-center py-20 text-gray-400">暂无文章</div>
                <?php endif; ?>
            </div>

            <!-- 右侧边栏 -->
            <?php $this->need('sidebar.php'); ?>
        </div>
    </div>
</main>

<?php $this->need('footer.php'); ?>

<script>
document.getElementById('searchBtn')?.addEventListener('click', () => {
    const keyword = document.getElementById('searchInput')?.value.trim();
    if (keyword) window.location.href = '<?php $this->options->siteUrl(); ?>search/' + encodeURIComponent(keyword);
});
document.getElementById('searchInput')?.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') document.getElementById('searchBtn')?.click();
});
</script>