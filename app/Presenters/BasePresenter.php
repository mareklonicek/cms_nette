<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette\Application\AbortException;
use Nette\Application\UI\Presenter;

/**
 * Základní presenter pro všechny ostatní presentery aplikace.
 */
abstract class BasePresenter extends Presenter
{
     /**
     * Na začátku každé akce u všech presenterů zkontroluje uživatelská oprávnění (v services je registrovaná služba security.authorizator).
     * @throws AbortException Jestliže uživatel nemá oprávnění k dané akci, bude přesměrován na homepage a zobrazí se upozornění.
     */
    protected function startup(): void
    {
        parent::startup();
        if (!$this->getUser()->isAllowed($this->getName(), $this->getAction())) {
            $this->flashMessage('Zobrazení této stránky je dostupné jen pro přihlášené uživatele.','error');
            $this->redirect('Homepage:default');
        }
    }
}