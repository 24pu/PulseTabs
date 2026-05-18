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

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: { DEFAULT: '#FF5E00', dark: '#E05000', light: '#FFF2E6' },
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
</head>
<body class="bg-white text-dark">

<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4 md:py-5">
            <!-- Logo 区域（尺寸加倍） -->
         
        <div class="flex items-center space-x-3">
            <?php if ($this->options->logoUrl): ?>
                <!-- 图片 Logo：原 h-9 (36px) → 改为 h-[54px] 或 h-14 (56px) -->
                 <a href="<?php $this->options->siteUrl(); ?>">
                <img src="<?php $this->options->logoUrl(); ?>" alt="<?php $this->options->title(); ?>" class="h-[68px] w-auto">
               </a>
                <?php else: ?>
                <!-- 文字 Logo：容器原 w-9 h-9 → 改为 w-14 h-14 (56px) -->
                 
                <div class="w-14 h-14 rounded-xl bg-dark flex items-center justify-center shadow-sm">
                    <i class="fas fa-waveform text-white text-2xl"></i> <!-- 图标略微加大 -->
                </div>
                <!-- 字体原 text-2xl → 改为 text-3xl，稍微加粗 -->
                 
                <a href="<?php $this->options->siteUrl(); ?>"> <span class="text-3xl font-bold tracking-tight text-dark">PULSE<span class="text-accent">|TABS</span></span></a>
            <?php endif; ?>
        </div>

            <!-- 桌面端导航 + 用户状态（后端实现） -->
            <div class="hidden md:flex items-center space-x-8">
                <nav class="flex space-x-8">
                    <a href="<?php $this->options->siteUrl(); ?>" class="<?php if($this->is('index')) echo 'text-dark border-b-2 border-accent pb-0.5'; else echo 'text-gray-600 hover:text-accent'; ?> font-medium transition">首页</a>
                    <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
                    <?php while($pages->next()): ?>
                        <a href="<?php $pages->permalink(); ?>" class="text-gray-600 hover:text-accent transition"><?php $pages->title(); ?></a>
                    <?php endwhile; ?>
                </nav>

                <!-- 用户状态区域（后端渲染） -->
                <div class="relative" id="userContainer">
                    <?php if($this->user->hasLogin()): ?>
                        <!-- 已登录状态 -->
                        <div id="userTrigger" class="flex items-center gap-2 bg-gray-100 rounded-full pl-2 pr-3 py-1 cursor-pointer hover:bg-gray-200 transition">
                           <?php $email = $this->user->mail; ?>
                            <img src="<?php echo getGravatar($email, 48); ?>" class="w-9 h-9 rounded-full mr-2">
                            <span class="text-dark font-medium text-sm"><?php $this->user->screenName(); ?></span>
                            <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                        </div>
                        <!-- 下拉菜单 -->
                        <div id="userDropdown" class="user-dropdown absolute right-0 mt-2 w-36 bg-white border border-gray-200 rounded-lg shadow-lg py-1 hidden z-50">
                            <!-- 在用户下拉菜单中添加 -->
                            <a href="<?php $this->options->siteUrl(); ?>dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-accent/10 hover:text-accent transition">
                            <i class="fas fa-tachometer-alt mr-2"></i> 用户中心
                        </a>
                            <a href="<?php $this->options->adminUrl(); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-accent/10 hover:text-accent transition">进入后台</a>
                            <a href="<?php $this->options->logoutUrl(); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-accent/10 hover:text-accent transition">退出登录</a>
                        </div>
                    <?php else: ?>
                        <!-- 未登录状态 -->
                        <a href="<?php $this->options->adminUrl('login.php'); ?>" class="inline-flex items-center px-4 py-2 rounded-full bg-accent text-white text-sm font-semibold hover:bg-accent-dark transition">
                            登录
                        </a>
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
        <a href="<?php $this->options->siteUrl(); ?>" class="block text-dark font-medium">首页</a>
        <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
        <?php while($pages->next()): ?>
            <a href="<?php $pages->permalink(); ?>" class="block text-gray-600"><?php $pages->title(); ?></a>
        <?php endwhile; ?>
        <div class="pt-2 border-t border-gray-100" id="mobileUserState">
            <!-- 移动端用户状态，与桌面端逻辑同步 -->
            <?php if($this->user->hasLogin()): ?>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <?php $email = $this->user->mail; ?>
                        <img src="<?php echo getGravatar($email, 40); ?>" class="w-8 h-8 rounded-full">
                        <span class="text-dark font-medium"><?php $this->user->screenName(); ?></span>
                    </div>
                    <div class="flex gap-3">
                         <a href="<?php $this->options->siteUrl(); ?>" class="text-accent text-sm">我的主页</a>
                        <a href="<?php $this->options->adminUrl(); ?>" class="text-accent text-sm">后台</a>
                        <a href="<?php $this->options->logoutUrl(); ?>" class="text-gray-500 text-sm">退出</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php $this->options->adminUrl('login.php'); ?>" class="inline-block w-full text-center px-4 py-2 rounded-full bg-accent text-white text-sm font-semibold">登录</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- 交互脚本（下拉菜单、移动端菜单） -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 桌面端用户下拉菜单
        const userTrigger = document.getElementById('userTrigger');
        const userDropdown = document.getElementById('userDropdown');
        if (userTrigger && userDropdown) {
            userTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });
            // 点击页面其他地方关闭下拉菜单
            document.addEventListener('click', function() {
                userDropdown.classList.add('hidden');
            });
            userDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

         // 移动端菜单
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