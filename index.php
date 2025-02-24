<?php
declare(strict_types=1);

use AlisonAdamus\ActiveRecordProject\Model\Article;
use AlisonAdamus\ActiveRecordProject\Model\Comment;
use AlisonAdamus\ActiveRecordProject\Util\Database;

require __DIR__ . '/vendor/autoload.php';

//$db = new Database();
//$db->createTables();

if(Article::deleteAll()){
    echo "Всі статті видалено.<br>";
}

if(Comment::deleteAll()){
    echo "Всі коментарі видалено.<br>";
}

$article = new Article();
$article->setTitle('Перша стаття');
$article->setContent('Це вміст першої статті.');
$article->setImage('image1.jpg');
if($article->save()){
    echo "Створена стаття з ID: " . $article->getId() . "<br>";
}

$comment = new Comment();
$comment->setArticleId($article->getId());
$comment->setContent('Це коментар до першої статті.');
if($comment->save()){
    echo "Створений коментар з ID: " . $comment->getId() . "<br>";
}

$articles = Article::findAll();
echo "Усі статті:<br>";
foreach ($articles as $art) {
    echo "ID: " . $art->getId() . ", Title: " . $art->getTitle() . ", Content: " . $art->getContent() . "<br>";
}

$foundArticle = Article::findById($article->getId());
if ($foundArticle) {
    echo "Знайдена стаття за ID " . $foundArticle->getId() . ": " . $foundArticle->getTitle() . "<br>";
} else {
    echo "Стаття не знайдена.<br>";
}

$article->setTitle('Оновлена стаття');
if($article->save()){
    echo "Оновлена стаття з ID: " . $article->getId() . "<br>";
}

if ($comment->delete()){
    echo "Коментар з ID " . $comment->getId() . " видалено.<br>";
}




