<?php
/**
 * Created by PhpStorm.
 * User: camronwood
 * Date: 2/20/17
 * Time: 9:20 AM
 */

namespace OmniHolding\LimeLightPayments;

use App\User;
use App\Subscription;
use KevinEm\LimeLightCRM\LimeLightCRM;
use KevinEm\LimeLightCRM\Exceptions\LimeLightCRMMembershipException;

/**
 * Class Subscriptions
 *
 * @package App\Http
 */
class Subscriptions
{
    /**
     * Gateway not approved message.
     */
    const GATEWAY_NOT_APPROVED = "Your Transaction has been Declined. Please Ensure your Information is Correct and Try Again.";

    /**
     * @var LimeLightCRM
     */
    private $limeLightCRM;

    /**
     * @var User
     */
    private $user;

    /**
     * @var integer
     */
    private $productId;

    /**
     * @var integer
     */
    private $campaignId;

    /**
     * @var integer
     */
    private $shippingId;

    /**
     * @var string
     */
    private $apiUserName;

    /**
     * @var string
     */
    private $apiPassword;

    /**
     * @var string
     */
    private $apiBaseUrl;

    /**
     * Subscriptions constructor.
     */
    public function __construct()
    {
        $this->user        = \Auth::user();
        $this->productId   = config('limelight-payments.productId');
        $this->campaignId  = config('limelight-payments.campaignId');
        $this->shippingId  = config('limelight-payments.shippingId');
        $this->apiUserName = config('limelight-payments.apiUserName');
        $this->apiPassword = config('limelight-payments.apiPassword');
        $this->apiBaseUrl  = config('limelight-payments.apiBaseUrl');

        $this->limeLightCRM = new LimeLightCRM([
            'base_url' => $this->apiBaseUrl,
            'username' => $this->apiUserName,
            'password' => $this->apiPassword,
        ]);
    }

    /**
     * @return mixed
     */
    function getIpAddress()
    {
        return ($_SERVER['REMOTE_ADDR']);
    }

