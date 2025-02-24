<?php

namespace AlisonAdamus\ActiveRecordProject\Model;

class Comment extends Entity
{
    private int $article_id;
    private string $content;

    public function getArticleId(): int
    {
        return $this->article_id;
    }

    public function setArticleId(int $article_id): void
    {
        $this->article_id = $article_id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    protected static function getTableName(): string
    {
        return 'comments';
    }
}