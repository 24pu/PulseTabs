<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php
/**
 * 文章归档
 *
 * @package custom
 */
?>
<?php $this->need('header.php'); ?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <header class="text-center mb-12">
        <h1 class="text-3xl font-bold text-dark mb-2">文章归档</h1>
        <p class="text-gray-500">所有过往，皆为序章。</p>
    </header>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <?php
        // 获取所有已发布的文章
        $db = Typecho_Db::get();
        $query = $db->select('cid', 'title', 'created', 'slug')
                    ->from('table.contents')
                    ->where('type = ?', 'post')
                    ->where('status = ?', 'publish')
                    ->order('created', Typecho_Db::SORT_DESC);
        $articles = $db->fetchAll($query);

        if (empty($articles)) {
            echo '<div class="text-center text-gray-400 py-8">暂无文章</div>';
        } else {
            $last_year = 0;
            echo '<div class="space-y-8">';
            foreach ($articles as $article) {
                $year = date('Y', $article['created']);
                // 获取文章的分类缩略名（取第一个分类）
                $cat_slug = 'uncategorized';
                $selectCat = $db->select('m.slug')
                                 ->from('table.relationships AS r')
                                 ->join('table.metas AS m', 'r.mid = m.mid')
                                 ->where('r.cid = ?', $article['cid'])
                                 ->where('m.type = ?', 'category')
                                 ->order('r.mid', 'ASC')
                                 ->limit(1);
                $catRow = $db->fetchRow($selectCat);
                if ($catRow && !empty($catRow['slug'])) {
                    $cat_slug = $catRow['slug'];
                }
                
                // 构建链接： /分类缩略名/文章缩略名.html
                $article_url = rtrim($this->options->siteUrl, '/') . '/' . $cat_slug . '/' . $article['slug'] . '.html';
                
                // 按年份分组输出
                if ($year != $last_year) {
                    if ($last_year != 0) echo '</div></div>';
                    echo '<div>';
                    echo '<h2 class="text-2xl font-bold text-dark border-l-4 border-accent pl-3 mb-4">' . $year . ' 年</h2>';
                    echo '<div class="pl-2 space-y-3">';
                    $last_year = $year;
                }
                
                echo '<div class="flex justify-between items-center hover:bg-gray-50 p-2 rounded-lg transition">';
                echo '<a href="' . htmlspecialchars($article_url) . '" class="text-gray-700 hover:text-accent">' . htmlspecialchars($article['title']) . '</a>';
                echo '<span class="text-sm text-gray-400"><i class="far fa-calendar-alt mr-1"></i>' . date('Y年m月d日', $article['created']) . '</span>';
                echo '</div>';
            }
            echo '</div></div>';
        }
        ?>
    </div>
</div>

<?php $this->need('footer.php'); ?>