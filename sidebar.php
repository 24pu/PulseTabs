<div class="lg:w-80 space-y-6">
    <!-- 分类目录 -->
    <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 transition" style="transition: box-shadow 0.2s;">
        <h3 class="font-bold text-dark text-lg flex items-center gap-2 mb-4">
            <i class="fas fa-folder-open text-accent"></i> 分类目录
        </h3>
        <ul class="space-y-2 text-gray-600">
            <?php $this->widget('Widget_Metas_Category_List')->parse('<li><a href="{permalink}" class="hover:text-accent transition">{name}</a> <span class="text-xs text-gray-400">({count})</span></li>'); ?>
        </ul>
    </div>

    <!-- 热门标签 -->
    <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
        <h3 class="font-bold text-dark text-lg flex items-center gap-2 mb-4">
            <i class="fas fa-tags text-accent"></i> 热门标签
        </h3>
        <div class="flex flex-wrap gap-2">
            <?php $this->widget('Widget_Metas_Tag_Cloud', 'sort=count&ignoreZeroCount=1&limit=20')->to($tags); ?>
            <?php while($tags->next()): ?>
                <a href="<?php $tags->permalink(); ?>" class="inline-block px-3 py-1 bg-white border border-gray-200 rounded-full text-xs text-gray-600 hover:bg-accent hover:text-white hover:border-accent transition"><?php $tags->name(); ?></a>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- 友情链接 -->
    <?php
    $friendLinks = $this->options->friendLinks;
    if ($friendLinks):
        $links = explode("\n", trim($friendLinks));
        $validLinks = array_filter($links, function($line) { return trim($line) !== ''; });
    ?>
        <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
            <h3 class="font-bold text-dark text-lg flex items-center gap-2 mb-4">
                <i class="fas fa-link text-accent"></i> 友情链接
            </h3>
            <ul class="space-y-2 text-sm">
                <?php foreach ($validLinks as $link): ?>
                    <?php
                    $parts = explode('|', $link);
                    $name = trim($parts[0]);
                    $url = isset($parts[1]) ? trim($parts[1]) : '#';
                    ?>
                    <li><a href="<?php echo $url; ?>" target="_blank" class="text-gray-600 hover:text-accent transition"><?php echo $name; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>