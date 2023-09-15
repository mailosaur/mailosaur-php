<?php

namespace Mailosaur\Operations;


use Mailosaur\Models\PreviewEmailClientListResult;

class Previews extends AOperation
{

    /**
     * <strong>List all email preview clients</strong>
     *
     * @return PreviewEmailClientListResult
     * @throws \Mailosaur\Models\MailosaurException
     */
    public function allEmailClients()
    {
        $response = $this->request('api/previews/clients');

        $response = json_decode($response);

        return new PreviewEmailClientListResult($response);
    }
}
