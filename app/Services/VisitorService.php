<?php

namespace App\Services;

use App\Models\Url;
use App\Models\User;
use App\Models\Visit;

class VisitorService
{
    public function __construct(
        public User $user,
    ) {
    }

    /**
     * Store the visitor data.
     *
     * @param Url $url \App\Models\Url
     * @return void
     */
    public function create(Url $url)
    {
        $visitorIpLookup = json_decode($this->visitorIpLookup());
        $visitorRegionCode = "";
        if(isset($visitorIpLookup)){
            $visitorRegionCode = $visitorIpLookup->location->country->code;
        }
        $logBotVisit = config('urlhub.track_bot_visits');
        if ($logBotVisit === false && \Browser::isBot() === true) {
            return;
        }

        Visit::create([
            'url_id'         => $url->id,
            'visitor_id'     => $this->user->signature(),
            'is_first_click' => $this->isFirstClick($url),
            'referer'        => request()->header('referer'),
            'visitor_region' => $visitorRegionCode
        ]);
    }

    /**
     * Check if the visitor has clicked the link before. If the visitor has not
     * clicked the link before, return true.
     *
     * @param Url $url \App\Models\Url
     */
    public function isFirstClick(Url $url): bool
    {
        $hasVisited = $url->visits()
            ->whereVisitorId($this->user->signature())
            ->exists();

        return $hasVisited ? false : true;
    }

    public function visitorIpLookup() {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.ipregistry.co/?key=mk3v2qoiob7ba95h',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        
        return $response;
    }
}
