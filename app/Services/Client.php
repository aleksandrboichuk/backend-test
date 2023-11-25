<?php

namespace App\Services;

use App\Interfaces\ClientInterface;

class Client implements ClientInterface
{
    /**
     * API url
     *
     * @var string
     */
    protected string $apiUrl = 'http://server:3000';

    /**
     * Necessary header for API requests
     *
     * @var string
     */
    protected string $xClientHeader = '237cd6a8-5a0e-4ff0-b7e2-0bf34675d058';

    /**
     * Client for API communication
     *
     * @var \GuzzleHttp\Client
     */
    protected \GuzzleHttp\Client $client;

    public function __construct()
    {
        $this->setClient();
    }

    /**
     * @inheritdoc
     */
    public function getCompanies(int $page = 1): array
    {
        // API returns key 'compaines' instead of 'companies',
        // then needs to specify variable with specific response data key
        return $this->getEntityItems('companies', $page, 'compaines');
    }

    /**
     * @inheritdoc
     */
    public function getUsers(int $page = 1): array
    {
        return $this->getEntityItems('users', $page);
    }

    /**
     * @inheritdoc
     */
    public function getCompanyPositions(string $companyId): mixed
    {
        return $this->prepareResponse(
            $this->request("/company/$companyId"),
            'positions'
        );
    }

    /**
     * Returns array entity items from the API (with pagination)
     *
     * @param string $entity
     * @param int $page
     * @param string|null $dataKey
     * @return array
     */
    protected function getEntityItems(string $entity, int $page = 1, string|null $dataKey = null): array
    {
        return $this->prepareResponse(
            $this->request("/$entity", ['page' => $page]),
            $dataKey ?? $entity
        );
    }

    /**
     * Returns response from the API by data key
     *
     * @param array|\stdClass|null $response
     * @param string $dataKey
     * @return array
     */
    protected function prepareResponse(array|\stdClass|null $response, string $dataKey): mixed
    {
        if($response && isset($response->$dataKey)){
            return $response->$dataKey;
        }

        return [];
    }

    /**
     * Sends request to API endpoints
     *
     * @param string $uri
     * @param array $body
     * @param string $method
     * @return \stdClass|null
     */
    protected function request(string $uri, array $body = [], string $method = "get"): ?\stdClass
    {
        try{

            [$uri, $options] = $this->prepareRequestUriAndOptions($uri, $body, $method);

            $response = $this->client->{$method}($this->apiUrl . $uri, $options);

            if($response->getStatusCode() != 200){
                // logging
                dump($response->getStatusCode(), json_decode($response->getBody()));
            }

            return json_decode($response->getBody());

        }catch (\Exception $exception){

            // logging

            dump($exception->getMessage());

            return null;
        }
    }

    /**
     * Prepares Request URI (with query string) or Body depends on request method
     *
     * @param string $uri
     * @param array $body
     * @param string $method
     * @return array
     */
    protected function prepareRequestUriAndOptions(string $uri, array $body, string $method): array
    {
        $options = [];

        switch ($method){
            case "put":
            case "post":
                $options = [
                    'body' => json_encode($body)
                ];
                break;
            case "get":
            case "delete":
                if(!empty($body)){

                    $queryString = "";

                    foreach ($body as $key => $item) {
                        $queryString .= (empty($queryString) ? "?" : "&") . "$key=$item";
                    }

                    $uri .= $queryString;
                }
                break;
        }

        return [$uri, $options];
    }

    /**
     * @inheritdoc
     */
    public function setClient(): void
    {
        $this->client = new \GuzzleHttp\Client([
            'http_errors' => false,
            'headers' => [
                'X-Client' => $this->xClientHeader
            ]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function setXClientHeader(string $xClientHeader): void
    {
        $this->xClientHeader = $xClientHeader;
    }

    /**
     * @inheritdoc
     */
    public function setApiUrl(string $apiUrl): void
    {
        $this->apiUrl = $apiUrl;
    }
}