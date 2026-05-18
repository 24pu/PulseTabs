<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<div id="comments" class="mt-10">
    <h3 class="text-xl font-bold text-dark mb-6 flex items-center gap-2">
        <i class="fas fa-comments text-accent"></i>
        评论 <span class="text-gray-400 text-base font-normal">(<?php $this->commentsNum('%d'); ?>)</span>
    </h3>

    <?php $this->comments()->to($comments); ?>

    <!-- 评论列表 -->
    <?php if ($comments->have()): ?>
        <ul class="space-y-6">
            <?php $comments->listComments(array(
                'before'        => '',
                'after'         => '',
                'beforeAuthor'  => '',
                'afterAuthor'   => '',
                'beforeDate'    => '',
                'afterDate'     => '',
                'dateFormat'    => 'Y-m-d H:i',
                'avatarSize'    => 48,
                'defaultAvatar' => null,
                'commentStatus' => '',
                'replyWord'     => '<i class="fas fa-reply"></i> 回复',
                'commentsTag'   => 'ul',
                'commentsClass' => 'comment-list',
                'commentTag'    => 'li',
                'commentClass'  => 'comment',
            )); ?>
        </ul>

        <!-- 分页 -->
        <?php if ($comments->have()) : ?>
            <div class="flex justify-center mt-8">
                <nav class="flex items-center space-x-2">
                    <?php $comments->pageLink('上一页', 'prev'); ?>
                    <?php $comments->pageLink('下一页', 'next'); ?>
                </nav>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="bg-gray-50 rounded-xl p-8 text-center text-gray-400">
            <i class="fas fa-comment-dots text-3xl mb-2 block"></i>
            暂无评论，快来抢沙发吧~
        </div>
    <?php endif; ?>

    <!-- 评论表单 -->
    <?php if($this->allow('comment')): ?>
    <div id="<?php $this->respondId(); ?>" class="mt-10 pt-6 border-t border-gray-100">
        <h4 class="text-lg font-bold text-dark mb-4">发表评论</h4>
        <form method="post" action="<?php $this->commentUrl() ?>" class="space-y-4">
            <?php if($this->user->hasLogin()): ?>
                <p class="text-gray-600">
                    欢迎回来，<strong><?php $this->user->screenName(); ?></strong>。
                    <a href="<?php $this->options->logoutUrl(); ?>" class="text-accent hover:underline ml-2">退出</a>
                </p>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="text" name="author" id="author" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition" placeholder="昵称 *" value="<?php $this->remember('author'); ?>" required>
                    <input type="email" name="mail" id="mail" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition" placeholder="邮箱 * (不会公开)" value="<?php $this->remember('mail'); ?>" required>
                    <input type="url" name="url" id="url" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition" placeholder="网站 (选填)" value="<?php $this->remember('url'); ?>">
                </div>
            <?php endif; ?>
            <textarea name="text" id="textarea" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent transition" rows="5" placeholder="写下你的想法..." required><?php $this->remember('text'); ?></textarea>
            <div class="flex justify-end">
                <button type="submit" class="bg-accent hover:bg-accent-dark text-white font-semibold px-6 py-2 rounded-full transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> 发布评论
                </button>
            </div>
        </form>
    </div>
    <?php else: ?>
        <div class="mt-10 text-center text-gray-400 text-sm py-4 border-t border-gray-100">
            评论功能已关闭。
        </div>
    <?php endif; ?>
</div>

<!-- 自定义评论回调样式（必须，用于控制每个评论项的结构） -->
<?php
// 自定义评论回调函数（与 Typecho 原生方法配合使用）
function threadedComments($comment, $options)
{
    $commentClass = '';
    if ($comment->authorId) {
        if ($comment->authorId == $comment->ownerId) {
            $commentClass .= ' comment-by-author';
        }
    }
?>
    <li id="li-<?php $comment->theId(); ?>" class="comment-item bg-white rounded-xl border border-gray-100 p-5 transition hover:shadow-sm <?php echo $commentClass; ?><?php if ($comment->levels > 0) echo ' ml-10 mt-4'; ?>">
        <div class="flex gap-4">
            <!-- 头像 -->
            <div class="flex-shrink-0">
                <?php $comment->gravatar(48, '', '', 'mm'); ?>
            </div>
            <div class="flex-1">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-dark"><?php $comment->author(); ?></span>
                        <?php if ($comment->authorId == $comment->ownerId): ?>
                            <span class="text-xs bg-accent/10 text-accent px-2 py-0.5 rounded-full">作者</span>
                        <?php endif; ?>
                    </div>
                    <span class="text-xs text-gray-400"><i class="far fa-clock mr-1"></i><?php $comment->date('Y-m-d H:i'); ?></span>
                </div>
                <div class="text-gray-600 leading-relaxed break-words">
                    <?php if ($comment->status == 'waiting'): ?>
                        <em class="text-amber-500">您的评论正在等待审核...</em>
                    <?php else: ?>
                        <?php $comment->content(); ?>
                    <?php endif; ?>
                </div>
                <div class="mt-3">
                    <?php $comment->reply('<span class="text-accent hover:text-accent-dark text-sm inline-flex items-center gap-1"><i class="fas fa-reply"></i> 回复</span>'); ?>
                </div>
            </div>
        </div>
    </li>
<?php
}
?>