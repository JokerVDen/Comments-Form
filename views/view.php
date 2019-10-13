<?php
/**
 * @var $flashes array
 * @var $csrf_token string
 * @var $comment \models\Comments
 * @var $comments array
 * @var $form string
 */

?>
<h1>Comments</h1>
<form action="/index.php" method="post">
    <?php if($flashes):?>
    <div class="flashes">
        <?php foreach ($flashes as $flash):?>
        <div>
            <?=$flash?>
        </div>
        <?php endforeach;?>
    </div>
    <?php endif;?>
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
    <div class="comment--name-wrap">
        <label for="name">Name:</label>
        <input type="text"
               id="name"
               class="comments--input-text  <?= isset($comment->errors['name']) ? "error" : "" ?>"
               name="<?= $form ?>[name]"
               value="<?= $comment->values['name'] ?>"
        >
        <?php if (isset($comment->errors['name'])) : ?>
            <span class="error"><?= $comment->errors['name'][0] ?></span>
        <?php endif; ?>
    </div>
    <div class="comment--email-wrap">
        <label for="email">E-mail:</label>
        <input type="email"
               id="email"
               class="comments--input-email <?= isset($comment->errors['email']) ? "error" : "" ?>"
               name="<?= $form ?>[email]"
               value="<?= $comment->values['email'] ?>">
        <?php if (isset($comment->errors['email'])) : ?>
            <span class="error"><?= $comment->errors['email'][0] ?></span>
        <?php endif; ?>
    </div>
    <div class="comment--text-wrap">
        <label for="text">Comment:</label>
        <textarea id="text" class="comments--input-textarea <?= isset($comment->errors['text']) ? "error" : "" ?>"
                  name="<?= $form ?>[text]"><?= $comment->values['text'] ?></textarea>
        <?php if (isset($comment->errors['text'])) : ?>
            <span class="error"><?= $comment->errors['text'][0] ?></span>
        <?php endif; ?>
    </div>
    <button type="submit">Public</button>
</form>

<?php if ($comments): ?>
<div class="comments--comments-list-wrap">
    <?php foreach ($comments as $comment_from_db):?>
    <div id="comment<?=$comment_from_db['id']?>" class="comments--comments-list-comment">
        <div class="comments--comments-list-name"><?=htmlspecialchars($comment_from_db['name'])?></div>
        <div class="comments--comments-list-text"><?=htmlspecialchars($comment_from_db['text'])?></div>
    </div>
    <?php endforeach;?>
</div>
<?php endif;?>