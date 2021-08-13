<?php

namespace Demo\common\Base;

use GuzzleHttp\Psr7\Response;

/**
 * Trait MakesHttpRequests.
 */
trait MakesHttpRequests
{
    /**
     * @throws ClientError
     */
    public function request($method, $uri, array $options = [])
    {
        $url = $this->app['config']->get('base_uri') . $uri;
        
        $response = $this->app['http_client']->request($method, $url, $options);

        return $this->transformResponse($response);
    }

    /**
     * @throws ClientError
     */
    protected function transformResponse(Response $response)
    {
        if (200 != $response->getStatusCode()) {
            throw new ClientError(
                "接口连接异常，异常码：{$response->getStatusCode()}, 异常信息:" . $response->getBody()->getContents(),
                $response->getStatusCode()
            );
        }
        return $response->getBody()->getContents();
    }
}
