<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class CompaniesApiEndpointTest extends TestCase
{
    protected string $endpoint = "/api/companies";

    protected array $responseStructure = [
        'companies' => [
            'data' => [
                [
                    'id',
                    'company_id',
                    'name',
                    'address',
                    'created_at',
                    'updated_at',
                    'users' => [
                        [
                            'id',
                            'user_id',
                            'first_name',
                            'last_name',
                            'email',
                            'created_at',
                            'updated_at',
                            'laravel_through_key'
                        ]
                    ]
                ]
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'first_page_url',
            'links',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total',
        ],
    ];

    /**
     * Test success getting companies
     */
    public function test_success_get_companies(): void
    {
        $response = $this->getJson($this->endpoint);

        $response->assertSuccessful();

        $response->assertJsonStructure($this->responseStructure);

        $this->checkUsersThroughKeyEqualsCategoryId(
            json_decode($response->getContent(), true)
        );
    }

    /**
     * Test success getting companies with per page parameter
     */
    public function test_success_get_companies_with_perPage_parameter(): void
    {
        $itemsPerPage = 10;

        $response = $this->getJson($this->endpoint . "?perPage=$itemsPerPage");

        $response->assertSuccessful();

        $response->assertJsonStructure($this->responseStructure);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals($content['companies']['per_page'], $itemsPerPage);

        $this->checkUsersThroughKeyEqualsCategoryId($content);
    }

    /**
     * Test success getting companies with name parameter (search by company name)
     */
    public function test_success_search_companies_by_name_parameter(): void
    {
        $this->testSuccessSearchCompaniesByFields(['name' => "first"]);
    }

    /**
     * Test success getting companies with address parameter (search by company address)
     */
    public function test_success_search_companies_by_address_parameter_with_results(): void
    {
        $this->testSuccessSearchCompaniesByFields(["address" => "doane street"]);
    }

    /**
     * Test success getting companies with name and address parameter (search by company name and company address)
     */
    public function test_success_search_companies_by_name_and_address_parameters_with_results(): void
    {
        $this->testSuccessSearchCompaniesByFields(["address" => "doane street", 'name' => "first"]);
    }

    /**
     * Test success getting companies with name and address parameter (search by company name and company address)
     * with add perPage pagination parameter
     */
    public function test_success_search_companies_by_name_and_address_with_per_page_parameter_with_results(): void
    {
        $this->testSuccessSearchCompaniesByFields(["address" => "doane street", 'name' => "first", 'perPage' => 2]);
    }

    /**
     * Test success getting companies with address parameter without results
     */
    public function test_success_search_companies_by_address_parameter_without_results(): void
    {
        $this->testSuccessSearchCompaniesByFields(["address" => Str::random(10)]);
    }

    /**
     * Test success getting companies with address parameter without results
     */
    public function test_success_search_companies_by_name_parameter_without_results(): void
    {
        $this->testSuccessSearchCompaniesByFields(["name" => Str::random(10)]);
    }

    /**
     * TTest failed getting companies: invalid search query parameters
     */
    public function test_failed_validation_with_invalid_field_format_search_companies_by_parameters(): void
    {
        $this->testSearchCompaniesByFieldsWithValidationError(
            [
                "name" => "%^:123",
                "address" => "332^66^:;",
            ],
            [
                "name" => "The Company name field format is invalid.",
                "address" => "The Company address field format is invalid.",
            ]);
    }

    /**
     * TTest failed getting companies: too long search query parameters
     */
    public function test_failed_validation_with_to_long_field_value_search_companies_by_parameters(): void
    {
        $this->testSearchCompaniesByFieldsWithValidationError(
            [
                "name" => Str::random(40),
                "address" => Str::random(60),
                "perPage" => 10000,
            ],
            [
                "name" => "The Company name field must not be greater than 30 characters.",
                "address" => "The Company address field must not be greater than 50 characters.",
                "perPage" => "The Companies per page field must not be greater than 100.",
            ]);
    }

    /**
     * TTest failed getting companies: too short search query parameters
     */
    public function test_failed_validation_with_to_short_field_value_search_companies_by_parameters(): void
    {
        $this->testSearchCompaniesByFieldsWithValidationError(
            [
                "name" => '1',
                "address" => '2',
                "perPage" => 0,
            ],
            [
                "name" => "The Company name field must be at least 2 characters.",
                "address" => "The Company address field must be at least 2 characters.",
                "perPage" => "The Companies per page field must be at least 1.",
            ]);
    }

    /**
     * Test failed getting companies: not defined query parameter
     */
    public function test_failed_validation_not_defined_query_parameter(): void
    {
        $fieldName = 'some-field';

        $this->testSearchCompaniesByFieldsWithValidationError(
            [
                $fieldName => "123",
            ],
            [
                $fieldName => "Parameter: '$fieldName' is not defined!",
            ]);
    }

    /**
     * Checks field laravel_through_key in user object equals his company_id
     *
     * @param array $content
     * @return void
     */
    private function checkUsersThroughKeyEqualsCategoryId(array $content): void
    {
        $companies = $content['companies']['data'];

        // through key company users is equals with company id
        foreach ($companies as $company) {
            foreach ($company['users'] as $user) {
                $this->assertEquals(
                    $company['company_id'],
                    $user['laravel_through_key']
                );
            }
        }
    }

    /**
     * Method for check validation errors for query parameters
     *
     * @param array $values
     * @param array $assertMessages
     * @return void
     */
    private function testSearchCompaniesByFieldsWithValidationError(array $values, array $assertMessages): void
    {
        $queryString = $this->prepareQueryString($values);

        $response = $this->getJson($this->endpoint . $queryString);

        $response->assertUnprocessable();

        $response->assertJsonStructure([
            "errors" => array_keys($values)
        ]);

        $errors = json_decode($response->getContent(), true)['errors'];

        foreach ($assertMessages as $field => $assertErrorMessage) {
            $this->assertContains($assertErrorMessage, $errors[$field]);
        }
    }

    /**
     * Method for success test of search by fields
     *
     * @param array $parameters
     * @return void
     */
    private function testSuccessSearchCompaniesByFields(array $parameters): void
    {
        $queryString = $this->prepareQueryString($parameters);

        $response = $this->getJson($this->endpoint . $queryString);

        $response->assertSuccessful();

        $baseResponseStructure = $this->responseStructure;

        unset($baseResponseStructure['companies']['data'][0]);

        // first check base response structure without data
        $response->assertJsonStructure($baseResponseStructure);

        $content = json_decode($response->getContent(), true);

        // checking per page field
        if(isset($parameters['perPage'])){

            $itemsPerPage = $parameters['perPage'];

            $this->assertEquals($content['companies']['per_page'], $itemsPerPage);

            unset($parameters['perPage']);
        }

        $companies = $content['companies']['data'];

        if (empty($companies)) {
            // if there are no results - check via request to database
            $this->assertFalse((new Company)->getSearchByFieldsBuilder($parameters)->exists());
        }else{
            // check additional response structure
            $response->assertJsonStructure($this->responseStructure);

            $this->checkCompanyFieldContains($companies, $parameters);
        }
    }

    /**
     * Checks category field contains with expected
     *
     * @param array $companies
     * @param array $fields
     * @return void
     */
    private function checkCompanyFieldContains(array $companies, array $fields): void
    {
        foreach ($companies as $company) {
            foreach ($fields as $searchField => $searchValue) {
                $this->assertTrue(
                    str_contains(
                        strtolower($company[$searchField]),
                        strtolower($searchValue))
                );
            }
        }
    }

    /**
     * Prepares query string for endopint request
     *
     * @param array $parameters
     * @return string
     */
    private function prepareQueryString(array $parameters): string
    {
        $queryString = "";

        foreach ($parameters as $key => $value) {
            $queryString .= (empty($queryString) ? "?" : "&") . "$key=$value";
        }

        return $queryString;
    }

}
