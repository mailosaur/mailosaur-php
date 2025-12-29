<?php

namespace Mailosaur\Operations;


use Mailosaur\Models\EmailClientListResult;

class Previews extends AOperation
{

    /**
     * <strong>List all email preview clients</strong>
     *
     * @return EmailClientListResult
     * @throws \Mailosaur\Models\MailosaurException
     */
    public function allEmailClients()
    {
        $response = $this->request('api/screenshots/clients');

        $response = json_decode($response);

        return new EmailClientListResult($response);
    }
}
