<?php

namespace Mailosaur\Operations;


use Mailosaur\Models\Device;
use Mailosaur\Models\DeviceCreateOptions;
use Mailosaur\Models\DeviceListResult;
use Mailosaur\Models\OtpResult;

class Devices extends AOperation
{

    /**
     * <strong>List all devices</strong>
     *
     * @return DeviceListResult
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
     * @param $deviceCreateOptions
     *
     * @return \Mailosaur\Models\Device
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
     * @return \Mailosaur\Models\OtpResult
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
     *
     * @param string $id The identifier of the device to be deleted.
     *
     * @throws \Mailosaur\Models\MailosaurException
     * @see     https://mailosaur.com/docs/api/#operation/Devices_Delete Delete a device
     * @example https://mailosaur.com/docs/api/#operation/Devices_Delete
     */
    public function delete($id)
    {
        $this->request('api/devices/' . urlencode($id), array(CURLOPT_CUSTOMREQUEST => 'DELETE'));
    }
}
