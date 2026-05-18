/**
 * PulseTabs 主题脚本
 */

document.addEventListener('DOMContentLoaded', function() {
    // 移动端菜单
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
});