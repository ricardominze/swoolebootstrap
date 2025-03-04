<?php

declare(strict_types=1);

namespace App\Infra\Telemetry;

use OpenTelemetry\API\Globals;
use OpenTelemetry\API\Logs\LogRecord;
use OpenTelemetry\API\Logs\EventLogger;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\API\Metrics\MeterInterface;
use OpenTelemetry\API\Metrics\CounterInterface;
use OpenTelemetry\Contrib\Otlp\LogsExporter;
use OpenTelemetry\Contrib\Otlp\MetricExporter;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\SDK\Common\Attribute\Attributes;
use OpenTelemetry\SDK\Common\Export\Stream\StreamTransportFactory;
use OpenTelemetry\SDK\Logs\LoggerProvider;
use OpenTelemetry\SDK\Logs\Processor\SimpleLogRecordProcessor;
use OpenTelemetry\SDK\Metrics\MeterProvider;
use OpenTelemetry\SDK\Metrics\MetricReader\ExportingReader;
use OpenTelemetry\SDK\Resource\ResourceInfo;
use OpenTelemetry\SDK\Resource\ResourceInfoFactory;
use OpenTelemetry\SDK\Sdk;
use OpenTelemetry\SDK\Trace\Sampler\AlwaysOnSampler;
use OpenTelemetry\SDK\Trace\Sampler\ParentBased;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SemConv\ResourceAttributes;

use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\Adapter;

class Telemetry
{
  public static ?array $metricCounters = null;
  public static ?ResourceInfo        $resource  = null;
  public static ?MeterProvider  $meterProvider  = null;
  public static ?TracerProvider $tracerProvider = null;
  public static ?LoggerProvider $loggerProvider = null;
  public static ?CollectorRegistry $collectorRegistry = null;

  public static function create(string $appName, string $appVersion, string $appNameSpace, string $appEnv): void
  {
    self::$resource = ResourceInfoFactory::emptyResource()->merge(ResourceInfo::create(Attributes::create([
      ResourceAttributes::SERVICE_NAME => $appName,
      ResourceAttributes::SERVICE_VERSION => $appVersion,
      ResourceAttributes::SERVICE_NAMESPACE => $appNameSpace,
      ResourceAttributes::DEPLOYMENT_ENVIRONMENT_NAME => $appEnv
    ])));
  }

  // Create OTLP HTTP exporter for traces (spans)
  public static function configTracer(string $otlpHttpEndpoint, string $contentType): void
  {
    $spanExporter = new SpanExporter(
      (new OtlpHttpTransportFactory())->create($otlpHttpEndpoint, $contentType)
    );
    self::$tracerProvider = TracerProvider::builder()
      ->addSpanProcessor(
        new SimpleSpanProcessor($spanExporter)
      )
      ->setResource(self::$resource)
      ->setSampler(new ParentBased(new AlwaysOnSampler()))
      ->build();
  }

  // Create OTLP HTTP exporter for logs
  public static function configLogger(string $otlpHttpEndpoint, string $contentType): void
  {
    $logExporter = new LogsExporter(
      (new OtlpHttpTransportFactory())->create($otlpHttpEndpoint, $contentType)
    );

    self::$loggerProvider = LoggerProvider::builder()
      ->setResource(self::$resource)
      ->addLogRecordProcessor(
        new SimpleLogRecordProcessor($logExporter)
      )
      ->build();
  }

  // Create OTLP HTTP exporter for metrics
  public static function configMetric(string $otlpHttpEndpoint, string $contentType): void
  {
    $reader = new ExportingReader(
      new MetricExporter(
        (new OtlpHttpTransportFactory())->create($otlpHttpEndpoint, $contentType)
      )
    );
    self::$meterProvider = MeterProvider::builder()
      ->setResource(self::$resource)
      ->addReader($reader)
      ->build();
  }

  // Confirg Prometheus Metrics
  public static function configPrometheus(Adapter $storageAdapter, bool $registerDefaultMetrics = true): void 
  {
    self::$collectorRegistry = new CollectorRegistry($storageAdapter, $registerDefaultMetrics);
  }

  public static function getTracer($name): TracerInterface
  {
    $tracer = Globals::tracerProvider();
    return $tracer->getTracer($name);
  }

  // public static function getLogger(string $name, string $version, string $schemaUrl, iterable $attributes = []): TracerInterface
  // {
  // $logger = self::$loggerProvider->getLogger($name, $version, $schemaUrl, $attributes);
  // $eventLogger = new EventLogger($logger, 'my-domain');
  // $record = (new LogRecord('hello world'))->setSeverityText('INFO')->setAttributes([/*attributes*/]);
  // $eventLogger->logEvent('foo', $record);    
  // }

  public static function getMeter(string $name, ?string $version = null, ?string $schemaUrl = null, iterable $attributes = []): MeterInterface
  {
    $meter = Globals::meterProvider();
    return $meter->getMeter($name, $version, $schemaUrl, $attributes);
  }

  public static function getPrometheusRegistry(): CollectorRegistry
  {
    return self::$collectorRegistry;
  }

  public static function getPrometheusOut(): string
  {
    $renderer = new RenderTextFormat();
    return $renderer->render(self::$collectorRegistry->getMetricFamilySamples());
  }

  // Config SDK
  private static function configureSdk(): void
  {
    $builder = Sdk::builder()
      ->setPropagator(TraceContextPropagator::getInstance())
      ->setAutoShutdown(true);

    if (isset(self::$loggerProvider)) {
      $builder->setLoggerProvider(self::$loggerProvider);
    }

    if (isset(self::$tracerProvider)) {
      $builder->setTracerProvider(self::$tracerProvider);
    }

    if (isset(self::$meterProvider)) {
      $builder->setMeterProvider(self::$meterProvider);
    }

    $builder->buildAndRegisterGlobal();
  }

  // Start Config
  public static function start(): void
  {
    self::configureSdk();
  }
}
