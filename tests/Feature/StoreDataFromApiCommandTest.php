<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreDataFromApiCommandTest extends TestCase
{
    protected string $commandName = 'api:store-data';

    /**
     * Test success import users
     */
    public function test_success_import_users(): void
    {
        $this->artisan("$this->commandName users")
            ->expectsOutputToContain('successfully')
            ->assertSuccessful();
    }

    /**
     * Test success import companies
     */
    public function test_success_import_companies(): void
    {
        $this->artisan("$this->commandName companies")
            ->expectsOutputToContain('successfully')
            ->assertSuccessful();
    }

    /**
     * Test success import companies with positions
     */
    public function test_success_import_companies_with_positions(): void
    {
        $this->artisan("$this->commandName companies --with-positions")
            ->expectsOutputToContain('successfully')
            ->assertSuccessful();
    }

    /**
     * Test failed import command without necessary argument
     */
    public function test_failed_import_without_specified_entity(): void
    {
        $this->expectExceptionMessage("Not enough arguments (missing: \"entity\").");

        $this->artisan("$this->commandName");
    }

    /**
     * Test failed import command with undefined entity
     */
    public function test_failed_import_with_undefined_entity(): void
    {
        $this->expectExceptionMessage("Undefined Entity.");

        $this->artisan("$this->commandName entity");
    }

    /**
     * Test failed import command with undefined option
     */
    public function test_failed_import_with_undefined_option(): void
    {
        $optionName = "--some-option";

        $this->expectExceptionMessage("The \"$optionName\" option does not exist.");

        $this->artisan("$this->commandName users $optionName");
    }
}
