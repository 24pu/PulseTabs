<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<?php getPostView($this); // 更新浏览量，函数已在 functions.php 中定义 ?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col lg:flex-row gap-10">
        <!-- 主要文章内容区 -->
        <div class="flex-1">
            <div class="mb-6">
                <a href="<?php $this->options->siteUrl(); ?>" class="inline-flex items-center text-accent hover:text-accent-dark gap-2">
                    <i class="fas fa-arrow-left"></i> 返回首页
                </a>
            </div>

            <article class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h1 class="text-3xl md:text34xl font-bold text-dark mb-4"><?php $this->title() ?></h1>
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-8 pb-4 border-b border-gray-100">
                    <span><i class="far fa-calendar-alt mr-1 text-accent"></i> <?php $this->date('Y-m-d'); ?></span>
                    <span><i class="fas fa-user mr-1 text-accent"></i> <?php $this->author(); ?></span>
                    <span><i class="fas fa-eye mr-1 text-accent"></i> <?php echo getPostViews($this); ?> 阅读</span>
                    <span><i class="fas fa-folder-open mr-1 text-accent"></i> <?php $this->category(','); ?></span>
                </div>
                <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed space-y-6">
                    <?php $this->content(); ?>
                </div>
                <div class="flex gap-4 mt-6">
                    <?php if (UserCenter_Plugin::can('favorite')): ?>
                    <button class="favorite-btn text-gray-500 hover:text-red-500 transition" data-cid="<?php echo $this->cid; ?>">
                        <i class="far fa-heart"></i> 收藏
                    </button>
                    <?php endif; ?>
                    <?php if (UserCenter_Plugin::can('like')): ?>
                    <button class="like-btn text-gray-500 hover:text-blue-500 transition" data-cid="<?php echo $this->cid; ?>">
                        <i class="far fa-thumbs-up"></i> 点赞
                    </button>
                    <?php endif; ?>
                </div>

                <script>
                document.querySelectorAll('.favorite-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const cid = this.dataset.cid;
                        fetch('<?php $this->options->siteUrl(); ?>ajax?action=favorite&cid=' + cid, { method: 'POST' })
                            .then(res => res.json())
                            .then(data => {
                                if (data.code === 200) {
                                    alert(data.msg);
                                    if (data.action === 'favorited') {
                                        btn.innerHTML = '<i class="fas fa-heart"></i> 已收藏';
                                        btn.classList.add('text-red-500');
                                    } else {
                                        btn.innerHTML = '<i class="far fa-heart"></i> 收藏';
                                        btn.classList.remove('text-red-500');
                                    }
                                } else if (data.code === 401) {
                                    window.location.href = '<?php $this->options->siteUrl(); ?>login';
                                } else {
                                    alert(data.msg);
                                }
                            });
                    });
                });

                document.querySelectorAll('.like-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const cid = this.dataset.cid;
                        fetch('<?php $this->options->siteUrl(); ?>ajax?action=like&cid=' + cid, { method: 'POST' })
                            .then(res => res.json())
                            .then(data => {
                                if (data.code === 200) {
                                    alert(data.msg);
                                    if (data.action === 'liked') {
                                        btn.innerHTML = '<i class="fas fa-thumbs-up"></i> 已点赞';
                                        btn.classList.add('text-blue-500');
                                    } else {
                                        btn.innerHTML = '<i class="far fa-thumbs-up"></i> 点赞';
                                        btn.classList.remove('text-blue-500');
                                    }
                                } else if (data.code === 401) {
                                    window.location.href = '<?php $this->options->siteUrl(); ?>login';
                                } else {
                                    alert(data.msg);
                                }
                            });
                    });
                });
                </script>

                <!-- 文章标签 -->
                <?php if($this->tags): ?>
                <div class="mt-8 pt-4 border-t border-gray-100">
                    <div class="flex flex-wrap gap-2">
                        <?php $this->tags('<span class="inline-block px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full hover:bg-accent hover:text-white transition">#', '</span> ', true); ?>
                    </div>
                </div>
                <?php endif; ?>
            </article>

            <!-- 评论区域 -->
            <?php $this->need('comments.php'); ?>
        </div>

        <!-- 右侧边栏（文章页也显示） -->
        <?php $this->need('sidebar.php'); ?>
    </div>
</main>

<?php $this->need('footer.php'); ?>