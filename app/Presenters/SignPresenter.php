<?php

declare(strict_types=1);

namespace App\Presenters;

use JetBrains\PhpStorm\NoReturn;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;

class SignPresenter extends BasePresenter
{
	/**
	 * Vykresluje formulář pro přihlášení uživatele.
	 *
	 * @return Form
	 */
	protected function createComponentSignInForm(): Form
	{
		$form = new Form;
		$form->addText('username', 'Uživatelské jméno:')
			->setRequired('Zadejte své uživatelské jméno.');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Zadejte heslo.');

		$form->addSubmit('send', 'Přihlásit');

		$form->onSuccess[] = [$this, 'signInFormSucceeded'];
		return $form;
	}

	/**
	 * Zkusí přihlásit uživatele dle zadaných přihlašovacích údajů.
	 *
	 * @param Form $form 
	 * @param \stdClass $data Data z přihlašovacího formuláře.
	 * @return void
	 */
	public function signInFormSucceeded(Form $form, \stdClass $data): void
	{
		try {
			$this->getUser()->login($data->username, $data->password);
			$this->redirect('Administration:default');
		} catch (AuthenticationException $e) {
			$form->addError('Chybně zadané přihlašovací jméno nebo heslo.');
		}
	}

	/**
	 * Odhlásí uživatele.
	 *
	 * @return void
	 */
	#[NoReturn] public function actionOut(): void
	{
		$this->getUser()->logout();
		$this->flashMessage('Odhlášení bylo úspěšné.', 'success');
		$this->redirect('Homepage:default');
	}
}
