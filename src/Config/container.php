<?php

use App\Controller\ApiController;
use App\Controller\AppController;
use App\Factory\TemplateFactory;
use App\Factory\ViewModelFactory;
use App\Service\CacheService;
use App\Service\ConfigService;
use App\Service\ContextService;
use App\Service\HttpService;
use App\Service\JsonService;
use App\Service\LanguageService;
use App\Service\MapperService;
use App\Service\PdfService;
use App\Service\SecurityService;
use App\Service\TextModulesService;
use App\Service\TwigService;
use App\Service\TypesService;
use App\Service\ValidatorService;
use App\Service\VhsBuildService;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

$containerBuilder = new ContainerBuilder();

$containerBuilder->register('cache_adapter', FilesystemAdapter::class);

$containerBuilder->register('cache_service', CacheService::class)
    ->addArgument(new Reference('cache_adapter'));

$containerBuilder->register('http_service', HttpService::class);
$containerBuilder->register('json_service', JsonService::class);
$containerBuilder->register('mapper_service', MapperService::class);
$containerBuilder->register('pdf_service', PdfService::class);
$containerBuilder->register('validator_service', ValidatorService::class);

$containerBuilder->register('config_service', ConfigService::class)
    ->addArgument(new Reference('json_service'));

$containerBuilder->register('context_service', ContextService::class)
    ->setArguments([
        new Reference('cache_service'),
        new Reference('config_service'),
        new Reference('http_service'),
        new Reference('json_service'),
        new Reference('mapper_service'),
    ]);

$containerBuilder->register('view_model_factory', ViewModelFactory::class)
    ->setArguments([
        new Reference('language_service'),
        new Reference('types_service'),
        new Reference('validator_service'),
    ]);

$containerBuilder->register('template_factory', TemplateFactory::class)
    ->setArguments([
        new Reference('context_service'),
        new Reference('security_service'),
        new Reference('text_modules_service'),
        new Reference('view_model_factory'),
    ]);

$containerBuilder->register('twig_service', TwigService::class);

$containerBuilder->register('security_service', SecurityService::class)
    ->setArguments([
        new Reference('cache_service'),
        new Reference('config_service'),
        new Reference('http_service'),
        new Reference('json_service'),
    ]);

$containerBuilder->register('vhs_build_service', VhsBuildService::class)
    ->setArguments([
        new Reference('cache_service'),
        new Reference('config_service'),
        new Reference('http_service'),
        new Reference('json_service'),
        new Reference('security_service'),
    ]);

$containerBuilder->register('language_service', LanguageService::class)
    ->setArguments([
        new Reference('cache_service'),
        new Reference('config_service'),
        new Reference('http_service'),
        new Reference('json_service'),
        new Reference('security_service'),
        new Reference('vhs_build_service'),
    ]);

$containerBuilder->register('types_service', TypesService::class)
    ->setArguments([
        new Reference('cache_service'),
        new Reference('config_service'),
        new Reference('http_service'),
        new Reference('json_service'),
        new Reference('security_service'),
        new Reference('vhs_build_service'),
    ]);

$containerBuilder->register('text_modules_service', TextModulesService::class)
    ->setArguments([
        new Reference('cache_service'),
        new Reference('config_service'),
        new Reference('http_service'),
        new Reference('json_service'),
    ]);

$containerBuilder->register('app_controller', AppController::class)
    ->setPublic(true)
    ->setArguments([
        new Reference('pdf_service'),
        new Reference('template_factory'),
        new Reference('twig_service'),
    ]);

$containerBuilder->register('api_controller', ApiController::class)
    ->setPublic(true)
    ->setArguments([
        new Reference('cache_service'),
        new Reference('types_service'),
    ]);

$containerBuilder->compile();

return $containerBuilder;
