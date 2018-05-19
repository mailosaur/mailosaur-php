<?php

namespace Mailosaur\Operations;


class Files extends AOperation
{
    /**
     * <strong>Download an attachment</strong>
     *
     * @param string $id The identifier of the attachment to be downloaded.
     *
     * @return string
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Files_GetAttachment Download an attachment documentation
     * @example https://mailosaur.com/docs/api/#operation/Files_GetAttachment
     */
    public function getAttachment($id)
    {
        return $this->request('/api/files/attachments/' . urlencode($id));
    }

    /**
     * <strong>Download EML</strong>
     * <p>Downloads an EML file representing the specified email.<br />
     * Simply supply the unique identifier for the required email.</p>
     *
     * @param string $id The identifier of the email to be downloaded.
     *
     * @return string
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Files_GetEmail Download EML documentation
     * @example https://mailosaur.com/docs/api/#operation/Files_GetEmail
     */
    public function getEmail($id)
    {
        return $this->request('/api/files/email/' . urlencode($id));
    }
}