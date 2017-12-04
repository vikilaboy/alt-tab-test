<?php

use App\Models\User;

class UsersTest extends TestCase
{
    public function testListUsers()
    {
        $response = $this->call('GET', '/v1/users', [], [], [],
            ['HTTP_Authorization' => 'Bearer '.$this->getToken()])
            ->getOriginalContent();

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(1, count($response['data']));
        $this->assertEquals(1, $response['data'][0]['id']);
    }

    public function testUserCreate()
    {
        $postData = [
            'email' => 'victor@niculae.net',
            'name' => 'Test User',
            'password' => 'test',
        ];

        $response = $this->call('POST', '/v1/users', $postData, [], [],
            ['HTTP_Authorization' => 'Bearer ' . $this->getToken()]);

        $this->assertEquals(200, $response->getStatusCode());

        $content = $response->getOriginalContent();

        $this->assertArrayHasKey('data', $content);
        $this->assertEquals('victor@niculae.net', $content['data']['email']);

        $this->seeInDatabase('users', ['email' => 'victor@niculae.net']);

        $this->assertEquals(2, User::count()); // including admin
    }

    public function testShowExistingUser()
    {
        $response = $this->call('GET', '/v1/users/me', [], [], [],
            ['HTTP_Authorization' => 'Bearer ' . $this->getToken()])
            ->getOriginalContent();

        $this->assertTrue(array_key_exists('data', $response));
        $this->assertEquals('Administrator', $response['data']['name']);
    }

    public function testShowNotExistingUser()
    {
        $response = $this->call('GET', '/v1/users/2', [], [], [],
            ['HTTP_Authorization' => 'Bearer ' . $this->getToken()]);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testShowLoggedInUser()
    {
        $response = $this->call('GET', '/v1/users/me', [], [], [],
            ['HTTP_Authorization' => 'Bearer ' . $this->getToken()])
            ->getOriginalContent();

        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(1, $response['data']['id']);
    }

    public function testUserUpdate()
    {
        $this->createTestUser();

        $postData = [
            'email' => 'victor@niculae.net',
            'name' => 'Updated Test User',
        ];

        $response = $this->call('PUT', '/v1/users/2', $postData, [], [],
            ['HTTP_Authorization' => 'Bearer '.$this->getToken()]);

        $this->assertEquals(200, $response->getStatusCode());

        $content = $response->getOriginalContent();

        $this->assertArrayHasKey('data', $content);
        $this->assertEquals('Updated Test User', $content['data']['name']);

        $this->seeInDatabase('users', ['name' => 'Updated Test User']);
    }

    public function testUserDelete()
    {
        $this->createTestUser();

        $response = $this->call('DELETE', '/v1/users/2', [], [], [],
            ['HTTP_Authorization' => 'Bearer ' . $this->getToken()]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->notSeeInDatabase('users', ['email' => 'victor@niculae.net']);

        $this->assertEquals(1, User::count());
    }

    protected function createTestUser()
    {
        User::create([
            'email' => 'victor@niculae.net',
            'name' => 'Test User',
            'password' => 'test',
        ]);
    }
}
