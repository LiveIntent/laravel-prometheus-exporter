<?php

namespace LiveIntent\LaravelPrometheusExporter\Http\Controllers;

use Prometheus\RenderTextFormat;
use Prometheus\CollectorRegistry;
use Illuminate\Routing\Controller;

class MetricsController extends Controller
{
    /**
     * List the recorded metrics.
     *
     * @param \Prometheus\CollectorRegistry  $registry
     * @return \Illuminate\Http\Response
     */
    public function __invoke(CollectorRegistry $registry)
    {
        $renderer = new RenderTextFormat();
        $content = $renderer->render($registry->getMetricFamilySamples());

        return response($content)->header('Content-Type', RenderTextFormat::MIME_TYPE);
    }
}
