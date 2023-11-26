<?php

namespace App\Console\Commands;

use App\Interfaces\ClientInterface;
use App\Models\Company;
use App\Models\CompanyPositions;
use App\Models\User;
use App\Services\Client;
use Illuminate\Console\Command;

class StoreDataFromApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:store-data {entity} {--with-positions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command retrieves data from API via Client and storing data to database';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle(ClientInterface $client): void
    {
        $entity = $this->argument('entity');

        $this->info("Start import $entity");

        switch ($entity){
            case 'users':

                $insertArray = $this->makeInsertArray($client->getUsers(), [
                    'user_id' => "Id",
                    'first_name' => "FirstName",
                    'last_name' => "LastName",
                    'email' => "Email",
                ]);

                User::query()->upsert($insertArray, ['user_id']);

                break;

            case 'companies':

                $companies = $client->getCompanies();

                $insertArray = $this->makeInsertArray($companies, [
                    'company_id' => "Id",
                    'name' => "Name",
                    'address' => "Address",
                ]);

                if($this->option('with-positions')){

                    $this->info("\nImporting companies positions");

                    $this->insertCompaniesPositions($companies, $client);

                    $this->info("\nCompanies positions imported successfully");
                }

                Company::query()->upsert($insertArray, ['company_id']);

                break;

            default:
                throw new \Exception("Undefined Entity.");
        }

        $this->info("\n" . ucfirst($entity) . " imported successfully!");
    }

    /**
     * Inserts positions of company by CompanyId
     *
     * @param array $companies
     * @param Client $client
     * @return void
     */
    private function insertCompaniesPositions(array $companies, Client $client): void
    {
        foreach ($companies as $company) {

            $companiesPositionsInsertArray = $this->makeInsertArray($client->getCompanyPositions($company->Id), [
                'company_id' => "CompanyId",
                'user_id' => "UserId",
                'position' => "Position",
            ]);

            foreach ($companiesPositionsInsertArray as $position) {
                // prevention mysql unique errors
                CompanyPositions::query()->updateOrCreate(
                    $position,
                    ['company_id' => $position['company_id'], 'user_id' => $position['user_id']]
                );
            }
        }
    }

    /**
     * Turns response array to insert array by fields
     *
     * @param array $data
     * @param array $keysAndFields - ['table_field' => 'apiResponseKey']
     * @return array
     */
    private function makeInsertArray(array $data, array $keysAndFields = []): array
    {
        $insertArray = [];

        if(!empty($data)){
            $this->withProgressBar($data, function ($datum) use ($keysAndFields, &$insertArray){

                $insertFields = [];

                foreach ($keysAndFields as $field => $key) {
                    // if key and field are the same
                    if(is_int($field)){
                        $field = $key;
                    }

                    if(isset($datum->$key)){
                        $insertFields[$field] = $datum->$key;
                    }
                }

                $insertArray[] = $insertFields;
            });
        }

        return $insertArray;
    }
}
