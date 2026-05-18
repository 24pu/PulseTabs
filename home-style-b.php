<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<main class="flex-grow">
     <!-- 企业风格 Hero 大图区域（改为风格 A 类似的青绿色渐变） -->
    <?php
    $bannerGradient = get_banner_gradient_style();
    $isInline = (strpos($bannerGradient, 'style=') === 0);
    ?>
    <section class="relative <?php echo $isInline ? 'text-dark' : $bannerGradient . ' text-dark'; ?>" <?php if ($isInline) echo $bannerGradient; ?>>
        <div class="absolute inset-0 bg-black/5"></div>
        <div class="relative max-w-7xl mx-auto px-4 py-24 md:py-32 text-center">
            <div class="inline-block bg-white/30 backdrop-blur-sm rounded-full px-4 py-1.5 mb-6 text-sm font-medium text-dark">
                <i class="fas fa-guitar mr-2"></i> 24小时专业吉他平台
            </div>
            <h1 class="text-4xl md:text-6xl font-extrabold mb-6 animate-fade-in">
                让音乐成为你的日常
            </h1>
            <p class="text-lg md:text-xl text-gray-700 max-w-2xl mx-auto mb-10">
                海量吉他谱、专业调音器、和弦库，一站式吉他学习工具
            </p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="#tools" class="inline-flex items-center px-6 py-3 rounded-full bg-dark text-white font-semibold shadow-lg hover:bg-gray-800 transition">
                    探索工具 <i class="fas fa-arrow-right ml-2"></i>
                </a>
                <a href="<?php $this->options->siteUrl(); ?>archives.html" class="inline-flex items-center px-6 py-3 rounded-full border-2 border-dark text-dark font-semibold hover:bg-gray-100 transition">
                    浏览曲谱 <i class="fas fa-music ml-2"></i>
                </a>
            </div>
        </div>
        <!-- 底部波浪装饰（保持与风格 A 一致） -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 80" fill="none" class="w-full h-10 md:h-14">
                <path d="M0 64L80 69.3C160 75 320 85 480 80C640 75 800 53 960 48C1120 43 1280 53 1360 58.7L1440 64V120H1360C1280 120 1120 120 960 120C800 120 640 120 480 120C320 120 160 120 80 120H0V64Z" fill="#FFFFFF" />
            </svg>
        </div>
    </section>

    <!-- 核心工具/服务模块（四个卡片） -->
    <section class="py-16 md:py-20 bg-white" id="tools">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-dark">专业工具集</h2>
                <p class="text-gray-500 mt-3">辅助练习·快速入门</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-gray-50 rounded-2xl p-6 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tachometer-alt text-accent text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-dark mb-2">调音器</h3>
                    <p class="text-gray-500 text-sm">精准识别音高，多模式支持</p>
                    <a href="<?php $this->options->siteUrl(); ?>tiaoyinqi.html" class="inline-block mt-4 text-accent text-sm font-medium hover:underline">立即使用 →</a>
                </div>
                <div class="bg-gray-50 rounded-2xl p-6 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-heartbeat text-accent text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-dark mb-2">节拍器</h3>
                    <p class="text-gray-500 text-sm">自由调节速度，培养节奏感</p>
                    <a href="<?php $this->options->siteUrl(); ?>jiepaiqi.html" class="inline-block mt-4 text-accent text-sm font-medium hover:underline">立即使用 →</a>
                </div>
                <div class="bg-gray-50 rounded-2xl p-6 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-simple text-accent text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-dark mb-2">和弦图</h3>
                    <p class="text-gray-500 text-sm">直观查看和弦指法，智能推荐</p>
                    <a href="<?php $this->options->siteUrl(); ?>hexian.html" class="inline-block mt-4 text-accent text-sm font-medium hover:underline">立即使用 →</a>
                </div>
                <div class="bg-gray-50 rounded-2xl p-6 text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-alt text-accent text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-dark mb-2">简谱编辑器</h3>
                    <p class="text-gray-500 text-sm">在线创作分享，一键转调</p>
                    <a href="<?php $this->options->siteUrl(); ?>jianpu.html" class="inline-block mt-4 text-accent text-sm font-medium hover:underline">立即使用 →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 关于品牌 / 特色展示（双栏） -->
    <section class="py-16 md:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-12 items-center">
                <div class="flex-1">
                    <h2 class="text-3xl font-bold text-dark mb-4">为什么选择24PU？</h2>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        24PU致力于为吉他爱好者提供最专业、最便捷的练习工具和海量谱库。从初学者到进阶玩家，你都能在这里找到适合自己的学习资源。
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3"><i class="fas fa-check-circle text-accent"></i> <span>数千首高品质吉他谱，持续更新</span></li>
                        <li class="flex items-center gap-3"><i class="fas fa-check-circle text-accent"></i> <span>免费调音器、节拍器、和弦库</span></li>
                        <li class="flex items-center gap-3"><i class="fas fa-check-circle text-accent"></i> <span>社区互动，与琴友交流心得</span></li>
                    </ul>
                </div>
                <div class="flex-1">
                    <img src="https://placehold.co/600x400/f5f5f5/FF5E00?text=Guitar+Image" alt="关于我们" class="rounded-2xl shadow-lg w-full">
                </div>
            </div>
        </div>
    </section>

    <!-- 最新吉他谱/文章列表（2列卡片） -->
    <section class="py-16 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-10">
                <h2 class="text-3xl font-bold text-dark">最新吉他谱</h2>
                <a href="<?php $this->options->siteUrl(); ?>archives.html" class="text-accent hover:underline">查看全部 →</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if ($this->have()): ?>
                    <?php while($this->next()): ?>
                        <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                            <?php if ($this->fields->thumbnail): ?>
                                <img src="<?php $this->fields->thumbnail(); ?>" class="w-full h-48 object-cover" alt="<?php $this->title(); ?>">
                            <?php else: ?>
                                <div class="w-full h-48 bg-gradient-to-br from-accent-light to-accent/30 flex items-center justify-center text-white">
                                    <i class="fas fa-music text-5xl"></i>
                                </div>
                            <?php endif; ?>
                            <div class="p-5">
                                <h3 class="text-xl font-bold text-dark mb-2">
                                    <a href="<?php $this->permalink(); ?>" class="hover:text-accent"><?php $this->title(); ?></a>
                                </h3>
                                <div class="text-sm text-gray-500 mb-3">
                                    <span><i class="far fa-calendar-alt mr-1"></i> <?php $this->date('Y-m-d'); ?></span>
                                    <span class="ml-3"><i class="fas fa-eye mr-1"></i> <?php echo getPostViews($this); ?> 阅读</span>
                                </div>
                                <p class="text-gray-600 line-clamp-2"><?php $this->excerpt(80, '...'); ?></p>
                                <a href="<?php $this->permalink(); ?>" class="inline-block mt-4 text-accent font-medium hover:underline">阅读全文 →</a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-gray-500 col-span-3 text-center">暂无吉他谱，请稍后再来~</p>
                <?php endif; ?>
            </div>
            <div class="mt-10">
                <?php $this->pageNav(); ?>
            </div>
        </div>
    </section>

    <!-- CTA 区域 -->
    <section class="py-16 bg-accent/10">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="text-2xl md:text-3xl font-bold text-dark mb-4">加入我们，一起进步</h2>
            <p class="text-gray-600 mb-6">注册账号，收藏你喜欢的吉他谱，参与社区讨论</p>
            <a href="<?php $this->options->siteUrl(); ?>register" class="inline-block bg-accent text-white px-8 py-3 rounded-full font-semibold hover:bg-accent-dark transition shadow-md">
                立即注册
            </a>
        </div>
    </section>
</main>