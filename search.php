<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-dark">搜索：<?php $this->archiveTitle(array('search'=>_t('%s')), '', ''); ?></h1>
        <p class="text-gray-500 mt-2">共 <?php echo $this->getTotal(); ?> 篇文章</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-10">
        <div class="flex-1 space-y-6">
            <?php if ($this->have()): ?>
                <?php while($this->next()): ?>
                    <article class="bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-md transition">
                        <div class="flex justify-between items-start flex-wrap gap-2 mb-2">
                            <h2 class="text-xl font-semibold text-dark hover:text-accent transition">
                                <a href="<?php $this->permalink() ?>"><?php $this->title() ?></a>
                            </h2>
                            <span class="text-sm text-gray-400"><i class="far fa-calendar-alt mr-1"></i><?php $this->date('Y-m-d'); ?></span>
                        </div>
                        <p class="text-gray-600 leading-relaxed"><?php $this->excerpt(100, '...'); ?></p>
                    </article>
                <?php endwhile; ?>
                <div class="flex justify-center mt-8">
                    <?php $this->pageLink('&laquo; 上一页', 'prev'); ?>
                    <?php $this->pageLink('下一页 &raquo;', 'next'); ?>
                </div>
            <?php else: ?>
                <div class="text-center py-20 text-gray-400">未找到相关内容</div>
            <?php endif; ?>
        </div>
        <?php $this->need('sidebar.php'); ?>
    </div>
</main>

<?php $this->need('footer.php'); ?>