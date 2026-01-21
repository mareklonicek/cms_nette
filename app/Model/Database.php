<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Explorer;
use Nette\SmartObject;

/**
 * Základní model pro přístup k práci s databází.
 */
class Database
{
    use SmartObject;

    /** @var Explorer Služba pro práci s databází. */
    protected Explorer $database;

    /**
     * Konstruktor s injektovanou službou pro práci s databází.
     * @param Explorer $database Automaticky injektovaná Nette služba pro práci s databází
     */
    public function __construct(Explorer $database)
    {
        $this->database = $database;
    }
}