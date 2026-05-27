<?php

namespace Mailosaur\Operations;


use Mailosaur\Models\Device;
use Mailosaur\Models\DeviceCreateOptions;
use Mailosaur\Models\DeviceListResult;
use Mailosaur\Models\OtpResult;

/**
 * Operations for managing virtual security devices and retrieving their current one-time passwords
 * (OTPs), used to automate testing of app-based multi-factor authentication. Accessed via
 * `client->devices`.
 */
class Devices extends AOperation
{

    /**
     * <strong>List all devices</strong>
     * <p>Returns a list of your virtual security devices.</p>
     *
     * @return DeviceListResult Your devices.
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Devices_List List all devices
     * @example https://mailosaur.com/docs/api/#operation/Devices_List
     */
    public function all()
    {
        $response = $this->request('api/devices');

        $response = json_decode($response);

        return new DeviceListResult($response);
    }

    /**
     * <strong>Create a device</strong>
     * <p>Creates a new virtual security device and returns it.</p>
     *
     * @param DeviceCreateOptions $deviceCreateOptions Options used to create a new Mailosaur virtual security device.
     *
     * @return \Mailosaur\Models\Device The newly-created device.
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Devices_Create Create a device
     * @example https://mailosaur.com/docs/api/#operation/Devices_Create
     */
    public function create(DeviceCreateOptions $deviceCreateOptions)
    {
        $payload = $deviceCreateOptions->toJsonString();

        $response = $this->request(
            'api/devices/',
            array(
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS    => $payload,
                CURLOPT_HTTPHEADER    => array('Content-Type:application/json', 'Content-Length: ' . strlen($payload))
            )
        );

        $response = json_decode($response);

        return new Device($response);
    }

    /**
     * <strong>Retrieves the current one-time password for a saved device, or given base32-encoded shared secret.</strong>
     *
     * @param string $query Either the unique identifier of the device, or a base32-encoded shared secret.
     *
     * @return \Mailosaur\Models\OtpResult The current one-time password.
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Devices_Otp Retrieve the current one-time password
     * @example https://mailosaur.com/docs/api/#operation/Devices_Otp
     */
    public function otp($query)
    {
        if (str_contains($query, "-")) {
            $response = $this->request('api/devices/' . urlencode($query) . '/otp');

            $response = json_decode($response);

            return new OtpResult($response);
        }

        $payload = json_encode(array('sharedSecret' => $query));
        $response = $this->request(
            'api/devices/otp',
            array(
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS    => $payload,
                CURLOPT_HTTPHEADER    => array('Content-Type:application/json', 'Content-Length: ' . strlen($payload))
            )
        );

        $response = json_decode($response);

        return new OtpResult($response);
    }

    /**
     * <strong>Delete a device</strong>
     * <p>Permanently deletes a virtual security device. This operation cannot be undone.</p>
     *
     * @param string $id The unique identifier of the device to be deleted.
     *
     * @return void
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Devices_Delete Delete a device
     * @example https://mailosaur.com/docs/api/#operation/Devices_Delete
     */
    public function delete($id)
    {
        $this->request('api/devices/' . urlencode($id), array(CURLOPT_CUSTOMREQUEST => 'DELETE'));
    }
}
