<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col lg:flex-row gap-10">
        <!-- 左侧：文章列表 -->
        <div class="flex-1">
            <!-- 归档标题 -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-dark">
                    <?php $this->archiveTitle(array(
                        'category' => _t('分类：%s'),
                        'search'   => _t('搜索：%s'),
                        'tag'      => _t('标签：%s'),
                        'author'   => _t('作者：%s'),
                        'date'     => _t('日期：%s')
                    ), '', ''); ?>
                </h1>
                <?php if ($this->is('date')): ?>
                    <p class="text-gray-500 mt-2">按时间归档的文章列表</p>
                <?php elseif ($this->is('category')): ?>
                    <p class="text-gray-500 mt-2">分类下的所有文章</p>
                <?php elseif ($this->is('tag')): ?>
                    <p class="text-gray-500 mt-2">标签下的所有文章</p>
                <?php elseif ($this->is('author')): ?>
                    <p class="text-gray-500 mt-2">作者发布的全部文章</p>
                <?php else: ?>
                    <p class="text-gray-500 mt-2">归档文章列表</p>
                <?php endif; ?>
            </div>

            <?php if ($this->have()): ?>
                <div class="space-y-6">
                    <?php while($this->next()): ?>
                        <article class="bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-md transition">
                            <div class="flex justify-between items-start flex-wrap gap-2 mb-2">
                                <h2 class="text-xl font-semibold text-dark hover:text-accent transition">
                                    <a href="<?php $this->permalink() ?>"><?php $this->title() ?></a>
                                </h2>
                                <span class="text-sm text-gray-400 whitespace-nowrap">
                                    <i class="far fa-calendar-alt mr-1"></i><?php $this->date('Y-m-d'); ?>
                                </span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-500 mb-3">
                                <span><i class="fas fa-user mr-1"></i><?php $this->author(); ?></span>
                                <span><i class="fas fa-folder-open mr-1"></i><?php $this->category(','); ?></span>
                            </div>
                            <p class="text-gray-600 leading-relaxed mb-4"><?php $this->excerpt(120, '...'); ?></p>
                            <?php if ($this->tags): ?>
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <?php $this->tags('<span class="inline-block px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">#', '</span> ', true); ?>
                                </div>
                            <?php endif; ?>
                            <a href="<?php $this->permalink() ?>" class="text-accent hover:text-accent-dark text-sm font-medium inline-flex items-center gap-1">
                                阅读全文 <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </article>
                    <?php endwhile; ?>
                </div>

                <!-- 分页 -->
                <div class="flex justify-center mt-10">
                    <nav class="flex items-center space-x-2">
                        <?php $this->pageLink('上一页', 'prev'); ?>
                        <?php $this->pageLink('下一页', 'next'); ?>
                    </nav>
                </div>
            <?php else: ?>
                <div class="bg-gray-50 rounded-xl p-12 text-center text-gray-400">
                    <i class="fas fa-archive text-4xl mb-3 block"></i>
                    <p>暂无文章</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- 右侧边栏 -->
        <?php $this->need('sidebar.php'); ?>
    </div>
</main>

<?php $this->need('footer.php'); ?>