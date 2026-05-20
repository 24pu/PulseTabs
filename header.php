<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php $this->archiveTitle(' &raquo; ', '', ' - '); ?><?php $this->options->title(); ?></title>
    <meta name="description" content="<?php $this->options->description() ?>">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- 主题自定义样式 -->
    <link rel="stylesheet" href="<?php $this->options->themeUrl('assets/css/custom.css'); ?>">
    
    <!-- Favicon 输出 -->
    <?php if ($this->options->faviconUrl): ?>
        <link rel="icon" href="<?php $this->options->faviconUrl(); ?>" type="image/x-icon">
        <link rel="shortcut icon" href="<?php $this->options->faviconUrl(); ?>">
    <?php endif; ?>

    <?php
    $colorScheme = $this->options->colorScheme ?: 'orange';
    $customAccent = $this->options->customAccent ?: '#FF5E00';

    if ($colorScheme === 'blue') {
        $accent = '#3B82F6';      // Tailwind blue-500
        $accentDark = '#2563EB';  // blue-600
        $accentLight = '#EFF6FF'; // blue-50
    } elseif ($colorScheme === 'emerald') {
        $accent = '#10B981';      // Tailwind emerald-500
        $accentDark = '#059669';  // emerald-600
        $accentLight = '#ECFDF5'; // emerald-50
    } elseif ($colorScheme === 'cyan') {
        $accent = '#06B6D4';      // Tailwind cyan-500
        $accentDark = '#0891B2';  // cyan-600
        $accentLight = '#ECFEFF'; // cyan-50
    } elseif ($colorScheme === 'custom' && preg_match('/^#[a-f0-9]{6}$/i', $customAccent)) {
        $accent = $customAccent;
        // 简单生成深色和浅色版本（使用 CSS 透明度，这里只定义主色，深色和浅色用 CSS 处理）
        $accentDark = $accent;
        $accentLight = $accent . '20';
    } else {
        // 默认橙色
        $accent = '#FF5E00';
        $accentDark = '#E05000';
        $accentLight = '#FFF2E6';
    }
    ?>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: { DEFAULT: '<?php echo $accent; ?>', dark: '<?php echo $accentDark; ?>', light: '<?php echo $accentLight; ?>' },
                        dark: '#1A1A1A',
                    },
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                    animation: { 'fade-in': 'fadeIn 0.6s ease-out' },
                    keyframes: { fadeIn: { from: { opacity: 0, transform: 'translateY(10px)' }, to: { opacity: 1, transform: 'translateY(0)' } } }
                }
            }
        }
    </script>

    <style>
    /* 用户下拉菜单样式 */
    .user-dropdown {
        transition: opacity 0.15s ease, visibility 0.15s;
    }
    .user-dropdown.hidden {
        opacity: 0;
        visibility: hidden;
    }
    /* 移动端菜单初始隐藏（强制） */
    #mobileMenu {
        display: none;
    }
    #mobileMenu.show {
        display: block;
    }
    @media (min-width: 768px) {
        #mobileMenu {
            display: none !important;
        }
    }
    </style>

    <?php $this->header(); ?>
    <!-- 统计代码 -->
    <?php if ($this->options->analyticsCode): ?>
        <?php echo $this->options->analyticsCode; ?>
    <?php endif; ?>

    <?php
    // 获取所有页面并构建父子关系
    $allPages = [];
    $pageWidget = $this->widget('Widget_Contents_Page_List');
    while ($pageWidget->next()) {
        $allPages[] = [
            'cid'       => $pageWidget->cid,
            'slug'      => $pageWidget->slug,
            'title'     => $pageWidget->title,
            'permalink' => $pageWidget->permalink,
            'parent'    => $pageWidget->parent,
            'order'     => $pageWidget->order
        ];
    }

    // 分组
    $children = [];
    $topPages = [];
    foreach ($allPages as $page) {
        if (empty($page['parent'])) {
            $topPages[] = $page;
        } else {
            $children[$page['parent']][] = $page;
        }
    }

    // 按 order 排序顶级页面
    usort($topPages, function ($a, $b) { return $a['order'] <=> $b['order']; });

    // 当前页面及其父级 slug（用于高亮）
    $parentSlug = null;
    if ($this->is('page')) {
        if ($this->parent) {
            $parentSlug = $this->parent->slug;
        }
    }
    ?>
