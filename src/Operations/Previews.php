<?php

namespace Mailosaur\Operations;


use Mailosaur\Models\EmailClientListResult;

/**
 * Operations for discovering the email clients available for generating email previews
 * (screenshots of an email rendered in real clients). Accessed via `client->previews`.
 */
class Previews extends AOperation
{

    /**
     * <strong>List all email preview clients</strong>
     * <p>Lists all email clients that can be used to generate email previews.</p>
     *
     * @return EmailClientListResult The available email clients.
     * @throws \Mailosaur\Models\MailosaurException
     */
    public function allEmailClients()
    {
        $response = $this->request('api/screenshots/clients');

        $response = json_decode($response);

        return new EmailClientListResult($response);
    }
}
