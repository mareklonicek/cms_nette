<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\Post;

/**
 * Presenter pro vykreslování homepage
 */
final class HomepagePresenter extends BasePresenter
{

    /** @var Post Model pro správu článků. */
    private Post $articleManager;

    /** * @var int Počet článků zobrazených na jedné stránce výpisu. */
    public int $posts_limit;

    /**
     * Konstruktor pro parametr z common.neon a model pro správu článků.
     * @param Post $articleManager
     */
    public function __construct(int $posts_limit, Post $articleManager)
    {
        parent::__construct();
        $this->articleManager = $articleManager;
        $this->posts_limit = $posts_limit;
    }

    /**
     * Zobrazí všechny články, defaultně od nejnovějších,
     * se stránkováním výpisu
     *
     * @param int $page výchozí číslo stránky
     * @param string $sort způsob řazení výpisu článků; defaultně DESC
     * @return void
     */
    public function renderDefault(int $page = 1, string $sort = 'DESC'): void
    {
        $this->template->sort = $sort; // proměnná do šablony pro řazení výpisu od nejnovějších/nestarších článků

        $posts = $this->template->posts = $this->articleManager->getArticles($sort, $this->posts_limit);

        if (!$posts) {
            $this->error('Nebyly nalezeny žádné články.');
        }

        // stránkoání výpisu - do šablony pošle pouze počet článků dle výpočtu metody page
        $lastPage = 0;
        $this->template->posts = $posts->page($page, $this->posts_limit, $lastPage);

        // data pro šablonu pro zobrazení stránkování
        $this->template->page = $page;
        $this->template->lastPage = $lastPage;
    }
}