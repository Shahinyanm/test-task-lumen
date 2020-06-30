<?php

namespace App\Services;

class NewsApi
{
    private $apiKey;
    public $baseUrl = 'https://newsapi.org/v2/';

    private $newsThemesDefault = [
        'Bitcoin', 'Litecoin', 'Ripple', 'Dash', 'Ethereum'
    ];

    /**
     * NewsApi constructor.
     *
     * @param $apiKey
     */
	public function __construct($apiKey)
	{
        $this->apiKey = $apiKey;
    }

    /**
     * Get everything endpoint
     *
     * @param $parameters
     * @return mixed
     */
    public function everything($parameters = [])
    {
        $endpointUrl = $this->baseUrl . 'everything?apiKey=' . $this->apiKey;
        $endpointUrl .= $this->setParametersToUrl($parameters, $this->newsThemesDefault);

        return $this->makeRequest($endpointUrl);
    }

    /**
     * Get sources endpoint
     *
     * @param $parameters
     * @return mixed
     */
    public function sources($parameters)
    {
        $endpointUrl = $this->baseUrl . 'sources?apiKey=' . $this->apiKey;
        $endpointUrl .= $this->setParametersToUrl($parameters, $this->newsThemesDefault);

        return $this->makeRequest($endpointUrl);
    }

    /**
     * Get top headlines endpoint
     *
     * @param $parameters
     * @return mixed
     */
    public function topHeadlines($parameters)
    {
        $endpointUrl = $this->baseUrl . 'topHeadlines?apiKey=' . $this->apiKey;
        $endpointUrl .= $this->setParametersToUrl($parameters, $this->newsThemesDefault);

        return $this->makeRequest($endpointUrl);
    }

    private function makeRequest($url)
    {
        try {
            $ch = curl_init();

            if ($ch === false) {
                throw new Exception('failed to initialize');
            }

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $content = curl_exec($ch);

            if ($content === false) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }

            curl_close($ch);

            return json_decode($content);
        } catch(Exception $e) {

            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);

        }
    }

    private function setParametersToUrl(array $parameters, $defaultQueryParam = null)
    {
        $endpointUrl = '';

        // search for multiple theme
        // if not set theme to search news, take default ones
        if (!isset($parameters[ 'q' ]) && $defaultQueryParam) {
            $parameters[ 'q' ] = $defaultQueryParam;
        }

        $parametersQuery = is_array($parameters[ 'q' ]) ? implode(' OR ', $parameters[ 'q' ]) : $parameters[ 'q' ];
        $endpointUrl .= '&q='.rawurlencode($parametersQuery);
        unset($parameters['q']);

        // add sources
        if (isset($parameters[ 'sources' ]) && is_array($parameters[ 'sources' ])) {
            $sources = implode(',', $parameters[ 'sources' ]);
            $endpointUrl .= '&sources='.rawurlencode($sources);
            unset($parameters[ 'sources' ]);
        }

        // add domains
        if (isset($parameters[ 'domains' ]) && is_array($parameters[ 'domains' ])) {
            $domains = implode(',', $parameters[ 'domains' ]);
            $endpointUrl .= '&domains='.rawurlencode($domains);
            unset($parameters[ 'domains' ]);
        }

        // add from
        if (isset($parameters[ 'from' ]) && is_array($parameters[ 'from' ])) {
            $from = (new \DateTime($parameters[ 'from' ]))->format('Y-m-d');
            $endpointUrl .= '&from='.rawurlencode($from);
            unset($parameters[ 'from' ]);
        }

        // add to
        if (isset($parameters[ 'to' ]) && is_array($parameters[ 'to' ])) {
            $to = (new \DateTime($parameters[ 'to' ]))->format('Y-m-d');
            $endpointUrl .= '&to='.rawurlencode($to);
            unset($parameters[ 'to' ]);
        }

        // add rest parameters which doesn't require filter
        foreach ($parameters as $parameterName => $parameter) {
            if ($parameterName && $parameter) {
                $endpointUrl .= '&' . $parameterName . '=' . $parameter;
                unset($parameters[$parameterName]);
            }

        }

        return $endpointUrl;
    }
}
