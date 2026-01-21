<?php

declare(strict_types=1);

namespace App\Presenters;

use JetBrains\PhpStorm\NoReturn;
use Nette\Application\UI\Form;
use App\Model\Post;
use App\Model\User;
use App\Model\Mail;

/**
 * Presenter pro zobrazování článků
 */
class PostPresenter extends BasePresenter
{
    /** @var Post Model pro správu článků. */
    private Post $articleManager;

    /** @var User Model pro správu uživatelů. */
    private User $userManager;

    /** @var Mail Model pro správu e-mailů. */
    private Mail $mailManager;


    /**
     * Konstruktor s injektovanými modely pro správu článků, uživatelů a mailů
     * @param Post $articleManager
     * @param User $userManager
     * @param Mail $mailManager
     */
    public function __construct(Post $articleManager, User $userManager, Mail $mailManager)
    {
        parent::__construct();
        $this->articleManager = $articleManager;
        $this->userManager = $userManager;
        $this->mailManager = $mailManager;
    }


    /**
     * Zobrazí článek podle ID
     *
     * @param int $postId ID článku
     * @return void
     */
    public function renderShow(int $postId): void
    {
        $post = $this->template->post =
            $this->articleManager->getArticleById($postId);

        if (!$post) {
            $this->error('Článek nebyl nalezen.');
        }

    }

    /**
     * Odstraní článek a přesměruje na stránku administrace
     * @param int|null $postId ID článku
     */
    #[NoReturn] public function actionRemove(int $postId = null): void
    {
        $this->articleManager->removeArticle($postId);
        $this->flashMessage('Článek byl úspěšně odstraněn.', 'success');
        $this->redirect('Administration:default');
    }

    /**
     * Vykresluje formulář pro editaci článku podle zadaného ID.
     * @param int|null $postId ID článku
     */
    public function actionEdit(int $postId = null): void
    {
        if ($postId) { // Výpis chybové hlášky.
            if (!($article = $this->articleManager->getArticleById($postId))) {
                $this->flashMessage('Článek nebyl nalezen.', 'error');
            }
            else { // Předání hodnot článku do editačního formuláře.
                $this['postForm']->setDefaults($article);
            }
        }
    }

    /**
     * Vytváří a vrací formulář pro editaci článků.
     * @return Form formulář pro editaci článků
     */
    protected function createComponentPostForm(): Form
    {
        $form = new Form;
        $form->addText('title', 'Titulek:')
            ->setRequired('Zadejte prosím titulek článku.');
        $form->addTextArea('content', 'Obsah:')
            ->setRequired('Zadejte prosím obsah článku.');
        $form->addHidden('author_id', $this->user->getId());
        $form->addHidden('article_id');

        $form->addSubmit('send', 'Uložit a publikovat');
        $form->getElementPrototype()->onsubmit('tinyMCE.triggerSave()');
        $form->onSuccess[] = [$this, 'postFormSucceeded'];

        return $form;
    }

    /**
     * Pokud existuje článek s daným ID, upraví daný článek.
     * Jinak uloží nový článek.
     * Volá notofikaci pro admina.
     * Přesměruje do administrace.
     *
     * @param array $data Data z formuláře editace článku.
     * @return void
     */
    #[NoReturn] public function postFormSucceeded(array $data): void
    {
        if ($data['article_id']) {

            $this->articleManager->updateArticle($data);

            $this->flashMessage("Příspěvek byl úspěšně změněn.", 'success');

            $this->redirect("Post:show", $data['article_id']);
        } else {

            $this->articleManager->saveArticle($data);

            $this->flashMessage("Příspěvek byl úspěšně uložen.", 'success');


            // pokud článek ukládá uživatel, který nemá roli "admin", je uživatelům role "admin" odeslána notifikace na mail o vložení nového článku
            if (!$this->user->isInRole('admin')) {

                $this->notifyAdmin($data);
            }

            $this->redirect("Administration:default");
        }
    }

    /**
     * Připraví data a zavolá Mail model k odeslání notifikačního mailu adminovi o vložení nového článku.
     *
     * @param array $data Data článku z editačního formuláře
     * @return void
     */
    private function notifyAdmin(array $data): void
    {
        //vrací array s mailovými adresami uživatelů, kteří mají roli "admin"
        $adminEmails = $this->userManager->getUserData('role', 'admin', 'email');

        // parametry pro latte šablonu
        $latteParams = [
            'title' => $data['title'],
            'content' =>  $data['content']
        ];

        // cesta k latte šabloně mailu
        $lattePath = __DIR__ . '/../Presenters/templates/@emailAdmin.latte';

        if (
            // zavolá mailer
            $this->mailManager->sendMail($adminEmails, $lattePath, $latteParams)
        ) {
            $this->flashMessage("Adminovi byl odeslán email.", 'success');
        } else {
            $this->flashMessage("Adminovi se nepodařilo odeslat email.", 'error');
        }
    }
}
