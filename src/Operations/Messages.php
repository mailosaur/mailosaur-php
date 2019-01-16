<?php

namespace Mailosaur\Operations;


use Mailosaur\Models\Message;
use Mailosaur\Models\MessageListResult;
use Mailosaur\Models\SearchCriteria;
use Mailosaur\Models\MailosaurException;

class Messages extends AOperation
{
    /**
     * <strong>Retrieves the detail for a single email message.</strong>
     * <p>Simply supply the unique identifier for the required message.</p>
     *
     * @param $id message id
     *
     * @return \Mailosaur\Models\Message
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Messages_Get Retrieve a message docs
     * @example https://mailosaur.com/docs/api/#operation/Messages_Get
     */
    public function get($id)
    {
        $message = $this->request('api/messages/' . urlencode($id));
        $message = json_decode($message);

        return new Message($message);
    }

    /**
     * <strong>Permanently deletes a message.</strong>
     * <p>This operation cannot be undone. Also deletes any attachments related to the message.</p>
     *
     * @param $id
     *
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Messages_Delete Delete a message docs
     * @example https://mailosaur.com/docs/api/#operation/Messages_Delete
     */
    public function delete($id)
    {
        $this->request('api/messages/' . urlencode($id), array(CURLOPT_CUSTOMREQUEST => 'DELETE'));
    }

    /**
     * <strong>List all messages</strong><br/>
     *
     * @param string $server       The identifier of the server hosting the messages.
     * @param int    $page         Used in conjunction with itemsPerPage to support pagination.
     * @param int    $itemsPerPage A limit on the number of results to be returned per page.
     *                             Can be set between 1 and 1000 items, the default is 50.
     *
     * @return \Mailosaur\Models\MessageListResult
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Messages_List List all messages
     * @example https://mailosaur.com/docs/api/#operation/Messages_List
     */
    public function all($server, $page = 0, $itemsPerPage = 50)
    {
        $messagesResponse = $this->request(
            'api/messages/?server=' . urlencode($server)
            . ($page > 0 ? '&page=' . urlencode($page) : '')
            . '&itemsPerPage=' . urlencode($itemsPerPage)
        );

        $messagesResponse = json_decode($messagesResponse);

        return new MessageListResult($messagesResponse);
    }

    /**
     * <strong>Delete all messages</strong>
     *
     * @param string $server The identifier of the server to be emptied.
     *
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Messages_DeleteAll Delete all messages
     * @example https://mailosaur.com/docs/api/#operation/Messages_DeleteAll
     */
    public function deleteAll($server)
    {
        $this->request('api/messages?server=' . urlencode($server), array(CURLOPT_CUSTOMREQUEST => 'DELETE'));
    }

    /**
     *  <strong>Search for messages</strong>
     * <p>Returns a list of messages matching the specified search criteria, in summary form.
     * The messages are returned sorted by received date, with the most recently-received messages appearing first.</p>
     *
     * @param string         $server         The identifier of the server hosting the messages.
     * @param SearchCriteria $searchCriteria Search criteria
     * @param int            $page           Used in conjunction with itemsPerPage to support pagination.
     * @param int            $itemsPerPage   A limit on the number of results to be returned per page.
     *                                       Can be set between 1 and 1000 items, the default is 50.
     *
     * @return \Mailosaur\Models\MessageListResult
     * @throws \Mailosaur\Models\MailosaurException
     */
    public function search($server, SearchCriteria $searchCriteria, $page = 0, $itemsPerPage = 50)
    {
        $payload = $searchCriteria->toJsonString();

        $path = 'api/messages/search?' . http_build_query(array('server' => $server, 'page' => $page, 'itemsPerPage' => $itemsPerPage));

        $messagesResponse = $this->request(
            $path,
            array(
                CURLOPT_URL           => $this->client->getBaseUri() . $path,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS    => $payload,
                CURLOPT_HTTPHEADER    => array('Content-Type:application/json', 'Content-Length: ' . strlen($payload))
            )
        );

        $messagesResponse = json_decode($messagesResponse);

        return new MessageListResult($messagesResponse);
    }

    /**
     * <strong>Wait for a specific message</strong>
     * <p>Returns as soon as a message matching the specified search criteria is found or until timeout has elapsed.
     * This is the most efficient method of looking up a message.</p>
     *
     * @param string                           $server         The identifier of the server hosting the message.
     * @param \Mailosaur\Models\SearchCriteria $searchCriteria Search criteria.
     * @param int                              $timeout        Timeout in seconds (15s default).
     *
     * @return \Mailosaur\Models\Message
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Messages_WaitFor Wait for a specific message docs
     * @example https://mailosaur.com/docs/api/#operation/Messages_WaitFor
     */
    public function waitFor($server, SearchCriteria $searchCriteria, $timeout=15)
    {
        $timeoutDatetime = new \DateTime('+'.$timeout.' seconds');

        while($timeoutDatetime > new \DateTime()) {
            $messageList = $this->search($server, $searchCriteria);

            if(sizeof($messageList->items) > 0) {
                $message = $this->get($messageList->items[0]->id);
                return $message;
            }

            sleep(2);
        }

        throw new MailosaurException(
            '',
            404
        );
    }
}