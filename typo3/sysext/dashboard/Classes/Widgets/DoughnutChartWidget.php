<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace TYPO3\CMS\Dashboard\Widgets;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Concrete Doughnut Chart widget implementation
 *
 * Shows a widget with a doughnut chart. The data for this chart will be provided by the data provider you will set.
 * You can add a button to the widget by defining a button provider.
 *
 * There are no options available for this widget
 *
 * @see ChartDataProviderInterface
 * @see ButtonProviderInterface
 */
class DoughnutChartWidget implements WidgetInterface, RequestAwareWidgetInterface, EventDataInterface, AdditionalCssInterface, JavaScriptInterface
{
    private ServerRequestInterface $request;

    public function __construct(
        private readonly WidgetConfigurationInterface $configuration,
        private readonly ChartDataProviderInterface $dataProvider,
        private readonly BackendViewFactory $backendViewFactory,
        // @deprecated since v12, will be removed in v13 together with services 'dashboard.views.widget' and Factory
        protected readonly ?StandaloneView $view = null,
        private readonly ?ButtonProviderInterface $buttonProvider = null,
        private readonly array $options = [],
    ) {
    }

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    public function renderWidgetContent(): string
    {
        $view = $this->backendViewFactory->create($this->request, 'typo3/cms-dashboard');
        $view->assignMultiple([
            'button' => $this->buttonProvider,
            'options' => $this->options,
            'configuration' => $this->configuration,
        ]);
        return $view->render('Widget/ChartWidget');
    }

    public function getEventData(): array
    {
        return [
            'graphConfig' => [
                'type' => 'doughnut',
                'options' => [
                    'maintainAspectRatio' => false,
                    'legend' => [
                        'display' => true,
                        'position' => 'bottom',
                    ],
                    'cutoutPercentage' => 60,
                ],
                'data' => $this->dataProvider->getChartData(),
            ],
        ];
    }

    public function getCssFiles(): array
    {
        return ['EXT:dashboard/Resources/Public/Css/Contrib/chart.css'];
    }

    public function getJavaScriptModuleInstructions(): array
    {
        return [
            JavaScriptModuleInstruction::forRequireJS('TYPO3/CMS/Dashboard/Contrib/chartjs'),
            JavaScriptModuleInstruction::forRequireJS('TYPO3/CMS/Dashboard/ChartInitializer'),
        ];
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
