<?php

namespace App\Service;

use Exception;
use Psr\Cache\InvalidArgumentException;

class ContextService
{
    /** @var CacheService */
    private $_cacheService;

    /** @var ConfigService */
    private $_configService;

    /** @var JsonService */
    private $_jsonService;

    /** @var HttpService */
    private $_httpService;

    /** @var MapperService */
    private $_mapperService;

    /**
     * @param CacheService $cacheService
     * @param ConfigService $configService
     * @param HttpService $httpService
     * @param JsonService $jsonService
     * @param MapperService $mapperService
     */
    public function __construct(
        CacheService $cacheService,
        ConfigService $configService,
        HttpService $httpService,
        JsonService $jsonService,
        MapperService $mapperService
    ) {
        $this->_cacheService = $cacheService;
        $this->_configService = $configService;
        $this->_httpService = $httpService;
        $this->_jsonService = $jsonService;
        $this->_mapperService = $mapperService;
    }

    /**
     * @param string $kind
     * @param string $type
     * @param string $identifiers
     * @param string $productId
     * @param string $jwt
     * @param bool $forceReload
     * @return array
     * @throws InvalidArgumentException
     */
    public function getContext(
        string $kind,
        string $type,
        string $identifiers,
        string $productId,
        string $jwt,
        bool $forceReload
    ): array {
        $contextCacheKey = $this->_cacheService->getContextCacheKey($kind, $type, $identifiers, $productId);
        if (!$forceReload && $this->_cacheService->has($contextCacheKey)) {
            $context = $this->_cacheService->get($contextCacheKey)->get();
        } else {
            $contextEndpointUrl = $this->_configService->getContextEndpointUrl($kind, $type, $identifiers, $productId);
            $context = $this->_httpService->getContext($contextEndpointUrl, $jwt);
            $context = $this->_jsonService->parseJson($context);
            $context = $context['response'];

            $this->_cacheService->set($contextCacheKey, $context);
        }

        return $this->_mapperService->map($context, $this->_configService->getMappingContext());
    }
}