    /**
     * @param $number
     *
     * @return bool|string
     */
    function getCardType($number)
    {
        // return this for the test card
        // first number: success, second number decline
        if ($number === '4321000000001234' || $number === '4321000000000000') {
            return 'visa';
        }

        $number = preg_replace('/[^\d]/', '', $number);

        if (preg_match('/^3[47][0-9]{13}$/', $number)) {
            return 'amex';
        } elseif (preg_match('/^6(?:011|5[0-9][0-9])[0-9]{12}$/', $number)) {
            return 'discover';
        } elseif (preg_match('/^5[1-5][0-9]{14}$/', $number)) {
            return 'master';
        } elseif (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $number)) {
            return 'visa';
        } else {
            return false;
        }
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function convertDataToFormArray($data)
    {
        $formDataArray = [
            'name-first'       => $data['first_name'],
            'name-last'        => $data['last_name'],
            'email'            => $data['email'],
            'phone'            => $data['phone'],
            'address'          => $data['billing_address1'],
            'city'             => $data['billing_city'],
            'state'            => $data['billing_state'],
            'zip-code'         => $data['billing_zip'],
            'country'          => $data['billing_country'],
            'cc-number'        => $data['cc_number'],
            'cc-expires-month' => $data['cc_expiration_date'],
            'cc-expires-year'  => $data['cc_expiration_date'],
            'cc-payment-type'  => $data['cc_payment_type'],
        ];

        return $formDataArray;
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function convertFormToLimeLightPostData($data)
    {
        $postData["firstName"]                       = $data["name-first"];
        $postData["lastName"]                        = $data["name-last"];
        $postData["phone"]                           = $data["phone"];
        $postData["email"]                           = $data["email"];
        $postData["shippingAddress1"]                = $data["address"];
        $postData["shippingCity"]                    = $data["city"];
        $postData["shippingState"]                   = $data["state"];
        $postData["shippingZip"]                     = $data["zip-code"];
        $postData["shippingCountry"]                 = 'US';
        $postData["billingFirstName"]                = $data["name-first"];
        $postData["billingLastName"]                 = $data["name-last"];
        $postData["billingAddress1"]                 = $data["address"];
        $postData["billingCity"]                     = $data["city"];
        $postData["billingState"]                    = $data["state"];
        $postData["billingZip"]                      = $data["zip-code"];
        $postData["billingCountry"]                  = 'US';
        $postData["creditCardType"]                  = $this->getCardType($data['cc-number']);
        $postData["creditCardNumber"]                = $data["cc-number"];
        $postData["expirationDate"]                  = $data["cc-expires-month"] . $data["cc-expires-year"];
        $postData["CVV"]                             = $data["cc-cvv"];
        $postData["tranType"]                        = 'Sale';
        $postData["productId"]                       = $this->productId;
        $postData["product_qty_" . $this->productId] = '1';
        $postData["campaignId"]                      = $this->campaignId;
        $postData["shippingId"]                      = $this->shippingId;
        $postData["ipAddress"]                       = $this->getIpAddress();

        return $postData;
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function createLimeLightSubscription($data)
    {
        $limeLightPostData = $this->convertFormToLimeLightPostData($data);
        $result            = $this->limeLightCRM->transaction()->newOrder($limeLightPostData);

        if (isset($result['errorFound']) && $result['errorFound'] == 0 && isset($result['customerId'])) {
            $response['success']        = true;
            $response['customerId']     = $result['customerId'];
            $response['orderId']        = $result['orderId'];
            $response['subscriptionId'] = $result['subscription_id[' . $this->productId . ']'];
        } else {
            $response['success']       = false;
            $response['errorMessage']  = (strlen($result['errorMessage']) != 0 ? $result['errorMessage'] : self::GATEWAY_NOT_APPROVED);
            $response['responseCode']  = $result['responseCode'];
            $response['declineReason'] = $result['declineReason'];
        }

        return $response;
    }

    /**
     * Get view of all customers for a product campaign.
     *
     * @param $subscription
     *
     * @return array|boolean
     */
    public function getLimeLightCustomerView($subscription)
    {
        $createdAt = $subscription->created_at;
        $createdAt = strtotime($createdAt);
        $createdAt = date('m/d/y', $createdAt);

        $response = $this->limeLightCRM->membership()->findCustomer(
            [
                'campaign_id' => $this->campaignId,
                'start_date'  => $createdAt,
                'end_date'    => $createdAt,
                'search_type' => 'all',
                'return_type' => 'customer_view',
            ]
        );

        $data = json_decode($response['data'], true);

        foreach ($data as $account) {
            if ($account['email'] === $this->user->email) {
                return $account;
            }
        }

        // users order was not found
        return false;
    }

    /**
     * Get view of order from lime light.
     *
     * @param $orderId
     *
     * @return array
     */
    public function getLimeLightOrderView($orderId)
    {
        $orderView = $this->limeLightCRM->membership()->viewOrder([
            'order_id'      => $orderId,
            'return_format' => '',
        ]);

        return $orderView;
    }

    /**
     * @param null $type
     *
     * @return array|string
     */
    public function getUpdateActions($type = null)
    {
        $string = 'first_name,last_name,email,phone,billing_address1,billing_city,billing_state,billing_zip,billing_country,cc_number,cc_expiration_date,cc_payment_type';

        if ($type === 'string') {
            return $string;
        } else {
            return explode(',', $string);
        }
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function updateLimeLightSubscription($data)
    {
        $postData     = $this->convertFormToLimeLightPostData($data);
        $subscription = Subscription::where('user_id', '=', $this->user->id)->firstOrFail();
        $customerView = $this->getLimeLightCustomerView($subscription);
        $orderIds     = '';
        $i            = 12;

        while ($i > 0) {
            $orderIds .= $customerView['order_list'] . ',';
            $i--;
        }

        // yes this looks awful but blame LimeLight not me.
        $response = $this->limeLightCRM->membership()->updateOrder([
            'order_ids' => rtrim($orderIds, ','),
            'sync_all'  => 0,
            'actions'   => $this->getUpdateActions('string'),
            'values'    => $postData['firstName']
                . ',' . $postData['lastName']
                . ',' . $postData['email']
                . ',' . $postData['phone']
                . ',' . $postData['billingAddress1']
                . ',' . $postData['billingCity']
                . ',' . $postData['billingState']
                . ',' . $postData['billingZip']
                . ',' . $postData['billingCountry']
                . ',' . $postData['creditCardNumber']
                . ',' . $postData['expirationDate']
                . ',' . $postData['creditCardType'],
        ]);

        $responseCodes = explode(',', $response['response_code']);
        $exceptions    = new LimeLightCRMMembershipException(0);
        $actions       = array_flip($this->getUpdateActions());

        // pair response codes with actions
        $i = 0;
        foreach ($actions as &$action) {
            if (array_key_exists($i, $responseCodes)) {
                $action = [
                    'response_code'         => $responseCodes[$i],
                    'response_code_message' => $exceptions->getExceptionMessage($responseCodes[$i]),
                ];
                $i++;
            }
        }

        $actions = $this->convertDataToFormArray($actions);

        return $actions;
    }

    /**
     * Cancels the subscription for the logged in user.
     *
     * @return array
     */
    public function cancelLimeLightSubscription()
    {
        $subscription    = Subscription::where('user_id', '=', $this->user->id)->firstOrFail();
        $account         = $this->getLimeLightCustomerView($subscription);
        $orderList       = explode(',', $account['order_list']);
        $results         = [];
        $activeUntilDate = '';

        foreach ($orderList as $orderId) {
            try {
                $orderView = $this->getLimeLightOrderView($orderId);

                $subscription->recurring_date = $orderView['recurring_date'];
                $subscription->save();

                $recurringDate   = strtotime($subscription->recurring_date);
                $activeUntilDate = date("Y-m-d", strtotime("+1 month", $recurringDate));

            } catch (\Exception $e) {
                // TODO: handle this if the unsubscribe for that order id fails
                dd($e->getMessage());
            }

            try {
                $response = $this->limeLightCRM->membership()->updateRecurringOrder([
                    'order_id' => $orderId,
                    'status'   => 'stop',
                ]);

                if ($response['response_code'] === '100') {
                    $results = [
                        'success' => true,
                        'message' => 'Account successfully unsubscribed. Your account will remain active until ' . $activeUntilDate,
                    ];
                } else {
                    $results = [
                        'success' => false,
                        'message' => 'Failed to unsubscribe this account.',
                    ];
                }

            } catch (\Exception $e) {
                $results = [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

}
