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

class Usage extends AOperation
{

    /**
     * <strong>Retrieve account limits</strong>
     *
     * @return UsageAccountLimits
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
     *
     * @return UsageTransactionListResult
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