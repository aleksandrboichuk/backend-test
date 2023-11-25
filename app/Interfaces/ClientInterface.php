<?php

namespace App\Interfaces;

interface ClientInterface
{
    /**
     * Api URL variable setter
     *
     * @param string $apiUrl
     */
    public function setApiUrl(string $apiUrl): void;

    /**
     * X-Client header variable setter
     *
     * @param string $xClientHeader
     */
    public function setXClientHeader(string $xClientHeader): void;

    /**
     * API communication client setter
     *
     * @return void
     */
    public function setClient(): void;

    /**
     * Returns companies positions from the API
     *
     * @param string $companyId
     * @return mixed
     */
    public function getCompanyPositions(string $companyId): mixed;

    /**
     * Returns users from the API (with pagination)
     *
     * @param int $page
     * @return mixed
     */
    public function getUsers(int $page = 1): array;

    /**
     * Returns companies from the API (with pagination)
     *
     * @param int $page
     * @return mixed
     */
    public function getCompanies(int $page = 1): array;
}