<?php namespace Riskified\OrderWebhook\Transport;
/**
 * Copyright 2013-2015 Riskified.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 * http://www.apache.org/licenses/LICENSE-2.0.html
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

use Riskified\OrderWebhook\Exception;
/**
 * Class CurlTransport
 * @package Riskified
 */
class CurlTransport extends AbstractTransport {

    /**
     * @var int
     */
    public $timeout = 10 * 1000;

    /**
     * @param $order object Order to send
     * @param $endpoint String API endpoint to send request
     * @return mixed
     * @throws \Riskified\OrderWebhook\Exception\UnsuccessfulActionException
     * @throws \Riskified\OrderWebhook\Exception\CurlException
     */
    protected function send_json_request($json, $endpoint) {
        $ch = curl_init($this->endpoint_prefix().$endpoint);
        $curl_options = array(
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $this->headers($json),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => $this->user_agent,
            CURLOPT_TIMEOUT_MS => $this->timeout,
            CURLOPT_FAILONERROR => false
        );
        curl_setopt_array($ch, $curl_options);


        $body = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception\CurlException(curl_error($ch), curl_errno($ch));
        }

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $this->json_response($body, $status);
    }

    /**
     * @param $json object Account Event to send
     * @param $endpoint String API endpoint to send request
     * @return mixed
     * @throws \Riskified\OrderWebhook\Exception\UnsuccessfulActionException
     * @throws \Riskified\OrderWebhook\Exception\CurlException
     * @throws Exception\MalformedJsonException
     */
    protected function send_account_json_request($json, $endpoint) {
        $ch = curl_init($this->endpoint_prefix('customers').$endpoint);
        $curl_options = array(
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $this->headers($json),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => $this->user_agent,
            CURLOPT_TIMEOUT_MS => $this->timeout,
            CURLOPT_FAILONERROR => false
        );
        curl_setopt_array($ch, $curl_options);


        $body = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception\CurlException(curl_error($ch), curl_errno($ch));
        }

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $this->json_response($body, $status);
    }

    /**
     * @param $body
     * @param $status
     * @return mixed
     * @throws \Riskified\OrderWebhook\Exception\MalformedJsonException
     * @throws \Riskified\OrderWebhook\Exception\UnsuccessfulActionException
     */
    private function json_response($body, $status) {
        $response = json_decode($body);

        if ($status != 200)
            throw new Exception\UnsuccessfulActionException($body, $status);
        if (!$response)
            throw new Exception\MalformedJsonException($body, $status);

        return $response;
    }
}
