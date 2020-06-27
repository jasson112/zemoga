<?php


namespace App\Controller;


use DG\Twitter\Exception;
use DG\Twitter\Twitter;

class TwitterZem
{
    protected $apiKey = 'KRy7l0v8wex3w8Sy5zThai3Ea';
    protected $apiSecretKey = 'X2eBm0Y21kYEuR74W3Frqc2JVIizOj8Q1EVGatDsEVVEJo0ucu';
    protected $accessToken = '1220032047516921859-otvXjhExyUTZ5GLxssc9h5ORqtPZja';
    protected $accessTokenSecret = 'tmJKqM4ORfQW6CH7wIVV8uKNpmSEmeFAP8lYwGb19uYjj';
    protected $sdk;
    protected $timeline;
    protected $TwitterUsername;

    public function __construct(string $TwitterUsername = ''){
        $this->TwitterUsername = $TwitterUsername;
        try {
            $this->sdk = new Twitter($this->apiKey, $this->apiSecretKey, $this->accessToken, $this->accessTokenSecret);
            if (!$this->sdk->authenticate()) {
                die('Invalid name or password');
            }
        } catch (Exception $e) {
            echo "Error: ", $e->getMessage();
        }
    }

    public function getTimeLine(){
        try {
            $this->timeline = $this->sdk->request('statuses/user_timeline', 'GET', ["screen_name" => $this->TwitterUsername]);
        } catch (Exception $e) {
            $this->timeline = null;
            echo "Error: ", $e->getMessage();
        }
        return $this->timeline;
    }
}