<?php namespace App\SupportedApps\Sonarr;

class Sonarr extends \App\SupportedApps implements \App\EnhancedApps {

    public $config;

    //protected $login_first = true; // Uncomment if api requests need to be authed first
    //protected $method = 'POST';  // Uncomment if requests to the API should be set by POST

    function __construct() {
        //$this->jar = new \GuzzleHttp\Cookie\CookieJar; // Uncomment if cookies need to be set
    }

    public function test()
    {
        $attrs = [
            'headers'  => ['Accept' => 'application/json']
        ];
        $test = parent::appTest($this->url('system/status'), $attrs);
        echo $test->status;
    }

    public function livestats()
    {
        $status = 'inactive';
        $data = [];
        $attrs = [
            'headers'  => ['Accept' => 'application/json']
        ];
        
        $today = date('Y-m-d',mktime(0, 0, 0, date("m"), date("d"), date("Y")));

        $tomorrow = date('Y-m-d',mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));

        $missing = json_decode(parent::execute($this->url('wanted/missing?sortKey=series.title&'), $attrs)->getBody());
        $upcoming = json_decode(parent::execute($this->url('calendar?start='.$today.'&end='.$tomorrow.'&'), $attrs)->getBody());
        $collect = collect($upcoming);
        $upcoming = $collect->where('hasFile', false);


        $data = [];

        if($missing || $queue) {
            $data['missing'] = $missing->totalRecords ?? 0;
            $data['upcoming'] = $upcoming->count() ?? 0;
        }

        return parent::getLiveStats($status, $data);
        
    }

    public function url($endpoint)
    {
        $api_url = parent::normaliseurl($this->config->url).'api/v3/'.$endpoint.'apikey='.$this->config->apikey;
        return $api_url;
    }
}