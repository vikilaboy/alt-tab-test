<?php

use App\Models\Category;
use App\Models\Permission;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;

class TestCase extends Laravel\Lumen\Testing\TestCase
{
    protected $token = null;

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    public function getToken($email = null, $password = null)
    {
        if (is_null($this->token)) {
            $postData = [
                'email' => is_null($email) ? 'victor@niculae.net' : $email,
                'password' => is_null($password) ? 'admin' : $password,
            ];

            $response = $this->call('POST', '/v1/auth/login', $postData)
                ->getOriginalContent();

            $this->token = $response['data']['token'];
        }

        return $this->token;
    }

    public function setUp()
    {
        parent::setUp();

        Artisan::call('migrate');

        User::create([
            'email' => 'victor@niculae.net',
            'name' => 'Administrator',
            'password' => 'admin',
        ]);

        Storage::extend('memory', function($app, $config) {
            return new Filesystem(new MemoryAdapter);
        });
    }
}
