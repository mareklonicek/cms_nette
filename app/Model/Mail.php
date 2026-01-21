<?php

declare(strict_types=1);

namespace App\Model;

use Latte\Engine;
use Nette\Mail\Mailer;
use Nette\Mail\Message;
use Nette\Mail\SendException;
use Tracy\Debugger;

/**
 * Model pro odesílání mailů
 */
class Mail
{
    /** * @var string defaultní emailová adresa odesílatele; předáno přes services z common.neon */
    public string $sendFrom;

    /** @var Mailer Nette mailer rozhraní */
    public Mailer $mailer;

    /** @var Engine Nette šablonovací systém Latte */
    private Engine $latte;


    /**
     * Konstruktor s injekrovaným parametrem z common.neon a službami Mailer a latte engine
     *
     * @param string $sendFrom
     * @param Mailer $mailer
     * @param Engine $latte
     */
    public function __construct(string $sendFrom, Mailer $mailer, Engine $latte)
    {
        $this->mailer = $mailer;
        $this->latte = $latte;
        $this->sendFrom = $sendFrom;
    }

    /**
     * Odeslání mailu
     *
     * @param string|array $mailTo  Obsahuje emailovou adresu adresáta - jednu(string) nebo více(array)
     * @param string $lattePath  Cesta k šabloně, která obsahuje tělo emailu.
     * @param array $latteParams  Parametry pro latte šablonu.
     * @return bool
     */
    public function sendMail(string|array $mailTo, string $lattePath, array $latteParams): bool
    {

        $mail = new Message();
        $mail->setFrom($this->sendFrom)
            ->setHtmlBody(
                $this->latte->renderToString($lattePath, $latteParams)
            );

        //pokud se má email odeslat na více adres, provede se metoda addTo pro každou emailovou adresu v poli
        if (is_array($mailTo)) {

            foreach ($mailTo as $email) {
                $mail->addTo($email);
            }

        } else {
            $mail->addTo($mailTo);
        }

        try {
            
            $this->mailer->send($mail); //odešle email
            return true;
        } catch (SendException $e) {
            Debugger::log($e, 'mail exception');
            return false;
        }

    }
}
