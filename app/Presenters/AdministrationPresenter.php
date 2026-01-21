<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\Post;

/**
 * Presenter pro vykreslování administrační sekce
 */
class AdministrationPresenter extends BasePresenter
{

    /** @var Post Model pro správu článků. */
    private Post $articleManager;

    /**
     * Konstruktor pro rodičovskou třídu Presenter a model pro správu článků.
     * @param Post $articleManager 
     */
    public function __construct(Post $articleManager)
    {
        parent::__construct();
        $this->articleManager = $articleManager;
    }

    /**
     * Zkontroluje, zda je uživatel přihlášený, pokud ne, přesměruje na stránku přihlášení.
     */
    public function startup(): void
    {
        parent::startup();

        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
    }

    /**
     * Před vykreslováním stránky pro přihlášení přesměruje do administrace, pokud je uživatel již přihlášen.
     */
    public function actionLogin(): void
    {
        if ($this->getUser()->isLoggedIn()) {
            $this->redirect('Administration:default');
        }
    }



    /** Předá do šablony data přihlášeného uživatele před vykreslením šablony administrační stránky. */
    public function beforeRender(): void
    {
        parent::beforeRender();

        if ($this->getUser()->isLoggedIn()) {
            $this->template->username = $this->user->getIdentity()->name;
            $this->template->role = $this->user->getIdentity()->role;
        }
    }

    /**
     * Zobrazí přehled článků - pokud je uživatel admin, zobrazí všechny články; jinak zobrazí jen články, jehož je uživatel autorem.
     *
     * @return void
     */
    public function renderDefault(): void
    {
        if ($this->user->getIdentity()->role === 'admin') {

            $this->template->allArticles = $this->articleManager->getArticlesByAllAuthors();
        } else {

            $this->template->myArticles = $this->articleManager->getArticlesByAuthor($this->user->getIdentity()->id);
        }
    }
}
