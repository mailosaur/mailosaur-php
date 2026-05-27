<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 5/16/2018
 * Time: 4:12 PM
 */

namespace Mailosaur\Operations;

use Mailosaur\Models\UsageAccountLimits;
use Mailosaur\Models\UsageTransactionListResult;

/**
 * Operations for inspecting your account's usage limits and recent transactional usage. These
 * endpoints require authentication with an account-level API key. Accessed via `client->usage`.
 */
class Usage extends AOperation
{

    /**
     * <strong>Retrieve account limits</strong>
     * <p>Retrieves account usage limits, detailing the current limits and usage for your account.
     * This endpoint requires authentication with an account-level API key.</p>
     *
     * @return UsageAccountLimits The usage limits for your account.
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#usage Retrieve account limits
     * @example https://mailosaur.com/docs/api/#usage
     */
    public function limits()
    {
        $response = $this->request('api/usage/limits');

        $response = json_decode($response);

        return new UsageAccountLimits($response);
    }

    /**
     * <strong>List usage transactions</strong>
     * <p>Retrieves the last 31 days of transactional usage. This endpoint requires authentication
     * with an account-level API key.</p>
     *
     * @return UsageTransactionListResult The transactional usage for the last 31 days.
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#usage Retrieve account limits
     * @example https://mailosaur.com/docs/api/#usage
     */
    public function transactions()
    {
        $response = $this->request('api/usage/transactions');

        $response = json_decode($response);

        return new UsageTransactionListResult($response);
    }
}