</head>
<body class="bg-white text-dark">

<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4 md:py-5">
            <!-- Logo 区域（尺寸加倍） -->
            <div class="flex items-center space-x-3">
                <?php if ($this->options->logoUrl): ?>
                    <a href="<?php $this->options->siteUrl(); ?>">
                        <img src="<?php $this->options->logoUrl(); ?>" alt="<?php $this->options->title(); ?>" class="h-[68px] w-auto">
                    </a>
                <?php else: ?>
                    <div class="w-14 h-14 rounded-xl bg-dark flex items-center justify-center shadow-sm">
                        <i class="fas fa-waveform text-white text-2xl"></i>
                    </div>
                    <a href="<?php $this->options->siteUrl(); ?>">
                        <span class="text-3xl font-bold tracking-tight text-dark">PULSE<span class="text-accent">|TABS</span></span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- 桌面端导航 + 用户状态（后端实现） -->
            <div class="hidden md:flex items-center space-x-8">
                <nav class="flex space-x-8">
                <a href="<?php $this->options->siteUrl(); ?>" class="<?php echo get_nav_class('index', null, false); ?> transition">
                    首页
                </a>
                <?php foreach ($topPages as $page): ?>
                    <?php if (isset($children[$page['cid']])): ?>
                        <!-- 有子页面的菜单项 -->
                        <div class="relative group">
                            <a href="<?php echo $page['permalink']; ?>" 
                            class="<?php echo get_nav_class('page', $page['slug'], false, $parentSlug); ?> inline-flex items-center transition cursor-pointer">
                                <?php echo $page['title']; ?>
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </a>
                            <div class="absolute left-0 mt-1 w-40 bg-white border border-gray-200 rounded-lg shadow-lg py-1 hidden group-hover:block z-50">
                                <?php foreach ($children[$page['cid']] as $child): ?>
                                    <a href="<?php echo $child['permalink']; ?>" 
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-accent/10 hover:text-accent transition <?php echo get_nav_class('page', $child['slug'], false, $parentSlug); ?>">
                                        <?php echo $child['title']; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- 无子页面的普通链接 -->
                        <a href="<?php echo $page['permalink']; ?>" 
                        class="<?php echo get_nav_class('page', $page['slug'], false, $parentSlug); ?> transition">
                            <?php echo $page['title']; ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>

                <!-- 用户状态区域（后端渲染） -->
                <div class="relative" id="userContainer">
                    <?php if($this->user->hasLogin()): ?>
                        <div id="userTrigger" class="flex items-center gap-2 bg-gray-100 rounded-full pl-2 pr-3 py-1 cursor-pointer hover:bg-gray-200 transition">
                            <?php $email = $this->user->mail; ?>
                            <img src="<?php echo getGravatar($email, 48); ?>" class="w-9 h-9 rounded-full mr-2">
                            <span class="text-dark font-medium text-sm"><?php $this->user->screenName(); ?></span>
                            <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                        </div>
                        <div id="userDropdown" class="user-dropdown absolute right-0 mt-2 w-36 bg-white border border-gray-200 rounded-lg shadow-lg py-1 hidden z-50">
                            <a href="<?php $this->options->siteUrl(); ?>dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-accent/10 hover:text-accent transition">
                                <i class="fas fa-tachometer-alt mr-2"></i> 用户中心
                            </a>
                            <a href="<?php $this->options->adminUrl(); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-accent/10 hover:text-accent transition">进入后台</a>
                            <a href="<?php $this->options->logoutUrl(); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-accent/10 hover:text-accent transition">退出登录</a>
                        </div>
                    <?php else: ?>
                        <?php 
                        $currentPath = $this->request->getPathInfo();
                        $isLoginPage = ($currentPath === '/login');
                        $isRegisterPage = ($currentPath === '/register');
                        ?>
                        <div class="flex items-center space-x-3">
                            <a href="<?php $this->options->siteUrl(); ?>login" 
                               class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold transition 
                                      <?php echo $isLoginPage ? 'bg-accent text-white' : 'bg-white border border-accent text-accent hover:bg-accent/10'; ?>">
                                登录
                            </a>
                            <a href="<?php $this->options->siteUrl(); ?>register" 
                               class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold transition 
                                      <?php echo $isRegisterPage ? 'bg-accent text-white' : 'bg-white border border-accent text-accent hover:bg-accent/10'; ?>">
                                注册
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 移动端菜单按钮 -->
            <div class="md:hidden">
                <button id="mobileMenuBtn" class="text-dark text-2xl"><i class="fas fa-bars"></i></button>
            </div>
        </div>
    </div>

    <!-- 移动端滑动菜单 -->
    <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-gray-200 px-4 pb-4 pt-2 space-y-3">
        <a href="<?php $this->options->siteUrl(); ?>" class="<?php echo get_nav_class('index', null, true); ?>">
            首页
        </a>
        <?php foreach ($topPages as $page): ?>
            <?php if (isset($children[$page['cid']])): ?>
                <details class="group">
                    <summary class="list-none cursor-pointer flex items-center justify-between <?php echo get_nav_class('page', $page['slug'], true, $parentSlug); ?>">
                        <?php echo $page['title']; ?>
                        <i class="fas fa-chevron-down ml-1 text-xs transition-transform group-open:rotate-180"></i>
                    </summary>
                    <div class="ml-4 space-y-1 mt-1">
                        <?php foreach ($children[$page['cid']] as $child): ?>
                            <a href="<?php echo $child['permalink']; ?>" 
                            class="block <?php echo get_nav_class('page', $child['slug'], true, $parentSlug); ?>">
                                <?php echo $child['title']; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </details>
            <?php else: ?>
                <a href="<?php echo $page['permalink']; ?>" 
                class="<?php echo get_nav_class('page', $page['slug'], true, $parentSlug); ?>">
                    <?php echo $page['title']; ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
        <div class="pt-2 border-t border-gray-100" id="mobileUserState">
            <?php if($this->user->hasLogin()): ?>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <?php $email = $this->user->mail; ?>
                        <img src="<?php echo getGravatar($email, 40); ?>" class="w-8 h-8 rounded-full">
                        <span class="text-dark font-medium"><?php $this->user->screenName(); ?></span>
                    </div>
                    <div class="flex gap-3">
                        <a href="<?php $this->options->siteUrl(); ?>dashboard" class="text-accent text-sm">用户中心</a>
                        <a href="<?php $this->options->adminUrl(); ?>" class="text-accent text-sm">后台</a>
                        <a href="<?php $this->options->logoutUrl(); ?>" class="text-gray-500 text-sm">退出</a>
                    </div>
                </div>
            <?php else: ?>
                <?php 
                $currentPath = $this->request->getPathInfo();
                $isLoginPage = ($currentPath === '/login');
                $isRegisterPage = ($currentPath === '/register');
                ?>
                <div class="flex flex-col space-y-2">
                    <a href="<?php $this->options->siteUrl(); ?>login" 
                       class="block text-center px-4 py-2 rounded-full text-sm font-semibold transition 
                              <?php echo $isLoginPage ? 'bg-accent text-white' : 'bg-white border border-accent text-accent hover:bg-accent/10'; ?>">
                        登录
                    </a>
                    <a href="<?php $this->options->siteUrl(); ?>register" 
                       class="block text-center px-4 py-2 rounded-full text-sm font-semibold transition 
                              <?php echo $isRegisterPage ? 'bg-accent text-white' : 'bg-white border border-accent text-accent hover:bg-accent/10'; ?>">
                        注册
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- 交互脚本（下拉菜单、移动端菜单） -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userTrigger = document.getElementById('userTrigger');
        const userDropdown = document.getElementById('userDropdown');
        if (userTrigger && userDropdown) {
            userTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', function() {
                userDropdown.classList.add('hidden');
            });
            userDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        const mobileBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        if (mobileBtn && mobileMenu) {
            mobileBtn.addEventListener('click', function(e) {
                e.preventDefault();
                mobileMenu.classList.toggle('show');
            });
        }
    });
</script>