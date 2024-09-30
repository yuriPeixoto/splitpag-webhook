<?php

namespace App\Service;

use App\Api\SplitpagApi;
use Psr\Log\LoggerInterface;

class ChargeService
{
    private $api;
    private $logger;

    public function __construct(SplitpagApi $api, LoggerInterface $logger)
    {
        $this->api = $api;
        $this->logger = $logger;
    }

    public function getCharges(string $page, string $search = '', string $from = '', string $to = '', string $status = ''): array
    {
        $params = ['page' => $page];
        if (!empty($search)) $params['search'] = $search;
        if (!empty($from)) $params['from'] = $from;
        if (!empty($to)) $params['to'] = $to;
        if (!empty($status)) $params['status'] = $status;

        return $this->api->makeRequest('GET', '/charge', $params);
    }

    public function getChargeCreateData(): array
    {
        return $this->api->makeRequest('GET', '/charge/create');
    }

    public function createCharge(array $data): array
    {
        $requiredFields = ['document', 'name', 'email', 'gender', 'birth_date', 'address', 'number_address', 'district', 'city', 'state', 'country', 'phone', 'type'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "Field '$field' is required"];
            }
        }

        // Additional validation based on charge type
        switch ($data['type']) {
            case 'u':
                $singleChargeFields = ['leverage_days_single', 'leverage_min_percent_single', 'description_single_charge', 'amount', 'due_date_single_charge'];
                foreach ($singleChargeFields as $field) {
                    if (empty($data[$field])) {
                        return ['success' => false, 'message' => "Field '$field' is required for single charge"];
                    }
                }
                break;
            case 'p':
                $installmentFields = ['description_installment_amount', 'recurrence_interval_installment', 'due_date_installment_billing', 'expiration_day_installments', 'total_installment', 'installment_amount'];
                foreach ($installmentFields as $field) {
                    if (empty($data[$field])) {
                        return ['success' => false, 'message' => "Field '$field' is required for installment charge"];
                    }
                }
                break;
            case 'r':
                $recurrenceFields = ['description_recurrence', 'recurrence_interval', 'due_date_recurrence', 'expiration_day_recurrence', 'amount_recurrence'];
                foreach ($recurrenceFields as $field) {
                    if (empty($data[$field])) {
                        return ['success' => false, 'message' => "Field '$field' is required for recurrence charge"];
                    }
                }
                break;
            default:
                return ['success' => false, 'message' => "Invalid charge type"];
        }

        // Validate charge method
        if (isset($data['charge_method']) && $data['charge_method'] == '1') {
            if (empty($data['method'])) {
                return ['success' => false, 'message' => "Field 'method' is required when charge_method is 1"];
            }
            if ($data['method'] == '2') {
                $creditCardFields = ['number', 'cvv', 'expiration_date', 'holder_name'];
                foreach ($creditCardFields as $field) {
                    if (empty($data[$field])) {
                        return ['success' => false, 'message' => "Field '$field' is required for credit card payment"];
                    }
                }
            }
        }

        return $this->api->makeRequest('POST', '/charge/create', $data);
    }

    public function getChargeStatus(string $hashChargeId): array
    {
        return $this->api->makeRequest('GET', "/charge/status/$hashChargeId");
    }
}
