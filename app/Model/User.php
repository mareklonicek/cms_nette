<?php

declare(strict_types=1);

namespace App\Model;

use Nette;
use Nette\Security\Authenticator;
use Nette\Security\SimpleIdentity;
use Nette\Database\Explorer;
use Nette\Security\Passwords;

/**
 * Model pro práci s uživateli
 */
class User implements Authenticator
{
	/** @var Explorer  Nette vrstva pro práci s databází. */
	private Explorer $database;

	/** @var Passwords  Nette hashování hesel. */
	private Passwords $passwords;

	 /**
     * Konstruktor s injektovanou službou pro práci s databází a hashování hesel.
	 * @param Explorer $database
	 * @param Passwords $passwords
	 */
	public function __construct(Explorer $database,	Passwords $passwords)
	{
		$this->database = $database;
		$this->passwords = $passwords;
	}

	/**
	 * Přihlásí uživatele - ověření zadané údaje a v případě úspěšného přihášení vrátí uživatelská data
	 * @param string $username 	uživatelské jméno
	 * @param string $password 	heslo uživatele
	 * @return SimpleIdentity  Identita uživatele (uživatelská data)
	 * @throws Nette\Security\AuthenticationException V případě chyby při přihlašování vyhodí výjimku.
	 */
	public function authenticate(string $username, string $password): SimpleIdentity
	{
		$getUser = $this->database
			->table('users')
			->where('username', $username)
			->fetch();

		if (!$getUser) {
			throw new Nette\Security\AuthenticationException('Uživatel nebyl nalezen.', self::IDENTITY_NOT_FOUND);
		}

		if (!$this->passwords->verify($password, $getUser->password)) {
			throw new Nette\Security\AuthenticationException('Zadané heslo není správné.', self::INVALID_CREDENTIAL);
		}

		return new SimpleIdentity(
			$getUser->id,
			$getUser->role,
			[
				'name' => $getUser->username,
				'email' => $getUser->email,
				'role' => $getUser->role
			]
		);
	}


	/**
	 * Vrátí uživatelská data z databáze
	 *
	 * @param string $field Sloupec dle kterého chceme vyhledávat v databázi.
	 * @param string|integer $params Podmínka pro vyhledávání.
	 * @param string $query Sloupec s daty, která hledáme.
	 * @return array $userData Pole s daty uživatelů dle zadaných parametrů vyhledávání.
	 */
	public function getUserData(string $field, string|int $params, string $query): array
	{

		$userData = [];
		$users = $this->database
			->table('users')
			->where($field, $params);

		foreach ($users as $user) {
			$userData[] = $user->$query;
		}

		return ($userData);
	}
}
