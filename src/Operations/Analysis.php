<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 5/16/2018
 * Time: 4:12 PM
 */

namespace Mailosaur\Operations;


use Mailosaur\Models\SpamAnalysisResult;

class Analysis extends AOperation
{
    /**
     * <strong>Perform a spam test</strong>
     *
     * @param string $email The identifier of the email to be analyzed.
     *
     * @return SpamAnalysisResult
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Analysis_Spam Perform a spam test docs
     * @example https://mailosaur.com/docs/api/#operation/Analysis_Spam
     */
    public function spam($email)
    {
        $response = $this->request('/api/analysis/spam/' . urlencode($email));

        $response = json_decode($response);

        return new SpamAnalysisResult($response);
    }
}