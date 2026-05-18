<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <article class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
        <h1 class="text-3xl md:text-3xl font-bold text-dark mb-4"><?php $this->title() ?></h1>
        <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
            <?php $this->content(); ?>
        </div>
    </article>
    <?php $this->need('comments.php'); ?>
</main>

<?php $this->need('footer.php'); ?>