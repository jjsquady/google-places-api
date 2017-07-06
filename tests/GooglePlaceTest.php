<?php
/**
 * Created by PhpStorm.
 * User: Jorge
 * Date: 06/07/2017
 * Time: 16:08
 */

namespace Tests;

use Dotenv\Dotenv;
use jjsquady\GoogleApi\Exceptions\GooglePlacesApiException;
use jjsquady\GoogleApi\PlacesApi;

class GooglePlaceTest extends TestCase
{
    protected $key;

    /** @var  PlacesApi */
    protected $places;

    protected function setUp()
    {
        /*
         * Uses DotEnv to load API_KEY from .env file in tests directory.
         * Required to dev/tests only.
        */
        (new Dotenv(__DIR__))->load();
        $this->places = new PlacesApi(env('API_TEST_KEY'));
    }

    /** @test */
    public function it_checks_for_a_valid_key()
    {
        $this->expectException(GooglePlacesApiException::class);

        (new PlacesApi())->placeDetails('some');
    }

    /** @test */
    public function it_radar_search_has_results()
    {
        $response = $this->places->radarSearch('-11.9355403048,-61.9998238963', 50000, ['type'=>'phamarcy']);

        $this->assertArrayHasKey('results', $response);
    }

    /** @test */
    public function it_get_place_details()
    {
        $placeDetails = $this->places->placeDetails('ChIJjTltpeJryJMRqZa0UNXeJT8')->get('result');

        $this->assertEquals('Je Farmácia Maciel', $placeDetails['name']);
    }

    /** @test */
    public function it_make_a_text_search_and_find_an_item()
    {
        $params = [
            'location' => '-11.7244406,-61.7935221',
            'radius' => 50000,
            'type' => 'pharmacy'
        ];

        $searchResult = $this->places->textSearch('Farmacia', $params)->get('results');

        $phamacyFound = $searchResult->where('name', 'Farmácia Globo Vista Alegre')->first();

        $this->assertNotEmpty($phamacyFound);
    }
}