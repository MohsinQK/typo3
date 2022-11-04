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

namespace TYPO3\CMS\Extbase\Tests\Functional\Mvc\Controller;

use ExtbaseTeam\ActionControllerTest\Controller\TestController;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;
use TYPO3\CMS\Extbase\Tests\Functional\Mvc\Controller\Fixture\Validation\Validator\CustomValidator;
use TYPO3\CMS\Extbase\Validation\Validator\ConjunctionValidator;
use TYPO3\CMS\Extbase\Validation\Validator\NotEmptyValidator;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class ActionControllerTest extends FunctionalTestCase
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var TestController
     */
    protected $subject;

    protected array $testExtensionsToLoad = [
        'typo3/sysext/extbase/Tests/Functional/Mvc/Controller/Fixture/Extension/action_controller_test',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $serverRequest = (new ServerRequest())->withAttribute('extbase', new ExtbaseRequestParameters());
        $this->request = new Request($serverRequest);
        $this->request = $this->request->withPluginName('Pi1');
        $this->request = $this->request->withControllerExtensionName('ActionControllerTest');
        $this->request = $this->request->withControllerName('Test');
        $this->request = $this->request->withFormat('html');

        $this->subject = $this->get(TestController::class);
    }

    /**
     * @test
     */
    public function customValidatorsAreProperlyResolved(): void
    {
        // Setup
        $this->request = $this->request->withControllerActionName('bar');
        $this->request = $this->request->withArgument('barParam', '');

        // Test run
        $this->subject->processRequest($this->request);

        // Assertions
        $arguments = $this->subject->getArguments();
        $argument = $arguments->getArgument('barParam');

        /** @var ConjunctionValidator $conjunctionValidator */
        $conjunctionValidator = $argument->getValidator();
        self::assertInstanceOf(ConjunctionValidator::class, $conjunctionValidator);

        $validators = $conjunctionValidator->getValidators();
        self::assertInstanceOf(\SplObjectStorage::class, $validators);

        $validators->rewind();
        self::assertInstanceOf(CustomValidator::class, $validators->current());
    }

    /**
     * @test
     */
    public function extbaseValidatorsAreProperlyResolved(): void
    {
        // Setup
        $this->request = $this->request->withControllerActionName('baz');
        $this->request = $this->request->withArgument('bazParam', [ 'notEmpty' ]);

        // Test run
        $this->subject->processRequest($this->request);

        // Assertions
        $arguments = $this->subject->getArguments();
        $argument = $arguments->getArgument('bazParam');

        /** @var ConjunctionValidator $conjunctionValidator */
        $conjunctionValidator = $argument->getValidator();
        self::assertInstanceOf(ConjunctionValidator::class, $conjunctionValidator);

        $validators = $conjunctionValidator->getValidators();
        self::assertInstanceOf(\SplObjectStorage::class, $validators);
        self::assertCount(1, $validators);

        $validators->rewind();
        self::assertInstanceOf(NotEmptyValidator::class, $validators->current());
    }

    /**
     * @test
     */
    public function resolveViewRespectsDefaultViewObjectName(): void
    {
        // Test setup
        $reflectionClass = new \ReflectionClass($this->subject);
        $reflectionMethod = $reflectionClass->getProperty('defaultViewObjectName');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->setValue($this->subject, JsonView::class);

        $this->request = $this->request->withControllerActionName('qux');

        // Test run
        $this->subject->processRequest($this->request);

        // Assertions
        $reflectionMethod = $reflectionClass->getProperty('view');
        $reflectionMethod->setAccessible(true);
        $view = $reflectionMethod->getValue($this->subject);
        self::assertInstanceOf(JsonView::class, $view);
    }
}
