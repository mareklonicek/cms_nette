<?php declare(strict_types=1);

namespace App\Model;

use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

/**
 * Model pro správu článků v redakčním systému.
 */
class Post extends Database
{
    private string $table_name;
    private string $created_at;
    private string $column_id;


    /**
     * Konstruktor parametry z common.neon pro DB tabulku Articles, obsahující články, a pro rodičovskou třídu Database
     *
     * @param array $articles
     * @param Explorer $database
     */
    public function __construct(array $articles, Explorer $database)
    {
        parent::__construct($database);
        $this->table_name = $articles['table_name'];
        $this->created_at = $articles['created_at'];
        $this->column_id = $articles['column_id'];
    }

    /**
     * Vrátí seznam všech článků v databázi seřazený sestupně od naposledy přidaného.
     * @return Selection výběr všech článků
     */
    public function getArticles(string $sort, int $limit): Selection
    {
        return $this->database
            ->table($this->table_name)
            ->order($this->created_at . ' ' . $sort)
            ->limit($limit);
    }

    /**
     * Vrátí článek z databáze podle id (primárního klíče).
     * @param int $id ID článku
     * @return false|ActiveRow článek, který odpovídá ID nebo false pokud článek s daným ID neexistuje
     */
    public function getArticleById(int $id): false|ActiveRow
    {
        return $this->database
            ->table($this->table_name)
            ->get($id);
    }

    /** 
     * Vrátí všechny články z databáze se jménem autora.
     * @return array $allArticles  pole s klíči 'post', kde jsou články a 'author' kde jsou jména autorů daných článků
     */
    public function getArticlesByAllAuthors(): array
    {
        $allArticles = [];

        $articles = $this->database->table($this->table_name);

        foreach ($articles as $article) {

            $author = $article->ref('users', 'author_id'); //join tabulek articles a users

            $username = $author->username ?? '';


            array_unshift($allArticles, ['post' => $article, 'author' => $username]); // článek + jméno autora uloží do pole allArticles
        }

        return $allArticles;
    }


    /**
     * Vrátí články z databáze podle ID autora.
     * @param int $userId Id autora článku
     * @return array $articles články, které odpovídají ID autora
     */
    public function getArticlesByAuthor(int $userId): array
    {

        $articles = [];
        //vybere uživatele dle userId
        $author = $this->database->table('users')->get($userId);

        if($author) {
            //vypíše všechny články autora (řazeno od nejnovějších)
            foreach ($author->related('articles.author_id') as $article) {
                array_unshift($articles, $article);
            }
        }

        return $articles;
    }


    /** 
     * Uloží článek do databáze.
     * @param array $article článek
     */
    public function saveArticle(array $article): void
    {
        $this->database->table($this->table_name)->insert($article);
    }


    /** 
     * Aktualizuje článek v databázi podle ID článku.
     * @param array $article článek
     */
    public function updateArticle(array $article): void
    {
        $this->database->table($this->table_name)->where($this->column_id, $article[$this->column_id])->update($article);
    }


    /**
     * Odstraní článek podle ID.
     * @param int $postId ID článku
     */
    public function removeArticle(int $postId): void
    {
        $this->database->table($this->table_name)->where($this->column_id, $postId)->delete();
    }

}
