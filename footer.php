<footer class="bg-dark text-gray-400 pt-10 pb-6 mt-12">
    <div class="max-w-7xl mx-auto px-4 text-center text-sm">
        <div class="flex justify-center space-x-6 mb-4">
            <?php if ($this->options->socialGithub): ?>
                <a href="<?php $this->options->socialGithub(); ?>" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition"><i class="fab fa-github text-lg"></i></a>
            <?php endif; ?>
            <?php if ($this->options->socialTwitter): ?>
                <a href="<?php $this->options->socialTwitter(); ?>" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition"><i class="fab fa-twitter text-lg"></i></a>
            <?php endif; ?>
        </div>
        <p><?php $this->options->footerCopyright(); ?></p>
        <p class="mt-2 text-xs">Powered by <a href="http://typecho.org" target="_blank" class="hover:text-white transition">Typecho</a> · Theme PulseTabs</p>
    </div>
</footer>

<!-- 主题脚本 -->
<script src="<?php $this->options->themeUrl('assets/js/main.js'); ?>"></script>
<?php $this->footer(); ?>
</body>
</html>