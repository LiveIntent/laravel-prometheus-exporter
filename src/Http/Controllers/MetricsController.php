<?php

namespace LiveIntent\TelescopePrometheusExporter\Http\Controllers;

use Illuminate\Routing\Controller;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;

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
