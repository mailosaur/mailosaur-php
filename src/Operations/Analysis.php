<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 5/16/2018
 * Time: 4:12 PM
 */

namespace Mailosaur\Operations;


use Mailosaur\Models\SpamAnalysisResult;
use Mailosaur\Models\DeliverabilityReport;

/**
 * Operations for analyzing the content and deliverability of an email, including SpamAssassin
 * scoring and per-provider deliverability reports. Accessed via `client->analysis`.
 */
class Analysis extends AOperation
{
    /**
     * <strong>Perform a spam test</strong>
     * <p>Performs a spam analysis of an email.</p>
     *
     * @param string $email The identifier of the message to be analyzed.
     *
     * @return SpamAnalysisResult The spam score and filter results.
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Analysis_Spam Perform a spam test docs
     * @example https://mailosaur.com/docs/api/#operation/Analysis_Spam
     */
    public function spam($email)
    {
        $response = $this->request('api/analysis/spam/' . urlencode($email));

        $response = json_decode($response);

        return new SpamAnalysisResult($response);
    }

    /**
     * <strong>Perform a deliverability report</strong>
     * <p>Performs a deliverability report of an email.</p>
     *
     * @param string $email The identifier of the message to be analyzed.
     *
     * @return DeliverabilityReport The deliverability report for the email.
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/analysis Perform a deliverability test docs
     * @example https://mailosaur.com/docs/api/analysis
     */
    public function deliverability($email)
    {
        $response = $this->request('api/analysis/deliverability/' . urlencode($email));

        $response = json_decode($response);
        return new DeliverabilityReport($response);
    }
}